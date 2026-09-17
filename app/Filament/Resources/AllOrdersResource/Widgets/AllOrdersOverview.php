<?php

namespace App\Filament\Resources\AllOrdersResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;

class AllOrdersOverview extends BaseWidget
{
    protected function getCards(): array
    {
            $year = session('admin_selected_year', now()->year);

            return [
                Card::make('Total Orders', Order::whereYear('created_at', $year)->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

                Card::make('Confirmed Orders', Order::whereYear('created_at', $year)->whereIn('status', ['payment_received'])->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

                Card::make('Packing Orders', Order::whereYear('created_at', $year)->whereIn('status', ['packing'])->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

                Card::make('Cancel Order', Order::whereYear('created_at', $year)->whereIn('status', ['cancelled'])->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
            ];
    }
}
