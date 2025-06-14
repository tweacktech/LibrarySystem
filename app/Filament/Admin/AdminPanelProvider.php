<?php

namespace App\Filament\Admin;

use Filament\Panel;
use App\Filament\Admin\Resources\PaymentResource;
use App\Filament\Admin\Widgets\PaymentWidget;
use App\Filament\Admin\Widgets\PaymentChartWidget;
use App\Filament\Admin\Widgets\LatestPaymentsWidget;

class AdminPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->resources([
                PaymentResource::class,
            ])
            ->widgets([
                PaymentWidget::class,
                PaymentChartWidget::class,
                LatestPaymentsWidget::class,
            ]);
    }
}