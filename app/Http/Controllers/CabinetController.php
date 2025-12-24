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
use App\Models\UserOptions;
use App\Models\EmailChange;
use App\Models\UserVerify;
use App\Mail\EmailChangedNew;
use App\Mail\EmailChangedOld;
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
use Illuminate\Support\Facades\Mail;

class CabinetController extends Controller
{

  public function dashboard()
  {
    return view('site.pages.profile-dashboard');
  }

  public function verify(Request $request)
  {
    $user = Auth::user();
    $verify_error = null;
    
    try {
      $verify_session = $user->getStripeVerifySession();
      if ($verify_session && isset($verify_session->last_error) && !empty($verify_session->last_error)) {
        $verify_error = $verify_session->last_error->reason;
      }
    } catch (\Exception $e) {
      Log::warning('Failed to retrieve Stripe verification session', [
        'user_id' => $user->id,
        'error' => $e->getMessage(),
      ]);
      // Continue without verification session - user can still fill the form
    }

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
      'zip' => 'required|string|regex:/^[0-9]+$/',
      'country' => 'required|string',
      'birthday' => 'required|string',
      'tax_id' => 'required|string',
      'phone' => 'sometimes|nullable|string',
      // Social media URLs - валидация URL только если чекбокс отмечен
      'youtube' => 'sometimes|nullable|url|max:255',
      'tiktok' => 'sometimes|nullable|url|max:255',
      'google' => 'sometimes|nullable|url|max:255',
      'facebook' => 'sometimes|nullable|url|max:255',
      'instagram' => 'sometimes|nullable|url|max:255',
      'twitter' => 'sometimes|nullable|url|max:255',
    ], [
      'full_name.required' => 'Please enter your Full Name.',
      'street.required' => 'Please enter a valid Address.',
      'city.required' => 'Please enter a valid Address.',
      'state.required' => 'Please enter a valid Address.',
      'zip.required' => 'Please enter a valid ZIP/Postal Code.',
      'zip.regex' => 'ZIP/Postal Code must contain only numbers.',
      'country.required' => 'Please enter a valid Address.',
      'birthday.required' => 'Please enter a valid Date of Birth.',
      'tax_id.required' => 'Please enter a valid Tax ID or Passport/ID Number.',
      'youtube.url' => 'Please enter a valid YouTube URL.',
      'tiktok.url' => 'Please enter a valid TikTok URL.',
      'google.url' => 'Please enter a valid Google URL.',
      'facebook.url' => 'Please enter a valid Facebook URL.',
      'instagram.url' => 'Please enter a valid Instagram URL.',
      'twitter.url' => 'Please enter a valid X (Twitter) URL.',
    ]);

    if (isset($valid['phone'])) $valid['phone'] = preg_replace('/[^0-9]+/is', '', $valid['phone']);
    
    // Очистка и преобразование zip в integer (только цифры)
    if (isset($valid['zip'])) {
      $zip = preg_replace('/[^0-9]+/is', '', $valid['zip']);
      $valid['zip'] = !empty($zip) ? (int) $zip : null;
    }

    // Обработка социальных сетей
    $socialNetworks = [
      'youtube' => 'youtube',
      'tiktok' => 'tiktok',
      'google' => 'google',
      'facebook' => 'facebook',
      'instagram' => 'instagram',
      'twitter' => 'xai', // В базе данных поле называется xai
    ];
    
    // Проверка обязательности URL для отмеченных чекбоксов
    foreach ($socialNetworks as $formField => $dbField) {
      $checkboxName = $formField . '_check';
      // Чекбоксы отправляются только если отмечены, поэтому проверяем наличие в запросе
      if ($request->has($checkboxName) && $request->input($checkboxName)) {
        // Если чекбокс отмечен, URL обязателен
        if (empty($valid[$formField])) {
          return redirect()->back()->withErrors([
            $formField => 'Please enter a valid URL for ' . ucfirst($formField) . '.',
          ])->withInput();
        }
      }
    }
    
    // Обработка и сохранение URL соцсетей
    foreach ($socialNetworks as $formField => $dbField) {
      $checkboxName = $formField . '_check';
      if ($request->has($checkboxName) && $request->input($checkboxName) && !empty($valid[$formField])) {
        // Сохраняем URL в правильное поле базы данных
        $valid[$dbField] = $valid[$formField];
      } else {
        // Если чекбокс не отмечен или URL пустой, очищаем поле
        $valid[$dbField] = null;
      }
      // Удаляем временное поле из формы
      unset($valid[$formField]);
    }
    
    // Проверка уникальности URL соцсетей
    $options = $user->options()->firstOrCreate([]);
    $visibility = $options->getSocialVisibility();
    
    foreach ($socialNetworks as $formField => $dbField) {
      if (!empty($valid[$dbField])) {
        $existingUser = UserOptions::where($dbField, $valid[$dbField])
          ->where('user_id', '!=', $user->id)
          ->first();
        if ($existingUser) {
          return redirect()->back()->withErrors([
            $formField => 'This ' . ucfirst($formField) . ' profile is already linked to another account.',
          ])->withInput();
        }
        // Устанавливаем видимость для заполненных соцсетей
        $visibility[$dbField] = true;
      } else {
        // Скрываем соцсети без URL
        $visibility[$dbField] = false;
      }
    }
    
    // Обновляем видимость соцсетей
    $valid['social_visibility'] = $visibility;

    // Сохраняем значение country для Stripe до преобразования
    $countryName = $valid['country'] ?? null;

    // Обработка country - находим страну по названию и сохраняем country_id
    if (isset($valid['country'])) {
      $country = \App\Models\Country::where('name', $valid['country'])->first();
      if ($country) {
        $valid['country_id'] = $country->id;
      }
      // Удаляем строковое поле country, так как в таблице используется country_id
      unset($valid['country']);
    }

    $user->options()->update($valid);
    $user->updateStripeCustomer([
      'address' => [
        'line1' => $valid['street'],
        'line2' => $valid['street2'] ?? null,
        'city' => $valid['city'],
        'country' => $countryName,
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
      Log::error('Verification error', [
        'user_id' => $user->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return redirect()->back()->withErrors([
        'form' => 'We encountered an error during verification. Please review the form and try again, or contact support if the issue persists.',
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

  public function edit(Request $request)
  {
    $user = Auth::user();

    if (!$user) {
      return redirect('/unknown');
    }

    if (!$user->hasRole('creator')) {
      return redirect()->route('profile');
    }

    return view('site.pages.profile-edit', [
      'user' => $user,
    ]);
  }

  public function public_profile(Request $request, string $slug)
  {
    $user = User::where('username', $slug)->first();
    
    if (!$user) {
      return redirect('/unknown');
    }

    // If the authenticated creator opens their own public profile, show creator page (with sidebar menu)
    // instead of showing the public-facing creator page.
    $authUser = Auth::user();
    if ($authUser && $authUser->id === $user->id && $authUser->hasRole('creator')) {
      return view('site.pages.profile-creator', [
        'user_id' => Crypt::encrypt($user->id),
      ]);
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

  public function confirmEmailChange(Request $request, string $token)
  {
    $user = $request->user();

    $hashedToken = hash('sha256', $token);

    $emailChange = EmailChange::where('token', $hashedToken)->first();

    if (!$emailChange || !$user || $emailChange->user_id !== $user->id) {
      session()->flash('email_change_error', 'The email change link is invalid or has already been used.');
      return redirect()->route('profile.settings');
    }

    if ($emailChange->created_at->lt(Carbon::now()->subHour())) {
      $emailChange->delete();
      session()->flash('email_change_error', 'The email change link has expired. Please request a new one.');
      return redirect()->route('profile.settings');
    }

    if (User::where('email', $emailChange->new_email)->where('id', '<>', $user->id)->exists()) {
      $emailChange->delete();
      session()->flash('email_change_error', 'The email address is already in use. Please choose another.');
      return redirect()->route('profile.settings');
    }

    $oldEmail = $user->email;
    $user->email = $emailChange->new_email;
    $user->email_verified_at = Carbon::now();
    $user->save();

    $emailChange->delete();

    // Notify both new and old email addresses (security)
    try {
      Mail::to($user->email)->send(new EmailChangedNew($user));
      Mail::to($oldEmail)->send(new EmailChangedOld($user, $user->email));
    } catch (\Throwable $e) {
      Log::warning('Failed to send email change notifications.', [
        'user_id' => $user->id,
        'old_email' => $oldEmail,
        'new_email' => $user->email,
        'error' => $e->getMessage(),
      ]);
      // We still consider the email change successful even if notifications fail.
    }

    session()->flash('email_change_success', 'Your email address has been updated.');

    return redirect()->route('profile.settings');
  }

  protected function getUser(string $slug)
  {
    return is_null($slug) ? Auth::user() : User::where('username', str_ireplace('@', '', $slug))->first();
  }
}
