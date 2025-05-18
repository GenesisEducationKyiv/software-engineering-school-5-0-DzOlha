<?php

use App\Interfaces\Api\Controllers\SubscriptionController;
use App\Interfaces\Api\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Weather endpoints
Route::get('/weather', [WeatherController::class, 'getCurrentWeather']);

// Subscription endpoints
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::get('/confirm/{token}', [SubscriptionController::class, 'confirm']);
Route::get('/unsubscribe/{token}', [SubscriptionController::class, 'unsubscribe']);
