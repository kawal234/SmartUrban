<?php

namespace Database\Factories;

use App\Models\TrafficZone;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrafficZoneFactory extends Factory
{
    protected $model = TrafficZone::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city . ' Zone',
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'congestion_level' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['low', 'normal', 'high', 'critical'])
        ];
    }
} 