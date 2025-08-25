<?php

namespace App\Filament\User\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BorrowedBooksOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        $totalBorrowed = Transaction::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->count();

        $totalReturned = Transaction::where('user_id', $user->id)
            ->where('status', 'returned')
            ->count();

        $totalDelayed = Transaction::where('user_id', $user->id)
            ->where('status', 'delayed')
            ->count();

        return [
            Stat::make('Currently Borrowed', $totalBorrowed)
                ->description('Books you have borrowed')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('Returned Books', $totalReturned)
                ->description('Books you have returned')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make('Delayed Returns', $totalDelayed)
                ->description('Books with delayed returns')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];
    }
}
