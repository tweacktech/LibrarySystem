<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Payment Trends';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Daily Revenue (₦)',
                    'data' => $data->pluck('total_amount')->toArray(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => '#10B981',
                ],
                [
                    'label' => 'Number of Payments',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => '#6366F1',
                    'backgroundColor' => '#6366F1',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}