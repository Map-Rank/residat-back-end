<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
        $user = User::query()->create([
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
            'avatar'=> '/storage/media/profile.png',
            'verified' => 1,
            'email_verified_at' => Carbon::now(),
            'activated_at' => Carbon::now(),
            'verified_at' => Carbon::now(),
        ]);

        // Attribuer le rôle par défaut (par exemple, 'admin') à l'utilisateur
        $adminRole = Role::where('name', 'admin')->first();
        $user->assignRole($adminRole);
    }
}
