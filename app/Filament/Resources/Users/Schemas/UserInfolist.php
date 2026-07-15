<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('p_id'),
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('position')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('managedSites.name')
                    ->label('Managed Sites')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('sites.name')
                    ->label('Assigned Sites')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->placeholder('-'),
            ]);
    }
}
