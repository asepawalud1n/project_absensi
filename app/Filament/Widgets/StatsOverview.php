<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\SchoolClass;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Guru', Teacher::count())
                ->description('Jumlah Wali Kelas Terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Siswa', Student::count())
                ->description('Jumlah seluruh siswa aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Total Kelas', SchoolClass::count())
                ->description('Jumlah kelas yang terdaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'admin';
    }

}
