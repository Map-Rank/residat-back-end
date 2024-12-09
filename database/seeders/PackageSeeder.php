<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'National Package',
                'level' => 'National',
                'price' => 50000,
                'description' => 'Top-tier package for national-scale organizations with advanced features.',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Regional Package',
                'level' => 'Regional',
                'price' => 35000,
                'description' => 'Comprehensive package for regional-level organizations.',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'name' => 'Divisional Package',
                'level' => 'Divisional',
                'price' => 25000,
                'description' => 'Specialized package for divisional-level requirements.',
                'is_active' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Subdivisional Package',
                'level' => 'Subdivisional',
                'price' => 15000,
                'description' => 'An entry-level package perfect for small organizations.',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        // Insert the packages into the database
        DB::table('packages')->insert($packages);
    }
}