<?php

namespace App\Filament\Resources\OrderResource\Widgets;


use Livewire\Attributes\On;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat as StatWidget;
use App\Filament\Resources\OrderResource;

class Stat extends BaseWidget
{
    public array $config = [];

    #[On('tableFiltered')]
    public function onTableFiltered($config): void
    {
      $this->config = $config;
    }

    public function mount($config)
    {
      $this->config = $config;
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        if (is_null($this->config)) {
            return [];
        }
        return [
            StatWidget::make('Orders', $this->config['total_orders'])
                ->label('Orders')
                ->color('success')
                ->icon('heroicon-o-shopping-cart')
                ,
            StatWidget::make('Orders Complete', $this->config['total_complete'])
                ->label('Orders Complete')
                ->color('success')
                ->icon('heroicon-o-shopping-cart')
                ,
            StatWidget::make('Revenue', "$".$this->config['total_revenue'])
                ->label('Revenue')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ,
            StatWidget::make('Profit', "$".$this->config['total_profit'])
                ->label('Profit')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ,
            StatWidget::make('Discount Amount', "$".$this->config['total_discount_amount'])
                ->label('Discount Amount')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ,
            StatWidget::make('Stripe Fee', "$".$this->config['total_stripe_fee'])
                ->label('Stripe Fee')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ,
            StatWidget::make('Seller Reward', "$".$this->config['total_seller_reward'])
                ->label('Seller Reward')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ,
            StatWidget::make('Referal Reward', "$".$this->config['total_referal_reward'])
                ->label('Referal Reward')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ,
        ];
    }
}
