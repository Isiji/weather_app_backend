<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::middleware('api')->group(function () {
    Route::get('/weather', [WeatherController::class, 'getWeather']);
});
