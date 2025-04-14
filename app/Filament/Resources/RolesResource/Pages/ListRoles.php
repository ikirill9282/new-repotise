<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\PermissionResource\Widgets\PermissionList;
use App\Filament\Resources\RolesResource;
use App\Filament\Resources\RolesResource\Widgets\AssignedPermissions;
use App\Filament\Resources\RolesResource\Widgets\RoleSelection;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RolesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
              ->modal()
              ->icon('heroicon-o-plus'),
        ];
    }

    protected function getFooterWidgets(): array
    {
      return [
        RoleSelection::class,
        AssignedPermissions::class,
      ];
    }
}
