<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\SectionResource;
use App\Filament\Widgets\SectionVariablesWidget;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSection extends EditRecord
{
  protected static string $resource = SectionResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }

  protected function handleRecordUpdate(Model $record, array $data): Model
  {
    $record->update($data);
    return $record;
  }

  protected function getFooterWidgets(): array
  {
    return [
      SectionVariablesWidget::make([
        'config' => [
          'search' => false,
          'filter' => false,
          'group' => false,
        ]
      ]),
    ];
  }

  
  public function getFooterWidgetsColumns(): int
  {
    return 4;
  }
}
