<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticsRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stops',
        'estimated_time_minutes',
        'actual_time_minutes',
        'delay_minutes',
        'traffic_zones_crossed',
        'status',
        'start_time'
    ];

    protected $casts = [
        'stops' => 'array',
        'traffic_zones_crossed' => 'array',
        'estimated_time_minutes' => 'integer',
        'actual_time_minutes' => 'integer',
        'delay_minutes' => 'integer',
        'start_time' => 'datetime'
    ];

    public function getEfficiencyScoreAttribute()
    {
        if (!$this->actual_time_minutes) {
            return null;
        }

        $ratio = $this->estimated_time_minutes / $this->actual_time_minutes;
        return min(100, $ratio * 100);
    }

    public function getDelayStatusAttribute()
    {
        if ($this->delay_minutes <= 0) {
            return 'On Time';
        } elseif ($this->delay_minutes <= 15) {
            return 'Slightly Delayed';
        } elseif ($this->delay_minutes <= 30) {
            return 'Delayed';
        } else {
            return 'Severely Delayed';
        }
    }

    public function calculateOptimalRoute()
    {
        // This would integrate with a routing service (Google Maps, etc.)
        // For now, we'll return a placeholder
        return [
            'optimized_stops' => $this->stops,
            'estimated_duration' => $this->estimated_time_minutes
        ];
    }

    public function getAffectedZones()
    {
        $zones = json_decode($this->traffic_zones_crossed, true) ?? [];
        return TrafficZone::whereIn('id', $zones)->get();
    }
} 