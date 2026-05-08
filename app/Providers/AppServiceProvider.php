<?php

namespace App\Providers;

use App\Models\Booking;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        /**
         * Route Model Binding untuk Booking
         * 
         * Custom binding untuk Booking model dengan SoftDeletes support.
         * Memungkinkan route binding menemukan booking bahkan yang soft-deleted.
         * Jika booking tidak ditemukan (termasuk soft-deleted), akan abort 404.
         */
        Route::bind('booking', function ($id) {
            return Booking::withTrashed()->find($id);
        });
    }
}
