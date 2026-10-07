<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorReading;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rack1_temp'     => 'required|numeric',
            'rack1_humidity' => 'required|numeric',
            'rack1_smoke'    => 'required|numeric',
            'rack2_temp'     => 'required|numeric',
            'rack2_humidity' => 'required|numeric',
            'rack2_smoke'    => 'required|numeric',
        ]);

        $reading = SensorReading::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Rack data recorded successfully',
            'data'    => $reading
        ], 201);
    }
}
