<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\SensorDataController;
use App\Models\SensorData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Telemetry endpoints for ESP32
Route::post('/api/sensor-data', [SensorDataController::class, 'store'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/api/data', [SensorDataController::class, 'store'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// JSON endpoint for real-time dashboard updates
Route::get('/api/latest-data', [SensorDataController::class, 'latest']);

// Standalone Telegram Test Route
Route::get('/test-telegram', function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $chatId = env('TELEGRAM_CHAT_ID');

    $response = Http::withoutVerifying() // Bypasses local SSL certificate checks
        ->timeout(10)                    // Prevents hanging indefinitely
        ->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "🎉 *Success!* Telegram notifications are working from Laravel on localhost.",
            'parse_mode' => 'Markdown',
        ]);

    return $response->json();
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $latest = SensorData::latest()->first();
        $readings = SensorData::latest()->take(10)->get();

        return view('welcome', compact('latest', 'readings'));
    });

    // Rack 1 routed to RackController so $rack data loads from DB
    Route::get('/rack-1', [RackController::class, 'show'])->name('rack1');
    Route::post('/racks/{id}/update', [RackController::class, 'update'])->name('racks.update');

    Route::get('/rack-2', [RackController::class, 'showRack2'])->name('rack2');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});