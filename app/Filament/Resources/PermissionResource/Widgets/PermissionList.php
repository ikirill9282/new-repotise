<?php

namespace App\Filament\Resources\PermissionResource\Widgets;

use App\Filament\Resources\PermissionResource;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Spatie\Permission\Models\Permission;

class PermissionList extends BaseWidget
{   
    // protected $listeners = ['roleSelectionUpdated'];

    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Analytics';

    protected array $default_config = [
      'role_id' => null,
    ];

    public array $config = [];

    public function mount(array $config = [])
    {
      $this->config = array_merge($this->default_config, $this->config, $config);
    }


    public function table(Table $table): Table
    {
      return $table->query(
          Permission::query()
            ->when(
              $this->config['role_id'],
              fn($q) => $q->whereHas('roles', fn($sq) => $sq->where('roles.id', $this->config['role_id'])),
              fn($q) => $q->where('id', 0)->first(),
            )
        )
        ->columns(PermissionResource::defaultTableColumns())
      ;
    }

    // public function roleSelectionUpdated($arg)
    // {
    //   dd($arg);
    // }

    // public static function canView(): bool
    // {
    //   return !is_null(static::$role_id);
    // }
}
