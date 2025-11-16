<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SiswaDashboardStats extends BaseWidget
{
    protected static ?int $sort = 1; // DITAMBAHKAN
    protected int | string | array $columnSpan = 'full'; // DITAMBAHKAN

    protected function getStats(): array
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return [
                Stat::make('Info', 'Siswa tidak ditemukan')
                    ->description('Hubungi admin')
                    ->icon('heroicon-o-information-circle')
                    ->color('warning'),
            ];
        }

        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $hadir = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'hadir')->count();

        $sakit = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'sakit')->count();

        $izin = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'izin')->count();

        $alpha = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'alpha')->count();

        return [
            Stat::make('Hadir', $hadir . ' hari')
                ->description('Kehadiran bulan ini')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            Stat::make('Sakit', $sakit . ' hari')
                ->description('Sakit bulan ini')
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle'),
            Stat::make('Izin', $izin . ' hari')
                ->description('Izin bulan ini')
                ->color('primary')
                ->icon('heroicon-o-document-text'),
            Stat::make('Alpha', $alpha . ' hari')
                ->description('Alpha bulan ini')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'siswa'; // Bukan $user->siswa, tapi $user->role
    }
}