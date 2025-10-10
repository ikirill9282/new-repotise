<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    public function form(Form $form): Form
    {
      return $form->schema([

      ]);
    }

    public function getPolicyForm(Form $form): Form
    {
      return $form
        ->schema([
          RichEditor::make('value')
            ->disableToolbarButtons([
              'attachFiles',
            ]),
        ])
        ->columns('full');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
