<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rack;

class RackController extends Controller
{
    public function show($id = 1)
    {
        $rack = Rack::firstOrCreate(
            ['id' => $id],
            [
                'name'             => 'Server Rack ' . $id,
                'location'         => 'Main Server Room (Aisle A2)',
                'esp_node'         => 'ESP32-RACK1-MAIN',
                'ip_address'       => '192.168.1.101',
                'tech_contact'     => 'Admin (Ext #402)',
                'max_temp'         => '30.0°C',
                'min_humidity'     => '20%',
                'max_humidity'     => '60%',
                'max_smoke'        => '400 PPM',
                'last_maintenance' => 'Sep 10, 2026',
            ]
        );

        return view('rack1', compact('rack'));
    }

    public function showRack2()
    {
        // Find or create Rack 2 specifications
        $rack = Rack::firstOrCreate(['id' => 2], [
            'location'         => 'Server Room B',
            'esp_node'         => 'ESP32-NODE-02',
            'ip_address'       => '192.168.1.151',
            'tech_contact'     => 'Admin',
            'max_temp'         => '30.0°C',
            'min_humidity'     => '40%',
            'max_humidity'     => '70%',
            'max_gas'        => '300 PPM',
            'last_maintenance' => date('Y-m-d'),
        ]);

        return view('rack2', compact('rack'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'location'         => 'nullable|string|max:255',
            'esp_node'         => 'nullable|string|max:255',
            'ip_address'       => 'nullable|string|max:255',
            'tech_contact'     => 'nullable|string|max:255',
            'max_temp'         => 'nullable|string|max:255',
            'min_humidity'     => 'nullable|string|max:255',
            'max_humidity'     => 'nullable|string|max:255',
            'max_gas'        => 'nullable|string|max:255',
            'last_maintenance' => 'nullable|string|max:255',
        ]);

        $rack = Rack::findOrFail($id);
        $rack->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rack limits updated successfully!',
            'data'    => $rack
        ]);
    }
}