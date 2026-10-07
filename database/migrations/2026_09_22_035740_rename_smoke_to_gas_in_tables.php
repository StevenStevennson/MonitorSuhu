<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename column in racks table
        Schema::table('racks', function (Blueprint $table) {
            $table->renameColumn('max_smoke', 'max_gas');
        });

        // Rename columns in sensor_data table
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->renameColumn('rack1_smoke', 'rack1_gas');
            $table->renameColumn('rack2_smoke', 'rack2_gas');
        });
    }

    public function down(): void
    {
        Schema::table('racks', function (Blueprint $table) {
            $table->renameColumn('max_gas', 'max_smoke');
        });

        Schema::table('sensor_data', function (Blueprint $table) {
            $table->renameColumn('rack1_gas', 'rack1_smoke');
            $table->renameColumn('rack2_gas', 'rack2_smoke');
        });
    }
};