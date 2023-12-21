<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group = Group::factory()->create(['name' => 'Cikanation Messanger Group']);
        $users = User::pluck('id')->all();
        $group->users()->attach($users);
    }
}
