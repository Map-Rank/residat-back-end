<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zone = Zone::factory()->create();
        
        User::query()->create([
            'id' => 1,
            'first_name' => 'users 1',
            'last_name' => 'last name 1',
            'phone' => '237698803158',
            'date_of_birth' => '1996-03-12',
            'email' => 'user@user.com',
            'password' => bcrypt('password!'),
            'gender' => 'male',
            'zone_id' => $zone->id,
            'active' => 1,
            'verified' => 1,
        ]);
    }
}
