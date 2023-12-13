<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message'    => $this->faker->sentence(6),
            'status'     => $this->faker->boolean(),
            'created_at' => $this->faker->dateTime(),
            'created_by' => rand(1,5),
        ];
    }
}
