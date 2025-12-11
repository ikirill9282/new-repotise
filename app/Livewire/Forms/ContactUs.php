<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use App\Jobs\OptimizeMedia;
use App\Services\RecaptchaService;

class ContactUs extends Component
{
    use WithFileUploads;

    public array $fields = [
      'name' => null,
      'email' => null,
      'subject' => null,
      'text' => null,
      'file' => null,
      'recaptcha_token' => null,
    ];
    
    protected function getRecaptchaService(): RecaptchaService
    {
        return app(RecaptchaService::class);
    }


    public function submit(Request $request)
    {
      $validator = Validator::make($this->fields, [
        'name' => 'required|string',
        'email' => 'required|email',
        'subject' => 'required|string',
        'text' => 'required|string',
        'file' => 'sometimes|nullable|file|max:102400',
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
      if (!empty($valid['file'])) {
        $disk = config('filesystems.default');
        $path = $valid['file']->store('forms', $disk);
        $valid['file'] = $path;

        OptimizeMedia::dispatch($disk, $path);
      }

      Form::create([
        'source' => 'ContactUs',
        'user_id' => Auth::check() ? Auth::user()->id : 0,
        'data' => json_encode($valid),
      ]);

      $this->dispatch('toastSuccess', [
        'message' => 'The form has been successfully submitted and will be forwarded to the administration for review. Thank you for your cooperation!',
      ]);
      
      $this->fields = [
        'name' => null,
        'email' => null,
        'subject' => null,
        'text' => null,
        'file' => null,
      ];

      $this->dispatch('resetForm');
    }

    public function removeFile()
    {
        $this->fields['file'] = null;
    }

    public function render()
    {
        return view('livewire.forms.contact-us');
    }
}
