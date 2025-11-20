<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntegrationResource\Pages;
use App\Models\History;
use App\Models\Integration;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class IntegrationResource extends Resource
{
    protected static ?string $model = Integration::class;

    protected static ?string $navigationGroup = 'settings';

    protected static ?string $navigationLabel = 'Integrations';

    // protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece'; // Icon removed - group has icon

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Integration Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Integration Name')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                        Select::make('type')
                            ->label('Type')
                            ->required()
                            ->options([
                                Integration::TYPE_PAYMENT => 'Payment',
                                Integration::TYPE_EMAIL => 'Email',
                                Integration::TYPE_ANALYTICS => 'Analytics',
                                Integration::TYPE_OTHER => 'Other',
                            ])
                            ->disabled()
                            ->dehydrated(),
                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                Integration::STATUS_ACTIVE => 'Active',
                                Integration::STATUS_INACTIVE => 'Inactive',
                                Integration::STATUS_NOT_CONFIGURED => 'Not Configured',
                            ])
                            ->default(Integration::STATUS_NOT_CONFIGURED),
                    ])
                    ->columns(3),
                Section::make('Configuration')
                    ->schema(function ($record) {
                        $schemas = [];
                        
                        if ($record && $record->name === 'stripe') {
                            $schemas = [
                                TextInput::make('config.api_key')
                                    ->label('Publishable Key')
                                    ->required()
                                    ->helperText('Stripe publishable key'),
                                TextInput::make('config.secret_key')
                                    ->label('Secret Key')
                                    ->required()
                                    ->password()
                                    ->helperText('Stripe secret key'),
                                TextInput::make('config.webhook_secret')
                                    ->label('Webhook Secret')
                                    ->password()
                                    ->helperText('Stripe webhook signing secret (optional)'),
                            ];
                        } elseif ($record && $record->name === 'mailgun') {
                            $schemas = [
                                TextInput::make('config.domain')
                                    ->label('Domain')
                                    ->required()
                                    ->helperText('Mailgun domain'),
                                TextInput::make('config.api_key')
                                    ->label('API Key')
                                    ->required()
                                    ->password()
                                    ->helperText('Mailgun API key'),
                                TextInput::make('config.endpoint')
                                    ->label('Endpoint')
                                    ->default('api.mailgun.net')
                                    ->helperText('Mailgun API endpoint'),
                            ];
                        } elseif ($record && $record->name === 'ga4') {
                            $schemas = [
                                TextInput::make('config.property_id')
                                    ->label('Property ID')
                                    ->required()
                                    ->helperText('Google Analytics 4 Property ID (numeric ID for API)'),
                                TextInput::make('config.measurement_id')
                                    ->label('Measurement ID')
                                    ->required()
                                    ->helperText('GA4 Measurement ID for frontend tracking (format: G-XXXXXXXXXX). Find it in GA4 Admin > Data Streams > Web Stream Details'),
                                TextInput::make('config.credentials_json')
                                    ->label('Credentials JSON')
                                    ->textarea()
                                    ->rows(5)
                                    ->helperText('Service account credentials JSON (for API access)'),
                            ];
                        } else {
                            $schemas = [
                                TextInput::make('config.api_key')
                                    ->label('API Key')
                                    ->helperText('API key for this integration'),
                                TextInput::make('config.secret_key')
                                    ->label('Secret Key')
                                    ->password()
                                    ->helperText('Secret key for this integration'),
                            ];
                        }
                        
                        return $schemas;
                    })
                    ->columns(2)
                    ->visible(fn($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Integration Name')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->badge()
                    ->color(fn($state) => match($state) {
                        Integration::TYPE_PAYMENT => 'success',
                        Integration::TYPE_EMAIL => 'info',
                        Integration::TYPE_ANALYTICS => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        Integration::STATUS_ACTIVE => 'success',
                        Integration::STATUS_INACTIVE => 'gray',
                        Integration::STATUS_NOT_CONFIGURED => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('last_updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        Integration::TYPE_PAYMENT => 'Payment',
                        Integration::TYPE_EMAIL => 'Email',
                        Integration::TYPE_ANALYTICS => 'Analytics',
                        Integration::TYPE_OTHER => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Integration::STATUS_ACTIVE => 'Active',
                        Integration::STATUS_INACTIVE => 'Inactive',
                        Integration::STATUS_NOT_CONFIGURED => 'Not Configured',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Action::make('test_connection')
                    ->label('Test Connection')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn($record) => $record->isConfigured())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            $result = static::testIntegration($record);
                            
                            if ($result['success']) {
                                Notification::make()
                                    ->title('Connection successful')
                                    ->body($result['message'])
                                    ->success()
                                    ->send();
                                
                                // Update status to active if test passes
                                if ($record->status !== Integration::STATUS_ACTIVE) {
                                    $record->update(['status' => Integration::STATUS_ACTIVE]);
                                    $record->touchLastUpdated();
                                }
                            } else {
                                Notification::make()
                                    ->title('Connection failed')
                                    ->body($result['message'])
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Test failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('disable')
                    ->label('Disable')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->visible(fn($record) => $record->status === Integration::STATUS_ACTIVE)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => Integration::STATUS_INACTIVE]);
                        $record->touchLastUpdated();
                        
                        History::info()
                            ->action('Integration Disabled')
                            ->initiator(Auth::id())
                            ->message("Integration {$record->name} was disabled")
                            ->payload(['ip_address' => request()->ip()])
                            ->write();
                        
                        Notification::make()
                            ->title('Integration disabled')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('name', 'asc');
    }

    protected static function testIntegration(Integration $integration): array
    {
        try {
            switch ($integration->name) {
                case 'stripe':
                    return static::testStripe($integration);
                case 'mailgun':
                    return static::testMailgun($integration);
                case 'ga4':
                    return static::testGA4($integration);
                default:
                    return ['success' => false, 'message' => 'Test not implemented for this integration'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected static function testStripe(Integration $integration): array
    {
        $apiKey = $integration->getConfig('secret_key');
        
        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'Stripe secret key is not configured'];
        }

        try {
            // Test Stripe API connection
            $stripe = new \Stripe\StripeClient($apiKey);
            $balance = $stripe->balance->retrieve();
            
            return ['success' => true, 'message' => 'Stripe connection successful. Balance retrieved.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Stripe connection failed: ' . $e->getMessage()];
        }
    }

    protected static function testMailgun(Integration $integration): array
    {
        $domain = $integration->getConfig('domain');
        $apiKey = $integration->getConfig('api_key');
        
        if (empty($domain) || empty($apiKey)) {
            return ['success' => false, 'message' => 'Mailgun domain or API key is not configured'];
        }

        try {
            // Test Mailgun by sending a test email
            $testEmail = config('mail.from.address', 'test@example.com');
            
            Mail::raw('This is a test email from TrekGuider integration test.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                    ->subject('TrekGuider Integration Test');
            });
            
            return ['success' => true, 'message' => 'Test email sent successfully to ' . $testEmail];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Mailgun test failed: ' . $e->getMessage()];
        }
    }

    protected static function testGA4(Integration $integration): array
    {
        $propertyId = $integration->getConfig('property_id');
        
        if (empty($propertyId)) {
            return ['success' => false, 'message' => 'GA4 Property ID is not configured'];
        }

        // GA4 testing would require Google Analytics API client
        // For now, just check if property ID is set
        return ['success' => true, 'message' => 'GA4 configuration appears valid (Property ID: ' . $propertyId . ')'];
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
            'index' => Pages\ListIntegrations::route('/'),
            'edit' => Pages\EditIntegration::route('/{record}/edit'),
        ];
    }
}

