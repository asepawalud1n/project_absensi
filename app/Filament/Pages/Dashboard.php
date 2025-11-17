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

    public function getTitle(): string
    {
        return 'Beranda';
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getColumns(): int | string | array
    {
        return 12;
    }

    // HAPUS method getWidgets() - biar otomatis dari canView()
}