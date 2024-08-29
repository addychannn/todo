<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GenderSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            UsersSeeder::class,
            AddressSeeder::class,
            TasksSeeder::class,
        ]);
    }
}
