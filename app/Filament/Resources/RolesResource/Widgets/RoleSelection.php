<?php

namespace App\Filament\Resources\RolesResource\Widgets;

use Filament\Forms\Form;
use Spatie\Permission\Models\Role;
use Filament\Widgets\Widget;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class RoleSelection extends Widget implements HasForms
{

  use InteractsWithForms;

  protected static string $view = 'filament.resources.roles-resource.widgets.role-permissions';

  public ?Model $record = null;

  public ?int $role = null;

  public ?int $permission = null;

  public function form(Form $form): Form
  {
    return $form->schema([
      Fieldset::make('Give Permissions')
        ->columns(1)
        ->schema([
          Select::make('role')
            ->options(Role::all()->pluck('title', 'id'))
            ->reactive()
            ->afterStateUpdated(function ($state) {
              $this->dispatch('roleSelectionUpdated', ['role_id' => $state]);
            }),

          Select::make('permission')
            ->live()
            ->options($this->getPermissionOptions())
        ]),
      Actions::make([
        Action::make('submit')
          ->label('Assign Permission')
          ->action(fn() => $this->submit())
          ->visible(function ($get) {
            return !is_null($get('permission'));
          })
      ])
    ])

      ->extraAttributes(['class' => 'mb-4']);
  }

  public function submit()
  {
    $error_message = 'Something went wrong...';
    $data = $this->form->getState();
    if (isset($data['role'], $data['permission'])) {
      try {
        $role = Role::find($data['role']);
        $permission = Permission::find($data['permission']);
  
        if ($role && $permission) {
          $role->givePermissionTo($permission);
          Notification::make()
            ->title('Permission assigned!')
            ->success()
            ->send();

          $this->reset('permission');
          $this->cachedForms['form']->getFlatFields()['permission']->options($this->getPermissionOptions());
          $this->dispatch('roleSelectionUpdated', ['role_id' => $this->role]);          
        
          return;
        }
      } catch(\Exception $e) {
        $error_message = $e->getMessage();
      }
    }

    Notification::make()
      ->title($error_message)
      ->danger()
      ->send();

    return;
  }


  protected function getPermissionOptions()
  {
    return Permission::query()
      ->when(
        $this->role,
        fn($q) => $q->whereDoesntHave('roles', fn($sq) => $sq->where('role_has_permissions.role_id', '=', $this->role)),
      )
      ->pluck('title', 'id');
  }
}
