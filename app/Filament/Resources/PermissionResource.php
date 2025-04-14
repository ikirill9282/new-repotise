<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;
use Filament\Tables\Columns\TextColumn;
use App\Helpers\Slug;

class PermissionResource extends Resource
{
  protected static ?string $model = Permission::class;

  protected static ?string $navigationGroup = 'Users';

  protected static ?string $navigationIcon = 'heroicon-o-scale';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('title'),
      ])
      ->columns(1)
    ;
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns(static::defaultTableColumns())
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make()
          ->modal()
          // ,
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
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
      'index' => Pages\ListPermissions::route('/'),
      // 'create' => Pages\CreatePermission::route('/create'),
      // 'edit' => Pages\EditPermission::route('/{record}/edit'),
    ];
  }

  public static function defaultTableColumns(): array
  {
    return [
      TextColumn::make('title'),
      TextColumn::make('created_at')->since(),
      TextColumn::make('updated_at')->since(),
    ];
  }
}
