<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider {
    public function boot(){
        parent::boot();
        Route::middleware('api')
            ->group(base_path('routes/larabudget.php'));
    }
}