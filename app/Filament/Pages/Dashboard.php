<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Beranda';
    protected static ?string $title = 'Beranda';

    public function getHeading(): string
    {
        return 'Beranda';
    }

    public function getColumns(): int | string | array
    {
        return 12;
    }

    // HAPUS method getWidgets() - biar otomatis dari canView()
}