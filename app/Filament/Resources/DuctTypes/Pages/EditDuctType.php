<?php

namespace App\Filament\Resources\DuctTypes\Pages;

use App\Filament\Resources\DuctTypes\DuctTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDuctType extends EditRecord
{
    protected static string $resource = DuctTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
