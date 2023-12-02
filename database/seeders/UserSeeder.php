<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $user = \App\Models\User::create([
            'department_id'     => rand(1,10),
            'name'              => 'Administrator',
            'username'          => 'administrator',
            'email'             => 'test@example.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Administrator');

    }
}
