<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\View\View;


class UserResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name'),
        TextInput::make('username'),
        TextInput::make('email'),
        Select::make('roles')
          ->relationship('roles')
          ->options(Role::all()->pluck('name', 'id')),
      ])
      ->columns(1);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('username')
          ->label('User info')
          ->formatStateUsing(fn (string $state, $record): View => view(
            'filament.tables.username',
            ['state' => $state, 'record' => $record],
        )),
        TextColumn::make('email_verified_at')->since(),
        TextColumn::make('roles.name'),
        TextColumn::make('created_at'),
        TextColumn::make('updated_at'),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        // Tables\Actions\BulkActionGroup::make([
        //     Tables\Actions\DeleteBulkAction::make(),
        // ]),
      ])
      ->recordUrl(false);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListUsers::route('/'),
      'create' => Pages\CreateUser::route('/create'),
      'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }
}
