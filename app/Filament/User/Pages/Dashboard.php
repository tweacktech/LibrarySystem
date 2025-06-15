<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\AvailableBooksForReservation;
use App\Filament\User\Widgets\BorrowedBooksOverview;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament-panels::pages.dashboard';
    protected static ?string $title = 'Dashboard';

  
    protected function getColumns(): int | array
    {
        return 3;
    }

    protected function getVisibleWidgets(): array
    {
        return [
            BorrowedBooksOverview::class,
            AvailableBooksForReservation::class,
        ];
    }
}
