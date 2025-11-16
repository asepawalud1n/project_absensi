<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolClassResource\Pages;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\Major;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Data Kelas';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return in_array($user?->role, ['admin', 'guru']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,  // 1 kolom di mobile
                    'sm' => 2,       // 2 kolom di tablet+
                ])->schema([
                    Forms\Components\Select::make('grade')
                        ->label('Tingkat')
                        ->options([
                            'X' => 'Kelas X',
                            'XI' => 'Kelas XI',
                            'XII' => 'Kelas XII',
                        ])
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state, callable $get) {
                            $majorId = $get('major_id');
                            if ($state && $majorId) {
                                $major = Major::find($majorId);
                                $set('name', $state . ' ' . $major->code);
                            }
                        })
                        ->columnSpan(1),  // Span penuh di mobile, setengah di sm+

                    Forms\Components\Select::make('major_id')
                        ->label('Jurusan')
                        ->options(Major::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state, callable $get) {
                            $grade = $get('grade');
                            if ($state && $grade) {
                                $major = Major::find($state);
                                $set('name', $grade . ' ' . $major->code);
                            }
                        })
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama Kelas')
                        ->placeholder('Contoh: X TJKT 1')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Nama otomatis terisi, bisa ditambah angka. Contoh: X TJKT 1')
                        ->columnSpanFull(),  // Selalu full width

                    Forms\Components\Select::make('homeroom_teacher_id')
                        ->label('Wali Kelas')
                        ->options(Teacher::all()->pluck('name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->placeholder('Pilih Wali Kelas')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Hilangkan containerAttributes karena tidak ada; gunakan CSS custom di atas
            ->columns([
                Tables\Columns\TextColumn::make('grade')
                    ->label('Tingkat')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(
                        fn (string $state): HtmlString =>
                            new HtmlString('<span style="display: inline-block; min-width: 40px; max-width: 100%; text-align: center; padding: 2px 4px;">' . $state . '</span>')  // Kurangi min-width untuk mobile
                    )
                    ->sortable()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-16 sm:w-auto']),  // Lebar adaptif

                Tables\Columns\TextColumn::make('major.code')
                    ->label('Jurusan')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(
                        fn (string $state): HtmlString =>
                            new HtmlString('<span style="display: inline-block; min-width: 60px; max-width: 100%; text-align: center; padding: 2px 4px;">' . $state . '</span>')  // Kurangi min-width
                    )
                    ->searchable()
                    ->sortable()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-20 sm:w-auto']),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->extraAttributes(['class' => 'min-w-32']),  // Pastikan nama kelas selalu visible

                // Sembunyikan kolom wali kelas di layar kecil (sm+)
                Tables\Columns\TextColumn::make('homeroomTeacher.name')
                    ->label('Wali Kelas')
                    ->searchable()
                    ->sortable()
                    ->default('Belum ada')
                    ->extraAttributes(['class' => 'hidden sm:table-cell'])
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(
                        fn (string $state): HtmlString =>
                            new HtmlString('<span style="display: inline-block; min-width: 40px; max-width: 100%; text-align: center; padding: 2px 4px;">' . $state . '</span>')
                    )
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-16 sm:w-auto']),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->extraAttributes(['class' => 'hidden lg:table-cell']),  // Sembunyikan di lg+ ke bawah
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade')
                    ->label('Tingkat')
                    ->options([
                        'X' => 'Kelas X',
                        'XI' => 'Kelas XI',
                        'XII' => 'Kelas XII',
                    ]),

                Tables\Filters\SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::all()->pluck('name', 'id')),
            ])
            ->headerActions([
                // TOMBOL BUAT KELAS (DALAM TABEL - BIRU)
                Tables\Actions\CreateAction::make()
                    ->label('Buat Kelas')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchoolClasses::route('/'),
            'create' => Pages\CreateSchoolClass::route('/create'),
            'edit' => Pages\EditSchoolClass::route('/{record}/edit'),
        ];
    }
}
