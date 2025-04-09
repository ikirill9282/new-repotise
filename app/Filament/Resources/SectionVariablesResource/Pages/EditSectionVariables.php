<?php

namespace App\Filament\Resources\SectionVariablesResource\Pages;

use App\Filament\Resources\SectionVariablesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionVariables extends EditRecord
{
    protected static string $resource = SectionVariablesResource::class;

    protected static ?string $title = 'Edit Section Variable';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave($data): array
    {
      if (is_array($data)) return $data;
      
      if (str_contains($data['value'], 'figure')) {
        preg_match_all('/<figure.*?<\/figure>/i', $data['value'], $figure);
        if (isset($figure[0])) {
          $figure = $figure[0];
          foreach ($figure as $item) {
            preg_match('/img\s+src="(.*?)"/i', $item, $img_src);
            $img_src = $img_src[1] ?? null;
            if ($img_src) {
              $img_path = preg_replace("/^.*?(\/storage.*?)$/is", "$1", $img_src);
              $img_url = url("/$img_path");
              $img = "<img src='$img_url' alt='Article image' />";
              $data['value'] = str_ireplace($item, $img, $data['value']);
            }
          }
        }
      }
  
      return $data;
    }
}
