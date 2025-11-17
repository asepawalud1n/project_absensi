<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Kelola Absen Siswa';
    protected static ?string $modelLabel = 'Kelola Absen Siswa';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'guru']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'guru']);
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isTeacher = $user->role === 'guru';
        $homeroomClass = $isTeacher && $user->teacher ? $user->teacher->homeroomClass : null;
        
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Absensi')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->maxDate(now()),

                        Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->relationship('schoolClass', 'name', function (Builder $query) use ($homeroomClass) {
                                if ($homeroomClass) {
                                    // ✅ Guru hanya bisa pilih kelas yang dia wakilin
                                    $query->where('id', $homeroomClass->id);
                                }
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('student_id', null))
                            ->disabled(fn () => $homeroomClass !== null)
                            ->default(fn () => $homeroomClass?->id),

                        Forms\Components\Select::make('student_id')
                            ->label('Nama Siswa')
                            ->relationship('student', 'name', fn (Builder $query, Forms\Get $get) => 
                                $query->when($get('class_id'), fn ($q, $classId) => $q->where('class_id', $classId))
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Forms\Get $get): bool => !filled($get('class_id')))
                            ->helperText('Pilih kelas terlebih dahulu'),

                        Forms\Components\Select::make('status')
                            ->label('Status Kehadiran')
                            ->options([
                                'hadir' => 'Hadir',
                                'sakit' => 'Sakit',
                                'izin' => 'Izin',
                                'alpha' => 'Alpha',
                            ])
                            ->required()
                            ->native(false)
                            ->default('hadir'),

                        Forms\Components\Textarea::make('note')
                            ->label('Catatan')
                            ->placeholder('Tambahkan catatan jika diperlukan...')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                
                // ✅ Jika guru, filter hanya siswa di kelas yang dia wakilin
                if ($user->role === 'guru') {
                    $teacher = $user->teacher;
                    
                    if ($teacher && $teacher->homeroomClass) {
                        $classId = $teacher->homeroomClass->id;
                        
                        // Filter attendance berdasarkan siswa di kelas tersebut
                        $query->whereHas('student', function ($q) use ($classId) {
                            $q->where('class_id', $classId);
                        });
                    }
                }
                // Admin bisa lihat semua
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

                Tables\Columns\TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),

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
                        '<span style="display: inline-block; min-width: 85px; text-align: center;">' .
                        match ($state) {
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
                    ->toggleable(isToggledHiddenByDefault: false)
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
                    ->relationship('schoolClass', 'name', function (Builder $query) {
                        $user = auth()->user();
                        if ($user->role === 'guru' && $user->teacher && $user->teacher->homeroomClass) {
                            $query->where('id', $user->teacher->homeroomClass->id);
                        }
                    }),

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
                Tables\Actions\CreateAction::make()
                    ->label('Buat Kelola Absen Siswa')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
