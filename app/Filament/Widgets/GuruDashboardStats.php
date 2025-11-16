<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class GuruDashboardStats extends BaseWidget
{
    protected static ?int $sort = 1; // DITAMBAHKAN
    protected int | string | array $columnSpan = 'full'; // DITAMBAHKAN

    protected function getStats(): array
    {
        $teacher = Auth::user()?->teacher;

        if (!$teacher || !$teacher->homeroomClass) {
            return [
                Stat::make('Info', 'Anda belum menjadi wali kelas')
                    ->description('Silahkan hubungi admin')
                    ->icon('heroicon-o-information-circle')
                    ->color('warning'),
            ];
        }

        $class = $teacher->homeroomClass;
        $classId = $class->id;
        $totalStudents = $class->students()->count();

        $today = now()->toDateString();

        // Jumlah hadir hari ini
        $hadirCount = Attendance::where('date', $today)
            ->where('status', 'hadir')
            ->whereHas('student', function($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->count();

        // Jumlah tidak hadir hari ini (sakit, izin, alpha)
        $tidakHadirCount = Attendance::where('date', $today)
            ->whereIn('status', ['sakit', 'izin', 'alpha'])
            ->whereHas('student', function($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->count();

        return [
            Stat::make('Kelas yang Diampu', $class->name)
                ->description($class->grade . ' - ' . $class->major->name)
                ->icon('heroicon-o-academic-cap')
                ->color('success'),

            Stat::make('Jumlah Siswa', $totalStudents . ' siswa')
                ->description('Total siswa di kelas')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Kehadiran Hari Ini', $hadirCount . ' siswa')
                ->description($hadirCount > 0 ? 'Siswa hadir hari ini' : 'Belum ada absensi')
                ->icon('heroicon-o-check-circle')
                ->color('info'),

            Stat::make('Tidak Hadir', $tidakHadirCount . ' siswa')
                ->description('Sakit, Izin, Alpha')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'guru';
    }
}