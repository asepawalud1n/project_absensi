<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Custom view untuk force heading
    protected static string $view = 'filament.pages.dashboard';

    // Static properties untuk navigation dan title
    protected static ?string $navigationLabel = 'Beranda';
    protected static ?string $title = 'Beranda';

    // Instance property untuk heading (PRIORITAS TERTINGGI!)
    protected ?string $heading = 'Beranda';

    // Override getHeading() - Method untuk heading halaman
    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Beranda';
    }

    // Override getTitle() - Untuk browser tab title
    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Beranda';
    }

    // Override getNavigationLabel() - Untuk sidebar
    public static function getNavigationLabel(): string
    {
        return 'Beranda';
    }

    // Mount hook untuk memastikan heading di-set saat component di-load
    public function mount(): void
    {
        $this->heading = 'Beranda';
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