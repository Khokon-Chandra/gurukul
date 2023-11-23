<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            PermissionSeeder::class,
            PermissionRoleSeeder::class,
            UserSeeder::class,
            AnnouncementSeeder::class,
            CashflowSeeder::class,
            NotificationSeeder::class,
        ]);

    }
}