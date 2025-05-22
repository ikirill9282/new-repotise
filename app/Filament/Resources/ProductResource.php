<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\OrderProducts;
use App\Models\Product;
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
                  ->url(fn($record) => url("/admin/articles/$record->id/edit"))
                  ->color(Color::Sky)
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
                  ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->join('order_products', 'products.id', '=', 'order_products.product_id')
                      ->selectRaw('products.*, sum(order_products.count) as sales')
                      ->orderBy('sales', $direction)
                      ;
                  })
                  ->searchable()
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
                //
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
                          $record->update(['status_id' => 1]);
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
                          $newRecord = $record->replicate();
                          $newRecord->save();
                          Storage::copy($record->preview, $newRecord->preview);
                      })
                      ,
                    
                    Tables\Actions\Action::make('Publish')
                      ->icon('heroicon-o-document-text')
                      ->visible(fn (Product $record): bool => $record->status_id === 2)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 1]);
                      })
                      ,

                    Tables\Actions\Action::make('Unpublish')
                      ->icon('heroicon-o-x-circle')
                      ->visible(fn (Product $record): bool => $record->status_id === 1)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 2]);
                      })
                      ,

                    
                    Tables\Actions\DeleteAction::make()
                      ,
                ])
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
