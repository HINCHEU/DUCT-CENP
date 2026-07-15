<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Change Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->form([
                    TextInput::make('new_password')
                        ->password()
                        ->required()
                        ->confirmed(),
                    TextInput::make('new_password_confirmation')
                        ->password()
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    $record->update([
                        'password' => Hash::make($data['new_password']),
                    ]);
                }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
