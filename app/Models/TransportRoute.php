<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'route_points',
        'usage_rate',
        'start_time',
        'end_time',
        'frequency_minutes',
        'start_location',
        'end_location',
        'distance',
        'status'
    ];

    protected $casts = [
        'route_points' => 'array',
        'usage_rate' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'frequency_minutes' => 'integer',
        'distance' => 'float'
    ];

    public function getEfficiencyAttribute()
    {
        // Calculate efficiency based on usage rate and capacity
        $maxCapacity = 100;
        return ($this->usage_rate / $maxCapacity) * 100;
    }

    public function isOperating()
    {
        $now = now();
        return $now->between($this->start_time, $this->end_time);
    }

    public function getNextDepartureTime()
    {
        if (!$this->isOperating()) {
            return null;
        }

        $now = now();
        $minutesSinceStart = $now->diffInMinutes($this->start_time) % $this->frequency_minutes;
        return $now->addMinutes($this->frequency_minutes - $minutesSinceStart);
    }
} 