<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use App\Models\User;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Carbon;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use App\Services\RecaptchaService;
use App\Services\IpRateLimitService;

class Auth extends Component
{
	use HasForm;

	public array $form = [
		'email' => null,
		'password' => null,
		'2fa' => null,
		'backup' => null,
		'recaptcha_token' => null,
	];

	public ?string $user_id = null;

	public int $step = 1;

	public ?string $recaptcha_token = null;

	public bool $showRecaptchaV2 = false;

	protected function getRecaptchaService(): RecaptchaService
	{
		return app(RecaptchaService::class);
	}

	protected function getRateLimitService(): IpRateLimitService
	{
		return app(IpRateLimitService::class);
	}

	public function prepareEmail()
	{
		$validator = Validator::make(
			$this->form,
			[
				'email' => 'required|email',
			],
			[
				'email.required' => 'Please enter your email address.',
				'email.email' => 'Please enter a valid email address.',
			]
		);

		if ($validator->fails()) {
			throw new ValidationException($validator);
		}

		$valid = $validator->validated();
		$user = User::firstWhere('email', $valid['email']);

		if (!$user) {
			$this->resetValidation();
			$this->dispatch('openModal', 'register', ['email' => $valid['email']]);
			return;
		}

		$this->user_id = Crypt::encrypt($user->id);
		$this->step = 2;

		// reCAPTCHA disabled
		// Check if we need to show reCAPTCHA v2 when moving to step 2
		// $ipAddress = request()->ip();
		// $rateLimitService = $this->getRateLimitService();
		// $failedAttempts = $rateLimitService->getRemainingAttempts($ipAddress, 'login', 10, 60);
		// $this->showRecaptchaV2 = (10 - $failedAttempts) >= 3;
		$this->showRecaptchaV2 = false;
	}

	public function attempt()
	{
		Log::info('Auth::attempt called', [
			'step' => $this->step,
			'email' => $this->form['email'] ?? null,
		]);

		if ($this->step == 1) {
			return $this->prepareEmail();
		}

		$ipAddress = request()->ip();
		$rateLimitService = $this->getRateLimitService();

		// Check IP block
		if ($rateLimitService->isBlocked($ipAddress, 'login')) {
			$validator = Validator::make([], []);
			$validator->errors()->add('email', 'Your IP address has been temporarily blocked due to multiple failed login attempts. Please try again in 1 hour.');
			throw new ValidationException($validator);
		}

		// reCAPTCHA disabled
		// Check failed attempts to show reCAPTCHA v2
		// Only update if not already shown (to prevent hiding after validation errors)
		// if (!$this->showRecaptchaV2) {
		// 	$failedAttempts = $rateLimitService->getRemainingAttempts($ipAddress, 'login', 10, 60);
		// 	$this->showRecaptchaV2 = (10 - $failedAttempts) >= 3;
		// }
		$this->showRecaptchaV2 = false;

		$validator = Validator::make(
			$this->form,
			[
				'email' => 'required|email|exists:users,email',
				'password' => 'required|string',
				'2fa' => 'sometimes|nullable|string',
				'backup' => 'sometimes|nullable|boolean',
				// reCAPTCHA disabled
				// 'recaptcha_token' => $this->showRecaptchaV2 ? 'required|string' : 'sometimes|nullable|string',
			],
			[
				'email.required' => 'Please enter your email address.',
				'email.email' => 'Please enter a valid email address.',
				'email.exists' => 'Account with this email was not found.',
				'password.required' => 'Please enter your password.',
				// 'recaptcha_token.required' => 'Please complete the reCAPTCHA verification.',
			]
		);

		if ($validator->fails()) {
			throw new ValidationException($validator);
		}

		$valid = $validator->validated();

		// reCAPTCHA verification disabled
		// Verify reCAPTCHA (only if configured)
		// $recaptchaService = $this->getRecaptchaService();
		// $siteKey = $recaptchaService->getSiteKey();
		// 
		// if (!empty($siteKey)) {
		// 	if ($this->showRecaptchaV2) {
		// 		$recaptchaResult = $recaptchaService->verifyV2($valid['recaptcha_token'] ?? null);
		// 		if (!$recaptchaResult['success']) {
		// 			$validator->errors()->add('form.recaptcha_token', 'reCAPTCHA verification failed. Please try again.');
		// 			throw new ValidationException($validator);
		// 		}
		// 	} else {
		// 		// Verify reCAPTCHA v3
		// 		$recaptchaResult = $recaptchaService->verifyV3($this->recaptcha_token, 'login');
		// 		if (!$recaptchaResult['success']) {
		// 			// If V3 fails, enable V2 for the next attempt
		// 			$this->showRecaptchaV2 = true;
		// 			$validator->errors()->add('recaptcha_token', 'Verification failed. Please complete the captcha below.');
		// 			throw new ValidationException($validator);
		// 		}
		// 	}
		// }

		$user = $this->getUser()?->fresh();

		if ($user && !$user->active && $this->canRestoreFromDeletion($user)) {
			$user->forceFill([
				'active' => 1,
				'deletion_requested_at' => null,
				'deletion_scheduled_for' => null,
			])->save();

			$user->refresh();
		}

		if (!$user || !$user->active) {
			$rateLimitService->recordAttempt($ipAddress, 'login', false, $user?->id);
			$validator->errors()->add('email', 'Your account is temporarily locked. Please try again later or contact support.');
			throw new ValidationException($validator);
		}

		if ($user->twofa) {
			$this->verifyTwofa($user, $validator, $valid);
		}

		if (AuthFacade::attempt(['email' => $valid['email'], 'password' => $valid['password']], true)) {
			$rateLimitService->recordAttempt($ipAddress, 'login', true, $user->id);
			Session::regenerate(true);
			$url = str_ireplace('&modal=auth', '', url()->previous());
			$url = str_ireplace('?modal=auth', '', $url);
			return redirect($url);
		}

		$rateLimitService->recordAttempt($ipAddress, 'login', false, $user->id);
		$validator->errors()->add('email', 'Invalid email or password. Please try again.');
		throw new ValidationException($validator);
	}

