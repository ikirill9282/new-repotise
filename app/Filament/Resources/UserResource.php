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
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;


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
    
    return $table->columns([
        TextColumn::make('id')
          ->label('#ID')
          ->searchable()
          ,
        TextColumn::make('username')
          ->searchable(['name', 'username', 'email'])
          ->label('User info')
          ->view('filament.tables.columns.username')
          ,
        TextColumn::make('Stripe verified')
          ->formatStateUsing(function($record) {
            return $record->verified ? 'Yes' : 'No';
          })
          ,
        TextColumn::make('email_verified_at')
          ->label('Email Verified')
          ->dateTime()
          ->icon('heroicon-o-clock'),
        TextColumn::make('roles.title'),
        TextColumn::make('stripe_id')
          ->searchable()
          ,
        TextColumn::make('created_at'),
        TextColumn::make('updated_at'),
      ])
      ->filters([
        Filter::make('email_verified_at')
          ->label('Email verify')
          ->query(function ($query, $data) {
            $query->when(
              (isset($data['isActive']) && $data['isActive']),
              fn($subquery) => $subquery->whereNotNull('email_verified_at'),
            );
          })
          ,
        Filter::make('verified')
          ->label('Stripe verify')
          ->query(function($query, $data) {
            $query->when(
              (isset($data['isActive']) && $data['isActive']),
              fn($subquery) => $subquery->where('verified', 1)
            );
          })
          ,
        Filter::make('2fa')
          ->label('Filter by 2FA Status:')
          ->query(function($query, $data) {
            $query->when(
              (isset($data['isActive']) && $data['isActive']),
              fn($subquery) => $subquery->where('2fa', 1)
            );
          })
          ,
        SelectFilter::make('roles')
          ->label('Filter by Role')
          ->relationship('roles', 'title')
          ->options(Role::all()->pluck('title', 'id'))
        ,
        SelectFilter::make('referal')
          ->label('Filter by Referral Status')
          ->options([
            1 => 'Referal',
            2 => 'Referer',
          ])
        ,
        DateRangeFilter::make('created_at')
          ->label('Filter by Registration Date')
          ->query(function ($query, array $data) {
            if (!empty($data['created_at'])) {
              $arr = explode('-', $data['created_at']);
              $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
              
              return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
            }
          })
          ,
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
