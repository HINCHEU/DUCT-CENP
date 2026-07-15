<?php

namespace App\Filament\Resources\DuctTypes\Pages;

use App\Filament\Resources\DuctTypes\DuctTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDuctType extends ViewRecord
{
    protected static string $resource = DuctTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
