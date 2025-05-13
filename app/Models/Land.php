<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Land extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'area',
        'device_id',
        'user_id',
        'is_active'
    ];

    protected $casts = [
        'area' => 'float',
        'is_active' => 'boolean'
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan SensorData
    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }

    // Scope untuk lahan aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
