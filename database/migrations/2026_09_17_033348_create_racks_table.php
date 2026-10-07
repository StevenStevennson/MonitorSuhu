<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Server Rack 1');
            $table->string('location')->nullable();
            $table->string('esp_node')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('tech_contact')->nullable();
            
            // Safety Threshold Parameters
            $table->string('max_temp')->default('30.0°C');
            $table->string('min_humidity')->default('20%');
            $table->string('max_humidity')->default('60%');
            $table->string('max_smoke')->default('400 PPM');
            
            $table->string('last_maintenance')->nullable();
            $table->timestamps();
        });
    }
};
