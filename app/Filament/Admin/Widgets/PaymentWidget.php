<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPayments = Payment::count();
        $totalAmount = Payment::where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->count();

        $recentPayments = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->sum('amount');

        return [
            Stat::make('Total Payments', number_format($totalPayments))
                ->description('All time payments')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Total Revenue', '₦' . number_format($totalAmount, 2))
                ->description('Completed payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pending Payments', number_format($pendingPayments))
                ->description('Awaiting completion')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Recent Revenue', '₦' . number_format($recentPayments, 2))
                ->description('Last 7 days')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
        ];
    }
}