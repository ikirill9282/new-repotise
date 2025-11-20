<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentErrorReportResource\Pages;
use App\Models\Report;
use App\Models\Article;
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
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class ContentErrorReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationGroup = 'content';

    protected static ?string $navigationLabel = 'Error Reports';

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', Report::TYPE_CONTENT_ERROR);
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
                    ->label('Report ID')
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
                    ->label('Reported Content')
                    ->formatStateUsing(function($record) {
                        $content = $record->reportable;
                        if (!$content || !($content instanceof Article)) return 'N/A';
                        return $content->title;
                    })
                    ->url(fn($record) => static::getErrorReportDetailsUrl($record))
                    ->color(Color::Sky),
                TextColumn::make('content_author')
                    ->label('Content Author')
                    ->formatStateUsing(function($record) {
                        $content = $record->reportable;
                        if (!$content || !($content instanceof Article)) return 'N/A';
                        return $content->author->name ?? 'N/A';
                    })
                    ->url(function($record) {
                        $content = $record->reportable;
                        if (!$content || !($content instanceof Article)) return null;
                        return $content->author ? UserResource::getUrl('view', ['record' => $content->author]) : null;
                    })
                    ->color(Color::Sky),
                TextColumn::make('user_message')
                    ->label('User Message')
                    ->formatStateUsing(function($record) {
                        $message = $record->message ?? '';
                        return strlen($message) > 50 ? substr(strip_tags($message), 0, 50) . '...' : strip_tags($message);
                    })
                    ->tooltip(fn($record) => strip_tags($record->message ?? '')),
                TextColumn::make('display_status')
                    ->label('Report Status')
                    ->formatStateUsing(function($record) {
                        if ($record->status === Report::STATUS_NEW) {
                            return 'New';
                        }
                        if ($record->status === Report::STATUS_RESOLVED) {
                            // Для error reports используем resolution_type для различения
                            // Но по ТЗ для error reports: Correction Made или Dismissed
                            // Используем resolution_type: если null или action_taken -> Correction Made, dismissed -> Dismissed
                            if ($record->resolution_type === Report::RESOLUTION_DISMISSED) {
                                return 'Dismissed';
                            }
                            return 'Correction Made';
                        }
                        return ucfirst($record->status);
                    })
                    ->badge()
                    ->color(function($record) {
                        if ($record->status === Report::STATUS_NEW) {
                            return 'warning';
                        }
                        if ($record->status === Report::STATUS_RESOLVED) {
                            if ($record->resolution_type === Report::RESOLUTION_DISMISSED) {
                                return 'gray';
                            }
                            return 'success';
                        }
                        return 'gray';
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'all' => 'All',
                        'new' => 'New',
                        'correction_made' => 'Correction Made',
                        'dismissed' => 'Dismissed',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        if ($data['value'] === 'new') {
                            return $query->where('status', Report::STATUS_NEW);
                        }
                        if ($data['value'] === 'correction_made') {
                            return $query->where('status', Report::STATUS_RESOLVED)
                                ->where(function($q) {
                                    $q->whereNull('resolution_type')
                                      ->orWhere('resolution_type', Report::RESOLUTION_ACTION_TAKEN);
                                });
                        }
                        if ($data['value'] === 'dismissed') {
                            return $query->where('status', Report::STATUS_RESOLVED)
                                ->where('resolution_type', Report::RESOLUTION_DISMISSED);
                        }
                        return $query;
                    })
                    ->default('new'),
                SelectFilter::make('content_type')
                    ->label('Filter by Content Type')
                    ->options([
                        'all' => 'All',
                        'article' => 'Article',
                        'news' => 'News',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null || $data['value'] === 'all') {
                            return $query;
                        }
                        return $query->whereHas('reportable', function($q) use ($data) {
                            if ($data['value'] === 'news') {
                                $q->whereHas('author', fn($sq) => $sq->whereHas('roles', fn($ssq) => $ssq->where('name', 'admin')));
                            } else {
                                $q->whereHas('author', fn($sq) => $sq->whereHas('roles', fn($ssq) => $ssq->where('name', '!=', 'admin')));
                            }
                        });
                    }),
                SelectFilter::make('article_id')
                    ->label('Filter by Content Item')
                    ->relationship('reportable', 'title', fn($query) => $query->where('reportable_type', Article::class))
                    ->searchable(),
                SelectFilter::make('reporter_id')
                    ->label('Filter by Reporter')
                    ->relationship('author', 'name')
                    ->searchable(),
                SelectFilter::make('content_author_id')
                    ->label('Filter by Content Author')
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null) {
                            return $query;
                        }
                        return $query->whereHas('reportable', function($q) use ($data) {
                            $q->where('user_id', $data['value']);
                        });
                    })
                    ->getSearchResultsUsing(fn($search) => \App\Models\User::where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])),
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
                    ->modalHeading('Error Report Details')
                    ->infolist(fn($record) => static::getErrorReportInfolist($record))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_correction_made')
                        ->label('Mark as Correction Made')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === Report::STATUS_NEW) {
                                    $record->update(['status' => Report::STATUS_RESOLVED]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} report(s) marked as correction made")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('mark_dismissed')
                        ->label('Mark as Dismissed')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
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
                                ->title("{$count} report(s) dismissed")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getErrorReportInfolist(Report $record): Infolist
    {
        $content = $record->reportable;
        
        return Infolist::make()
            ->schema([
                Section::make('Report Info')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Report ID'),
                        TextEntry::make('created_at')
                            ->label('Date Filed')
                            ->dateTime(),
                        TextEntry::make('display_status')
                            ->label('Status')
                            ->formatStateUsing(function($record) {
                                if ($record->status === Report::STATUS_NEW) {
                                    return 'New';
                                }
                                if ($record->status === Report::STATUS_RESOLVED) {
                                    if ($record->resolution_type === Report::RESOLUTION_DISMISSED) {
                                        return 'Dismissed';
                                    }
                                    return 'Correction Made';
                                }
                                return ucfirst($record->status);
                            })
                            ->badge()
                            ->color(function($record) {
                                if ($record->status === Report::STATUS_NEW) {
                                    return 'warning';
                                }
                                if ($record->status === Report::STATUS_RESOLVED) {
                                    if ($record->resolution_type === Report::RESOLUTION_DISMISSED) {
                                        return 'gray';
                                    }
                                    return 'success';
                                }
                                return 'gray';
                            }),
                        TextEntry::make('author.name')
                            ->label('Reporter')
                            ->url(fn($record) => $record->author ? UserResource::getUrl('view', ['record' => $record->author]) : null),
                        TextEntry::make('message')
                            ->label('User Message')
                            ->formatStateUsing(fn($record) => strip_tags($record->message ?? ''))
                            ->html(),
                    ])
                    ->columns(2),
                Section::make('Reported Content Details')
                    ->schema([
                        TextEntry::make('content_author')
                            ->label('Content Author')
                            ->formatStateUsing(function() use ($content) {
                                if (!$content || !($content instanceof Article)) return 'N/A';
                                return $content->author->name ?? 'N/A';
                            })
                            ->url(function() use ($content) {
                                if (!$content || !($content instanceof Article)) return null;
                                return $content->author ? UserResource::getUrl('view', ['record' => $content->author]) : null;
                            }),
                        TextEntry::make('content_type')
                            ->label('Content Type')
                            ->formatStateUsing(function() use ($content) {
                                if (!$content || !($content instanceof Article)) return 'N/A';
                                return $content->author->hasRole('admin') ? 'News' : 'Article';
                            }),
                        TextEntry::make('content_title')
                            ->label('Content Title')
                            ->formatStateUsing(function() use ($content) {
                                if (!$content || !($content instanceof Article)) return 'N/A';
                                return $content->title;
                            }),
                        TextEntry::make('edit_content_link')
                            ->label('Edit Content')
                            ->formatStateUsing(fn() => 'Edit Article')
                            ->url(function() use ($content) {
                                if (!$content || !($content instanceof Article)) return null;
                                return url("/admin/articles/{$content->id}/edit");
                            })
                            ->color(Color::Sky),
                    ])
                    ->columns(2),
                Section::make('Moderation Panel')
                    ->schema([
                        Actions::make([
                            InfolistAction::make('mark_correction_made')
                                ->label('Mark as Correction Made')
                                ->color('success')
                                ->visible(fn($record) => $record->status === Report::STATUS_NEW)
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    $record->resolve(null, null, Report::RESOLUTION_ACTION_TAKEN);
                                    Notification::make()
                                        ->title('Report marked as correction made')
                                        ->success()
                                        ->send();
                                }),
                            InfolistAction::make('mark_dismissed')
                                ->label('Mark as Dismissed')
                                ->color('gray')
                                ->visible(fn($record) => $record->status === Report::STATUS_NEW)
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    $record->dismiss();
                                    Notification::make()
                                        ->title('Report dismissed')
                                        ->success()
                                        ->send();
                                }),
                            InfolistAction::make('edit_content')
                                ->label('Edit Content')
                                ->color('info')
                                ->url(function($record) {
                                    $content = $record->reportable;
                                    if (!$content || !($content instanceof Article)) return null;
                                    return url("/admin/articles/{$content->id}/edit");
                                })
                                ->openUrlInNewTab(),
                        ]),
                    ]),
            ]);
    }

    protected static function getErrorReportDetailsUrl(Report $record): ?string
    {
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
            'index' => Pages\ListContentErrorReports::route('/'),
        ];
    }
}
