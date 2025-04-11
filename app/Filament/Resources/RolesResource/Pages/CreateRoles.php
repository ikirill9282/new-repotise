<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\RolesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Slug;

class CreateRoles extends CreateRecord
{
    protected static string $resource = RolesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
      $data['name'] = str_ireplace('-', '_', Slug::makeEn($data['title']));

      return $data;
    }
}
