<?php

namespace App\Filament\Resources\TypeResource\Pages;

use App\Filament\Resources\TypeResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use App\Models\Status;
use Filament\Forms\Components\TextInput;

class EditType extends EditRecord
{
    protected static string $resource = TypeResource::class;

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
