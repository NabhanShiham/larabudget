<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider {
    public function boot(){
        $this->configureRateLimiting();
        parent::boot();
        Route::middleware('web')
            ->group(base_path('routes/larabudget.php'));
    }
}