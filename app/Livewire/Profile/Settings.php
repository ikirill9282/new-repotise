<?php

namespace App\Livewire\Profile;

use App\Models\Policies;
use App\Models\User;
use App\Models\UserOptions;
use App\Traits\StoresStripePaymentMethods;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Settings extends Component
{
    use StoresStripePaymentMethods, WithFileUploads;

    protected $listeners = [
        'twofa-enabled' => 'onTwofaEnabled',
        'twofa-disabled' => 'onTwofaDisabled',
        'payment-method-added' => 'refreshPaymentMethods',
    ];

    protected bool $skipTwofaWatcher = false;

    public ?User $user = null;

    public ?UserOptions $options = null;

    public array $profile = [
        'full_name' => '',
        'username' => '',
    ];

    public array $security = [
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
        'twofa' => false,
    ];

    public array $preferences = [
        'creator_visible' => true,
        'show_donate' => true,
        'show_products' => true,
        'show_insights' => true,
    ];

    public array $notificationLabels = [
        'product_updates' => 'New Product Updates',
        'referral_updates' => 'Referral Program Updates',
        'news_updates' => 'News & Updates',
        'insights_updates' => 'Travel Insights Subscriptions',
    ];

    public array $notifications = [];

    public array $paymentMethods = [];
    public array $payoutMethods = [];
    public ?string $selectedPaymentMethod = null;
    public ?string $selectedPayoutMethod = null;
    public bool $showPaymentForm = false;
    public ?string $paymentSetupSecret = null;
    public string $stripePublishableKey = '';

    public array $returnPolicies = [];
    public ?int $selectedReturnPolicy = null;

    public bool $isSeller = false;

    public $avatar = null;

    protected array $messages = [
        'security.password.same' => 'Passwords do not match.',
        'security.password_confirmation.same' => 'Passwords do not match.',
    ];

    public function mount(): void
    {
        $this->user = Auth::user();

        if (!$this->user) {
            abort(403);
        }

        $this->user->loadMissing('roles');

        $this->loadReturnPolicies();
        $this->refreshState();

        if (session()->has('email_change_success')) {
            $this->dispatch('toastSuccess', ['message' => session()->pull('email_change_success')]);
        }

        if (session()->has('email_change_error')) {
            $this->dispatch('toastError', ['message' => session()->pull('email_change_error')]);
        }

        $this->stripePublishableKey = config('cashier.key') ?? env('STRIPE_KEY', '');
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'avatar') {
            $this->uploadAvatar();
            return;
        }

        if (!in_array($propertyName, ['security.password', 'security.password_confirmation'], true)) {
            return;
        }

        $this->resetValidation(['security.password', 'security.password_confirmation']);

        $password = (string) ($this->security['password'] ?? '');
        $confirmation = (string) ($this->security['password_confirmation'] ?? '');

        if ($password === '' || $confirmation === '') {
            return;
        }

        if ($password !== $confirmation) {
            $this->addError('security.password', 'Passwords do not match.');
            $this->addError('security.password_confirmation', 'Passwords do not match.');
        }
    }

    public function saveAll(): void
    {
        $this->normalizePasswordFields();

        try {
            $this->validateProfile();
            $this->validateSecurity();
            $this->validatePreferences();
            $this->validateNotifications();

            $this->user->username = $this->profile['username'];
            $this->user->email = $this->security['email'];
            $this->user->twofa = $this->security['twofa'] ? 1 : 0;

            if (!empty($this->profile['full_name'])) {
                $this->user->name = $this->profile['full_name'];
            }

            if (!empty($this->security['password'])) {
                $this->user->password = Hash::make($this->security['password']);
            }

            $this->user->save();

            $this->options->fill([
                'full_name' => $this->profile['full_name'] ?: null,
                'preferred_payment_method' => $this->selectedPaymentMethod,
                'preferred_payout_method' => $this->selectedPayoutMethod,
                'return_policy_id' => $this->selectedReturnPolicy,
                'creator_visible' => $this->preferences['creator_visible'],
                'show_donate' => $this->preferences['show_donate'],
                'show_products' => $this->preferences['show_products'],
                'show_insights' => $this->preferences['show_insights'],
                'notification_settings' => $this->notifications,
            ]);

            $this->options->save();

            $this->security['password'] = '';
            $this->security['password_confirmation'] = '';

            session()->flash('status', 'Account settings updated.');

            $this->dispatch('toastSuccess', ['message' => 'Account settings updated.']);

            $this->refreshState();
        } catch (ValidationException $e) {
            $this->dispatch('toastError', ['message' => 'Please correct the highlighted errors.']);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Failed to update account settings.', [
                'user_id' => $this->user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Failed to update account settings. Please try again.']);
            throw $e;
        }
    }

    public function cancel(): void
    {
        $this->refreshState();
        session()->flash('status', 'Changes reverted.');
    }

    protected function refreshState(): void
    {
        $this->user->refresh()->loadMissing('roles');
        $this->options = $this->user->options()->firstOrCreate([]);

        $this->isSeller = $this->user->hasAnyRole(['creator', 'seller']);

        $this->profile['full_name'] = $this->options->full_name ?? $this->user->name ?? '';
        $this->profile['username'] = $this->user->username ?? '';

        $this->security['email'] = $this->user->email ?? '';
        $this->setTwofaToggle((bool) ($this->user->twofa ?? false));
        $this->security['password'] = '';
        $this->security['password_confirmation'] = '';

        $this->preferences = [
            'creator_visible' => $this->options->creator_visible ?? true,
            'show_donate' => $this->options->show_donate ?? true,
            'show_products' => $this->options->show_products ?? true,
            'show_insights' => $this->options->show_insights ?? true,
        ];

        $defaultNotifications = array_fill_keys(array_keys($this->notificationLabels), false);
        $storedNotifications = $this->options->notification_settings ?? [];
        $this->notifications = array_merge($defaultNotifications, $storedNotifications);

        $this->selectedReturnPolicy = $this->options->return_policy_id;

        $this->loadPaymentData();

        if ($this->showPaymentForm) {
            $this->cancelPaymentMethodForm();
        }
    }

    protected function loadPaymentData(): void
    {
        $methods = $this->fetchStripePaymentMethods();

        $this->paymentMethods = $methods;
        $this->payoutMethods = $methods;

        $this->selectedPaymentMethod = $this->options->preferred_payment_method ?? ($this->paymentMethods[0]['id'] ?? null);
        $this->selectedPayoutMethod = $this->options->preferred_payout_method ?? ($this->payoutMethods[0]['id'] ?? null);
    }

    public function startAddPaymentMethod(): void
    {
        if ($this->showPaymentForm) {
            return;
        }

        if ($this->stripePublishableKey === '') {
            Log::error('Stripe publishable key missing for settings payment form.');
            $this->dispatch('toastError', ['message' => 'Payment configuration is missing. Please contact support.']);
            return;
        }

        try {
            if (empty($this->user->stripe_id)) {
                $this->user->createOrGetStripeCustomer();
            }

            $intent = Cashier::stripe()->setupIntents->create([
                'customer' => $this->user->stripe_id,
                'payment_method_types' => ['card'],
                'usage' => 'off_session',
            ]);

            $this->paymentSetupSecret = $intent->client_secret;
            $this->showPaymentForm = true;

            $this->dispatch('settings-payment-form-ready', [
                'publishableKey' => $this->stripePublishableKey,
                'clientSecret' => $this->paymentSetupSecret,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to initialize Stripe setup intent for settings page.', [
                'user_id' => $this->user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Unable to start card registration. Please try again.']);
        }
    }

    public function cancelPaymentMethodForm(): void
    {
        $this->showPaymentForm = false;
        $this->paymentSetupSecret = null;
        $this->dispatch('settings-payment-form-reset');
    }

    #[On('settings-payment-method-submitted')]
    public function completeAddPaymentMethod(string|array $paymentMethodId): void
    {
        if (is_array($paymentMethodId)) {
            $paymentMethodId = $paymentMethodId['paymentMethodId'] ?? '';
        }

        if ($paymentMethodId === '') {
            $this->dispatch('settings-payment-form-error');
            $this->dispatch('toastError', ['message' => 'Payment method identifier missing.']);
            return;
        }

        try {
            $paymentMethod = $this->storeStripePaymentMethod($this->user, $paymentMethodId);
            $this->user->updateDefaultPaymentMethod($paymentMethod->id);

            $this->cancelPaymentMethodForm();
            $this->refreshPaymentMethods($paymentMethod->id);

            $this->dispatch('toastSuccess', ['message' => 'Card saved successfully.']);
        } catch (\Throwable $e) {
            Log::error('Failed to attach payment method from settings form.', [
                'user_id' => $this->user->id ?? null,
                'payment_method' => $paymentMethodId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('settings-payment-form-error');
            $this->dispatch('toastError', ['message' => 'Unable to add payment method. Please try again.']);
        }
    }

    public function refreshPaymentMethods(?string $paymentMethodId = null): void
    {
        $this->loadPaymentData();

        if ($paymentMethodId) {
            $this->selectedPaymentMethod = $paymentMethodId;
        }
    }

    protected function fetchStripePaymentMethods(): array
    {
        try {
            $collection = $this->user->paymentMethods();
        } catch (\Throwable $e) {
            Log::warning('Failed to load Stripe payment methods for settings page', [
                'user_id' => $this->user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $collection = collect();
        }

        if (!($collection instanceof Collection) || $collection->isEmpty()) {
            return [];
        }

        return $collection->map(function ($method) {
            $brand = ucfirst($method->card->brand ?? 'Card');
            $last4 = $method->card->last4 ?? '0000';
            $expires = sprintf('%02d/%s', $method->card->exp_month, $method->card->exp_year);

            return [
                'id' => $method->id,
                'label' => $brand,
                'last4' => $last4,
                'expires' => $expires,
            ];
        })->all();
    }

    protected function loadReturnPolicies(): void
    {
        $this->returnPolicies = Policies::query()
            ->select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(fn($policy) => [
                'id' => $policy->id,
                'title' => $policy->title,
            ])
            ->toArray();
    }

    protected function validateProfile(): void
    {
        $this->validate([
            'profile.full_name' => ['nullable', 'string', 'max:255'],
            'profile.username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->user->id),
            ],
        ]);
    }

    protected function validateSecurity(): void
    {
        $this->validate([
            'security.email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'security.password' => ['nullable', 'string', 'min:8', 'same:security.password_confirmation'],
            'security.password_confirmation' => ['nullable', 'string', 'min:8', 'same:security.password'],
            'security.twofa' => ['boolean'],
        ]);
    }

    protected function validatePreferences(): void
    {
        $this->validate([
            'preferences.creator_visible' => ['boolean'],
            'preferences.show_donate' => ['boolean'],
            'preferences.show_products' => ['boolean'],
            'preferences.show_insights' => ['boolean'],
            'selectedReturnPolicy' => ['nullable', 'integer', Rule::exists('policies', 'id')],
            'selectedPaymentMethod' => ['nullable', 'string'],
            'selectedPayoutMethod' => ['nullable', 'string'],
        ]);
    }

    protected function validateNotifications(): void
    {
        foreach (array_keys($this->notificationLabels) as $key) {
            $this->validate([
                "notifications.$key" => ['boolean'],
            ]);
        }
    }

    protected function normalizePasswordFields(): void
    {
        $password = $this->security['password'] ?? null;
        $confirmation = $this->security['password_confirmation'] ?? null;

        $password = is_string($password) && trim($password) === '' ? null : $password;
        $confirmation = is_string($confirmation) && trim($confirmation) === '' ? null : $confirmation;

        if ($confirmation === null) {
            $password = null;
        }

        $this->security['password'] = $password;
        $this->security['password_confirmation'] = $confirmation;
    }

    public function handleTwofaToggle($checked): void
    {
        $checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
        if ($checked) {
            if ($this->user->twofa) {
                $this->setTwofaToggle(true);
                return;
            }

            $this->setTwofaToggle(false);
            $this->dispatch('openModal', 'twofa');
            return;
        }

        if (!$this->user->twofa) {
            $this->setTwofaToggle(false);
            return;
        }

        $this->setTwofaToggle(true);
        $this->dispatch('openModal', 'twofa-disable');
    }

    protected function setTwofaToggle(bool $state): void
    {
        $this->skipTwofaWatcher = true;
        $this->security['twofa'] = $state;
        $this->skipTwofaWatcher = false;
    }

    public function onTwofaEnabled(): void
    {
        $this->refreshState();
        $this->setTwofaToggle(true);
    }

    public function onTwofaDisabled(): void
    {
        $this->refreshState();
        $this->setTwofaToggle(false);
    }

    public function uploadAvatar(): void
    {
        if (!$this->avatar) {
            return;
        }

        $this->validate([
            'avatar' => 'required|image|max:2048|mimes:jpeg,jpg,png,gif',
        ]);

        try {
            // Удаляем старую аватарку, если она не дефолтная
            $oldAvatar = $this->options->avatar;
            if ($oldAvatar && $oldAvatar !== '/storage/images/default_avatar.png' && strpos($oldAvatar, 'default_avatar') === false) {
                $oldPath = str_replace('/storage/', '', $oldAvatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Сохраняем новую аватарку
            $path = $this->avatar->store('images/avatars', 'public');
            $avatarPath = "/storage/$path";

            // Обновляем аватарку в user_options
            $this->options->avatar = $avatarPath;
            $this->options->save();

            // Сбрасываем загруженный файл
            $this->avatar = null;

            // Обновляем состояние
            $this->refreshState();

            $this->dispatch('toastSuccess', ['message' => 'Avatar updated successfully.']);
        } catch (ValidationException $e) {
            $this->dispatch('toastError', ['message' => 'Please select a valid image file (max 2MB, jpeg/jpg/png/gif).']);
            $this->avatar = null;
        } catch (\Throwable $e) {
            Log::error('Failed to upload avatar.', [
                'user_id' => $this->user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Failed to upload avatar. Please try again.']);
            $this->avatar = null;
        }
    }

    public function render()
    {
        return view('livewire.profile.settings');
    }
}
