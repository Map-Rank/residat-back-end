<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Level;
use Illuminate\Database\Seeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ZoneSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\ReportsSeeder;
use Database\Seeders\VectorsSeeder;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\MetricTypeSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\VectorKeysSeeder;
use Database\Seeders\ReportItemsSeeder;
use Database\Seeders\SubDivisionSeeder;
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
            PermissionSeeder::class,
            TypeInteractionSeeder::class,
            SectorSeeder::class,
            LevelSeeder::class,
            ZoneSeeder::class,
            DivisionSeeder::class,
            SubDivisionSeeder::class,
            UserSeeder::class,
            // PostSeeder::class,

            MetricTypeSeeder::class,
            // ReportsSeeder::class,
            // ReportItemsSeeder::class,
            // VectorsSeeder::class,
            // VectorKeysSeeder::class,
        ]);
    }
}
