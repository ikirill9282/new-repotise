<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Colors\Color;
use Filament\Infolists\Infolist;

class RolesPermissions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.roles-permissions';

    protected static ?string $navigationGroup = 'users';

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?int $navigationSort = 2;

    public function rolesTable(Table|Infolist $table): Table|Infolist
    {
        if ($table instanceof Infolist) {
            return $table;
        }
        
        return $table
            ->query(Role::query())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('Role Name')
                    ->searchable()
                    ->sortable()
                    ->color(Color::Sky),
                
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('name');
    }

    public function permissionsTable(Table|Infolist $table): Table|Infolist
    {
        if ($table instanceof Infolist) {
            return $table;
        }
        
        return $table
            ->query(Permission::query())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('Permission Name')
                    ->searchable()
                    ->sortable()
                    ->color(Color::Sky),
                
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('name');
    }
}


