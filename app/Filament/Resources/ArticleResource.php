<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use App\Models\Status;
use Filament\Forms;
use Filament\Forms\Components\Select;
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
use Filament\Support\Colors\Color;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;
use App\Models\User;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Database\Eloquent\Collection;

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
            ->recordUrl(fn() => null)
            ->query(static::getEloquentQuery()->orderByDesc('id'))
            ->columns([
                TextColumn::make('id')
                  ->label('Article ID')
                  ,
                TextColumn::make('preview')
                  ->label('Image')
                  ->view('filament.tables.columns.image')
                  ,
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->color(Color::Sky)
                    // ->url(fn($record) => $record->makeUrl(), true)
                    ->url(fn($record) => url("/admin/articles/$record->id/edit"))
                    ,
                TextColumn::make('user')
                    ->view('filament.tables.columns.author')
                    ->searchable(query: function ($query, $search) {
                        $query->orWhereHas('user', function ($q) use ($search) {
                            $q->where('username', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('status.title')
                  ->label('Status')
                  ->searchable()
                  ->sortable()

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
                TextColumn::make('views')
                  ->sortable()
                  ->searchable()
                  ,
                TextColumn::make('comments_count')
                  ->sortable()
                  ->getStateUsing(function($record) {
                    return $record->getFullCommentsCount();
                  })
                  ,
                
                TextColumn::make('scheduled_at')
                  ->dateTime()
                  ->sortable()
                  ->searchable()
                  ->toggleable()
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
                SelectFilter::make('type')
                  ->label('Filter by Content Type')
                  ->options([
                    'article' => 'Article',
                    'news' => 'News',
                  ])
                  ->query(function($query, $state) {
                    if (!empty($state['value'])) {
                      $query->when($state['value'] == 'article', function ($query) {
                        $query->whereHas(
                          'author', 
                          fn($subquery) => $subquery->whereHas(
                            'roles', 
                            fn($ssq) => $ssq->where('roles.name', '!=', 'admin')
                          )
                        );
                      })
                      ->when($state['value'] == 'news', function ($query) {
                        $query->whereHas(
                          'author',
                          fn($subquery) => $subquery->whereHas(
                            'roles',
                            fn($ssq) => $ssq->where('roles.name', 'admin')
                          )
                        );
                      })
                      // ->ddRawSql()
                      ;
                    }
                  })
                  ,
                SelectFilter::make('user_id')
                  ->label('Filter by Author')
                  ->searchable()
                  ->options(User::whereHas('articles')->get()->pluck('name', 'id'))
                  ->query(function($query, $state) {
                    if (!empty($state['value'])) {
                      // dd($state['value']);
                      $query->where('user_id', $state['value']);
                    }
                  })
                  ,
                SelectFilter::make('status_id')
                  ->label('Filter by Status')
                  ->searchable()
                  ->options(Status::all()->pluck('title', 'id'))
                  ->query(function($query, $state) {
                    if (!empty($state['value'])) {
                      $query->where('status_id', $state['value']);
                    }
                  })
                  ,
                DateRangeFilter::make('created_at') // поле модели для фильтрации по дате
                  ->label('Filter by Date created')
                  ->query(function ($query, array $data) {
                    if (!empty($data['created_at'])) {
                      $arr = explode('-', $data['created_at']);
                      $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
                      
                      return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
                    }
                  })
                  ,
                DateRangeFilter::make('updated_at') // поле модели для фильтрации по дате
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
            ->recordUrl(fn() => null)
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
                      $newRecord = $record->replicate(['status_id', 'published_at']);
                      // $newRecord->status_id = 3;
                      // $newRecord->published_at = null;
                      $newRecord->save();
                      $record->copyGallery($newRecord, 'articles');
                  })
                  ,
                
                Action::make('Approve')
                  ->icon('heroicon-o-check-circle')
                  ->visible(fn (Article $record): bool => $record->status_id == 3)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                  })
                  ,
                Action::make('Reject')
                  ->icon('heroicon-o-shield-exclamation')
                  ->visible(fn (Article $record): bool => $record->status_id == 3)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 5]);
                  })
                  ,

                Action::make('Publish')
                  ->icon('heroicon-o-document-text')
                  ->visible(fn (Article $record): bool => $record->status_id === 2)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                  })
                  ,

                Action::make('Unpublish')
                  ->icon('heroicon-o-x-circle')
                  ->visible(fn (Article $record): bool => $record->status_id === 1)
                  ->action(function (Article $record) {
                      $record->update(['status_id' => 2, 'published_at' => null]);
                  })
                  ,

                DeleteAction::make(),
              ]),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),


                    Tables\Actions\BulkAction::make('Need Review')
                      ->icon('heroicon-o-check-circle')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Article::whereIn('id', $items)->update(['status_id' => 3, 'published_at' => null]);
                      })
                      ,
                    Tables\Actions\BulkAction::make('Approve')
                      ->icon('heroicon-o-check-circle')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Article::whereIn('id', $items)->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,
                    Tables\Actions\BulkAction::make('Reject')
                      ->icon('heroicon-o-shield-exclamation')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Article::whereIn('id', $items)->update(['status_id' => 5, 'published_at' => null]);
                      })
                      ,
                    
                    Tables\Actions\BulkAction::make('Publish')
                      ->icon('heroicon-o-document-text')
                      ->action(function (Collection $records) {
                          $items = $records->pluck('id');
                          Article::whereIn('id', $items)->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                          // $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,

                    Tables\Actions\BulkAction::make('Unpublish')
                      ->icon('heroicon-o-x-circle')
                      ->action(function (Collection $records) {
                          $items = $records->pluck('id');
                          Article::whereIn('id', $items)->update(['status_id' => 2, 'published_at' => null]);
                          // $record->update(['status_id' => 2, 'published_at' => null]);
                      })
                      ,
                    
                    Tables\Actions\BulkAction::make('Duplicate')
                      ->icon('heroicon-o-document-duplicate')
                      ->action(function ($records) {
                          foreach ($records as $record) {
                            $newRecord = $record->replicate(['status_id', 'published_at']);
                            $newRecord->save();
                            $record->copyGallery($newRecord, 'articles');
                          }
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
