<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\SensorDataController;

Route::post('/sensor-data', [SensorDataController::class, 'store']);
Route::get('/sensor-data/latest', [SensorDataController::class, 'latest']);
Route::post('/settings/update', [SensorDataController::class, 'updateSettings']);