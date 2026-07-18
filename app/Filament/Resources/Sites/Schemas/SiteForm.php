<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('project_code')
                    ->label('Project Code')
                    ->required(),
                Select::make('manager_id')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
            ]);
    }
}
