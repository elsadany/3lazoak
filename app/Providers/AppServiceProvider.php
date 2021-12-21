<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
             \Schema::defaultStringLength(191);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Blade::include('backend.partials.success', 'success');
        Blade::include('backend.partials.errors', 'errors');
        Blade::include('backend.partials.deletejs', 'deletejs');
        Blade::include('backend.partials.breadcrumb', 'breadcrumb');
    }

}
