<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use App\Models\Status;
use App\Models\Article;
use App\Enums\Status as StatusEnum;
use App\Filament\Resources\UserResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationGroup = 'content';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Comments';

    protected static ?int $navigationSort = 2;

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
                CheckboxColumn::make('selected'),
                TextColumn::make('id')
                    ->label('Comment ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('article.title')
                    ->label('Content Title')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => $record->article ? url("/admin/articles/{$record->article->id}/edit") : null)
                    ->color(Color::Sky),
                TextColumn::make('content_type')
                    ->label('Content Type')
                    ->formatStateUsing(function($record) {
                        if (!$record->article) return 'N/A';
                        return $record->article->author->hasRole('admin') ? 'News' : 'Article';
                    })
                    ->badge()
                    ->color(fn($record) => $record->article && $record->article->author->hasRole('admin') ? 'info' : 'success'),
                TextColumn::make('text')
                    ->label('Comment Text')
                    ->limit(50)
                    ->tooltip(fn($record) => strip_tags($record->text))
                    ->html(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable(['author.username', 'author.email', 'author.name'])
                    ->url(fn($record) => $record->author ? UserResource::getUrl('view', ['record' => $record->author]) : null)
                    ->color(Color::Sky),
                TextColumn::make('parent_info')
                    ->label('Parent / Reply')
                    ->formatStateUsing(function($record) {
                        if (!$record->parent_id) return '-';
                        return "Reply to #{$record->parent_id}";
                    })
                    ->badge()
                    ->color('gray'),
                TextColumn::make('display_status')
                    ->label('Status')
                    ->formatStateUsing(function($record) {
                        return match($record->status_id) {
                            StatusEnum::ACTIVE => 'Published',
                            StatusEnum::PENDING => 'Pending Approval',
                            StatusEnum::REJECT => 'Rejected',
                            StatusEnum::SPAM => 'Spam',
                            default => $record->status->title ?? 'Unknown',
                        };
                    })
                    ->badge()
                    ->color(fn($record) => match($record->status_id) {
                        StatusEnum::ACTIVE => 'success',
                        StatusEnum::PENDING => 'warning',
                        StatusEnum::REJECT => 'danger',
                        StatusEnum::SPAM => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Filter by Status')
                    ->options([
                        'all' => 'All',
                        StatusEnum::ACTIVE => 'Published',
                        StatusEnum::PENDING => 'Pending Approval',
                        StatusEnum::REJECT => 'Rejected',
                        StatusEnum::SPAM => 'Spam',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->where('status_id', $data['value']);
                    })
                    ->default(StatusEnum::PENDING),
                SelectFilter::make('content_type')
                    ->label('Filter by Content Type')
                    ->options([
                        'all' => 'All',
                        'article' => 'Articles',
                        'news' => 'News',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->whereHas('article', function($q) use ($data) {
                            if ($data['value'] === 'news') {
                                $q->whereHas('author', fn($sq) => $sq->whereHas('roles', fn($ssq) => $ssq->where('name', 'admin')));
                            } else {
                                $q->whereHas('author', fn($sq) => $sq->whereHas('roles', fn($ssq) => $ssq->where('name', '!=', 'admin')));
                            }
                        });
                    }),
                SelectFilter::make('article_id')
                    ->label('Filter by Content Item')
                    ->relationship('article', 'title')
                    ->searchable(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading('Comment Details')
                        ->infolist(fn($record) => static::getCommentInfolist($record)),
                    EditAction::make(),
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn($record) => $record->status_id == StatusEnum::PENDING)
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update(['status_id' => StatusEnum::ACTIVE]);
                            Notification::make()
                                ->title('Comment approved')
                                ->success()
                                ->send();
                        }),
                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn($record) => $record->status_id == StatusEnum::PENDING)
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update(['status_id' => StatusEnum::REJECT]);
                            Notification::make()
                                ->title('Comment rejected')
                                ->success()
                                ->send();
                        }),
                    Action::make('mark_as_spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('gray')
                        ->visible(fn($record) => $record->status_id != StatusEnum::SPAM)
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $spamStatus = Status::where('title', 'Spam')->first();
                            if ($spamStatus) {
                                $record->update(['status_id' => $spamStatus->id]);
                            }
                            Notification::make()
                                ->title('Comment marked as spam')
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make()
                        ->label('Delete Permanently')
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status_id == StatusEnum::PENDING) {
                                    $record->update(['status_id' => StatusEnum::ACTIVE]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} comment(s) approved")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status_id == StatusEnum::PENDING) {
                                    $record->update(['status_id' => StatusEnum::REJECT]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} comment(s) rejected")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('mark_as_spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('gray')
                        ->action(function ($records) {
                            $spamStatus = Status::where('title', 'Spam')->first();
                            if (!$spamStatus) return;
                            
                            $count = 0;
                            foreach ($records as $record) {
                                $record->update(['status_id' => $spamStatus->id]);
                                $count++;
                            }
                            Notification::make()
                                ->title("{$count} comment(s) marked as spam")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getCommentInfolist(Comment $record): Infolist
    {
        return Infolist::make()
            ->schema([
                Section::make('Comment Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Comment ID'),
                        TextEntry::make('author.name')
                            ->label('Author')
                            ->url(fn($record) => $record->author ? UserResource::getUrl('view', ['record' => $record->author]) : null),
                        TextEntry::make('text')
                            ->label('Comment Text')
                            ->html(),
                        TextEntry::make('article.title')
                            ->label('Content Title')
                            ->url(fn($record) => $record->article ? url("/admin/articles/{$record->article->id}/edit") : null),
                        TextEntry::make('content_type')
                            ->label('Content Type')
                            ->formatStateUsing(fn($record) => $record->article && $record->article->author->hasRole('admin') ? 'News' : 'Article'),
                        TextEntry::make('parent.text')
                            ->label('Parent Comment')
                            ->visible(fn($record) => $record->parent_id !== null)
                            ->html(),
                        TextEntry::make('display_status')
                            ->label('Status')
                            ->formatStateUsing(function($record) {
                                return match($record->status_id) {
                                    StatusEnum::ACTIVE => 'Published',
                                    StatusEnum::PENDING => 'Pending Approval',
                                    StatusEnum::REJECT => 'Rejected',
                                    StatusEnum::SPAM => 'Spam',
                                    default => $record->status->title ?? 'Unknown',
                                };
                            })
                            ->badge()
                            ->color(fn($record) => match($record->status_id) {
                                StatusEnum::ACTIVE => 'success',
                                StatusEnum::PENDING => 'warning',
                                StatusEnum::REJECT => 'danger',
                                StatusEnum::SPAM => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Date Created')
                            ->dateTime(),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListComments::route('/'),
        ];
    }
}
