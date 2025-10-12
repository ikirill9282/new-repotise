<?php

namespace App\Http\Controllers;

use App\Helpers\CustomEncrypt;
use App\Jobs\CheckStripeVerification;
use App\Models\Article;
use App\Models\Product;
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
use App\Models\Order;
use App\Models\OrderProducts;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class CabinetController extends Controller
{

  public function dashboard()
  {
    return view('site.pages.profile-dashboard');
  }

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
      'phone' => 'sometimes|nullable|string',
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

    CheckStripeVerification::dispatch($user);
    
    return redirect($user->makeProfileUrl());
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

  public function profile(Request $request)
  {
    $user = Auth::user();
    if (!$user->hasRole('creator')) {
      return redirect()->route('profile.purchases');
    }

    return view('site.pages.profile-creator', [
      'user_id' => Crypt::encrypt($user->id),
    ]);
  }

  public function public_profile(Request $request, string $slug)
  {
    $user = User::where('username', $slug)->first();
    
    if (!$user) {
      return redirect('/unknown');
    }

    return view('site.pages.profile', [
      'user_id' => Crypt::encrypt($user->id),
    ]);
  }

  public function purchases(Request $request, ?string $type = null)
  {
    $user = Auth::user();
    if (!$user) {
      return redirect('/unknown');
    }

    return view('site.pages.profile-purchases', [
      'user' => $user,
      'type' => $type,
    ]);
  }

  public function referal(Request $request)
  {
    $user = Auth::user();
    if (!$user) {
      return redirect('/unknown');
    }
    return view('site.pages.profile-referal', [
      'user' => $user,
    ]);
  }

  public function settings(Request $request)
  {
    $user = Auth::user();
    if (!$user) {
      return redirect('/unknown');
    }
    return view('site.pages.profile-settings', [
      'user' => $user,
    ]);
  }

  public function checkout(Request $request)
  {
    $valid = $request->validate(['order' => 'required|string']);
    $id = CustomEncrypt::getId($valid['order']);
    Session::put('checkout', $id);
    
    return redirect()->route('checkout');
  }

  public function products(Request $request)
  {
    $user = Auth::user();
    
    return view('site.pages.profile-products', [
      'user' => $user,
    ]);
  }

  public function articles(Request $request)
  {
    $user = Auth::user();

    return view('site.pages.profile-articles', [
      'user' => $user,
    ]);
  }

  public function reviews(Request $request)
  {
    $user = Auth::user();

    return view('site.pages.profile-reviews', [
      'user' => $user,
    ]);
  }

  public function sales(Request $request)
  {
    $user = Auth::user();

    return view('site.pages.profile-sales', [
      'user' => $user,
    ]);
  }

  public function create_article(Request $request)
  {
    $id = null;

    if (!empty($request->get('aid'))) {
      $decryptionError = false;
      try {
        $aid = Crypt::decrypt($request->get('aid'));
      }
      catch (\Exception $e) { $decryptionError = true; }
      catch (\Error $e) { $decryptionError = true; }

      if ($decryptionError) {
        Log::warning('Invlid AID parameter on product creation page', ['request' => $request, 'user' => Auth::user(), 'error' => $e]);
        $aid = null;
      }

      $article = Article::find($aid);
      
      if (!$article) {
        Log::warning('Undefined Product ID on product creation page', ['request' => $request, 'user' => Auth::user()]);
        $aid = null;
      } elseif ($article->author->id !== $request->user()->id) {
        Log::emergency('Not Product Owner on product creation page', ['request' => $request, 'user' => Auth::user()]);
        return redirect('profile');
      }

      if ($aid) $id = $request->get('aid');
    }

    return view('site.pages.create-article', ['article_id' => $id]);
  }


  public function create_product(Request $request)
  {
    $id = null;

    if (!empty($request->get('pid'))) {
      $decryptionError = false;
      try {
        $pid = Crypt::decrypt($request->get('pid'));
      }
      catch (\Exception $e) { $decryptionError = true; }
      catch (\Error $e) { $decryptionError = true; }
      
      if ($decryptionError) {
        Log::warning('Invlid PID parameter on product creation page', ['request' => $request, 'user' => Auth::user(), 'error' => $e]);
        $pid = null;
      }

      $product = Product::find($pid);
      if (!$product) {
        Log::warning('Undefined Product ID on product creation page', ['request' => $request, 'user' => Auth::user()]);
        $pid = null;
      } elseif ($product->author->id !== $request->user()->id) {
        Log::emergency('Not Product Owner on product creation page', ['request' => $request, 'user' => Auth::user()]);
        return redirect('profile');
      }

      if ($pid) $id = $request->get('pid');
    }
    return view('site.pages.create-product', ['product_id' => $id]);
  }

  public function create_product_media(Request $request)
  {
    if (!empty($request->get('pid'))) {
      $decryptionError = false;
      try {
        $pid = Crypt::decrypt($request->get('pid'));
      }
      catch (\Exception $e) { $decryptionError = true; }
      catch (\Error $e) { $decryptionError = true; }
      
      if ($decryptionError) {
        Log::warning('Invlid PID parameter on product creation page', ['request' => $request, 'user' => Auth::user(), 'error' => $e]);
        $pid = null;
      }

      $product = Product::find($pid);
      if (!$product) {
        Log::warning('Undefined Product ID on product creation page', ['request' => $request, 'user' => Auth::user()]);
        $pid = null;
      } elseif ($product->author->id !== $request->user()->id) {
        Log::emergency('Not Product Owner on product creation page', ['request' => $request, 'user' => Auth::user()]);
        return redirect('profile');
      }

      if ($pid) $id = $request->get('pid');
      return view('site.pages.create-product-media', ['product_id' => $id]);
    }
    return redirect()->route('profile.products.create');
  }

  protected function getUser(string $slug)
  {
    return is_null($slug) ? Auth::user() : User::where('username', str_ireplace('@', '', $slug))->first();
  }
}
