<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_id',
        'ph_value',
        'moisture_value',
        'temperature',
        'humidity',
        'timestamp'
    ];

    protected $casts = [
        'ph_value' => 'float',
        'moisture_value' => 'float',
        'temperature' => 'float',
        'humidity' => 'float',
        'timestamp' => 'datetime'
    ];

    // Relasi dengan Land
    public function land()
    {
        return $this->belongsTo(Land::class);
    }
}
