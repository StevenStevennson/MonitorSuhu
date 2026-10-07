<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SensorDataController extends Controller
{
    // Saves incoming HTTP POST data from the ESP32 to MySQL and replies with active thresholds
    public function store(Request $request)
    {
        $sensorData = SensorData::create([
            'rack1_temp'     => $request->input('rack1_temp'),
            'rack1_humidity' => $request->input('rack1_humidity'),
            'rack1_gas'      => $request->input('rack1_smoke', $request->input('rack1_gas')),
            'rack2_temp'     => $request->input('rack2_temp'),
            'rack2_humidity' => $request->input('rack2_humidity'),
            'rack2_gas'      => $request->input('rack2_smoke', $request->input('rack2_gas')),
        ]);

        // Evaluate Telegram notification thresholds
        $this->checkAndSendAlert($sensorData);

        // Fetch thresholds from DB
        $tempMax = (float) Setting::get('temp_max', 32.0);
        $gasMax  = (int) Setting::get('gas_max', 1500);

        // Returns status AND active thresholds back to ESP32 in JSON response
        return response()->json([
            'status'     => 'success',
            'message'    => 'Telemetry saved',
            'thresholds' => [
                'temp_max' => $tempMax,
                'gas_max'  => $gasMax,
            ],
            'data'       => $sensorData
        ], 201);
    }

    // Evaluates sensor readings using dynamic MySQL settings
    private function checkAndSendAlert($data)
    {
        $tempMax = (float) Setting::get('temp_max', 32.0);
        $gasMax  = (int) Setting::get('gas_max', 1500);

        $alerts = [];

        if ($data->rack1_temp > $tempMax) {
            $alerts[] = "⚠️ *Rak 1 Suhu Tinggi:* {$data->rack1_temp}°C (Batas: {$tempMax}°C)";
        }
        if ($data->rack1_gas > $gasMax) {
            $alerts[] = "🚨 *Rak 1 Asap/Gas Terdeteksi:* {$data->rack1_gas} (Batas: {$gasMax})";
        }
        if ($data->rack2_temp > $tempMax) {
            $alerts[] = "⚠️ *Rak 2 Suhu Tinggi:* {$data->rack2_temp}°C (Batas: {$tempMax}°C)";
        }
        if ($data->rack2_gas > $gasMax) {
            $alerts[] = "🚨 *Rak 2 Asap/Gas Terdeteksi:* {$data->rack2_gas} (Batas: {$gasMax})";
        }

        if (!empty($alerts)) {
            $message = "⚠️ *PERINGATAN BAHAYA RAK SENSOR!*\n\n" . implode("\n", $alerts);
            $this->sendTelegramMessage($message);
        }
    }

    private function sendTelegramMessage($message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId   = env('TELEGRAM_CHAT_ID');

        if (!$botToken || !$chatId) {
            Log::warning('Telegram bot token or chat ID missing in .env');
            return;
        }

        try {
            Http::timeout(3)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram alert failed to send: ' . $e->getMessage());
        }
    }

    // Fetches live data and current database thresholds for dashboard UI
    public function latest()
    {
        return response()->json([
            'latest'     => SensorData::latest()->first(),
            'readings'   => SensorData::latest()->take(10)->get(),
            'thresholds' => [
                'temp_max' => (float) Setting::get('temp_max', 32.0),
                'gas_max'  => (int) Setting::get('gas_max', 1500),
            ]
        ]);
    }

    // Updates thresholds when edited via dashboard web UI form
    public function updateSettings(Request $request)
    {
        $request->validate([
            'temp_max' => 'required|numeric',
            'gas_max'  => 'required|numeric',
        ]);

        Setting::set('temp_max', $request->input('temp_max'));
        Setting::set('gas_max', $request->input('gas_max'));

        return response()->json([
            'status'  => 'success',
            'message' => 'Ambang batas berhasil diperbarui!',
            'data'    => [
                'temp_max' => (float) Setting::get('temp_max'),
                'gas_max'  => (int) Setting::get('gas_max'),
            ]
        ]);
    }
}