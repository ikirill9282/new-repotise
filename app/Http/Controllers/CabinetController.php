<?php

namespace App\Http\Controllers;

use App\Helpers\CustomEncrypt;
use App\Jobs\CheckStripeVerification;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use App\Models\UserNotification;

class CabinetController extends Controller
{
  public function verify(Request $request)
  {
    $user = Auth::user();
    $verify_session = $user->getStripeVerifySession();
    $verify_error = ($verify_session && isset($verify_session->last_error) && !empty($verify_session->last_error))
      ? $verify_session->last_error->reason
      : null;

    return view('site.pages.verify', [
      'user' => $user,
      'errors' => (new ViewErrorBag())->put('default', new MessageBag(['form' => $verify_error])),
    ]);
  }

  public function verificate(Request $request)
  {
    $user = $request->user();
    // if ($user->verify()->where('type', 'stripe')->exists()) {
    //   $verify = $user->verify()->where('type', 'stripe')->first();
    //   $verify_session = Cashier::stripe()->identity->verificationSessions->retrieve($verify->code);
    //   dd($verify_session);
    // }
    $valid = $request->validate([
      'full_name' => 'required|string',
      'street' => 'required|string',
      'street2' => 'sometimes|nullable|string',
      'city' => 'required|string',
      'state' => 'required|string',
      'zip' => 'required|integer',
      'country' => 'required|string',
      'birthday' => 'required|string',
      'tax_id' => 'sometimes|nullable|integer',
      // 'tax_id' => 'required|integer',
      'phone' => 'sometimes|nullable|string',
      // 'phone' => 'required|string',
    ]);

    if (isset($valid['phone'])) $valid['phone'] = preg_replace('/[^0-9]+/is', '', $valid['phone']);

    $user->options()->update($valid);
    $user->updateStripeCustomer([
      'address' => [
        'line1' => $valid['street'],
        'line2' => $valid['street2'] ?? null,
        'city' => $valid['city'],
        'country' => $valid['country'],
        'postal_code' => $valid['zip'],
        'state' => $valid['state'],
      ],
      'phone' => preg_replace('/[^0-9]+/is', '', $valid['phone']),
    ]);

    DB::beginTransaction();
    try {
      $verify_session = Cashier::stripe()->identity->verificationSessions->create([
        'client_reference_id' => $user->stripe_id,
        'metadata' => [
          'user_id' => $user->id,
          'user_email' => $user->email,
        ],
        'provided_details' => [
          'email' => $user->email,
          'phone' => $user->options->phone,
        ],
        'related_customer' => $user->stripe_id,
        'return_url' => $user->makeCompletetVerifyUrl(),
        'type' => (isset($valid['tax_id']) && !empty($valid['tax_id'])) ? 'id_number' : 'document',
      ]);

      UserVerify::firstOrCreate(
        [
          'user_id' => $user->id,
          'type' => 'stripe',
        ],
        [
          'code' => $verify_session->id,
          'created_at' => Carbon::now()->timestamp,
        ]
      );
      History::userStartVerify($user, $verify_session->toArray());
      Log::info("Begin user verififcation $user->name", [
        'user' => $user,
        'session' => $verify_session->toArray(),
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->withErrors([
        'form' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return redirect($verify_session->url);
  }

  public function verifyComplete(Request $request)
  {
    $valid = $request->validate([
      'token' => 'required|string',
    ]);

    $data = CustomEncrypt::decodeUrlHash($valid['token']);
    $user = User::find($data['id']);

    // UserNotification::clear($user->id, 'stripe_verification');
    // UserNotification::create([
    //   'user_id' => $user->id,
    //   'type' => 'info',
    //   'message' => "Your verification is in progress. Please wait.",
    //   'group' => 'stripe_verification',
    //   'closable' => 0,
    // ]);
    CheckStripeVerification::dispatch($user);
    
    return redirect($user->makeProfileUrl());
    
    // $verify = $user->getStripeVerify();
    // $verify_session = $user->getStripeVerifySession();
    
    // if ($verify_session->status == 'requires_input') {
    //   $response = redirect($user->makeProfileVerificationUrl());
    //   if (!empty($verify_session->last_error)) {
    //     $response->withErrors([
    //       'form' => $verify_session->last_error->reason,
    //     ]);
    //   }
    //   return $response;
    // }

    // if ($verify_session->status == 'processing') {
    //   dd('proc');
    // }

    // dd($verify_session);

    // DB::beginTransaction();
    // try {
    //   History::userVerified($user);
    //   Log::info("User verification success $user->username", [
    //     'user' => $user,
    //     'verify' => $verify,
    //     'data' => $data,
    //   ]);
    //   $verify->delete();
    //   $user->update(['verified' => 1, 'stripe_verified_at' => Carbon::now()->format('Y-m-d H:i:s')]);
    // } catch (\Exception $e) {
    //   DB::rollBack();
    //   Log::error('Error while complete user verification', [
    //     'user' => $user,
    //     'verify' => $verify,
    //     'data' => $data,
    //     'error' => $e,
    //   ]);

    //   return redirect($user->makeProfileUrl());
    // }

    // DB::commit();
    // return redirect($user->makeProfileUrl() . '/?modal=success');
  }

  public function verifyCancel(Request $request)
  {
    $user = Auth::user();
    $verify = $user->getStripeVerify();
    if ($verify) {
      History::userCancelVerify($user);
      Log::info("User $user->username cancel verification.", [
        'user' => $user,
        'verify' => $verify,
      ]);
      
      Cashier::stripe()->identity->verificationSessions->cancel($verify->code);
      $verify->delete();

      return redirect($user->makeProfileVerificationUrl());
    }
  }

  public function profile(Request $request, ?string $slug = null)
  {
    $user = is_null($slug) ? Auth::user() : User::where('username', str_ireplace('@', '', $slug))->first();
    if (!$user) {
      return redirect('/unknown');
    }
    return view('site.pages.profile', [
      'user' => $user,
    ]);
  }
}
