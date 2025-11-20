<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserComplaintResource\Pages;
use App\Models\Report;
use App\Models\Comment;
use App\Models\Review;
use App\Filament\Resources\UserResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class UserComplaintResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationGroup = 'community';

    protected static ?string $navigationLabel = 'Complaints';

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', Report::TYPE_COMPLAINT);
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
                CheckboxColumn::make('selected'),
                TextColumn::make('id')
                    ->label('Complaint ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date Filed')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Reporter')
                    ->searchable(['author.username', 'author.email', 'author.name'])
                    ->url(fn($record) => $record->author ? UserResource::getUrl('view', ['record' => $record->author]) : null)
                    ->color(Color::Sky),
                TextColumn::make('reported_content')
                    ->label('Reported Content (Text)')
                    ->formatStateUsing(function($record) {
                        $content = $record->reportable;
                        if (!$content) return 'N/A';
                        
                        $text = $content->text ?? $content->message ?? '';
                        return strlen($text) > 50 ? substr(strip_tags($text), 0, 50) . '...' : strip_tags($text);
                    })
                    ->tooltip(function($record) {
                        $content = $record->reportable;
                        if (!$content) return null;
                        return strip_tags($content->text ?? $content->message ?? '');
                    })
                    ->url(fn($record) => static::getComplaintDetailsUrl($record)),
                TextColumn::make('content_author')
                    ->label('Content Author')
                    ->formatStateUsing(function($record) {
                        $content = $record->reportable;
                        if (!$content) return 'N/A';
                        
                        if (method_exists($content, 'author')) {
                            return $content->author->name ?? 'N/A';
                        }
                        return 'N/A';
                    })
                    ->url(function($record) {
                        $content = $record->reportable;
                        if (!$content || !method_exists($content, 'author')) return null;
                        return $content->author ? UserResource::getUrl('view', ['record' => $content->author]) : null;
                    })
                    ->color(Color::Sky),
                TextColumn::make('content_location')
                    ->label('Content Location')
                    ->formatStateUsing(function($record) {
                        $content = $record->reportable;
                        if (!$content) return 'N/A';
                        
                        if ($content instanceof Comment) {
                            return $content->article ? $content->article->title : 'N/A';
                        }
                        if ($content instanceof Review) {
                            return $content->product ? $content->product->title : 'N/A';
                        }
                        return 'N/A';
                    })
                    ->url(function($record) {
                        $content = $record->reportable;
                        if ($content instanceof Comment && $content->article) {
                            return url("/admin/articles/{$content->article->id}/edit");
                        }
                        if ($content instanceof Review && $content->product) {
                            return url("/admin/products/{$content->product->id}/edit");
                        }
                        return null;
                    })
                    ->color(Color::Sky),
                TextColumn::make('reason')
                    ->label('Reason')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('display_status')
                    ->label('Complaint Status')
                    ->formatStateUsing(fn($record) => $record->getDisplayStatus())
                    ->badge()
                    ->color(fn($record) => match($record->status) {
                        Report::STATUS_NEW => 'warning',
                        Report::STATUS_RESOLVED => $record->resolution_type === Report::RESOLUTION_ACTION_TAKEN ? 'success' : 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('reported_content_type')
                    ->label('Filter by Reported Content Type')
                    ->options([
                        'all' => 'All',
                        Comment::class => 'Comment',
                        Review::class => 'Review',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->where('reportable_type', $data['value']);
                    }),
                SelectFilter::make('reason')
                    ->label('Filter by Complaint Reason')
                    ->options([
                        'all' => 'All',
                        Report::REASON_SPAM_OR_SCAM => Report::REASON_SPAM_OR_SCAM,
                        Report::REASON_OFFENSIVE => Report::REASON_OFFENSIVE,
                        Report::REASON_INAPPROPRIATE => Report::REASON_INAPPROPRIATE,
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->where('reason', $data['value']);
                    }),
                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'all' => 'All',
                        Report::STATUS_NEW => 'New',
                        Report::STATUS_RESOLVED => 'Resolved',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->where('status', $data['value']);
                    }),
                DateRangeFilter::make('created_at')
                    ->label('Date Range')
                    ->query(function ($query, array $data) {
                        if (!empty($data['created_at'])) {
                            $arr = explode('-', $data['created_at']);
                            $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
                            
                            return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
                        }
                    }),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Complaint Details')
                    ->infolist(fn($record) => static::getComplaintInfolist($record))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('resolve_keep')
                        ->label('Mark as Resolved - Keep Content')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === Report::STATUS_NEW) {
                                    $record->dismiss();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} complaint(s) resolved")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('resolve_reject')
                        ->label('Mark as Resolved - Reject Content')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === Report::STATUS_NEW && $record->reportable) {
                                    $content = $record->reportable;
                                    if (method_exists($content, 'update')) {
                                        $rejectStatus = \App\Models\Status::where('title', 'Reject')->first();
                                        if ($rejectStatus) {
                                            $content->update(['status_id' => $rejectStatus->id]);
                                        }
                                    }
                                    $record->resolve();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} complaint(s) resolved")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('resolve_spam')
                        ->label('Mark as Resolved - Mark Content as Spam')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === Report::STATUS_NEW && $record->reportable) {
                                    $content = $record->reportable;
                                    if (method_exists($content, 'update')) {
                                        $spamStatus = \App\Models\Status::where('title', 'Spam')->first();
                                        if ($spamStatus) {
                                            $content->update(['status_id' => $spamStatus->id]);
                                        }
                                    }
                                    $record->resolve();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} complaint(s) resolved")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getComplaintInfolist(Report $record): Infolist
    {
        $content = $record->reportable;
        
        return Infolist::make()
            ->schema([
                Section::make('Complaint Info')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Complaint ID'),
                        TextEntry::make('created_at')
                            ->label('Date Filed')
                            ->dateTime(),
                        TextEntry::make('display_status')
                            ->label('Status')
                            ->formatStateUsing(fn($record) => $record->getDisplayStatus())
                            ->badge()
                            ->color(fn($record) => match($record->status) {
                                Report::STATUS_NEW => 'warning',
                                Report::STATUS_RESOLVED => $record->resolution_type === Report::RESOLUTION_ACTION_TAKEN ? 'success' : 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('author.name')
                            ->label('Reporter')
                            ->url(fn($record) => $record->author ? UserResource::getUrl('view', ['record' => $record->author]) : null),
                        TextEntry::make('reason')
                            ->label('Reason')
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(2),
                Section::make('Reported Content Details')
                    ->schema([
                        TextEntry::make('content_author')
                            ->label('Content Author')
                            ->formatStateUsing(function($record) use ($content) {
                                if (!$content || !method_exists($content, 'author')) return 'N/A';
                                return $content->author->name ?? 'N/A';
                            })
                            ->url(function($record) use ($content) {
                                if (!$content || !method_exists($content, 'author')) return null;
                                return $content->author ? UserResource::getUrl('view', ['record' => $content->author]) : null;
                            }),
                        TextEntry::make('content_type')
                            ->label('Content Type')
                            ->formatStateUsing(fn() => $content instanceof Comment ? 'Comment' : ($content instanceof Review ? 'Review' : 'N/A')),
                        TextEntry::make('content_text')
                            ->label('Full Text')
                            ->formatStateUsing(function() use ($content) {
                                if (!$content) return 'N/A';
                                $text = $content->text ?? $content->message ?? '';
                                return strip_tags($text);
                            })
                            ->html(),
                        TextEntry::make('content_status')
                            ->label('Current Status')
                            ->formatStateUsing(function() use ($content) {
                                if (!$content || !isset($content->status_id)) return 'N/A';
                                return $content->status->title ?? 'N/A';
                            }),
                        TextEntry::make('content_location')
                            ->label('Location')
                            ->formatStateUsing(function() use ($content) {
                                if ($content instanceof Comment) {
                                    return $content->article ? $content->article->title : 'N/A';
                                }
                                if ($content instanceof Review) {
                                    return $content->product ? $content->product->title : 'N/A';
                                }
                                return 'N/A';
                            })
                            ->url(function() use ($content) {
                                if ($content instanceof Comment && $content->article) {
                                    return url("/admin/articles/{$content->article->id}/edit");
                                }
                                if ($content instanceof Review && $content->product) {
                                    return url("/admin/products/{$content->product->id}/edit");
                                }
                                return null;
                            }),
                    ])
                    ->columns(2),
                Section::make('Moderation Panel - Actions on Content')
                    ->schema([
                        Actions::make([
                            InfolistAction::make('keep_published')
                                ->label('Keep Content Published')
                                ->color('success')
                                ->visible(fn($record) => $record->status === Report::STATUS_NEW)
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    $record->dismiss();
                                    Notification::make()
                                        ->title('Complaint dismissed')
                                        ->success()
                                        ->send();
                                }),
                            InfolistAction::make('reject_content')
                                ->label('Reject Content')
                                ->color('danger')
                                ->visible(fn($record) => $record->status === Report::STATUS_NEW && $record->reportable)
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    $content = $record->reportable;
                                    if ($content && method_exists($content, 'update')) {
                                        $rejectStatus = \App\Models\Status::where('title', 'Reject')->first();
                                        if ($rejectStatus) {
                                            $content->update(['status_id' => $rejectStatus->id]);
                                        }
                                    }
                                    $record->resolve();
                                    Notification::make()
                                        ->title('Content rejected')
                                        ->success()
                                        ->send();
                                }),
                            InfolistAction::make('mark_spam')
                                ->label('Mark Content as Spam')
                                ->color('gray')
                                ->visible(fn($record) => $record->status === Report::STATUS_NEW && $record->reportable)
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    $content = $record->reportable;
                                    if ($content && method_exists($content, 'update')) {
                                        $spamStatus = \App\Models\Status::where('title', 'Spam')->first();
                                        if ($spamStatus) {
                                            $content->update(['status_id' => $spamStatus->id]);
                                        }
                                    }
                                    $record->resolve();
                                    Notification::make()
                                        ->title('Content marked as spam')
                                        ->success()
                                        ->send();
                                }),
                            InfolistAction::make('delete_permanently')
                                ->label('Delete Content Permanently')
                                ->color('danger')
                                ->visible(fn($record) => $record->status === Report::STATUS_NEW && $record->reportable)
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    $content = $record->reportable;
                                    if ($content && method_exists($content, 'delete')) {
                                        $content->delete();
                                    }
                                    $record->resolve();
                                    Notification::make()
                                        ->title('Content deleted')
                                        ->success()
                                        ->send();
                                }),
                            InfolistAction::make('view_author_profile')
                                ->label('View Author Profile')
                                ->color('info')
                                ->url(function($record) {
                                    $content = $record->reportable;
                                    if (!$content || !method_exists($content, 'author')) return null;
                                    return $content->author ? UserResource::getUrl('view', ['record' => $content->author]) : null;
                                })
                                ->openUrlInNewTab(),
                        ]),
                    ]),
            ]);
    }

    protected static function getComplaintDetailsUrl(Report $record): ?string
    {
        // Возвращаем URL для открытия модалки деталей
        return null; // Будет открываться через action
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
            'index' => Pages\ListUserComplaints::route('/'),
        ];
    }
}
