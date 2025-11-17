<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Admin')
                ->color('primary')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.resources.admin-resource.list-header', [
            'actions' => $this->getCachedHeaderActions(),
        ]);
    }
}