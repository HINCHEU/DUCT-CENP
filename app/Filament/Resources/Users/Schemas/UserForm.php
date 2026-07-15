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
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
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
                Select::make('sites')
                    ->relationship('sites', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
