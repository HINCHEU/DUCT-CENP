<?php

namespace App\Filament\Resources\DuctTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DuctTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('formula_key')
                    ->required(),
                Textarea::make('config')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
