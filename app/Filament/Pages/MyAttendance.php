<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Attendance;

class MyAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.pages.my-attendance';
    protected static ?string $title = 'Riwayat Absensi Saya';

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'siswa';
    }

    public function table(Table $table): Table
    {
        $student = auth()->user()->student;

        return $table
            ->query(
                Attendance::query()
                    ->where('student_id', $student->id)
                    ->orderBy('date', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('day_name')
                    ->label('Hari')
                    ->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->date)->isoFormat('dddd')),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'hadir',
                        'warning' => 'sakit',
                        'primary' => 'izin',
                        'danger'  => 'alpha',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('note')
                    ->label('Keterangan')
                    ->default('-'),
            ])
            ->defaultSort('date', 'desc')
            ->paginated([10, 25, 50]);
    }
}
