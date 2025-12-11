<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use App\Models\User;
use App\Models\UserMessages;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Services\RecaptchaService;
use App\Services\IpRateLimitService;

class Contact extends Component
{

    public string $user_id;
    
    public ?string $recaptcha_token = null;
    
    protected function getRecaptchaService(): RecaptchaService
    {
        return app(RecaptchaService::class);
    }
    
    protected function getRateLimitService(): IpRateLimitService
    {
        return app(IpRateLimitService::class);
    }

    public function mount(string $user_id)
    {
      $this->user_id = $user_id;
    }
    
    public function showContacts()
    {
        if (!Auth::check()) {
            $this->dispatch('closeModal');
            $this->dispatch('openModal', 'auth');
            return;
        }
        
        $user = Auth::user();
        $ipAddress = request()->ip();
        $rateLimitService = $this->getRateLimitService();
        
        // Check user limit (10 per day)
        if (!$rateLimitService->checkUserLimit($user->id, 'show_contacts', 10, 60 * 24)) {
            $this->dispatch('toastError', ['message' => 'You have reached the daily limit for viewing contacts. Please try again tomorrow.']);
            return;
        }
        
        // Verify reCAPTCHA v2
        $recaptchaService = $this->getRecaptchaService();
        $recaptchaResult = $recaptchaService->verifyV2($this->recaptcha_token);
        if (!$recaptchaResult['success']) {
            $this->dispatch('toastError', ['message' => 'reCAPTCHA verification failed. Please try again.']);
            return;
        }
        
        // Record successful attempt
        $rateLimitService->recordAttempt($ipAddress, 'show_contacts', true, $user->id);
        
        // Contacts are already shown in render, so we just need to verify
        $this->dispatch('toastSuccess', ['message' => 'Contacts displayed successfully.']);
    }

    public function getUser(): ?User
    {
      return User::where('id', Crypt::decrypt($this->user_id))->with('options')->first();
    }

    public function getContacts(): array
    {
      $user = $this->getUser();
      $result = [];
      
      if ($user->options->contact) {
        $result[] = $user->options->contact;
      }
      
      if ($user->options->contact2) {
        $result[] = $user->options->contact2;
      }

      return $result;
    }

    public function render()
    {
      return view('livewire.modals.contact', [
        'contacts' => $this->getContacts(),
        'user' => $this->getUser(),
      ]);
    }
}
