<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\History;
use App\Models\RevenueShare;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ViewUser extends ViewRecord
{
  protected static string $resource = UserResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\EditAction::make()
        ->slideOver()
        ->using(function (array $data, $record) {
          $oldRoles = $record->roles->pluck('name')->toArray();
          $oldStatus = $record->status;
          $oldEmail = $record->email;
          $oldName = $record->name;
          
          // Сохраняем роли отдельно
          $roleIds = $data['roles'] ?? [];
          unset($data['roles']);
          
          // Обновляем пользователя
          $record->update($data);
          
          // Сохраняем роли через Spatie Permission
          if (!empty($roleIds)) {
            $roles = Role::whereIn('id', $roleIds)->get();
            $newRoles = $roles->pluck('name')->toArray();
            $record->syncRoles($roles);
            
            // Логируем изменение роли
            if ($oldRoles !== $newRoles) {
              History::info()
                ->action('Role Changed')
                ->userId($record->id)
                ->initiator(Auth::id())
                ->values(implode(', ', $newRoles), implode(', ', $oldRoles))
                ->message("User roles changed from [" . implode(', ', $oldRoles) . "] to [" . implode(', ', $newRoles) . "]")
                ->payload(['ip_address' => request()->ip()])
                ->write();
            }
          } else {
            $record->syncRoles([]);
          }
          
          // Логируем изменения
          if (isset($data['status']) && $oldStatus !== $data['status']) {
            History::info()
              ->action('Status Changed')
              ->userId($record->id)
              ->initiator(Auth::id())
              ->values($data['status'], $oldStatus)
              ->message("User status changed from {$oldStatus} to {$data['status']}")
              ->payload(['ip_address' => request()->ip()])
              ->write();
          }
          
          if (isset($data['email']) && $oldEmail !== $data['email']) {
            History::info()
              ->action('Email Changed')
              ->userId($record->id)
              ->initiator(Auth::id())
              ->values($data['email'], $oldEmail)
              ->message("User email changed from {$oldEmail} to {$data['email']}")
              ->payload(['ip_address' => request()->ip()])
              ->write();
          }
          
          if (isset($data['name']) && $oldName !== $data['name']) {
            History::info()
              ->action('Name Changed')
              ->userId($record->id)
              ->initiator(Auth::id())
              ->values($data['name'], $oldName)
              ->message("User name changed from {$oldName} to {$data['name']}")
              ->payload(['ip_address' => request()->ip()])
              ->write();
          }
          
          return $record;
        }),
      Actions\Action::make('block')
        ->label(fn($record) => $record->isBlocked() ? 'Unblock' : 'Block')
        ->icon('heroicon-o-lock-closed')
        ->color(fn($record) => $record->isBlocked() ? 'success' : 'danger')
        ->requiresConfirmation()
        ->modalHeading(fn($record) => $record->isBlocked() ? 'Unblock User' : 'Block User')
        ->modalDescription(fn($record) => $record->isBlocked() 
          ? 'Are you sure you want to unblock this user?'
          : 'Are you sure you want to block this user? This will prevent them from logging in.')
        ->action(function ($record) {
          $wasBlocked = $record->isBlocked();
          
          if ($wasBlocked) {
            $record->unblock();
            $message = "User {$record->username} has been unblocked";
          } else {
            $record->block();
            $message = "User {$record->username} has been blocked";
          }
          
          History::warning()
            ->action($wasBlocked ? 'User Unblocked' : 'User Blocked')
            ->userId($record->id)
            ->initiator(Auth::id())
            ->message($message)
            ->payload(['ip_address' => request()->ip()])
            ->write();
          
          Notification::make()
            ->title($wasBlocked ? 'User unblocked' : 'User blocked')
            ->success()
            ->send();
        }),
      Actions\Action::make('delete')
        ->label('Delete User')
        ->icon('heroicon-o-trash')
        ->color('danger')
        ->requiresConfirmation()
        ->modalHeading('Delete User')
        ->modalDescription('Deleting a user will result in loss of access. Related data (products/orders) will remain in the system.')
        ->modalSubmitActionLabel('Confirm Deletion')
        ->action(function ($record) {
          $username = $record->username;
          $record->delete();
          
          History::warning()
            ->action('User Deleted')
            ->userId($record->id)
            ->initiator(Auth::id())
            ->message("User {$username} was deleted")
            ->payload(['ip_address' => request()->ip()])
            ->write();
          
          Notification::make()
            ->title('User deleted')
            ->success()
            ->send();
          
          return redirect(UserResource::getUrl('index'));
        }),
      Actions\Action::make('reset_password')
        ->label('Reset Password')
        ->icon('heroicon-o-key')
        ->color('warning')
        ->requiresConfirmation()
        ->modalHeading('Reset Password')
        ->modalDescription('A new password will be generated and sent to the user\'s email.')
        ->form([
          TextInput::make('password')
            ->label('New Password')
            ->password()
            ->required()
            ->minLength(8),
        ])
        ->action(function ($record, array $data) {
          $record->update(['password' => Hash::make($data['password'])]);
          
          History::info()
            ->action('Password Reset')
            ->userId($record->id)
            ->initiator(Auth::id())
            ->message("Password was reset for user {$record->username}")
            ->payload(['ip_address' => request()->ip()])
            ->write();
          
          Notification::make()
            ->title('Password reset')
            ->success()
            ->send();
        }),
      Actions\Action::make('change_commission')
        ->label('Change Commission')
        ->icon('heroicon-o-currency-dollar')
        ->color('info')
        ->visible(fn($record) => $record->hasRole('creator', 'refered-seller'))
        ->requiresConfirmation()
        ->modalHeading('Change Commission')
        ->modalDescription('Are you sure you want to change the commission for this seller?')
        ->form([
          TextInput::make('commission')
            ->label('Commission %')
            ->numeric()
            ->required()
            ->minValue(0)
            ->maxValue(100)
            ->default(fn($record) => $record->getCurrentCommission()),
        ])
        ->action(function ($record, array $data) {
          $oldCommission = $record->getCurrentCommission();
          $newCommission = (float) $data['commission'];
          
          if (!$record->options) {
            $record->options()->create(['fee' => $newCommission]);
          } else {
            $record->options->update(['fee' => $newCommission]);
          }
          
          History::info()
            ->action('Commission Changed')
            ->userId($record->id)
            ->initiator(Auth::id())
            ->values($newCommission, $oldCommission)
            ->message("Commission changed from {$oldCommission}% to {$newCommission}% for seller {$record->username}")
            ->payload(['ip_address' => request()->ip()])
            ->write();
          
          Notification::make()
            ->title('Commission updated')
            ->success()
            ->send();
        }),
    ];
  }

  public function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Infolists\Components\Tabs::make('User Information')
          ->tabs([
            Infolists\Components\Tabs\Tab::make('Basic Information')
              ->schema([
                Section::make('User Details')
                  ->schema([
                    TextEntry::make('id')
                      ->label('ID'),
                    TextEntry::make('username')
                      ->label('Username'),
                    TextEntry::make('name')
                      ->label('Name'),
                    TextEntry::make('email')
                      ->label('Email')
                      ->copyable(),
                    TextEntry::make('display_role')
                      ->label('Role')
                      ->formatStateUsing(fn($record) => $record->getDisplayRoleName())
                      ->badge()
                      ->color(fn($record) => match($record->getDisplayRoleName()) {
                        'Super Admin' => 'danger',
                        'Admin' => 'warning',
                        'Moderator' => 'info',
                        'Seller', 'Seller (Referral)' => 'success',
                        default => 'gray',
                      }),
                    TextEntry::make('display_status')
                      ->label('Status')
                      ->formatStateUsing(fn($record) => $record->getDisplayStatus())
                      ->badge()
                      ->color(fn($record) => match($record->getDisplayStatus()) {
                        'Active' => 'success',
                        'Blocked' => 'danger',
                        'Pending Verification' => 'warning',
                        'Deleted' => 'gray',
                        default => 'gray',
                      }),
                    TextEntry::make('created_at')
                      ->label('Registration Date')
                      ->dateTime(),
                    TextEntry::make('email_verified_at')
                      ->label('Email Verified At')
                      ->dateTime()
                      ->placeholder('Not verified'),
                    TextEntry::make('last_ip')
                      ->label('Last IP Address')
                      ->formatStateUsing(fn($record) => $record->getLastIpAddress() ?? 'N/A'),
                    TextEntry::make('country')
                      ->label('Country')
                      ->formatStateUsing(fn($record) => $record->country ?? 'N/A'),
                    TextEntry::make('profile_url')
                      ->label('Seller Profile')
                      ->formatStateUsing(fn($record) => $record->hasRole('creator', 'refered-seller') 
                        ? $record->makeProfileUrl() 
                        : 'N/A')
                      ->url(fn($record) => $record->hasRole('creator', 'refered-seller') 
                        ? $record->makeProfileUrl() 
                        : null)
                      ->openUrlInNewTab(),
                  ])
                  ->columns(2),
              ]),
            Infolists\Components\Tabs\Tab::make('Financial Information')
              ->visible(fn($record) => $record->hasRole('creator', 'refered-seller'))
              ->schema([
                Section::make('Commission')
                  ->schema([
                    TextEntry::make('current_commission')
                      ->label('Current Commission')
                      ->formatStateUsing(fn($record) => number_format($record->getCurrentCommission(), 2) . '%'),
                    TextEntry::make('individual_rate')
                      ->label('Individual Rate')
                      ->formatStateUsing(fn($record) => $record->options?->fee 
                        ? number_format($record->options->fee, 2) . '%' 
                        : 'Using level default'),
                    TextEntry::make('platform_default')
                      ->label('Platform Default')
                      ->formatStateUsing(fn($record) => $record->options?->level?->fee 
                        ? number_format($record->options->level->fee, 2) . '%' 
                        : 'N/A'),
                  ])
                  ->columns(3),
                Section::make('Earnings')
                  ->schema([
                    TextEntry::make('total_earnings')
                      ->label('Total Earnings')
                      ->formatStateUsing(fn($record) => '$' . number_format($record->getTotalEarnings(), 2)),
                    TextEntry::make('balance')
                      ->label('Amount Available for Withdrawal')
                      ->formatStateUsing(fn($record) => '$' . number_format($record->balance, 2)),
                    TextEntry::make('platform_commission')
                      ->label('Platform Commission (All Time)')
                      ->formatStateUsing(fn($record) => '$' . number_format($record->getPlatformCommission(), 2)),
                    TextEntry::make('stripe_fees')
                      ->label('Stripe Fees (All Time)')
                      ->formatStateUsing(fn($record) => '$' . number_format($record->getStripeFees(), 2)),
                  ])
                  ->columns(2),
              ]),
            Infolists\Components\Tabs\Tab::make('Activity Log')
              ->schema([
                Section::make('Recent Activity')
                  ->schema([
                    TextEntry::make('activity_log')
                      ->label('')
                      ->formatStateUsing(function ($record) {
                        $histories = History::where('user_id', $record->id)
                          ->with('initer')
                          ->orderBy('created_at', 'desc')
                          ->limit(50)
                          ->get();
                        
                        if ($histories->isEmpty()) {
                          return 'No activity recorded.';
                        }
                        
                        return view('filament.infolists.components.activity-log', [
                          'histories' => $histories
                        ])->render();
                      })
                      ->html(),
                  ]),
              ]),
          ]),
      ]);
  }

  protected function mutateFormDataBeforeFill(array $data): array
  {
    $data['roles'] = $this->record->roles->pluck('id')->toArray();
    return $data;
  }
}

