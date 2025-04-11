<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UsersTable;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationGroup = 'Users';

  protected static ?string $navigationIcon = 'heroicon-o-user-group';


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('name'),
        TextInput::make('username'),
        TextInput::make('email'),
        Select::make('roles')
          ->relationship('roles')
          ->options(Role::all()->pluck('title', 'id')),
      ])
      ->columns(1);
  }

  public static function table(Table $table): Table
  {
    return UsersTable::getTableConfig($table);
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
      // 'create' => Pages\CreateUser::route('/create'),
      // 'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }
}
