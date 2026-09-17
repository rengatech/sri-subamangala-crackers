<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;

class OrderOverviewcopy extends BaseWidget
{
    protected function getStats(): array
    {
        $year = session('admin_selected_year', now()->year);

        return [
            Stat::make('Total Orders', Order::whereYear('created_at', $year)->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

            Stat::make('Incoming Orders', Order::whereYear('created_at', $year)->whereIn('status', ['placed'])->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

            Stat::make('Confirm Orders', Order::whereYear('created_at', $year)->whereIn('status', ['payment_received'])->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

            Stat::make('Cancelled Orders', Order::whereYear('created_at', $year)->whereIn('status', ['cancelled'])->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

        ];
    }
}
