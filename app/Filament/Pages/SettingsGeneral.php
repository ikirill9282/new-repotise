<?php

namespace App\Filament\Pages;

use App\Models\History;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsGeneral extends Page implements HasForms
{
    use InteractsWithForms;

    // protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth'; // Icon removed - group has icon

    protected static ?string $navigationGroup = 'settings';

    protected static ?string $navigationLabel = 'General Settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.settings-general';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => settings('site_name', config('app.name')),
            'site_logo' => settings('site_logo'),
            'support_email' => settings('support_email', config('mail.from.address')),
            'support_phone' => settings('support_phone'),
            'platform_currency' => settings('platform_currency', config('cashier.currency', 'usd')),
            'platform_locale' => settings('platform_locale', config('app.locale', 'en')),
            'platform_timezone' => settings('platform_timezone', config('app.timezone', 'UTC')),
        ]);
    }

    public function form(Form $form): Form
    {
        $currencies = [
            'usd' => 'USD - US Dollar',
            'eur' => 'EUR - Euro',
            'gbp' => 'GBP - British Pound',
            'rub' => 'RUB - Russian Ruble',
            'jpy' => 'JPY - Japanese Yen',
            'cny' => 'CNY - Chinese Yuan',
        ];

        $locales = [
            'en' => 'English',
            'ru' => 'Русский',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
        ];

        $timezones = [];
        foreach (timezone_identifiers_list() as $tz) {
            $timezones[$tz] = $tz;
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Platform Information')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site / Platform Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('The name of your platform'),
                        FileUpload::make('site_logo')
                            ->label('Logo')
                            ->image()
                            ->directory('logos')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'])
                            ->helperText('Upload platform logo (PNG, JPG, SVG, max 2MB)')
                            ->imagePreviewHeight('100')
                            ->downloadable()
                            ->openable(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        TextInput::make('support_email')
                            ->label('Support Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->helperText('Email address for customer support'),
                        TextInput::make('support_phone')
                            ->label('Support Phone')
                            ->tel()
                            ->maxLength(255)
                            ->helperText('Phone number for customer support (optional)'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Localization')
                    ->schema([
                        Select::make('platform_currency')
                            ->label('Platform Currency')
                            ->required()
                            ->options($currencies)
                            ->helperText('Currency used for displaying prices in the platform'),
                        Select::make('platform_locale')
                            ->label('Interface Language')
                            ->required()
                            ->options($locales)
                            ->helperText('Default language for the platform interface'),
                        Select::make('platform_timezone')
                            ->label('Timezone')
                            ->required()
                            ->searchable()
                            ->options($timezones)
                            ->helperText('Default timezone for date and time display'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $oldValues = [];

        try {
            // Store old values for history
            $oldValues['site_name'] = settings('site_name');
            $oldValues['platform_currency'] = settings('platform_currency');
            $oldValues['platform_locale'] = settings('platform_locale');
            $oldValues['platform_timezone'] = settings('platform_timezone');

            // Handle logo upload
            if (isset($data['site_logo']) && is_array($data['site_logo'])) {
                $logoPath = $data['site_logo'][0] ?? null;
                if ($logoPath) {
                    // If it's a new upload, get the path
                    if (Storage::disk('public')->exists($logoPath)) {
                        $data['site_logo'] = '/storage/' . $logoPath;
                    }
                } else {
                    // Keep existing logo if not changed
                    $data['site_logo'] = settings('site_logo');
                }
            } else {
                // Keep existing logo if not provided
                $data['site_logo'] = settings('site_logo');
            }

            // Save settings
            foreach ($data as $key => $value) {
                if ($value !== null) {
                    SystemSetting::set($key, $value);
                }
            }

            // Clear cache
            SystemSetting::clearCache();

            // Log changes to history
            $changes = [];
            foreach ($data as $key => $newValue) {
                if (isset($oldValues[$key]) && $oldValues[$key] != $newValue) {
                    $changes[] = "{$key}: '{$oldValues[$key]}' → '{$newValue}'";
                }
            }

            if (!empty($changes)) {
                History::info()
                    ->action('Settings Updated')
                    ->initiator(Auth::id())
                    ->message('General settings updated: ' . implode(', ', $changes))
                    ->payload(['ip_address' => request()->ip()])
                    ->write();
            }

            Notification::make()
                ->title('Settings saved successfully')
                ->success()
                ->send();

            // Refresh form with new values
            $this->mount();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to save settings')
                ->body('Please check the data and try again.')
                ->danger()
                ->send();

            throw $e;
        }
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save')
                ->submit('save'),
            Forms\Components\Actions\Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->action(fn() => $this->mount()),
        ];
    }
}
