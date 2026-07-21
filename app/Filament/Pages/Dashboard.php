<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';  // We render our own heading in the Blade view
    }
}
