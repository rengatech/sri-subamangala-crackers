<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;

class OrderOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $year = session('admin_selected_year', now()->year);

        return [
            Stat::make('Total Orders', Order::whereYear('created_at', $year)->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

            Stat::make('Confirm Orders', Order::whereYear('created_at', $year)->whereIn('status', ['payment_received'])->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

            Stat::make('Dispatched Orders', Order::whereYear('created_at', $year)->whereIn('status', ['dispatched'])->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

            Stat::make('packing Orders', Order::whereYear('created_at', $year)->whereIn('status', ['packing'])->count())
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),

            Stat::make('Total Sales Amount', function () use ($year) {
                $totalSales = Order::whereYear('created_at', $year)->sum('sub_total');
                return '₹ ' . number_format($totalSales, 0);
            })
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('primary'),
        ];
    }
}
