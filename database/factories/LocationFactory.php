<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'description' => fake()->sentence(10),
            'deviceId' => 'device_' . fake()->unique()->regexify('[A-Za-z0-9]{10}'),
            'controllerShadowName' => fake()->unique()->word(),
            'sensorShadowName' => fake()->unique()->word(),
        ];
    }
}
