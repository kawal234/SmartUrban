<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'congestion_level',
        'status'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'congestion_level' => 'integer'
    ];

    public function utilityUsages()
    {
        return $this->hasMany(UtilityUsage::class);
    }

    public function getCongestionStatusAttribute()
    {
        if ($this->congestion_level >= 80) {
            return 'critical';
        } elseif ($this->congestion_level >= 60) {
            return 'high';
        } elseif ($this->congestion_level >= 40) {
            return 'normal';
        } else {
            return 'low';
        }
    }
} 