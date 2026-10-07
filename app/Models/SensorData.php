<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor_data';

    protected $fillable = [
        'rack1_temp',
        'rack1_humidity',
        'rack1_gas',
        'rack2_temp',
        'rack2_humidity',
        'rack2_gas',
    ];
}