<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendancesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Status',
            'Catatan',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date->format('d/m/Y'),
            $attendance->student->name ?? '-',
            $attendance->student->nis ?? '-',
            $attendance->schoolClass->name ?? '-',
            match($attendance->status) {
                'hadir' => 'Hadir',
                'sakit' => 'Sakit',
                'izin' => 'Izin',
                'alpha' => 'Alpha',
                default => '-'
            },
            $attendance->note ?? '-',
        ];
    }
}
