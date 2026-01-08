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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Carbon;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use App\Services\RecaptchaService;
use App\Services\IpRateLimitService;
use Livewire\Attributes\On;

class Auth extends Component
{
	use HasForm;

	public array $form = [
		'email' => null,
		'password' => null,
		'code' => null,
		'use_backup' => false,
		'recaptcha_token' => null,
	];

	public ?string $user_id = null;

	public int $step = 1; // 1 = email only, 2 = email+password, 3 = 2FA

	public bool $emailVerified = false;

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

	public function checkEmail()
	{
		Log::info('Auth::checkEmail called', [
			'email' => $this->form['email'] ?? null,
		]);

		$ipAddress = request()->ip();
		$rateLimitService = $this->getRateLimitService();

		// Check IP block
		if ($rateLimitService->isBlocked($ipAddress, 'login')) {
			$validator = Validator::make([], []);
			$validator->errors()->add('email', 'Your IP address has been temporarily blocked due to multiple failed login attempts. Please try again in 1 hour.');
			throw new ValidationException($validator);
		}

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
		$user = User::firstWhere('email', $valid['email'])?->fresh();

		if (!$user) {
			// User not found - open registration modal
			$this->resetValidation();
			// Use openModalAfterClose to close current modal and open registration modal with email
			// Livewire dispatch passes parameters in order: first param, second param, etc.
			$this->dispatch('openModalAfterClose', 'register', ['email' => $valid['email']]);
			return;
		}

		// User found - mark email as verified and show password field
		$this->emailVerified = true;
		$this->step = 2;
		$this->resetValidation();
	}

