<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\Order as EnumsOrder;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;

class ListOrders extends ListRecords
{
  protected static string $resource = OrderResource::class;

  public function updated(): void
  {
    $data = $this->getStatWidgetsData();
    $this->dispatch('tableFiltered', $data);
  }

  protected function getHeaderActions(): array
  {
    return [
      // Actions\CreateAction::make(),
    ];
  }

  public function getHeaderWidgets(): array
  {
    $data = $this->getStatWidgetsData();
    return [
      OrderResource\Widgets\Stat::make(['config' => $data]),
    ];
  }

  public function getHeaderWidgetsColumns(): int|string|array
  {
    return 5;
  }

  public function table(Tables\Table $table): Tables\Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('#ID')
          ->sortable()
          ->searchable()
          ,
        TextColumn::make('user')
          ->view('filament.tables.columns.author'),
        TextColumn::make('status')
          ->sortable()
          ->color(fn(Model $record) => EnumsOrder::color($record->status_id))
          ->formatStateUsing(fn(Model $record) => EnumsOrder::label($record->status_id)),
        TextColumn::make('cost')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('discount_amount')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('tax')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('cost_without_discount')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('cost_without_tax')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('stripe_fee')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('base_reward')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('seller_reward')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('referal_reward')
          ->money('usd')
          ->sortable()
          ->searchable(),
        TextColumn::make('platform_reward')
          ->money('usd')
          ->sortable()
          ->searchable(),
        // TextColumn::make('discount')
        //   ->sortable()
        //   ->searchable()
        //   ->label('Discount')
        //   ->formatStateUsing(fn (Order $record) => $record->discount ? ucfirst($record->discount->group) . ' ' . ucfirst($record->discount->type) : null)  
        //   ,
        // TextColumn::make('payment_id')
        //   ->label('Payment ID')
        //   ->sortable()
        //   ->searchable()
        //   ,
        // TextColumn::make('gift')
        //   ->label('Is Gift')
        //   ->sortable()
        //   ,
        // TextColumn::make('recipient')
        //   ->label('Recipient')
        //   ->sortable()
        //   ->searchable()
        //   ,
        // TextColumn::make('recipient_message')
        //   ->label('Recipient Message')
        //   ->sortable()
        //   ->searchable()
        //   ,
        TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->searchable(),
        TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->searchable(),
      ])
      ->filters([
        SelectFilter::make('status_id')
          ->label('Status')
          ->options(EnumsOrder::toArray()),
        DateRangeFilter::make('created_at')
          ->label('Filter by Date created')
          ->query(function ($query, array $data) {
            if (!empty($data['created_at'])) {
              $arr = explode('-', $data['created_at']);
              $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);

              return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
            }
          })
        // ->defaultToday()
        ,
      ])
      ->actions([

        ActionGroup::make([
          EditAction::make(),
          ViewAction::make('view')
            ->extraAttributes(['target' => '_blank']),
        ]),
      ], position: ActionsPosition::BeforeColumns)
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public function updatedTableFilters(): void
  {
    parent::updatedTableFilters();
    $this->refreshStat();
  }

  public function updatedTableSearch(): void
  {
    parent::updatedTableSearch();
    $this->refreshStat();
  }

  public function removeTableFilter(string $filterName, ?string $field = null, bool $isRemovingAllFilters = false): void
  {
    parent::removeTableFilter($filterName, $field, $isRemovingAllFilters);
    $this->refreshStat();
  }

  public function resetTableFiltersForm(): void
  {
    parent::resetTableFiltersForm();
    $this->refreshStat();
  }

  protected function getStatWidgetsData()
  {
    $query = $this->getFilteredSortedTableQuery();
    if (is_null($query)) {
      return [];
    }
    return [
      'total_orders' => $query->count(),
      'total_complete' => $query->where('status_id', EnumsOrder::COMPLETE)->count(),
      'total_revenue' => $query->sum('cost'),
      'total_profit' => $query->sum('platform_reward'),

      'total_discount_amount' => $query->sum('discount_amount'),
      'total_stripe_fee' => $query->sum('stripe_fee'),
      'total_seller_reward' => $query->sum('seller_reward'),
      'total_referal_reward' => $query->sum('platform_reward'),
    ];
  }

  protected function refreshStat(): void
  {
    $data = $this->getStatWidgetsData();
    $this->dispatch('tableFiltered', $data);
  }
}
