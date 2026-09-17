<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasYearFilter
{
    protected static function applyYearFilter(Builder $query): Builder
    {
        $year = session('admin_selected_year', now()->year);
        return $query->whereYear('created_at', $year);
    }

    protected static function getSelectedYear(): int
    {
        return (int) session('admin_selected_year', now()->year);
    }
}
