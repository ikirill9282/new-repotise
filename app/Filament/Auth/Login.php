<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Validator;

class Login extends BaseLogin
{
  // public function form(Form $form): Form
  // {
  //   return $form->schema([
  //     $this->getLoginFormComponent(), 
  //     $this->getPasswordFormComponent(),
  //     $this->getRememberFormComponent(),
  //   ])
  //   ->statePath('data');
  // }

  // protected function getLoginFormComponent(): Component
  // {
  //   return TextInput::make('username')
  //     ->label('Username')
  //     ->required()
  //     ->autocomplete()
  //     ->autofocus()
  //     ->extraInputAttributes(['tabindex' => 1]);
  // }

  // public function getCredentialsFromFormData(array $data): array
  // {
  //   $valid = Validator::validate($data, [
  //     'username' => 'required|string',
  //     'password' => 'required|string',
  //   ]);

  //   return $valid;
  // }
}