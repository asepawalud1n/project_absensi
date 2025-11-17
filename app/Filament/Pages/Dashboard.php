<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Beranda';
    protected static ?string $title = 'Beranda';

    // Override getHeading() - Method utama untuk heading halaman
    public function getHeading(): string
    {
        return 'Beranda';
    }

    // Override getTitle() - Untuk browser tab title
    public function getTitle(): string
    {
        return 'Beranda';
    }

    // Override getNavigationLabel() - Untuk sidebar
    public static function getNavigationLabel(): string
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