	public function attempt()
	{
		Log::info('Auth::attempt called', [
			'step' => $this->step,
			'email' => $this->form['email'] ?? null,
		]);

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

		// Step 1: Only email - should call checkEmail() instead
		if ($this->step == 1 && !$this->emailVerified) {
			$this->checkEmail();
			return;
		}

		// Step 2: Email verified, need password
		if ($this->step == 2) {
			$validator = Validator::make(
				$this->form,
				[
					'email' => 'required|email',
					'password' => 'required|string',
					// reCAPTCHA disabled
					// 'recaptcha_token' => $this->showRecaptchaV2 ? 'required|string' : 'sometimes|nullable|string',
				],
				[
					'email.required' => 'Please enter your email address.',
					'email.email' => 'Please enter a valid email address.',
					'password.required' => 'Please enter your password.',
				]
			);
		} else {
			$validator = Validator::make(
				$this->form,
				[
					'code' => ['required', 'string', 'regex:/^(?:[0-9]{6}|[A-Za-z0-9]{6}|[A-Za-z0-9]{8,15})$/'],
					'use_backup' => 'sometimes|boolean',
				],
				[
					'code.required' => 'Please enter the authentication code.',
					'code.regex' => 'Invalid code format.',
				]
			);
		}

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

		$user = null;
		if ($this->step == 2) {
			// Email already verified, get user by email
			$user = User::firstWhere('email', $valid['email'])?->fresh();
		} else {
			// Step 3: 2FA - get user from encrypted ID
			$user = $this->getUser()?->fresh();
		}

		if ($user && !$user->active && $this->canRestoreFromDeletion($user)) {
			$user->forceFill([
				'active' => 1,
				'deletion_requested_at' => null,
				'deletion_scheduled_for' => null,
			])->save();

			$user->refresh();

			// I.toast.8 (TЗ): Account Deletion Canceled 👍
			$this->dispatch('toastSuccess', [
				'message' => 'Your account deletion request has been canceled. Welcome back!',
				'heading' => 'Account Deletion Canceled 👍',
			]);
		}

		if (!$user || !$user->active) {
			$rateLimitService->recordAttempt($ipAddress, 'login', false, $user?->id);
			$validator->errors()->add('email', 'Your account is temporarily locked. Please try again later or contact support.');
			throw new ValidationException($validator);
		}

		if ($this->step == 2) {
			// Validate credentials first (no 2FA field on this screen).
			if (!Hash::check((string) $valid['password'], (string) $user->password)) {
				$rateLimitService->recordAttempt($ipAddress, 'login', false, $user->id);
				$validator->errors()->add('password', 'Invalid password. Please try again.');
				throw new ValidationException($validator);
			}

			// If 2FA is enabled - go to step 3, ask for code/backup code.
			if ($user->twofa) {
				$this->user_id = Crypt::encrypt($user->id);
				$this->step = 3;
				$this->form['code'] = null;
				$this->form['use_backup'] = false;
				return;
			}

			// No 2FA: proceed with normal login.
			if (AuthFacade::attempt(['email' => $valid['email'], 'password' => $valid['password']], true)) {
				$rateLimitService->recordAttempt($ipAddress, 'login', true, $user->id);
				Session::regenerate(true);
				$url = str_ireplace('&modal=auth', '', url()->previous());
				$url = str_ireplace('?modal=auth', '', $url);
				return redirect($url);
			}

			$rateLimitService->recordAttempt($ipAddress, 'login', false, $user->id);
			$validator->errors()->add('password', 'Invalid password. Please try again.');
			throw new ValidationException($validator);
		}

		// Step 3: verify 2FA or backup code then log in.
		if (!$user->twofa) {
			$this->step = 2;
			$this->user_id = null;
			$validator->errors()->add('code', 'Two-factor authentication is not enabled for this account.');
			throw new ValidationException($validator);
		}

		$this->verifyTwofa($user, $validator, $valid);

		AuthFacade::login($user, true);
		$rateLimitService->recordAttempt($ipAddress, 'login', true, $user->id);
		Session::regenerate(true);

		$url = str_ireplace('&modal=auth', '', url()->previous());
		$url = str_ireplace('?modal=auth', '', $url);
		return redirect($url);
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

	public function backToLogin(): void
	{
		$this->step = 2;
		$this->user_id = null;
		$this->form['code'] = null;
		$this->form['use_backup'] = false;
		$this->resetValidation();
	}

	public function resetForm(): void
	{
		$this->step = 1;
		$this->emailVerified = false;
		$this->user_id = null;
		$this->form['email'] = null;
		$this->form['password'] = null;
		$this->form['code'] = null;
		$this->form['use_backup'] = false;
		$this->resetValidation();
	}

	public function mount(): void
	{
		// Reset form state when component is mounted
		$this->resetForm();
	}

	#[On('modal-opened')]
	public function handleModalOpened($data = null): void
	{
		// Reset form state when modal is opened
		// Handle different event formats
		$modal = null;
		if (is_array($data)) {
			$modal = $data['modal'] ?? $data[0]['modal'] ?? null;
		}
		
		// Reset if this is the auth modal or if no specific modal is specified
		if ($modal === 'auth' || $modal === null) {
			$this->resetForm();
		}
	}

	protected function verifyTwofa(User $user, $validator, array $valid): void
	{
		$code = isset($valid['code']) ? trim((string) $valid['code']) : '';
		$useBackup = (bool) ($valid['use_backup'] ?? false);

		if ($useBackup) {
			if ($code === '') {
				$validator->errors()->add('code', 'Please enter your backup code.');
				throw new ValidationException($validator);
			}

			$backup = $user->backup()->where('code', $code)->first();

			if (!$backup) {
				$validator->errors()->add('code', 'Invalid backup code.');
				throw new ValidationException($validator);
			}

			$backup->delete();

			return;
		}

		if ($code === '') {
			$validator->errors()->add('code', 'Enter the code from your authenticator app.');
			throw new ValidationException($validator);
		}

		if (empty($user->google2fa_secret)) {
			$validator->errors()->add('code', 'Two-factor authentication is not configured. Please contact support.');
			throw new ValidationException($validator);
		}

		try {
			$secret = Crypt::decryptString($user->google2fa_secret);
		} catch (\Throwable $e) {
			$validator->errors()->add('code', 'Unable to verify the authentication code. Please try again later.');
			throw new ValidationException($validator);
		}

		if (!Google2FA::verifyKey($secret, preg_replace('/\s+/', '', $code), 4)) {
			$validator->errors()->add('code', 'Invalid authenticator app code.');
			throw new ValidationException($validator);
		}
	}

	public function render()
	{
		return view('livewire.modals.auth');
	}
}
