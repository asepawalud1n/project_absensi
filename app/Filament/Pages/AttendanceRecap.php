<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Exports\AttendancesExport;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceRecap extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.pages.attendance-recap';
    protected static ?string $navigationLabel = 'Rekap Absen Siswa';
    protected static ?string $title = 'Rekap Absen Siswa';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'guru']);
    }

    public function table(Table $table): Table 
    {
        return $table
            ->query(function () {
                $query = Attendance::query();
                $user = auth()->user();
                if ($user && $user->role === 'guru') {
                    $teacher = $user->teacher;
                    if ($teacher && $teacher->homeroomClass) {
                        $classId = $teacher->homeroomClass->id;
                        $query->whereHas('student', function ($q) use ($classId) {
                            $q->where('class_id', $classId);
                        });
                    } else {
                        // Guru yang bukan wali kelas --> data kosong
                        $query->whereNull('id');
                    }
                }
                // Admin: tampilkan semua
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString(
                        '<span style="display: inline-block; min-width: 85px; text-align: center;">' . $state . '</span>'
                    )),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'sakit' => 'warning',
                        'izin' => 'info',
                        'alpha' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString(
                        '<span style="display: inline-block; min-width: 85px; text-align: center;">' . match ($state) {
                            'hadir' => 'Hadir',
                            'sakit' => 'Sakit',
                            'izin' => 'Izin',
                            'alpha' => 'Alpha',
                        } . '</span>'
                    ))
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('-'),
                Tables\Columns\TextColumn::make('recordedBy.name')
                    ->label('Dicatat Oleh')
                    ->default('Sistem')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'alpha' => 'Alpha',
                    ]),
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->options(SchoolClass::all()->pluck('name', 'id')),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari_tanggal'], fn (Builder $q) => $q->whereDate('date', '>=', $data['dari_tanggal']))
                        ->when($data['sampai_tanggal'], fn (Builder $q) => $q->whereDate('date', '<=', $data['sampai_tanggal']))
                    ),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $query = $this->getFilteredTableQuery();
                        return Excel::download(
                            new AttendancesExport($query),
                            'rekap-absensi-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }),
            ]);
    }
}
