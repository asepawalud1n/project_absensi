<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyAttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Kehadiran Bulan Ini';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $teacher = auth()->user()?->teacher;
        $class = $teacher?->homeroomClass;
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        if ($class) {
            $attendanceData = Attendance::select(
                    DB::raw('DATE(date) as day'),
                    DB::raw('COUNT(*) as total_records')
                )
                ->where('class_id', $class->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->groupBy('day')
                ->orderBy('day')
                ->get();
        } else {
            $attendanceData = collect();
        }

        $labels = [];
        $dataPoints = [];
        $daysInMonth = Carbon::now()->daysInMonth;
        $currentDay = Carbon::now()->startOfMonth();

        for ($i = 0; $i < $daysInMonth; $i++) {
            $dateString = $currentDay->toDateString();
            $labels[] = $currentDay->format('d');
            $record = $attendanceData->firstWhere('day', $dateString);
            $dataPoints[] = $record ? $record->total_records : 0;
            $currentDay->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Absensi',
                    'data' => $dataPoints,
                    'borderColor' => '#2563EB',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
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
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
                'x' => [
                    'ticks' => [
                        'maxRotation' => 0,
                        'autoSkip' => true,
                        'maxTicksLimit' => 10,
                    ],
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'guru';
    }
}