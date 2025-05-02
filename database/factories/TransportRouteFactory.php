<?php

namespace Database\Factories;

use App\Models\TransportRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransportRouteFactory extends Factory
{
    protected $model = TransportRoute::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word . ' Route',
            'type' => $this->faker->randomElement(['bus', 'train', 'tram']),
            'route_points' => json_encode([
                [$this->faker->latitude, $this->faker->longitude],
                [$this->faker->latitude, $this->faker->longitude],
                [$this->faker->latitude, $this->faker->longitude]
            ]),
            'usage_rate' => $this->faker->numberBetween(0, 100),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'frequency_minutes' => $this->faker->randomElement([5, 10, 15, 20, 30]),
            'status' => $this->faker->randomElement(['active', 'delayed', 'completed'])
        ];
    }
} 