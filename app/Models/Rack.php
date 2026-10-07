<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    protected $fillable = [
        'name', 'location', 'esp_node', 'ip_address', 'tech_contact',
        'max_temp', 'min_humidity', 'max_humidity', 'max_gas', 'last_maintenance'
    ];
}