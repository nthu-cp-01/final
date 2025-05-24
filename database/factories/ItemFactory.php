<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'purchase_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'description' => fake()->paragraph(),
            'manager_id' => User::factory(),
            'location_id' => Location::factory(),
            'owner_id' => User::factory(),
            'status' => fake()->randomElement(['registered', 'normal', 'gone']),
        ];
    }
}
