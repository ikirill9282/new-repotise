<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\View\View;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Spatie\Permission\Models\Role;

class UsersTable extends BaseWidget
{
  public function table(Table $table): Table
  {
    return static::table($table);
  }

  public static function getTableConfig($table): Table
  {
    return $table->columns([
      TextColumn::make('username')
        ->searchable(['name', 'username', 'email'])
        ->label('User info')
        ->view('filament.tables.columns.username')
        ,
      TextColumn::make('verified')
        ->formatStateUsing(function($record) {
          
        })
        ,
      TextColumn::make('email_verified_at')
        ->label('Email Verified')
        ->dateTime()
        ->icon('heroicon-o-clock'),
      TextColumn::make('roles.title'),
      TextColumn::make('created_at'),
      TextColumn::make('updated_at'),
    ])
      ->filters([
        Filter::make('email_verified_at')
          ->label('Verified')
          ->query(function ($query, $data) {
            $query->when(
              (isset($data['isActive']) && $data['isActive']),
              fn($subquery) => $subquery->whereNotNull('email_verified_at'),
            );
          }),
        SelectFilter::make('role')
          ->relationship('roles', 'title')
          ->options(Role::all()->pluck('title', 'id')),
      ])
      ->actions([
        Tables\Actions\EditAction::make()
          ->slideOver(),
      ])
      ->bulkActions([
        // Tables\Actions\BulkActionGroup::make([
        //     Tables\Actions\DeleteBulkAction::make(),
        // ]),
      ])
      ->recordUrl(false);
  }
}
