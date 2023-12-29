<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::all()->random()->id,
            'name' => $this->faker->sentence(),
            'amount'    => $this->faker->randomFloat(),
            'created_at' => $this->faker->dateTime(),
        ];
    }
}
