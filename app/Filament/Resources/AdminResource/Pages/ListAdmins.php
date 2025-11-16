<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Admin')
                ->icon('heroicon-o-plus-circle')
                ->color('primary'),
        ];
    }

    public function getTitle(): string
    {
        return 'Kelola Admin';
    }

    public function getHeading(): string
    {
        return 'Daftar Admin';
    }
}