	public function googleAuth()
	{
		return redirect()->away(
			Socialite::driver('google')
				->with(['prompt' => 'select_account'])
				->redirect()
				->getTargetUrl()
		);
	}

	public function fbAuth()
	{
		return redirect()->away(Socialite::driver('facebook')->redirect()->getTargetUrl());
	}

	public function xAuth()
	{
		return redirect()->away(
			Socialite::driver('x')
				->scopes(['tweet.read', 'users.read', 'offline.access']) // Request necessary scopes
				->redirect()
				->getTargetUrl()
		);
	}

	public function getUser(): ?User
	{
		return $this->user_id ? User::find(Crypt::decrypt($this->user_id)) : null;
	}

	protected function canRestoreFromDeletion(User $user): bool
	{
		if (!$user->deletion_scheduled_for) {
			return false;
		}

		return Carbon::parse($user->deletion_scheduled_for)->isFuture();
	}

	protected function verifyTwofa(User $user, $validator, array $valid): void
	{
		$code = isset($valid['2fa']) ? trim((string) $valid['2fa']) : '';
		$useBackup = (bool) ($valid['backup'] ?? false);

		if ($useBackup) {
			if ($code === '') {
				$validator->errors()->add('2fa', 'Please enter your backup code.');
				throw new ValidationException($validator);
			}

			$backup = $user->backup()->where('code', $code)->first();

			if (!$backup) {
				$validator->errors()->add('2fa', 'Invalid backup code.');
				throw new ValidationException($validator);
			}

			$backup->delete();

			return;
		}

		if ($code === '') {
			$validator->errors()->add('2fa', 'Enter the code from your authenticator app.');
			throw new ValidationException($validator);
		}

		if (empty($user->google2fa_secret)) {
			$validator->errors()->add('2fa', 'Two-factor authentication is not configured. Please contact support.');
			throw new ValidationException($validator);
		}

		try {
			$secret = Crypt::decryptString($user->google2fa_secret);
		} catch (\Throwable $e) {
			$validator->errors()->add('2fa', 'Unable to verify the authentication code. Please try again later.');
			throw new ValidationException($validator);
		}

		if (!Google2FA::verifyKey($secret, preg_replace('/\s+/', '', $code), 4)) {
			$validator->errors()->add('2fa', 'Invalid authenticator app code.');
			throw new ValidationException($validator);
		}
	}

	public function render()
	{
		return view('livewire.modals.auth');
	}
}
