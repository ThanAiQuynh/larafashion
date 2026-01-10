<?php

use App\Http\Controllers\Api\ProductFeedController;
use App\Http\Controllers\Api\TudongchatWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Product Feed for Tudongchat Knowledge Base
Route::get('/products/feed', [ProductFeedController::class, 'index'])
    ->name('api.products.feed');

// Tudongchat Webhook
Route::prefix('webhook')->group(function () {
    Route::get('/tudongchat/ping', [TudongchatWebhookController::class, 'ping'])
        ->name('api.webhook.tudongchat.ping');
    
    Route::post('/tudongchat', [TudongchatWebhookController::class, 'handle'])
        ->name('api.webhook.tudongchat');
});
