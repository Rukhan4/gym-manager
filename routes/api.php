<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update']);
Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy']);
