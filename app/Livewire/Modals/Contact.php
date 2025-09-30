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

class Contact extends Component
{

    public string $sender_id;
    public string $recipient_id;
    public ?string $text = null;

    public function mount(string $sender_id, string $recipient_id)
    {
      $this->sender_id = $sender_id;
      $this->recipient_id = $recipient_id;
    }

    public function getRecipient(): ?User
    {
      return $this->getModel($this->recipient_id);
    }

    public function getSender(): ?User
    {
      return $this->getModel($this->sender_id);
    }

    public function getModel(string $hash)
    {
      return User::find(Crypt::decrypt($hash));
    }
    
    public function submit()
    {
      $attributes = [
        'sender_id' => Crypt::decrypt($this->sender_id),
        'recipient_id' => Crypt::decrypt($this->recipient_id),
        'text' => $this->text,
      ];

      $validator = Validator::make($attributes, [
        'sender_id' => 'required|integer',
        'recipient_id' => 'required|integer',
        'text' => 'required|string',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }
      
      $valid = $validator->validated();
      
      $last_hour_messages = UserMessages::where([
          'recipient_id' => $valid['recipient_id'], 
          'sender_id' => $valid['sender_id'],
        ])
        ->where('created_at', '>=', Carbon::now()->modify('-1 hour'))
        ->exists()
        ;
      
      if ($last_hour_messages) {
        $this->dispatch('toastError', ['message' => 'You have already sent a message to this user. Please try again in an hour.']);
        return ;
      }

      try {
        UserMessages::create($valid);
      } catch (\Exception $e) {
        Log::error('Error contact with author', [
          'attributes' => $valid,
          'error' => $e,
        ]);
        $this->dispatch('toastError', ['message' => 'Something went wrong...']);
      } catch (\Error $e) {
        Log::error('Error contact with author', [
          'attributes' => $valid,
          'error' => $e,
        ]);
        $this->dispatch('toastError', ['message' => 'Something went wrong...']);
      }
      
      $this->dispatch('closeModal');
      $this->dispatch('toastSuccess', ['message' => 'Your message has been sent. You will receive a notification when the creator gets it.']);
    }

    public function rendered($view, $html)
    {
      $this->dispatch('form-rendered');
    }

    public function render()
    {
      return view('livewire.modals.contact');
    }
}
