<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Helpers\Slug;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
      $data['name'] = str_ireplace('-', '_', Slug::makeEn($data['title']));

      return $data;
    }
}
