<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('larabudget/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('profile', function () {
    return Inertia::render('larabudget/Profile');
})->middleware(['auth', 'verified'])->name('profile');

Route::get('spending', function () {
    return Inertia::render('larabudget/Spending');
})->middleware(['auth', 'verified'])->name('spending');

Route::get('friends', function () {
    return Inertia::render('larabudget/Friends');
})->middleware(['auth', 'verified'])->name('friends');

Route::get('collaborate', function () {
    return Inertia::render('larabudget/Collaborate');
})->middleware(['auth', 'verified'])->name('collaborate');

Route::get('/hasone', function () {
    $user = new User();
    $user->name = 'John Doe';
    $user->email = 'john@example.com';
    $user->password = bcrypt('password');

    $profile = new \App\Models\Profile();
    $profile->mainbudget = 6925.5;
    $profile->currentspent = 0.0;
    $user->profile()->save($profile);

    $user = User::find(1);
    $profile = $user->profile;
    dump($profile->attributesToArray());

    $profile = \App\Models\Profile::find(1);
    $user = $profile->user;
    dump($user->attributesToArray);
});

Route::middleware(['auth'])->prefix('larabudget')->group(function () {
    Route::get('/edit', [\App\Http\Controllers\larabudget\UserProfileController::class, 'edit'])
        ->name('larabudget.edit');

    Route::patch('/update', [\App\Http\Controllers\larabudget\UserProfileController::class, 'update'])
        ->name('larabudget.update');

    Route::get('/profile', [\App\Http\Controllers\larabudget\UserProfileController::class, 'show'])
        ->name('larabudget.show');
});

Route::middleware('auth')->group(function () {
    Route::resource('categories', \App\Http\Controllers\larabudget\CategoryController::class)->except(['show']);

    Route::post('/receipts', [\App\Http\Controllers\larabudget\ReceiptController::class, 'store']);
    Route::delete('/receipts/{receipt}', [\App\Http\Controllers\larabudget\ReceiptController::class, 'destroy']);
    Route::post('/categories/{category}/receipts', [ReceiptController::class, 'store']);
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
