<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'utility_type',
        'usage_amount',
        'unit',
        'date',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'usage_amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function trafficZone()
    {
        return $this->belongsTo(TrafficZone::class);
    }
} 