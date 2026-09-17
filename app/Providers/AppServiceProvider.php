<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Performance: prevent N+1 queries in non-production
        Model::preventLazyLoading(! app()->isProduction());

        // Performance: disable destructive commands in production
        DB::prohibitDestructiveCommands(app()->isProduction());

        Filament::registerNavigationGroups([
            'Orders',
            'Categories',
            'Products',
            'Customers',
            'Admin',
        ]);
    }
}
