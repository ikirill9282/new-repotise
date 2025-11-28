<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\On;
use App\Filament\Widgets\UsersWidget;
use App\Filament\Widgets\ProductsWidget;
use App\Filament\Widgets\TransactionsWidget;
use App\Filament\Widgets\ComplaintsWidget;
use App\Filament\Widgets\ModerationWidget;
use App\Filament\Widgets\ActivityWidget;
use App\Filament\Widgets\RevenueWidget;
use App\Filament\Widgets\NotificationsWidget;
use App\Filament\Widgets\KeyMetricsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use Illuminate\Support\Carbon;

class Dashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?int $navigationSort = -1;

    protected static ?string $title = 'Dashboard';

    public ?string $datePreset = 'last_30_days';
    public ?string $startDate = null;
    public ?string $endDate = null;
    
    // Триггер для принудительного обновления виджетов
    public int $widgetsUpdateKey = 0;

    public function mount(): void
    {
        // Инициализируем widgetsUpdateKey
        $this->widgetsUpdateKey = 0;
        
        // Инициализируем форму
        $this->form->fill([
            'date_preset' => session('dashboard_date_preset', 'last_30_days'),
            'start_date' => session('dashboard_start_date'),
            'end_date' => session('dashboard_end_date'),
        ]);
        
        // Устанавливаем даты по умолчанию
        $this->updateDates();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('date_preset')
                    ->label('Date Range')
                    ->options([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'last_7_days' => 'Last 7 Days',
                        'last_30_days' => 'Last 30 Days',
                        'this_month' => 'This Month',
                        'last_90_days' => 'Last 90 Days',
                        'this_year' => 'This Year',
                        'custom' => 'Custom Range',
                    ])
                    ->default('last_30_days')
                    ->live()
                    ->afterStateUpdated(fn () => $this->updateDates()),
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->visible(fn ($get) => $get('date_preset') === 'custom')
                    ->live()
                    ->afterStateUpdated(fn () => $this->updateDates()),
                DatePicker::make('end_date')
                    ->label('End Date')
                    ->visible(fn ($get) => $get('date_preset') === 'custom')
                    ->live()
                    ->afterStateUpdated(fn () => $this->updateDates()),
            ])
            ->columns(3);
    }

    protected function updateDates(): void
    {
        try {
            $state = $this->form->getRawState();
            $preset = $state['date_preset'] ?? 'last_30_days';
            
            if ($preset === 'custom') {
                $this->startDate = $state['start_date'] ?? null;
                $this->endDate = $state['end_date'] ?? null;
            } else {
                [$this->startDate, $this->endDate] = $this->getDatesFromPreset($preset);
            }
            
            // Сохраняем в session для виджетов
            session([
                'dashboard_date_preset' => $preset,
                'dashboard_start_date' => $this->startDate,
                'dashboard_end_date' => $this->endDate,
            ]);
            
            // Сохраняем session сразу
            session()->save();
            
            // Инкрементируем ключ для принудительного обновления виджетов через wire:key
            // Это заставит Livewire пересоздать всю страницу с новыми датами
            $this->widgetsUpdateKey++;
            
            // Отправляем Livewire событие для обновления виджетов ПОСЛЕ сохранения session
            $this->dispatch('dashboard-date-range-updated', 
                start: $this->startDate,
                end: $this->endDate
            );
        } catch (\Exception $e) {
            \Log::error('Error updating dashboard dates', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function getDatesFromPreset(string $preset): array
    {
        return match($preset) {
            'today' => [
                Carbon::today()->format('Y-m-d'),
                Carbon::today()->format('Y-m-d'),
            ],
            'yesterday' => [
                Carbon::yesterday()->format('Y-m-d'),
                Carbon::yesterday()->format('Y-m-d'),
            ],
            'last_7_days' => [
                Carbon::now()->subDays(7)->startOfDay()->format('Y-m-d'),
                Carbon::now()->endOfDay()->format('Y-m-d'),
            ],
            'last_30_days' => [
                Carbon::now()->subDays(30)->startOfDay()->format('Y-m-d'),
                Carbon::now()->endOfDay()->format('Y-m-d'),
            ],
            'this_month' => [
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfDay()->format('Y-m-d'),
            ],
            'last_90_days' => [
                Carbon::now()->subDays(90)->startOfDay()->format('Y-m-d'),
                Carbon::now()->endOfDay()->format('Y-m-d'),
            ],
            'this_year' => [
                Carbon::now()->startOfYear()->format('Y-m-d'),
                Carbon::now()->endOfDay()->format('Y-m-d'),
            ],
            default => [
                Carbon::now()->subDays(30)->startOfDay()->format('Y-m-d'),
                Carbon::now()->endOfDay()->format('Y-m-d'),
            ],
        };
    }

    public function getStartDate(): Carbon
    {
        return $this->startDate 
            ? Carbon::parse($this->startDate)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
    }

    public function getEndDate(): Carbon
    {
        return $this->endDate 
            ? Carbon::parse($this->endDate)->endOfDay()
            : Carbon::now()->endOfDay();
    }

    protected function getUserWidgets(): array
    {
        return [
            UsersWidget::class,
            ActivityWidget::class,
        ];
    }

    protected function getProductWidgets(): array
    {
        return [
            ProductsWidget::class,
            TransactionsWidget::class,
        ];
    }

    protected function getSystemWidgets(): array
    {
        return [
            ComplaintsWidget::class,
            ModerationWidget::class,
            NotificationsWidget::class,
        ];
    }
}
