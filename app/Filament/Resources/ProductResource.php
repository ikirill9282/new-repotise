<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\OrderProducts;
use App\Models\Product;
use App\Models\Status;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;
use App\Models\User;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Products';
    protected static ?string $navigationLabel = 'Product List';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn() => null)
            ->query(static::getEloquentQuery()->orderByDesc('id'))
            ->columns([
                TextColumn::make('id')
                  ->label('Product ID')
                  ->width('50px')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ,
                TextColumn::make('preview')
                  ->label('Image')
                  ->view('filament.tables.columns.image')
                  ,
                TextColumn::make('title')
                  ->label('Product Name')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  // ->url(fn($record) => $record->makeUrl(), true)
                  ->color(Color::Sky)
                  ->url(fn($record) => url("/admin/products/$record->id/edit"))
                  ,
                TextColumn::make('type.title')
                  ->label('Product Type')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ,

                TextColumn::make('categories.title')
                  ->label('Product Categories')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ->extraAttributes(['class' => '!w-[250px] whitespace-normal'])
                  ,
                TextColumn::make('author.name')
                  ->label('Seller')
                  ->view('filament.tables.columns.author')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ,

                TextColumn::make('price')
                  ->label('Price')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ->money('usd', true)
                  ,

                TextColumn::make('old_price')
                  ->label('Old Price')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ->money('usd', true)
                  ->color(Color::Gray)
                  // ->extraAttributes(['class' => 'line-through'])
                  ,
                TextColumn::make('status.title')
                  ->label('Status')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ->badge()
                  ->color(fn($record) => match($record->status_id) {
                    1 => Color::Emerald,
                    2 => Color::Indigo,
                    3 => Color::Amber,
                    4 => Color::Sky,
                    5 => Color::Red,
                    6 => Color::Orange,
                  })
                  ,

                TextColumn::make('sales')
                  ->label('Sales')
                  // ->sortable(query: function (Builder $query, string $direction): Builder {
                  //   return $query->join('order_products', 'products.id', '=', 'order_products.product_id')
                  //     ->selectRaw('products.*, sum(order_products.count) as sales')
                  //     ->orderBy('sales', $direction)
                  //     ;
                  // })
                  ->toggleable()
                  ->getStateUsing(function (Product $record) {
                      return OrderProducts::where('product_id', $record->id)->sum('count');
                  })
                  ,
                TextColumn::make('published_at')
                  ->label('Published At')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ->dateTime('Y-m-d H:i:s')
                  ->icon('heroicon-o-check-badge')
                  ->iconColor(Color::Sky)
                  ,
                TextColumn::make('created_at')
                  ->icon('heroicon-o-clock')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ,
                TextColumn::make('updated_at')
                  ->icon('heroicon-o-clock')
                  ->sortable()
                  ->searchable()
                  ->toggleable()
                  ,
            ])
            ->filters([
                SelectFilter::make('status_id')
                  ->label('Filter by Status')
                  ->options(Status::all()->pluck('title', 'id'))
                  ,
                SelectFilter::make('model')
                  ->label('Filter by Purchase Model')
                  ->options([
                    0 => 'Product',
                    1 => 'Subscription',
                  ])
                  ->query(function($query, $state) {
                    if (!empty($state['value'])) {
                      $query->where('subscription', $state['value']);
                    }
                  })
                  ,
                SelectFilter::make('type_id')
                  ->label('Filter by Product Type')
                  ->searchable()
                  ->multiple()
                  ->options(Type::pluck('title', 'id'))
                  ,
                SelectFilter::make('user_id')
                  ->label('Filter by Seller')
                  ->searchable()
                  ->options(
                    User::query()
                      ->whereHas('roles', fn($q) => $q->whereIn('name', ['system', 'creator', 'admin']))
                      ->pluck('username', 'id')
                  )
                  ,
                
                DateRangeFilter::make('created_at')
                  ->label('Filter by Date created')
                  ->query(function ($query, array $data) {
                    if (!empty($data['created_at'])) {
                      $arr = explode('-', $data['created_at']);
                      $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
                      
                      return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
                    }
                  })
                  ,
                DateRangeFilter::make('updated_at')
                  ->label('Filter by Date updated')
                  ->query(function ($query, array $data) {
                    if (!empty($data['updated_at'])) {
                      $arr = explode('-', $data['updated_at']);
                      $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
                      
                      return $query->whereBetween('updated_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
                    }
                  })
                  ,

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
                      ->url(fn (Product $record): string => url($record->makeUrl()))
                      ->extraAttributes(['target' => '_blank'])
                      ,
                    Tables\Actions\Action::make('Approve')
                      ->icon('heroicon-o-check-circle')
                      ->visible(fn (Product $record): bool => $record->status_id === 3)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,
                    Tables\Actions\Action::make('Reject')
                      ->icon('heroicon-o-shield-exclamation')
                      ->visible(fn (Product $record): bool => $record->status_id === 3)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 5]);
                      })
                      ,
                    Tables\Actions\Action::make('Duplicate')
                      ->icon('heroicon-o-document-duplicate')
                      ->action(function (Product $record) {
                          $newRecord = $record->replicate(['status_id', 'published_at']);
                          // $newRecord->status_id = 3;
                          // $newRecord->published_at = null;
                          $newRecord->save();
                          $record->copyGallery($newRecord, 'products');
                      })
                      ,
                    
                    Tables\Actions\Action::make('Publish')
                      ->icon('heroicon-o-document-text')
                      ->visible(fn (Product $record): bool => $record->status_id === 2)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,

                    Tables\Actions\Action::make('Unpublish')
                      ->icon('heroicon-o-x-circle')
                      ->visible(fn (Product $record): bool => $record->status_id === 1)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 2, 'published_at' => null]);
                      })
                      ,

                    
                    Tables\Actions\DeleteAction::make()
                      ,
                ])
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\EditBulkAction::make(),
                    // Tables\Actions\ViewAction::make()
                    //   ->url(fn (Product $record): string => url($record->makeUrl()))
                    //   ->extraAttributes(['target' => '_blank'])
                    //   ,

                    Tables\Actions\BulkAction::make('Need Review')
                      ->icon('heroicon-o-check-circle')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Product::whereIn('id', $items)->update(['status_id' => 3, 'published_at' => null]);
                      })
                      ,
                    Tables\Actions\BulkAction::make('Approve')
                      ->icon('heroicon-o-check-circle')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Product::whereIn('id', $items)->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,
                    Tables\Actions\BulkAction::make('Reject')
                      ->icon('heroicon-o-shield-exclamation')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Product::whereIn('id', $items)->update(['status_id' => 5, 'published_at' => null]);
                      })
                      ,
                    
                    Tables\Actions\BulkAction::make('Publish')
                      ->icon('heroicon-o-document-text')
                      ->action(function (Collection $records) {
                          $items = $records->pluck('id');
                          Product::whereIn('id', $items)->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                          // $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,

                    Tables\Actions\BulkAction::make('Unpublish')
                      ->icon('heroicon-o-x-circle')
                      ->action(function (Collection $records) {
                          $items = $records->pluck('id');
                          Product::whereIn('id', $items)->update(['status_id' => 2, 'published_at' => null]);
                          // $record->update(['status_id' => 2, 'published_at' => null]);
                      })
                      ,
                    
                    Tables\Actions\BulkAction::make('Duplicate')
                      ->icon('heroicon-o-document-duplicate')
                      ->action(function ($records) {
                          foreach ($records as $record) {
                            $newRecord = $record->replicate(['status_id', 'published_at']);
                            $newRecord->save();
                            $record->copyGallery($newRecord, 'products');
                          }
                          // $newRecord = $record->replicate(['status_id', 'published_at']);
                          // $newRecord->status_id = 3;
                          // $newRecord->published_at = null;
                          // $newRecord->save();
                          // $record->copyGallery($newRecord, 'products');
                      })
                      ,
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
