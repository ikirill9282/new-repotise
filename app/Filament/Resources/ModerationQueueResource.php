<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModerationQueueResource\Pages;
use App\Models\ModerationQueue;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Review;
use App\Models\Report;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\CommentResource;
use App\Filament\Resources\ReviewResource;
use App\Filament\Resources\UserComplaintResource;
use App\Filament\Resources\ContentErrorReportResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class ModerationQueueResource extends Resource
{
    protected static ?string $model = ModerationQueue::class;

    protected static ?string $navigationGroup = 'content';

    protected static ?string $navigationLabel = 'Moderation Queue';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 6;

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
                    ->label('Task ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('model')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'article' => 'info',
                        'comment' => 'success',
                        'review' => 'warning',
                        'report' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('reason')
                    ->label('Reason')
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->formatStateUsing(function($state) {
                        return match($state) {
                            ModerationQueue::PRIORITY_URGENT => 'Urgent',
                            ModerationQueue::PRIORITY_HIGH => 'High',
                            ModerationQueue::PRIORITY_NORMAL => 'Normal',
                            ModerationQueue::PRIORITY_LOW => 'Low',
                            default => 'Normal',
                        };
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        ModerationQueue::PRIORITY_URGENT => 'danger',
                        ModerationQueue::PRIORITY_HIGH => 'warning',
                        ModerationQueue::PRIORITY_NORMAL => 'info',
                        ModerationQueue::PRIORITY_LOW => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('item_details')
                    ->label('Item Details')
                    ->formatStateUsing(function($record) {
                        $instance = $record->getModelInstance();
                        if (!$instance) return 'N/A';
                        
                        if ($instance instanceof Article) {
                            return $instance->title;
                        }
                        if ($instance instanceof Comment) {
                            return 'Comment #' . $instance->id . ' on "' . ($instance->article->title ?? 'N/A') . '"';
                        }
                        if ($instance instanceof Review) {
                            return 'Review #' . $instance->id . ' on "' . ($instance->product->title ?? 'N/A') . '"';
                        }
                        if ($instance instanceof Report) {
                            return 'Report #' . $instance->id;
                        }
                        return 'Item #' . $instance->id;
                    })
                    ->url(function($record) {
                        $instance = $record->getModelInstance();
                        if (!$instance) return null;
                        
                        if ($instance instanceof Article) {
                            return url("/admin/articles/{$instance->id}/edit");
                        }
                        if ($instance instanceof Comment) {
                            return url("/admin/comments");
                        }
                        if ($instance instanceof Review) {
                            return url("/admin/reviews");
                        }
                        if ($instance instanceof Report) {
                            if ($instance->type === Report::TYPE_COMPLAINT) {
                                return UserComplaintResource::getUrl('index');
                            }
                            if ($instance->type === Report::TYPE_CONTENT_ERROR) {
                                return ContentErrorReportResource::getUrl('index');
                            }
                        }
                        return null;
                    })
                    ->color(Color::Sky),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('model')
                    ->label('Filter by Type')
                    ->options([
                        'all' => 'All',
                        'article' => 'Article',
                        'comment' => 'Comment',
                        'review' => 'Review',
                        'report' => 'Report',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->where('model', $data['value']);
                    }),
                SelectFilter::make('priority')
                    ->label('Filter by Priority')
                    ->options([
                        'all' => 'All',
                        ModerationQueue::PRIORITY_URGENT => 'Urgent',
                        ModerationQueue::PRIORITY_HIGH => 'High',
                        ModerationQueue::PRIORITY_NORMAL => 'Normal',
                        ModerationQueue::PRIORITY_LOW => 'Low',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->where('priority', $data['value']);
                    }),
            ])
            ->actions([
                Action::make('view_item')
                    ->label('View Item')
                    ->icon('heroicon-o-eye')
                    ->url(function($record) {
                        $instance = $record->getModelInstance();
                        if (!$instance) return null;
                        
                        if ($instance instanceof Article) {
                            return url("/admin/articles/{$instance->id}/edit");
                        }
                        if ($instance instanceof Comment) {
                            return CommentResource::getUrl('index');
                        }
                        if ($instance instanceof Review) {
                            return ReviewResource::getUrl('index');
                        }
                        if ($instance instanceof Report) {
                            if ($instance->type === Report::TYPE_COMPLAINT) {
                                return UserComplaintResource::getUrl('index');
                            }
                            if ($instance->type === Report::TYPE_CONTENT_ERROR) {
                                return ContentErrorReportResource::getUrl('index');
                            }
                        }
                        return null;
                    })
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('priority', 'desc')
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
            'index' => Pages\ListModerationQueues::route('/'),
        ];
    }
}

