<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\OccupancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('booking', BookingController::class)
    ->only([
        'store',
        'update',
    ]);

Route::get('daily-occupancy-rates/{day}', [OccupancyController::class, 'index'])
    ->name('daily.booking');

Route::get('monthly-occupancy-rates/{month}', [OccupancyController::class, 'indexTwo'])
    ->name('monthly.booking');
