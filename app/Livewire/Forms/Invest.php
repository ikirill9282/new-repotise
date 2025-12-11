<?php

namespace App\Livewire\Forms;

use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Services\RecaptchaService;

class Invest extends Component
{

    public array $fields = [
      'name' => null,
      'topic' => null,
      'text' => null,
      'recaptcha_token' => null,
    ];
    
    protected function getRecaptchaService(): RecaptchaService
    {
        return app(RecaptchaService::class);
    }
    
    public function submit()
    {
      $validator = Validator::make($this->fields, [
        'name' => 'required|string',
        'topic' => 'required|string',
        'text' => 'required|string',
        'recaptcha_token' => 'required|string',
      ], [
        'recaptcha_token.required' => 'Please complete the reCAPTCHA verification.',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();
      
      // Verify reCAPTCHA v2
      $recaptchaService = $this->getRecaptchaService();
      $recaptchaResult = $recaptchaService->verifyV2($valid['recaptcha_token'] ?? null);
      if (!$recaptchaResult['success']) {
        $validator->errors()->add('fields.recaptcha_token', 'reCAPTCHA verification failed. Please try again.');
        throw new ValidationException($validator);
      }
      
      // Remove recaptcha_token from data before saving
      unset($valid['recaptcha_token']);
      Form::create([
        'source' => 'Investment',
        'user_id' => Auth::check() ? Auth::user()->id : 0,
        'data' => json_encode($valid),
      ]);

      $this->dispatch('toastSuccess', [
        'message' => 'The form has been successfully submitted and will be forwarded to the administration for review. Thank you for your cooperation!'
      ]);
      $this->fields = [
        'name' => null,
        'topic' => null,
        'text' => null,
      ];
      $this->dispatch('resetForm');
    }

    public function render()
    {
        return view('livewire.forms.invest');
    }
}
