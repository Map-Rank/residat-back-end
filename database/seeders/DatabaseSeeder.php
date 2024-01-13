<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Level;
use Illuminate\Database\Seeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\TypeInteractionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            // RoleSeeder::class,
            // PermissionSeeder::class,
            // UserSeeder::class,
            // TypeInteractionSeeder::class,
            // SectorSeeder::class,
            // LevelSeeder::class,
            // ZoneSeeder::class,
            // DivisionSeeder::class,
            // SubDivisionSeeder::class,
            PostSeeder::class,
        ]);
    }
}
