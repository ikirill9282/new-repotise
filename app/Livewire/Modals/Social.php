<?php

namespace App\Livewire\Modals;

use App\Models\User;
use App\Models\UserOptions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Social extends Component
{
    protected $user;

    public string $user_id;
    public array $socials = [];
    public array $social_icons = [];

    public function mount(string $user_id)
    {
      $this->user_id = $user_id;

      $user = $this->getUser();
      $this->socials = $user->options->getSocial();
      $this->social_icons = $user->options::getSocialIcons();
    }

    public function getIcon(string $key): ?string
    {
      return $this->social_icons[$key];
    }

    public function getUser()
    {
      return User::find(Crypt::decrypt($this->user_id));
    }

    public function submit()
    {
      $validator = Validator::make($this->socials, [
        'youtube' => 'sometimes|nullable|url',
        'tiktok' => 'sometimes|nullable|url',
        'facebook' => 'sometimes|nullable|url',
        'instagram' => 'sometimes|nullable|url',
        'google' => 'sometimes|nullable|url',
        'xai' => 'sometimes|nullable|url',
        'website' => 'sometimes|nullable|url',
        'other' => 'sometimes|nullable|url',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();
      $user = $this->getUser();
      $options = $user->options()->firstOrCreate([]);
      $visibility = $options->getSocialVisibility();

      foreach($valid as $key => &$val) {
        if (!is_null($val) && UserOptions::whereNot('user_id', $user->id)->where($key, $val)->exists()) {
          $validator->errors()->add($key, 'The link already exists.');
        }
        if (empty($val)) {
          $val = null;
          $visibility[$key] = false;
        } else {
          $visibility[$key] = true;
        }
      }

      if ($validator->errors()->isNotEmpty()) {
        throw new ValidationException($validator);
      }

      $options->update(array_merge($valid, [
        'social_visibility' => $visibility,
      ]));
      $this->dispatch('toastSuccess', ['message' => 'You links was updated successfull!']);
      $this->dispatch('closeModal');
      $this->dispatch('resetPage');
    }

    public function render()
    {
        return view('livewire.modals.social');
    }
}
