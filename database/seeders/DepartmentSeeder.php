<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::insert([
            ["name"=>"cs", "created_at" => now()],
            ["name"=>"finance", "created_at" => now()],
            ["name"=>"audit", "created_at" => now()],
            ["name"=>"hr", "created_at" => now()],
            ["name"=>"Purchasing", "created_at" => now()],
            ["name"=>"Inventory", "created_at" => now()],
            ["name"=>"Marketing", "created_at" => now()],
            ["name"=>"general affair", "created_at" => now()],
            ["name"=>"it", "created_at" => now()],
            ["name"=>"restaurant", "created_at" => now()],
        ]);
    }
}
