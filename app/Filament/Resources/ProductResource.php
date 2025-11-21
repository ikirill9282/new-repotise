<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\OrderProducts;
use App\Models\Product;
use App\Models\Status;
use App\Models\Type;
use App\Models\Category;
use App\Models\Location;
use App\Models\Gallery;
use App\Helpers\Collapse;
use App\Jobs\OptimizeMedia;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;
use App\Models\User;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'products';
    protected static ?string $navigationLabel = 'All Products';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        RichEditor::make('text')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                            ]),
                        
                        Select::make('user_id')
                            ->label('Seller')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($record) => $record !== null)
                            ->dehydrated(),
                        
                        Select::make('status_id')
                            ->label('Status')
                            ->options(Status::all()->pluck('title', 'id'))
                            ->required()
                            ->default(3),
                    ])
                    ->columns(2),
                
                Section::make('Categories & Locations')
                    ->schema([
                        Select::make('categories')
                            ->label('Categories')
                            ->relationship('categories', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select at least one category'),
                        
                        Select::make('locations')
                            ->label('Locations')
                            ->relationship('locations', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Optional: Select product locations. Only existing locations can be selected.')
                            ->createOptionForm([]) // Disable creating new locations
                            ->createOptionUsing(function () {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Cannot create location')
                                    ->body('Please select an existing location. To create a new location, use the Locations section in the admin panel.')
                                    ->send();
                                return null;
                            }),
                    ])
                    ->columns(2),
                
                Section::make('Pricing')
                    ->schema([
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01),
                        
                        TextInput::make('old_price')
                            ->label('Old Price (Sale Price)')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Optional: Original price before discount')
                            ->dehydrated(false), // Don't save to database if column doesn't exist
                    ])
                    ->columns(2),
                
                Section::make('Images')
                    ->schema([
                        FileUpload::make('preview_image')
                            ->label('Preview Image (Main Image)')
                            ->image()
                            ->directory('images')
                            ->disk('public')
                            ->maxSize(10240) // 10MB
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Main product image that will be displayed as preview')
                            ->default(fn ($record) => $record?->preview?->image)
                            ->columnSpanFull(),
                        
                        FileUpload::make('gallery_images')
                            ->label('Gallery Images')
                            ->image()
                            ->directory('images')
                            ->disk('public')
                            ->multiple()
                            ->maxFiles(8)
                            ->maxSize(10240) // 10MB per file
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Additional product images (up to 8 images)')
                            ->default(fn ($record) => $record?->gallery->where('preview', 0)->pluck('image')->toArray() ?? [])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record !== null),
                
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->rows(3)
                            ->maxLength(500)
                            ->disabled(fn ($record) => $record?->status_id !== 5)
                            ->helperText('This field is only visible for rejected products')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn() => null)
            ->query(static::getEloquentQuery()->orderByDesc('id'))
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
                  // ->url(fn($record) => $record->makeUrl(), true)
                  ->color(Color::Sky)
                  ->url(fn($record) => url("/admin/products/$record->id/edit"))
                  ,
                TextColumn::make('types.title')
                  ->label('Product Type')
                  ->sortable()
                  ->searchable(query: function (Builder $query, string $search): Builder {
                      return $query->whereHas('types', function ($q) use ($search) {
                          $q->where('title', 'like', "%{$search}%");
                      });
                  })
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

                // TextColumn::make('old_price')
                //   ->label('Old Price')
                //   ->sortable()
                //   ->searchable()
                //   ->toggleable()
                //   ->money('usd', true)
                //   ->color(Color::Gray)
                //   // ->extraAttributes(['class' => 'line-through'])
                //   ,
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
                  // ->sortable(query: function (Builder $query, string $direction): Builder {
                  //   return $query->join('order_products', 'products.id', '=', 'order_products.product_id')
                  //     ->selectRaw('products.*, sum(order_products.count) as sales')
                  //     ->orderBy('sales', $direction)
                  //     ;
                  // })
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
                SelectFilter::make('status_id')
                  ->label('Filter by Status')
                  ->options(Status::all()->pluck('title', 'id'))
                  ,
                SelectFilter::make('model')
                  ->label('Filter by Purchase Model')
                  ->options([
                    0 => 'Product',
                    1 => 'Subscription',
                  ])
                  ->query(function($query, $state) {
                    if (!empty($state['value'])) {
                      $query->where('subscription', $state['value']);
                    }
                  })
                  ,
                SelectFilter::make('type_id')
                  ->label('Filter by Product Type')
                  ->searchable()
                  ->multiple()
                  ->options(Type::pluck('title', 'id'))
                  ,
                SelectFilter::make('categories')
                  ->label('Filter by Category')
                  ->searchable()
                  ->multiple()
                  ->relationship('categories', 'title', fn (Builder $query) => $query)
                  ->query(function (Builder $query, array $data): Builder {
                    if (!empty($data['values']) && is_array($data['values'])) {
                      return $query->whereHas('categories', function ($q) use ($data) {
                        $q->whereIn('categories.id', $data['values']);
                      });
                    }
                    return $query;
                  })
                  ,
                SelectFilter::make('user_id')
                  ->label('Filter by Seller')
                  ->searchable()
                  ->options(
                    User::query()
                      ->whereHas('roles', fn($q) => $q->whereIn('name', ['system', 'creator', 'admin']))
                      ->pluck('username', 'id')
                  )
                  ,
                
                DateRangeFilter::make('created_at')
                  ->label('Filter by Date created')
                  ->query(function ($query, array $data) {
                    if (!empty($data['created_at'])) {
                      $arr = explode('-', $data['created_at']);
                      $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
                      
                      return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
                    }
                  })
                  ,
                DateRangeFilter::make('updated_at')
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
                          $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,
                    Tables\Actions\Action::make('Reject')
                      ->icon('heroicon-o-shield-exclamation')
                      ->visible(fn (Product $record): bool => $record->status_id === 3)
                      ->color('danger')
                      ->requiresConfirmation()
                      ->modalHeading('Reject Product')
                      ->modalDescription('Please provide a reason for rejecting this product.')
                      ->form([
                          Forms\Components\Textarea::make('rejection_reason')
                              ->label('Rejection Reason')
                              ->required()
                              ->rows(3)
                              ->maxLength(500)
                              ->helperText('Please explain why this product is being rejected.')
                      ])
                      ->action(function (Product $record, array $data) {
                          $record->update([
                              'status_id' => 5,
                              'rejection_reason' => $data['rejection_reason']
                          ]);
                      })
                      ,
                    Tables\Actions\Action::make('Duplicate')
                      ->icon('heroicon-o-document-duplicate')
                      ->action(function (Product $record) {
                          $newRecord = $record->replicate(['status_id', 'published_at']);
                          // $newRecord->status_id = 3;
                          // $newRecord->published_at = null;
                          $newRecord->save();
                          $record->copyGallery($newRecord, 'products');
                      })
                      ,
                    
                    Tables\Actions\Action::make('Publish')
                      ->icon('heroicon-o-document-text')
                      ->visible(fn (Product $record): bool => $record->status_id === 2)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,

                    Tables\Actions\Action::make('Unpublish')
                      ->icon('heroicon-o-x-circle')
                      ->visible(fn (Product $record): bool => $record->status_id === 1)
                      ->action(function (Product $record) {
                          $record->update(['status_id' => 2, 'published_at' => null]);
                      })
                      ,

                    
                    Tables\Actions\DeleteAction::make()
                      ,
                ])
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $totalOrders = 0;
                            foreach ($records as $record) {
                                $totalOrders += $record->getOrdersCount();
                            }
                            if ($totalOrders > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->warning()
                                    ->title('Some products have orders')
                                    ->body("Selected products have {$totalOrders} associated order(s). Products will be hidden (soft deleted) but data will be preserved.")
                                    ->persistent()
                                    ->send();
                            }
                        })
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Products deleted')
                                ->body('Selected products have been hidden (soft deleted).')
                        ),
                    // Tables\Actions\EditBulkAction::make(),
                    // Tables\Actions\ViewAction::make()
                    //   ->url(fn (Product $record): string => url($record->makeUrl()))
                    //   ->extraAttributes(['target' => '_blank'])
                    //   ,

                    Tables\Actions\BulkAction::make('Need Review')
                      ->icon('heroicon-o-check-circle')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Product::whereIn('id', $items)->update(['status_id' => 3, 'published_at' => null]);
                      })
                      ,
                    Tables\Actions\BulkAction::make('Approve')
                      ->icon('heroicon-o-check-circle')
                      ->action(function (Collection $records) {
                        $items = $records->pluck('id');
                        Product::whereIn('id', $items)->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,
                    Tables\Actions\BulkAction::make('Reject')
                      ->icon('heroicon-o-shield-exclamation')
                      ->color('danger')
                      ->requiresConfirmation()
                      ->modalHeading('Reject Selected Products')
                      ->modalDescription('Please provide a reason for rejecting these products.')
                      ->form([
                          Forms\Components\Textarea::make('rejection_reason')
                              ->label('Rejection Reason')
                              ->required()
                              ->rows(3)
                              ->maxLength(500)
                              ->helperText('This reason will be applied to all selected products.')
                      ])
                      ->action(function (Collection $records, array $data) {
                        $items = $records->pluck('id');
                        Product::whereIn('id', $items)->update([
                            'status_id' => 5,
                            'published_at' => null,
                            'rejection_reason' => $data['rejection_reason']
                        ]);
                      })
                      ,
                    
                    Tables\Actions\BulkAction::make('Publish')
                      ->icon('heroicon-o-document-text')
                      ->action(function (Collection $records) {
                          $items = $records->pluck('id');
                          Product::whereIn('id', $items)->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                          // $record->update(['status_id' => 1, 'published_at' => Carbon::now()]);
                      })
                      ,

                    Tables\Actions\BulkAction::make('Unpublish')
                      ->icon('heroicon-o-x-circle')
                      ->action(function (Collection $records) {
                          $items = $records->pluck('id');
                          Product::whereIn('id', $items)->update(['status_id' => 2, 'published_at' => null]);
                          // $record->update(['status_id' => 2, 'published_at' => null]);
                      })
                      ,
                    
                    Tables\Actions\BulkAction::make('Duplicate')
                      ->icon('heroicon-o-document-duplicate')
                      ->action(function ($records) {
                          foreach ($records as $record) {
                            $newRecord = $record->replicate(['status_id', 'published_at']);
                            $newRecord->save();
                            $record->copyGallery($newRecord, 'products');
                          }
                          // $newRecord = $record->replicate(['status_id', 'published_at']);
                          // $newRecord->status_id = 3;
                          // $newRecord->published_at = null;
                          // $newRecord->save();
                          // $record->copyGallery($newRecord, 'products');
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
            'index' => Pages\ListProducts::route('/'),
            'moderation' => Pages\ListProductsModeration::route('/moderation'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
