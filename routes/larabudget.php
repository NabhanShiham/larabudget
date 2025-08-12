<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Larabudget\UserProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('web')->group(function () {
    Route::redirect('larabudget', '/larabudget/profile');

    Route::get('larabudget/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('larabudget/profile', [UserProfileController::class, 'update'])->name('laraprofile.update');

});