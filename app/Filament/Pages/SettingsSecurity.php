<?php

namespace App\Filament\Pages;

use App\Models\History;
use App\Models\LoginHistory;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use Filament\Support\Colors\Color;

class SettingsSecurity extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    // protected static ?string $navigationIcon = 'heroicon-o-shield-check'; // Icon removed - group has icon

    protected static ?string $navigationGroup = 'settings';

    protected static ?string $navigationLabel = 'Security';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.settings-security';

    public ?array $passwordData = [];
    public ?string $twofaSecret = null;
    public ?string $twofaQrCode = null;
    public ?string $twofaCode = null;
    public ?string $twofaDisableCode = null;
    public bool $twofaEnabled = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->twofaEnabled = !empty($user->google2fa_secret);
        
        $this->passwordForm->fill([
            'current_password' => '',
            'new_password' => '',
            'new_password_confirmation' => '',
        ]);
    }

    public function passwordForm(Forms\Form|Infolist $form): Forms\Form|Infolist
    {
        if ($form instanceof Infolist) {
            return $form;
        }
        
        return $form
            ->schema([
                Section::make('Change Password')
                    ->description('Change your administrator password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required()
                            ->currentPassword()
                            ->autocomplete('current-password'),
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->rules(['regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'])
                            ->helperText('Password must be at least 8 characters and contain uppercase, lowercase, and numbers')
                            ->autocomplete('new-password'),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->required()
                            ->same('new_password')
                            ->autocomplete('new-password'),
                    ])
                    ->columns(1),
            ])
            ->statePath('passwordData');
    }

    public function changePassword(): void
    {
        $data = $this->passwordForm->getState();
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Invalid current password')
                ->danger()
                ->send();
            return;
        }

        // Update password
        $user->password = Hash::make($data['new_password']);
        $user->save();

        // Log to history
        History::warning()
            ->action('Password Changed')
            ->userId($user->id)
            ->initiator($user->id)
            ->message('Administrator password changed')
            ->payload(['ip_address' => request()->ip()])
            ->write();

        // Clear form
        $this->passwordForm->fill([
            'current_password' => '',
            'new_password' => '',
            'new_password_confirmation' => '',
        ]);

        Notification::make()
            ->title('Password changed successfully')
            ->success()
            ->send();
    }

    public function enableTwoFactor(): void
    {
        $user = Auth::user();
        
        if (!empty($user->google2fa_secret)) {
            Notification::make()
                ->title('2FA is already enabled')
                ->warning()
                ->send();
            return;
        }

        try {
            $this->twofaSecret = Google2FA::generateSecretKey();
            
            $issuer = rawurlencode(config('app.name', 'TrekGuider'));
            $account = rawurlencode($user->email);
            $label = $issuer . '%3A' . $account;
            
            $otpAuthUrl = sprintf(
                'otpauth://totp/%s?secret=%s&issuer=%s',
                $label,
                $this->twofaSecret,
                $issuer
            );
            
            $this->twofaQrCode = sprintf(
                'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=%s',
                urlencode($otpAuthUrl)
            );
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to generate 2FA secret')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function confirmTwoFactor(): void
    {
        $user = Auth::user();
        
        if (empty($this->twofaSecret)) {
            Notification::make()
                ->title('2FA secret not generated')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->twofaCode)) {
            Notification::make()
                ->title('Verification code is required')
                ->danger()
                ->send();
            return;
        }
        
        $code = preg_replace('/\s+/', '', $this->twofaCode);
        
        if (!Google2FA::verifyKey($this->twofaSecret, $code, 4)) {
            Notification::make()
                ->title('Invalid verification code')
                ->danger()
                ->send();
            return;
        }

        try {
            $user->google2fa_secret = Crypt::encryptString($this->twofaSecret);
            $user->twofa = 1;
            $user->save();

            $this->twofaEnabled = true;
            $this->twofaSecret = null;
            $this->twofaQrCode = null;
            $this->twofaCode = null;

            History::info()
                ->action('2FA Enabled')
                ->userId($user->id)
                ->initiator($user->id)
                ->message('Two-factor authentication enabled')
                ->payload(['ip_address' => request()->ip()])
                ->write();

            Notification::make()
                ->title('2FA enabled successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to enable 2FA')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function disableTwoFactor(): void
    {
        $user = Auth::user();
        
        if (empty($user->google2fa_secret)) {
            Notification::make()
                ->title('2FA is not enabled')
                ->warning()
                ->send();
            return;
        }

        if (empty($this->twofaDisableCode)) {
            Notification::make()
                ->title('Verification code is required')
                ->danger()
                ->send();
            return;
        }
        
        try {
            $secret = Crypt::decryptString($user->google2fa_secret);
            $code = preg_replace('/\s+/', '', $this->twofaDisableCode);
            
            if (!Google2FA::verifyKey($secret, $code, 4)) {
                Notification::make()
                    ->title('Invalid verification code')
                    ->danger()
                    ->send();
                return;
            }

            $user->google2fa_secret = null;
            $user->twofa = 0;
            $user->save();

            $this->twofaEnabled = false;
            $this->twofaDisableCode = null;

            History::warning()
                ->action('2FA Disabled')
                ->userId($user->id)
                ->initiator($user->id)
                ->message('Two-factor authentication disabled')
                ->payload(['ip_address' => request()->ip()])
                ->write();

            Notification::make()
                ->title('2FA disabled successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to disable 2FA')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function unblockUser(int $userId): void
    {
        $user = User::find($userId);
        
        if (!$user) {
            Notification::make()
                ->title('User not found')
                ->danger()
                ->send();
            return;
        }

        $user->login_locked_until = null;
        $user->failed_login_attempts = 0;
        $user->last_failed_login_at = null;
        $user->save();

        History::info()
            ->action('User Unblocked')
            ->userId($user->id)
            ->initiator(Auth::id())
            ->message("User {$user->username} was unblocked from login")
            ->payload(['ip_address' => request()->ip()])
            ->write();

        Notification::make()
            ->title('User unblocked successfully')
            ->success()
            ->send();
    }

    public function table(Table|Infolist $table): Table|Infolist
    {
        if ($table instanceof Infolist) {
            return $table;
        }
        
        return $table
            ->query(LoginHistory::query()->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(['user.name', 'user.email', 'email'])
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state === LoginHistory::STATUS_SUCCESS ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('failure_reason')
                    ->label('Failure Reason')
                    ->visible(fn($record) => $record->status === LoginHistory::STATUS_FAILED)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        LoginHistory::STATUS_SUCCESS => 'Success',
                        LoginHistory::STATUS_FAILED => 'Failed',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    protected function getTableHeading(): string
    {
        return 'Login History';
    }

    protected function getTableDescription(): ?string
    {
        return 'Recent login attempts and authentication history';
    }
}
