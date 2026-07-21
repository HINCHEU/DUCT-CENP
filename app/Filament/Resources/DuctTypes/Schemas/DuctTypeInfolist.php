<?php

namespace App\Filament\Resources\DuctTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DuctTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('formula_key'),
                TextEntry::make('config')
                    ->formatStateUsing(fn ($state) => is_array($state) || is_object($state) ? new \Illuminate\Support\HtmlString('<pre style="white-space: pre-wrap; font-size: 13px; background: rgba(0,0,0,0.05); padding: 10px; border-radius: 6px;">' . json_encode($state, JSON_PRETTY_PRINT) . '</pre>') : $state)
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
