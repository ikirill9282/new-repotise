<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\History;
use App\Models\RevenueShare;
use App\Models\Payout;
use App\Models\Withdrawal;
use App\Models\Order;
use App\Models\Subscriptions;
use App\Models\UserReferal;
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
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

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
            Infolists\Components\Tabs\Tab::make('Verification')
              ->visible(fn($record) => $record->hasRole('creator', 'refered-seller'))
              ->schema([
                Section::make('Stripe Connect Status')
                  ->schema([
                    TextEntry::make('stripe_account_status')
                      ->label('Stripe Account Status')
                      ->formatStateUsing(function($record) {
                        // TODO: Get from Stripe Connect API
                        if ($record->stripe_id) {
                          return 'Connected';
                        }
                        return 'Not Connected';
                      })
                      ->badge()
                      ->color(function($record) {
                        return $record->stripe_id ? 'success' : 'gray';
                      }),
                    TextEntry::make('stripe_id')
                      ->label('Stripe Customer ID')
                      ->formatStateUsing(fn($record) => $record->stripe_id ?? 'N/A')
                      ->copyable(),
                    TextEntry::make('stripe_verified_at')
                      ->label('Stripe Verified At')
                      ->formatStateUsing(fn($record) => $record->stripe_verified_at 
                        ? $record->stripe_verified_at->format('Y-m-d H:i:s') 
                        : 'Not verified')
                      ->dateTime(),
                  ])
                  ->columns(3),
                Section::make('Verification History')
                  ->schema([
                    TextEntry::make('verification_history')
                      ->label('')
                      ->formatStateUsing(function($record) {
                        $verifications = $record->verify()->orderByDesc('created_at')->limit(10)->get();
                        
                        if ($verifications->isEmpty()) {
                          return 'No verification history found.';
                        }
                        
                        $html = '<div class="space-y-2">';
                        foreach ($verifications as $verify) {
                          $html .= '<div class="p-2 bg-gray-50 dark:bg-gray-800 rounded text-sm">';
                          $html .= '<div class="font-medium">' . ($verify->type ?? 'N/A') . '</div>';
                          $html .= '<div class="text-xs text-gray-600 dark:text-gray-400">';
                          $html .= 'Status: ' . ($verify->status ?? 'N/A') . ' | ';
                          $html .= 'Date: ' . $verify->created_at->format('Y-m-d H:i:s');
                          $html .= '</div></div>';
                        }
                        $html .= '</div>';
                        
                        return $html;
                      })
                      ->html(),
                  ]),
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
            Infolists\Components\Tabs\Tab::make('Buyer Activity')
              ->visible(fn($record) => $record->hasRole('buyer') || $record->orders()->exists())
              ->schema([
                Section::make('Purchase Summary')
                  ->schema([
                    TextEntry::make('total_spent')
                      ->label('Total Spent')
                      ->formatStateUsing(function($record) {
                        $total = Order::where('user_id', $record->id)
                          ->where('status_id', '>=', 2) // PAID or higher
                          ->sum('cost');
                        return '$' . number_format($total, 2);
                      }),
                    TextEntry::make('total_orders')
                      ->label('Total Orders')
                      ->formatStateUsing(fn($record) => 
                        Order::where('user_id', $record->id)
                          ->where('status_id', '>=', 2)
                          ->count()
                      ),
                    TextEntry::make('active_subscriptions')
                      ->label('Active Subscriptions')
                      ->formatStateUsing(function($record) {
                        return $record->subscriptions()
                          ->where('stripe_status', 'active')
                          ->count();
                      }),
                  ])
                  ->columns(3),
                Section::make('Recent Orders')
                  ->schema([
                    TextEntry::make('recent_orders')
                      ->label('')
                      ->formatStateUsing(function($record) {
                        $orders = Order::where('user_id', $record->id)
                          ->with(['order_products.product'])
                          ->orderByDesc('created_at')
                          ->limit(10)
                          ->get();
                        
                        if ($orders->isEmpty()) {
                          return 'No orders found.';
                        }
                        
                        return view('filament.infolists.components.recent-orders', [
                          'orders' => $orders
                        ])->render();
                      })
                      ->html(),
                  ]),
              ]),
            Infolists\Components\Tabs\Tab::make('Financials & Payouts')
              ->visible(fn($record) => $record->hasRole('creator', 'refered-seller'))
              ->schema([
                Section::make('Stripe Balance')
                  ->schema([
                    TextEntry::make('stripe_balance_available')
                      ->label('Available Balance')
                      ->formatStateUsing(function($record) {
                        // TODO: Get from Stripe API
                        return '$' . number_format($record->balance, 2);
                      }),
                    TextEntry::make('stripe_balance_pending')
                      ->label('Pending Balance')
                      ->formatStateUsing(function($record) {
                        // TODO: Get from Stripe API
                        return '$0.00';
                      }),
                  ])
                  ->columns(2),
                Section::make('Recent Payouts')
                  ->schema([
                    TextEntry::make('recent_payouts')
                      ->label('')
                      ->formatStateUsing(function($record) {
                        $payouts = Payout::where('user_id', $record->id)
                          ->orderByDesc('created_at')
                          ->limit(10)
                          ->get();
                        
                        if ($payouts->isEmpty()) {
                          return 'No payouts found.';
                        }
                        
                        return view('filament.infolists.components.recent-payouts', [
                          'payouts' => $payouts
                        ])->render();
                      })
                      ->html(),
                  ]),
              ]),
            Infolists\Components\Tabs\Tab::make('Tiers & Commissions')
              ->visible(fn($record) => $record->hasRole('creator', 'refered-seller'))
              ->schema([
                Section::make('Current Level & Commission')
                  ->schema([
                    TextEntry::make('current_level')
                      ->label('Current Level')
                      ->formatStateUsing(function($record) {
                        return $record->options?->level?->title ?? 'Default';
                      }),
                    TextEntry::make('current_commission_rate')
                      ->label('Commission Rate')
                      ->formatStateUsing(fn($record) => number_format($record->getCurrentCommission(), 2) . '%'),
                    TextEntry::make('commission_source')
                      ->label('Commission Source')
                      ->formatStateUsing(function($record) {
                        // Check if user has individual commission rate
                        if ($record->options?->fee) {
                          return 'Admin Override';
                        }
                        // Check if referral bonus
                        if ($record->hasRole('refered-seller')) {
                          return 'Referral Bonus';
                        }
                        return 'Standard Tier';
                      })
                      ->badge()
                      ->color(function($record) {
                        if ($record->options?->fee) return 'warning';
                        if ($record->hasRole('refered-seller')) return 'info';
                        return 'success';
                      }),
                    TextEntry::make('storage_allocation')
                      ->label('Storage Allocation')
                      ->formatStateUsing(function($record) {
                        // TODO: Get from level or user options
                        return '10 GB'; // Placeholder
                      }),
                    TextEntry::make('storage_usage')
                      ->label('Current Storage Usage')
                      ->formatStateUsing(function($record) {
                        // TODO: Calculate actual usage
                        return '2.5 GB / 10 GB (25%)';
                      }),
                  ])
                  ->columns(2),
              ]),
            Infolists\Components\Tabs\Tab::make('Referral Program')
              ->schema([
                Section::make('Referral Status')
                  ->schema([
                    TextEntry::make('referral_type')
                      ->label('Type')
                      ->formatStateUsing(function($record) {
                        $isReferrer = $record->referals()->exists();
                        $isReferral = $record->referrer()->exists();
                        if ($isReferrer) return 'Referrer';
                        if ($isReferral) return 'Referral';
                        return 'None';
                      })
                      ->badge()
                      ->color(function($record) {
                        $isReferrer = $record->referals()->exists();
                        $isReferral = $record->referrer()->exists();
                        if ($isReferrer || $isReferral) return 'info';
                        return 'gray';
                      }),
                    TextEntry::make('referrer_name')
                      ->label('Invited By')
                      ->formatStateUsing(function($record) {
                        $referrer = $record->referrer;
                        return $referrer ? $referrer->username : 'N/A';
                      })
                      ->visible(fn($record) => $record->referrer()->exists()),
                    TextEntry::make('referral_date')
                      ->label('Referral Date')
                      ->formatStateUsing(function($record) {
                        $referral = UserReferal::where('referal_id', $record->id)->first();
                        return $referral ? $referral->created_at->format('Y-m-d H:i:s') : 'N/A';
                      })
                      ->visible(fn($record) => $record->referrer()->exists()),
                  ])
                  ->columns(3),
                Section::make('Referral Statistics')
                  ->visible(fn($record) => $record->referals()->exists())
                  ->schema([
                    TextEntry::make('total_referrals')
                      ->label('Total Referrals')
                      ->formatStateUsing(fn($record) => $record->referals()->count()),
                    TextEntry::make('referral_buyers')
                      ->label('Referred Buyers')
                      ->formatStateUsing(fn($record) => $record->referal_buyers()->count()),
                    TextEntry::make('referral_sellers')
                      ->label('Referred Sellers')
                      ->formatStateUsing(fn($record) => $record->referals()->whereHas('roles', fn($q) => $q->whereIn('name', ['creator', 'refered-seller']))->count()),
                    TextEntry::make('referral_earnings')
                      ->label('Referral Earnings')
                      ->formatStateUsing(function($record) {
                        $total = $record->referal_income()->sum('sum');
                        return '$' . number_format($total, 2);
                      }),
                  ])
                  ->columns(4),
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

