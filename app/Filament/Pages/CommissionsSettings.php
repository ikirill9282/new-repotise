<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Models\Level;
use App\Models\UserOptions;
use Illuminate\Support\Facades\DB;

class CommissionsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    // protected static ?string $navigationIcon = 'heroicon-o-currency-dollar'; // Icon removed - group has icon

    protected static string $view = 'filament.pages.commissions-settings';

    protected static ?string $navigationGroup = 'financials';

    protected static ?string $navigationLabel = 'Commissions & Fees';

    protected static ?int $navigationSort = 5;

    public $defaultCommission = 10.0;
    public $levels = [];

    public function mount(): void
    {
        $this->loadCommissions();
        $this->form->fill([
            'defaultCommission' => $this->defaultCommission,
            'levels' => $this->levels,
        ]);
    }

    protected function loadCommissions(): void
    {
        // Загружаем базовый уровень (по умолчанию первый или с минимальным fee)
        $defaultLevel = Level::orderBy('fee')->first();
        $this->defaultCommission = $defaultLevel ? (float) $defaultLevel->fee : 10.0;
        
        // Загружаем все уровни
        $this->levels = Level::all()->map(function ($level) {
            return [
                'id' => $level->id,
                'title' => $level->title,
                'fee' => (float) $level->fee,
            ];
        })->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Platform Default Commission')
                ->description('Set the default commission percentage for all sellers. Individual sellers can have custom commissions set in their user profile.')
                ->schema([
                    TextInput::make('defaultCommission')
                        ->label('Default Commission (%)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%')
                        ->helperText('This is the base commission rate. Sellers can have individual rates set in their profiles.'),
                ]),
            
            Section::make('Level-Based Commissions')
                ->description('Configure commission rates for different seller levels. These rates can override the default commission.')
                ->schema([
                    Repeater::make('levels')
                        ->label('Levels')
                        ->schema([
                            TextInput::make('title')
                                ->label('Level Title')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('fee')
                                ->label('Commission (%)')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->maxValue(100)
                                ->step(0.01)
                                ->suffix('%'),
                        ])
                        ->columns(2)
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->defaultItems(count($this->levels))
                        ->collapsible(),
                ]),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        try {
            DB::beginTransaction();

            // Update default commission (first level or level with minimum fee)
            if (!empty($data['defaultCommission'])) {
                $firstLevel = Level::orderBy('id')->first();
                if ($firstLevel) {
                    $firstLevel->update(['fee' => $data['defaultCommission']]);
                }
            }

            // Update level commissions
            if (!empty($data['levels'])) {
                foreach ($data['levels'] as $levelData) {
                    if (isset($levelData['id']) && isset($levelData['fee'])) {
                        Level::where('id', $levelData['id'])->update([
                            'fee' => $levelData['fee'],
                        ]);
                    }
                }
            }

            DB::commit();
            
            Notification::make()
                ->success()
                ->title('Commission Settings Saved')
                ->body('Commission settings have been updated successfully.')
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to save commission settings: ' . $e->getMessage())
                ->send();
        }
    }
}

