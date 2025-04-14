<?php

namespace App\Filament\Resources\RolesResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Permission\Models\Permission;
use App\Filament\Resources\PermissionResource;

class AssignedPermissions extends BaseWidget
{

  public ?int $role_id = null;

  protected $listeners = ['roleSelectionUpdated'];

  public function roleSelectionUpdated($arg)
  {
    $this->role_id = $arg['role_id'] ?? null;
    $this->resetTable();
  }

  public function table(Table $table): Table
  {
    return $table
      ->query(
        Permission::query()
          ->when(
            $this->role_id,
            fn($q) => $q->whereHas('roles', fn($sq) => $sq->where('roles.id', $this->role_id)),
            fn($q) => $q->where('id', 0)->first(),
          )
      )
      ->columns($this->getColumns())
      ;
  }

  protected function getColumns()
  {
    return PermissionResource::defaultTableColumns();
  }
}
