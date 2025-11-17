<?php

namespace App\Filament\Resources;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Filament\Notifications\Notification;
use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Models\SchoolClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Data Siswa';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return in_array($user?->role, ['admin', 'guru']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'admin'; // Hanya admin yang bisa create
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'admin'; // Hanya admin yang bisa edit
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'admin'; // Hanya admin yang bisa delete
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'admin'; // Hanya admin yang bisa bulk delete
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        // Guru hanya dapat siswa di kelas yang ia walikan
        if ($user && $user->role === 'guru' && $user->teacher && $user->teacher->homeroomClass) {
            return parent::getEloquentQuery()->where('class_id', $user->teacher->homeroomClass->id);
        }
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nis')
                ->label('NIS')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(15),
            Forms\Components\TextInput::make('nisn')
                ->label('NISN')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(15),
            Forms\Components\TextInput::make('name')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('gender')
                ->label('Jenis Kelamin')
                ->options([
                    'L' => 'Laki-laki',
                    'P' => 'Perempuan',
                ])
                ->required(),
            Forms\Components\Select::make('class_id')
                ->label('Kelas')
                ->options(SchoolClass::all()->pluck('name', 'id'))
                ->searchable()
                ->nullable()
                ->placeholder('Pilih Kelas'),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->unique(ignoreRecord: true)
                ->nullable()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NIS disalin!')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NISN disalin!')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('JK')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'L' => 'info',
                        'P' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): HtmlString =>
                        new HtmlString(
                            '<span style="display: inline-block; min-width: 100px; text-align: center;">'
                            . match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            }
                            . '</span>'
                        )
                    )
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn (?string $state): HtmlString =>
                        new HtmlString(
                            '<span style="display: inline-block; min-width: 85px; text-align: center;">'
                            . ($state ?? 'Belum ada kelas')
                            . '</span>'
                        )
                    )
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->options(SchoolClass::all()->pluck('name', 'id')),
            ])
            ->headerActions(
                // Hanya tampilkan header actions untuk admin
                $isAdmin ? [
                    Tables\Actions\CreateAction::make()
                        ->label('Tambah Siswa')
                        ->icon('heroicon-o-plus')
                        ->color('primary'),
                    Tables\Actions\Action::make('importExcel')
                        ->label('Import Excel')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('success')
                        ->form([
                            Forms\Components\FileUpload::make('file')
                                ->label('File Excel (.xlsx, .xls)')
                                ->acceptedFileTypes([
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-excel'
                                ])
                                ->required()
                                ->helperText('Format kolom: NIS | NISN | Nama | JK | Kelas | Email'),
                        ])
                        ->action(function (array $data) {
                            $filePath = storage_path('app/public/' . $data['file']);
                            try {
                                Excel::import(new StudentsImport, $filePath);
                                Notification::make()
                                    ->title('Import Berhasil!')
                                    ->body('Data siswa berhasil diimport dari Excel.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Import Gagal!')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ] : [] // Kosong untuk guru
            )
            ->actions(
                // Hanya tampilkan actions untuk admin
                $isAdmin ? [
                    Tables\Actions\EditAction::make()->label('Ubah'),
                    Tables\Actions\DeleteAction::make()->label('Hapus'),
                ] : [] // Kosong untuk guru (view only)
            )
            ->bulkActions(
                // Hanya tampilkan bulk actions untuk admin
                $isAdmin ? [
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                        ExportBulkAction::make()->label('Export Excel'),
                    ]),
                ] : [] // Kosong untuk guru
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}