<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReviewController;

Route::get('/', function () {
    return redirect('/subscriptions');
});

// Inertia page
Route::get('/subscriptions', [SubscriptionController::class, 'index']);

// Members CRUD
Route::get('/members/list', [MemberController::class, 'list']);
Route::post('/members', [MemberController::class, 'store']);
Route::put('/members/{member}', [MemberController::class, 'update']);
Route::delete('/members/{member}', [MemberController::class, 'destroy']);

// Reviews 

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
