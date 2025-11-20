<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\History;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack'; // Icon removed - group has icon

    protected static ?string $navigationGroup = 'settings';

    protected static ?string $navigationLabel = 'Pages';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Page Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                if (empty($get('slug'))) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-friendly identifier for the page')
                            ->disabled(fn($record) => $record && $record->type === Page::TYPE_SYSTEM),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                Page::TYPE_SYSTEM => 'System',
                                Page::TYPE_CUSTOM => 'Custom',
                            ])
                            ->default(Page::TYPE_CUSTOM)
                            ->disabled(fn($record) => $record && $record->type === Page::TYPE_SYSTEM)
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                Page::STATUS_DRAFT => 'Draft',
                                Page::STATUS_PUBLISHED => 'Published',
                            ])
                            ->default(Page::STATUS_DRAFT)
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Page Content')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make('SEO Settings')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO Title')
                            ->maxLength(255)
                            ->helperText('Meta title for search engines'),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO Description')
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('Meta description for search engines'),
                        TextInput::make('seo_keywords')
                            ->label('SEO Keywords')
                            ->maxLength(255)
                            ->helperText('Comma-separated keywords'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Page ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->color(Color::Sky),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn($state) => $state === Page::TYPE_SYSTEM ? 'warning' : 'gray')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state === Page::STATUS_PUBLISHED ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->icon('heroicon-o-clock')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->icon('heroicon-o-clock')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        Page::TYPE_SYSTEM => 'System',
                        Page::TYPE_CUSTOM => 'Custom',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Page::STATUS_DRAFT => 'Draft',
                        Page::STATUS_PUBLISHED => 'Published',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->type !== Page::TYPE_SYSTEM)
                    ->requiresConfirmation()
                    ->modalHeading('Delete Page')
                    ->modalDescription('Are you sure you want to delete this page? This action cannot be undone.')
                    ->action(function ($record) {
                        $title = $record->title;
                        $record->delete();
                        
                        History::warning()
                            ->action('Page Deleted')
                            ->initiator(Auth::id())
                            ->message("Page '{$title}' was deleted")
                            ->payload(['ip_address' => request()->ip()])
                            ->write();
                        
                        Notification::make()
                            ->title('Page deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->type === Page::TYPE_SYSTEM) {
                                    continue; // Skip system pages
                                }
                                $record->delete();
                                $count++;
                            }
                            
                            if ($count > 0) {
                                History::warning()
                                    ->action('Pages Deleted (Bulk)')
                                    ->initiator(Auth::id())
                                    ->message("{$count} page(s) were deleted")
                                    ->payload(['ip_address' => request()->ip()])
                                    ->write();
                            }
                            
                            Notification::make()
                                ->title("{$count} page(s) deleted")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
