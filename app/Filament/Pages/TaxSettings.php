<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class TaxSettings extends Page implements HasForms
{
    use InteractsWithForms;

    // protected static ?string $navigationIcon = 'heroicon-o-calculator'; // Icon removed - group has icon

    protected static string $view = 'filament.pages.tax-settings';

    protected static ?string $navigationGroup = 'financials';

    protected static ?string $navigationLabel = 'Taxes';

    protected static ?int $navigationSort = 6;

    public ?float $vat_rate = 0;

    public function mount(): void
    {
        $this->loadTaxSettings();
    }

    protected function loadTaxSettings(): void
    {
        try {
            $setting = DB::table('tax_settings')
                ->where('key', 'vat_rate')
                ->first();
            
            $this->vat_rate = $setting ? (float) $setting->value : 5.0;
        } catch (\Exception $e) {
            $this->vat_rate = 5.0;
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Tax Rates')
                ->description('Configure tax rates for the platform')
                ->schema([
                    TextInput::make('vat_rate')
                        ->label('VAT Rate (%)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%')
                        ->helperText('VAT rate in percentage. This will be applied to all orders.'),
                ]),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        try {
            DB::table('tax_settings')
                ->updateOrInsert(
                    ['key' => 'vat_rate'],
                    [
                        'value' => $data['vat_rate'],
                        'description' => 'VAT rate in percentage',
                        'updated_at' => now(),
                    ]
                );
            
            Notification::make()
                ->success()
                ->title('Tax Settings Saved')
                ->body('Tax settings have been updated successfully.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to save tax settings: ' . $e->getMessage())
                ->send();
        }
    }
}

