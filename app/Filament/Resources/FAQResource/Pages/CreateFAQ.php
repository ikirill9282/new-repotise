<?php

namespace App\Filament\Resources\FAQResource\Pages;

use App\Filament\Resources\FAQResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFAQ extends CreateRecord
{
    protected static string $resource = FAQResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
      $data['type'] = $this->data['type'];
      return $data;
    }
}
