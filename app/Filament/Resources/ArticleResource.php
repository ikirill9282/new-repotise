<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationGroup = 'Articles';

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';

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
                TextColumn::make('id'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author')
                    ->view('filament.tables.columns.author')
                    ->searchable()
                    ,
                TextColumn::make('status_id')
                  ->label('Status')
                  ->searchable()
                  ->sortable()
                  ,
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ,
                TextColumn::make('views')
                    ->sortable()
                    ->searchable()
                    ,
                TextColumn::make('comments_count')
                    ->sortable()
                    ->searchable()
                    ,
            ])
            ->filters([
                //
            ])
            ->actions([
                
              ActionGroup::make([
                EditAction::make(),
                ViewAction::make('view')
                  ->url(fn (Article $record): string => $record->makeFeedUrl())
                  ->extraAttributes(['target' => '_blank'])
                  ,
                
                Action::make('Duplicate')
                  ->icon('heroicon-o-document-duplicate')
                  ->action(function (Article $record) {
                      $newRecord = $record->replicate();
                      $newRecord->save();
                      Storage::copy($record->preview, $newRecord->preview);
                  })
                  ,
                
                Action::make('Approve')
                  ->visible(fn (Article $record): bool => $record->status_id == 3)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 1]);
                  })
                  ,
                Action::make('Reject')
                  ->visible(fn (Article $record): bool => $record->status_id == 3)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 5]);
                  })
                  ,

                Action::make('Publish')
                  ->icon('heroicon-o-document-text')
                  ->visible(fn (Article $record): bool => $record->status_id === 2)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 1]);
                  })
                  ,

                Action::make('Unpublish')
                  ->icon('heroicon-o-x-circle')
                  ->visible(fn (Article $record): bool => $record->status_id === 1)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 2]);
                  })
                  ,

                DeleteAction::make(),
              ]),
            ])
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
