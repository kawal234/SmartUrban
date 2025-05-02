<?php

namespace Database\Factories;

use App\Models\UtilityUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

class UtilityUsageFactory extends Factory
{
    protected $model = UtilityUsage::class;

    public function definition()
    {
        $types = [
            'electricity' => 'kWh',
            'water' => 'm³',
            'gas' => 'm³'
        ];
        $type = $this->faker->randomElement(array_keys($types));

        return [
            'utility_type' => $type,
            'usage_amount' => $this->faker->randomFloat(2, 10, 1000),
            'unit' => $types[$type],
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(['normal', 'high', 'critical'])
        ];
    }
} 