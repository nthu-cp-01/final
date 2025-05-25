<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoaningForm>
 */
class LoaningFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startAt = $this->faker->optional()->dateTimeBetween('-1 month', '+1 month');
        $endAt = $startAt ? $this->faker->dateTimeBetween($startAt, '+2 months') : null;

        return [
            'item_id' => Item::factory(),
            'applicant_id' => User::factory(),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => $this->faker->randomElement(['requested', 'approved', 'rejected']),
        ];
    }

    public function requested()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'requested',
            'start_at' => null,
            'end_at' => null,
        ]);
    }

    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'start_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_at' => $this->faker->dateTimeBetween('now', '+2 months'),
        ]);
    }

    public function rejected()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'start_at' => null,
            'end_at' => null,
        ]);
    }
}
