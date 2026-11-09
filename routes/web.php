<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderReviewController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/order-review', [OrderReviewController::class, 'index'])->name('order.review');
