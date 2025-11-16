<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SiswaAttendanceCompositionChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Kehadiran Bulan Ini';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $student = auth()->user()?->student;
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        
        if ($student) {
            $statusCounts = Attendance::where('student_id', $student->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->select('status')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
        } else {
            $statusCounts = collect();
        }

        $labels = ['Hadir', 'Sakit', 'Izin', 'Alpha'];
        $data = [
            $statusCounts['hadir'] ?? 0,
            $statusCounts['sakit'] ?? 0,
            $statusCounts['izin'] ?? 0,
            $statusCounts['alpha'] ?? 0,
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Status Kehadiran',
                    'data' => $data,
                    'backgroundColor' => [
                        '#10B981',
                        '#F59E0B',
                        '#3B82F6',
                        '#EF4444',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => true,
            'aspectRatio' => 2,
            'layout' => [
                'padding' => 0
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'siswa';
    }
}