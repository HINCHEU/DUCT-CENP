<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required(),
                TextInput::make('site_id')
                    ->required()
                    ->numeric(),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                TextInput::make('approved_by')
                    ->numeric()
                    ->default(null),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('priority')
                    ->required()
                    ->default('normal'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                DatePicker::make('requested_delivery_date'),
                Textarea::make('rejection_reason')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('revision_of')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
