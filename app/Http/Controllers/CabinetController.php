<?php

namespace App\Http\Controllers;

use App\Helpers\CustomEncrypt;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CabinetController extends Controller
{
  public function verify(Request $request)
  {
    return view('site.pages.verify', [
      'user' => Auth::user(),
    ]);
  }

  public function verificate(Request $request)
  {
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

    $user = $request->user();
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
      if ($user->verify()->where('type', 'stripe')->exists()) {
        $verify = $user->verify()->where('type', 'stripe')->first();
        $verify_session = Cashier::stripe()->identity->verificationSessions->retrieve($verify->code);
      } else {
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
          'return_url' => url('/profile/verify/complete?token=' . CustomEncrypt::generateUrlHash(['id' => $user->id])),
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
      }
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
    $verify = $user->verify()->where('type', 'stripe')->first();

    DB::beginTransaction();
    try {
      $verify->delete();
      History::userVerified($user);
      $user->update(['verified' => 1, 'stripe_verified_at' => Carbon::now()->format('Y-m-d H:i:s')]);
      Log::info("User verification success $user->username", [
        'user' => $user,
        'verify' => $verify,
        'data' => $data,
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error while complete user verification', [
        'user' => $user,
        'verify' => $verify,
        'data' => $data,
        'error' => $e,
      ]);

      return redirect($user->makeProfileUrl());
    }

    DB::commit();
    return redirect($user->makeProfileUrl() . '/?modal=success');
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
