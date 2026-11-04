<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Webhook\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — WhatsApp Cloud API & Services
|--------------------------------------------------------------------------
*/

Route::prefix('webhook')->group(function () {
    Route::get('/whatsapp', [WhatsAppWebhookController::class, 'verify']);
    Route::post('/whatsapp', [WhatsAppWebhookController::class, 'handle']);
});

Route::post('/simulator/chat', [DashboardController::class, 'simulateChat'])->name('api.simulator.chat');
