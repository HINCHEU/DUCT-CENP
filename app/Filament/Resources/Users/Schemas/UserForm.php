<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('p_id')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->hidden(fn (string $context): bool => $context === 'edit'),
                Select::make('position')
                    ->options([
                        'Admin' => 'Admin',
                        'Project Manager' => 'Project Manager',
                        'Engineer' => 'Engineer',
                        'Workshop' => 'Workshop',
                    ])
                    ->default(null),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('managedSites')
                    ->label('Managed Sites (Project Manager)')
                    ->relationship('managedSites', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('sites')
                    ->label('Assigned Sites (Engineer/Worker)')
                    ->relationship('sites', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
