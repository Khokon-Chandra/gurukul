<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserIp>
 */
class UserIpFactory extends Factory
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
            'ip'          => $this->faker->ipv4(),
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by'  => 1,
            'created_at'  => now(),
        ];
    }
}
