<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
            RoleSeeder::class,
        ]);

        $this->call([
            PermissionSeeder::class,
        ]);

        $this->call([
            UserSeeder::class,
        ]);

        $this->call([
            TypeInteractionSeeder::class,
        ]);

         $this->call([
            SectorSeeder::class,
        ]);
    }
}
