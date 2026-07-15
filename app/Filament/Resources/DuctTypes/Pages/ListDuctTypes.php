<?php

namespace App\Filament\Resources\DuctTypes\Pages;

use App\Filament\Resources\DuctTypes\DuctTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDuctTypes extends ListRecords
{
    protected static string $resource = DuctTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
