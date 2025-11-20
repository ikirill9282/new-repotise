<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductComplaintResource\Pages;
use App\Filament\Resources\ProductComplaintResource\RelationManagers;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\ProductResource;
use App\Models\Report;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ProductComplaintResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'products';

    protected static ?string $navigationLabel = 'Product Complaints';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('reportable_type', Product::class)
            ->with(['author', 'reportable', 'resolvedBy']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('message')
                    ->label('Complaint Message')
                    ->disabled()
                    ->rows(4)
                    ->columnSpanFull(),
                
                Select::make('status')
                    ->label('Status')
                    ->options([
                        Report::STATUS_NEW => 'New',
                        Report::STATUS_IN_PROGRESS => 'In Progress',
                        Report::STATUS_RESOLVED => 'Resolved',
                    ])
                    ->required(),
                
                Textarea::make('resolution_note')
                    ->label('Resolution Note')
                    ->rows(3)
                    ->maxLength(500)
                    ->visible(fn ($record) => $record?->status === Report::STATUS_RESOLVED),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('author.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->user_id]))
                    ->color(Color::Sky),
                
                TextColumn::make('reportable.title')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->reportable ? ProductResource::getUrl('edit', ['record' => $record->reportable_id]) : null)
                    ->color(Color::Sky)
                    ->limit(50),
                
                TextColumn::make('reportable.author.name')
                    ->label('Seller')
                    ->searchable()
                    ->url(fn ($record) => $record->reportable?->user_id ? UserResource::getUrl('view', ['record' => $record->reportable->user_id]) : null)
                    ->color(Color::Sky),
                
                TextColumn::make('reason')
                    ->label('Reason')
                    ->badge()
                    ->color('warning'),
                
                TextColumn::make('message')
                    ->label('Complaint')
                    ->limit(50)
                    ->wrap(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => match($record->status) {
                        Report::STATUS_NEW => 'danger',
                        Report::STATUS_IN_PROGRESS => 'warning',
                        Report::STATUS_RESOLVED => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($record) => $record->getDisplayStatus()),
                
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Report::STATUS_NEW => 'New',
                        Report::STATUS_IN_PROGRESS => 'In Progress',
                        Report::STATUS_RESOLVED => 'Resolved',
                    ]),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ProductComplaintResource::getUrl('view', ['record' => $record->id])),
                
                Action::make('unpublish_product')
                    ->label('Unpublish Product')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Unpublish Product')
                    ->modalDescription('This will change the product status to Draft (unpublished).')
                    ->action(function ($record) {
                        if ($record->reportable) {
                            $record->reportable->update(['status_id' => 2]); // Draft
                        }
                    })
                    ->visible(fn ($record) => $record->reportable && $record->reportable->status_id !== 2),
                
                Action::make('block_seller')
                    ->label('Block Seller')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Block Seller')
                    ->modalDescription('This will block the seller account.')
                    ->action(function ($record) {
                        if ($record->reportable && $record->reportable->author) {
                            $record->reportable->author->update(['status' => 'blocked']);
                        }
                    })
                    ->visible(fn ($record) => $record->reportable && $record->reportable->author && $record->reportable->author->status !== 'blocked'),
                
                Action::make('resolve')
                    ->label('Mark as Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('resolution_note')
                            ->label('Resolution Note')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->action(function ($record, array $data) {
                        $record->resolve(
                            auth()->id(),
                            $data['resolution_note'] ?? null
                        );
                    })
                    ->visible(fn ($record) => $record->status !== Report::STATUS_RESOLVED),
            ])
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
            'index' => Pages\ListProductComplaints::route('/'),
            'view' => Pages\ViewProductComplaint::route('/{record}'),
        ];
    }
}
