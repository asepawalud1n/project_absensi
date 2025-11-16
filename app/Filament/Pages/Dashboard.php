<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getColumns(): int | string | array
    {
        return 12;
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    // HAPUS method getWidgets() - biar otomatis dari canView()
}