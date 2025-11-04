<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\VoteController;

Route::get('/', function () {
    return view('home');
});

// Blog routes (public)
Route::get('/blog', [NewsController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [NewsController::class, 'blogShow'])->name('blog.show');

// Admin routes (protected by custom session check in controller)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('news', NewsController::class);
    
    // Vote management routes
    Route::get('vote', [VoteController::class, 'adminIndex'])->name('vote.index');
    Route::get('vote/create', [VoteController::class, 'create'])->name('vote.create');
    Route::post('vote', [VoteController::class, 'store'])->name('vote.store');
    Route::get('vote/{vote}/edit', [VoteController::class, 'edit'])->name('vote.edit');
    Route::put('vote/{vote}', [VoteController::class, 'update'])->name('vote.update');
    Route::delete('vote/{vote}', [VoteController::class, 'destroy'])->name('vote.destroy');
});

Route::get('/account', [AccountController::class, 'show']);
Route::get('/account', [AccountController::class, 'show'])->name('login'); // Define login route
Route::post('/account', [AccountController::class, 'login']);
Route::post('/account/register', [AccountController::class, 'register']);
Route::get('/account/profile', [AccountController::class, 'profile']);
Route::get('/account/game-accounts', [AccountController::class, 'gameAccounts']);
Route::post('/account/game-accounts', [AccountController::class, 'createGameAccount']);
Route::post('/account/game-accounts/change-password', [AccountController::class, 'changeGameAccountPassword']);
Route::get('/account/ygg-points', [AccountController::class, 'yggPoints']);
Route::get('/account/votes', [VoteController::class, 'index'])->name('vote.index');
Route::post('/account/votes/{id}', [VoteController::class, 'vote'])->name('vote.submit');
Route::get('/account/orders', [AccountController::class, 'orders']);
Route::post('/logout', [AccountController::class, 'logout']);

Route::get('/download', function () {
    return view('download');
});

Route::get('/us', function () {
    return view('us');
});
