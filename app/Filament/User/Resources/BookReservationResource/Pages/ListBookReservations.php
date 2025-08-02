<?php

namespace App\Filament\User\Resources\BookReservationResource\Pages;

use App\Filament\User\Resources\BookReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListBookReservations extends ListRecords
{
    protected static string $resource = BookReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('user_id', Auth::id());
    }
}