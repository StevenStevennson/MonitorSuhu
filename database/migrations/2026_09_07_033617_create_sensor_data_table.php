<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('sensor_data', function (Blueprint $table) {
        $table->id();
        $table->float('rack1_temp')->nullable();
        $table->float('rack1_humidity')->nullable();
        $table->integer('rack1_smoke')->nullable();
        $table->float('rack2_temp')->nullable();
        $table->float('rack2_humidity')->nullable();
        $table->integer('rack2_smoke')->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};