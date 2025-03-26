<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Helpers\Slug;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePage extends CreateRecord
{
  protected static string $resource = PageResource::class;

  public ?Model $record = null;

  public function form(Form $form): Form
  {
    return $form->schema([
      TextInput::make('title')->required(),
    ]);
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['slug'] = Slug::makeEn($data['title']);
    return $data;
  }

  public function getFooterWidgetsColumns(): int|string|array
  {
    return 4;
  }
}
