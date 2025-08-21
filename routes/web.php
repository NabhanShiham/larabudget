<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Larabudget;



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

Route::middleware(['auth'])->prefix('category')->group(function () {
    Route::post('/create', [\App\Http\Controllers\larabudget\CategoryController::class, 'store'])
        ->name('category.store');
    Route::get('/show', [\App\Http\Controllers\larabudget\CategoryController::class, 'show'])
        ->name('categories.show');
    Route::delete('{category}', [\App\Http\Controllers\larabudget\CategoryController::class, 'destroy'])
        ->name('category.delete');
    Route::get('{category}/purchases', [CategoryController::class, 'showCategoryPurchases'])
    ->name('categories.purchases');
});

Route::middleware(['auth'])->prefix('purchases')->group(function (){
    Route::post('/store', [\App\Http\Controllers\larabudget\PurchaseController::class, 'store'])
        ->name('purchases.store');
    Route::get('', [\App\Http\Controllers\larabudget\PurchaseController::class, 'showCategoryPurchases'])
        ->name('purchases.index');

});

Route::get('/get-receipt/{id}', [\App\Http\Controllers\larabudget\PurchaseController::class, 'viewReceipt'])
    ->name('purchases.receipt.view');

Route::get('/search', [\App\Http\Controllers\larabudget\FriendController::class, 'search'])
    ->name('search.friends');

Route::get('/get-user', [\App\Http\Controllers\larabudget\FriendController::class,'getCurrentUser'])
    ->name('current.user');

Route::middleware(['auth'])->group(function () {
    Route::post('/friend-request/send', [\App\Http\Controllers\larabudget\FriendController::class, 'sendFriendRequest'])
        ->name('friend.request.send');
    Route::post('/friend-request/respond', [\App\Http\Controllers\larabudget\FriendController::class, 'respondToFriendRequest'])
        ->name('friend.request.respond');
    Route::get('/friend-requests/pending', [\App\Http\Controllers\larabudget\FriendController::class, 'pendingRequests'])
        ->name('friend.requests.pending');
    Route::get('/friends-list', [\App\Http\Controllers\larabudget\FriendController::class, 'listFriends'])
        ->name('friends.list');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});




require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
