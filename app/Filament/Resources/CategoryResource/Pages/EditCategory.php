<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Status;
use Filament\Forms\Form;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    public function form(Form $form): Form
    {
      return $form->schema([
        TextInput::make('title'),
        TextInput::make('slug'),
        Select::make('status_id')
          ->label('Status')
          ->options(Status::pluck('title', 'id'))
          ,
      ]); 
    }
}
