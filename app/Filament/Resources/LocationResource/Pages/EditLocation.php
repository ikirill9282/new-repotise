<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Status;
use Filament\Forms\Form;

class EditLocation extends EditRecord
{
    protected static string $resource = LocationResource::class;

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
