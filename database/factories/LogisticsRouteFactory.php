<?php

namespace Database\Factories;

use App\Models\LogisticsRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogisticsRouteFactory extends Factory
{
    protected $model = LogisticsRoute::class;

    public function definition()
    {
        $estimatedTime = $this->faker->numberBetween(15, 120);
        $actualTime = $this->faker->optional(0.7)->numberBetween($estimatedTime, $estimatedTime + 60);
        $delay = $actualTime ? $actualTime - $estimatedTime : 0;

        return [
            'name' => 'Delivery Route ' . $this->faker->unique()->numberBetween(1, 100),
            'stops' => json_encode([
                ['lat' => $this->faker->latitude, 'lng' => $this->faker->longitude],
                ['lat' => $this->faker->latitude, 'lng' => $this->faker->longitude],
                ['lat' => $this->faker->latitude, 'lng' => $this->faker->longitude]
            ]),
            'estimated_time_minutes' => $estimatedTime,
            'actual_time_minutes' => $actualTime,
            'delay_minutes' => $delay,
            'traffic_zones_crossed' => json_encode([1, 2, 3]),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'start_time' => $this->faker->dateTimeBetween('-1 day', 'now')
        ];
    }
} 