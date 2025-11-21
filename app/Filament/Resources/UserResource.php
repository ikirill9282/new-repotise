<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UsersTable;
use App\Models\User;
use App\Models\History;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\View\View;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class UserResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationGroup = 'users';

  protected static ?string $navigationIcon = 'heroicon-o-user-group';

  protected static ?string $navigationLabel = 'All Users';

  protected static ?int $navigationSort = 1;

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()
      ->withoutGlobalScopes([SoftDeletingScope::class])
      ->with('roles');
    
    // Check if system_users filter is active via request
    // Filament stores toggle filter state in tableFilters parameter
    $tableFilters = request()->get('tableFilters', []);
    if (!isset($tableFilters['system_users']) || !$tableFilters['system_users']) {
      // By default, exclude system users
      // Also include users with ID 0 (if they exist) even if they have system role
      $query->where(function($q) {
        $q->whereDoesntHave('roles', fn($subq) => $subq->where('name', 'system'))
          ->orWhere('id', 0);
      });
    }
    // If filter is active, don't exclude system users (show all)
    
    return $query;
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name')
          ->required()
          ->maxLength(255),
        TextInput::make('username')
          ->required()
          ->maxLength(255)
          ->unique(ignoreRecord: true),
        TextInput::make('email')
          ->email()
          ->required()
          ->maxLength(255)
          ->unique(ignoreRecord: true),
        Select::make('roles')
          ->multiple()
          ->options(Role::where('name', '!=', 'system')->pluck('title', 'id'))
          ->default(function ($record) {
            return $record?->roles->pluck('id')->toArray() ?? [];
          }),
        Select::make('status')
          ->options([
            'active' => 'Active',
            'blocked' => 'Blocked',
            'pending_verification' => 'Pending Verification',
          ])
          ->default('active'),
      ])
      ->columns(1);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('ID')
          ->sortable()
          ->searchable(),
        TextColumn::make('username')
          ->label('Name / Username')
          ->searchable(['name', 'username', 'email'])
          ->formatStateUsing(fn($record) => $record->name . ' / @' . $record->username)
          ->sortable(),
        TextColumn::make('email')
          ->searchable()
          ->sortable()
          ->copyable(),
        TextColumn::make('user_role')
          ->label('Role')
          ->getStateUsing(function($record) {
            // Ensure roles are loaded
            if (!$record->relationLoaded('roles')) {
              $record->load('roles');
            }
            return $record->getDisplayRoleName();
          })
          ->badge()
          ->color(function($record) {
            // Ensure roles are loaded
            if (!$record->relationLoaded('roles')) {
              $record->load('roles');
            }
            $roleName = $record->getDisplayRoleName();
            return match($roleName) {
              'Super Admin' => 'danger',
              'Admin' => 'warning',
              'Moderator' => 'info',
              'Seller', 'Seller (Referral)' => 'success',
              default => 'gray',
            };
          }),
        TextColumn::make('user_status')
          ->label('Status')
          ->getStateUsing(fn($record) => $record->getDisplayStatus())
          ->badge()
          ->color(function($record) {
            $status = $record->getDisplayStatus();
            return match($status) {
              'Active' => 'success',
              'Blocked' => 'danger',
              'Pending Verification' => 'warning',
              'Deleted' => 'gray',
              default => 'gray',
            };
          }),
        TextColumn::make('created_at')
          ->label('Registration Date')
          ->dateTime()
          ->sortable(),
      ])
      ->filters([
        SelectFilter::make('status')
          ->label('Status')
          ->options([
            'active' => 'Active',
            'blocked' => 'Blocked',
            'pending_verification' => 'Pending Verification',
          ])
          ->query(function (Builder $query, array $data): Builder {
            if (!isset($data['value']) || $data['value'] === null) {
              return $query;
            }
            return $query->where('status', $data['value']);
          }),
        Filter::make('unverified')
          ->label('Unverified Email')
          ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at'))
          ->toggle(),
        Filter::make('deleted')
          ->label('Deleted')
          ->query(fn (Builder $query): Builder => $query->onlyTrashed())
          ->toggle(),
        Filter::make('system_users')
          ->label('Show System Users')
          ->toggle(),
        SelectFilter::make('roles')
          ->label('Role')
          ->relationship('roles', 'title', fn($query) => $query->where('name', '!=', 'system'))
          ->multiple(),
        DateRangeFilter::make('created_at')
          ->label('Registration Date')
          ->query(function ($query, array $data) {
            if (!empty($data['created_at'])) {
              $arr = explode('-', $data['created_at']);
              $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
              
              return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
            }
          }),
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make()
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
            
            // Логируем изменение статуса
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
            
            // Логируем изменение email
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
            
            // Логируем изменение имени
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
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('change_role')
            ->label('Change Role')
            ->icon('heroicon-o-user-circle')
            ->form([
              Select::make('role')
                ->label('New Role')
                ->options(Role::where('name', '!=', 'system')->pluck('title', 'id'))
                ->required()
                ->multiple(),
            ])
            ->requiresConfirmation()
            ->modalHeading('Change Role for Selected Users')
            ->modalDescription('Are you sure you want to change the role for the selected users?')
            ->action(function ($records, array $data) {
              $roleIds = $data['role'] ?? [];
              $roles = Role::whereIn('id', $roleIds)->get();
              $roleNames = $roles->pluck('name')->toArray();
              
              $count = 0;
              foreach ($records as $record) {
                if ($record->isSystemUser()) {
                  continue;
                }
                
                $oldRoles = $record->roles->pluck('name')->toArray();
                $record->syncRoles($roles);
                
                History::info()
                  ->action('Role Changed (Bulk)')
                  ->userId($record->id)
                  ->initiator(Auth::id())
                  ->values(implode(', ', $roleNames), implode(', ', $oldRoles))
                  ->message("User roles changed from [" . implode(', ', $oldRoles) . "] to [" . implode(', ', $roleNames) . "] (bulk operation)")
                  ->payload(['ip_address' => request()->ip()])
                  ->write();
                
                $count++;
              }
              
              Notification::make()
                ->title("Role changed for {$count} user(s)")
                ->success()
                ->send();
            }),
          Tables\Actions\BulkAction::make('block')
            ->label('Block Users')
            ->icon('heroicon-o-lock-closed')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Block Selected Users')
            ->modalDescription('Are you sure you want to block the selected users? They will not be able to log in.')
            ->action(function ($records) {
              $count = 0;
              foreach ($records as $record) {
                if ($record->isSystemUser() || $record->isBlocked()) {
                  continue;
                }
                
                $record->block();
                
                History::warning()
                  ->action('User Blocked (Bulk)')
                  ->userId($record->id)
                  ->initiator(Auth::id())
                  ->message("User {$record->username} was blocked (bulk operation)")
                  ->payload(['ip_address' => request()->ip()])
                  ->write();
                
                $count++;
              }
              
              Notification::make()
                ->title("{$count} user(s) blocked")
                ->success()
                ->send();
            }),
          Tables\Actions\BulkAction::make('unblock')
            ->label('Unblock Users')
            ->icon('heroicon-o-lock-open')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Unblock Selected Users')
            ->modalDescription('Are you sure you want to unblock the selected users?')
            ->action(function ($records) {
              $count = 0;
              foreach ($records as $record) {
                if ($record->isSystemUser() || !$record->isBlocked()) {
                  continue;
                }
                
                $record->unblock();
                
                History::info()
                  ->action('User Unblocked (Bulk)')
                  ->userId($record->id)
                  ->initiator(Auth::id())
                  ->message("User {$record->username} was unblocked (bulk operation)")
                  ->payload(['ip_address' => request()->ip()])
                  ->write();
                
                $count++;
              }
              
              Notification::make()
                ->title("{$count} user(s) unblocked")
                ->success()
                ->send();
            }),
          Tables\Actions\DeleteBulkAction::make()
            ->label('Delete Users')
            ->requiresConfirmation()
            ->modalHeading('Delete Selected Users')
            ->modalDescription('Deleting users will result in loss of access. Related data (products/orders) will remain in the system.')
            ->modalSubmitActionLabel('Confirm Deletion')
            ->action(function ($records) {
              $count = 0;
              foreach ($records as $record) {
                if ($record->isSystemUser()) {
                  continue;
                }
                
                $username = $record->username;
                $record->delete();
                
                History::warning()
                  ->action('User Deleted (Bulk)')
                  ->userId($record->id)
                  ->initiator(Auth::id())
                  ->message("User {$username} was deleted (bulk operation)")
                  ->payload(['ip_address' => request()->ip()])
                  ->write();
                
                $count++;
              }
              
              Notification::make()
                ->title("{$count} user(s) deleted")
                ->success()
                ->send();
            }),
        ]),
      ])
      ->defaultSort('created_at', 'desc')
      ->recordUrl(fn($record) => Pages\ViewUser::getUrl(['record' => $record]));
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListUsers::route('/'),
      'view' => Pages\ViewUser::route('/{record}'),
    ];
  }
}
