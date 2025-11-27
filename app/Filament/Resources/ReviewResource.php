<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use App\Models\Status;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\ActionsPosition;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationGroup = 'community';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['author', 'product', 'status']);
    }

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
            ->columns([
                TextColumn::make('id')
                    ->label('Review ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->author->name ?? 'N/A')
                    ->url(fn($record) => $record->author ? \App\Filament\Resources\UserResource::getUrl('view', ['record' => $record->author->id]) : null)
                    ->color(Color::Sky)
                    ->placeholder('N/A'),
                TextColumn::make('product.title')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->product->title ?? 'N/A')
                    ->url(fn($record) => $record->product ? \App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $record->product->id]) : null)
                    ->color(Color::Sky)
                    ->placeholder('N/A'),
                TextColumn::make('parent_id')
                    ->label('Is Reply')
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn($state) => $state ? 'info' : 'gray'),
                TextColumn::make('status.title')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record->status->title ?? 'N/A')
                    ->color(fn($record) => match($record->status_id ?? null) {
                        1 => 'success',
                        2 => 'danger',
                        3 => 'warning',
                        default => 'gray',
                    })
                    ->sortable()
                    ->placeholder('N/A'),
                TextColumn::make('edited')
                    ->label('Edited')
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn($state) => $state ? 'warning' : 'gray'),
                TextColumn::make('text')
                    ->label('Review Text')
                    ->limit(50)
                    ->tooltip(fn($record) => strip_tags($record->text ?? ''))
                    ->html(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->options(Status::all()->pluck('title', 'id'))
                    ->searchable(),
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'title')
                    ->searchable(),
                SelectFilter::make('user_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable(),
                SelectFilter::make('edited')
                    ->label('Edited')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]),
                SelectFilter::make('parent_id')
                    ->label('Type')
                    ->options([
                        'with_parent' => 'Replies',
                        'without_parent' => 'Top-level Reviews',
                    ])
                    ->query(function($query, $state) {
                        if ($state['value'] === 'with_parent') {
                            return $query->whereNotNull('parent_id');
                        }
                        if ($state['value'] === 'without_parent') {
                            return $query->whereNull('parent_id');
                        }
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
