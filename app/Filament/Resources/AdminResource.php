<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Kelola Admin';
    protected static ?string $modelLabel = 'Admin';
    protected static ?string $pluralModelLabel = 'Admin';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Admin')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->helperText(fn (string $context): string => 
                                $context === 'edit' 
                                    ? 'Kosongkan jika tidak ingin mengubah password' 
                                    : 'Minimal 8 karakter'
                            ),
                        
                        Forms\Components\Hidden::make('role')
                            ->default('admin'),
                        
                        Forms\Components\Toggle::make('email_verified_at')
                            ->label('Email Terverifikasi')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->dehydrateStateUsing(fn ($state) => $state ? now() : null)
                            ->afterStateHydrated(fn ($component, $state) => 
                                $component->state(filled($state))
                            )
                            ->helperText('Aktifkan jika email sudah terverifikasi'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Admin')
                    ->icon('heroicon-o-plus'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Admin')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email disalin!'),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar Sejak')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Status Verifikasi Email')
                    ->nullable()
                    ->trueLabel('Terverifikasi')
                    ->falseLabel('Belum Terverifikasi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->before(function (User $record) {
                        if ($record->id === auth()->id()) {
                            throw new \Exception('Anda tidak dapat menghapus akun Anda sendiri!');
                        }
                        
                        $adminCount = User::where('role', 'admin')->count();
                        if ($adminCount <= 1) {
                            throw new \Exception('Tidak dapat menghapus admin terakhir! Minimal harus ada 1 admin.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            if ($records->contains('id', auth()->id())) {
                                throw new \Exception('Anda tidak dapat menghapus akun Anda sendiri!');
                            }
                            
                            $adminCount = User::where('role', 'admin')->count();
                            if ($adminCount - $records->count() < 1) {
                                throw new \Exception('Tidak dapat menghapus semua admin! Minimal harus ada 1 admin.');
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}