<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $latestZoneWithLevelFour = Zone::factory()->existingWithLevelFour()->create();
        
        return [
            'first_name' => 'users',
            'last_name' => 'last name',
            'phone' => '237698803159',
            'date_of_birth' => '1996-03-11',
            'email' => 'users@user.com',
            'password' => bcrypt('password'),
            'gender' => 'male',
            'type' => $this->faker->randomElement(['COUNCIL', 'default']),
            'zone_id' => $latestZoneWithLevelFour->id,
            'active' => 1,
            'verified' => 1,
            'fcm_token' => $this->faker->uuid(),
            'email_verified_at' => Carbon::now(),
            'activated_at' => Carbon::now(),
            'verified_at' => Carbon::now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $user->assignRole($adminRole);
        });
    }

    public function default(): static
    {
        return $this->afterCreating(function (User $user) {
            $default = Role::firstOrCreate(['name' => 'default', 'guard_name' => 'web']);
            $user->assignRole($default);
        });
    }

    /**
     * Create a COUNCIL type user.
     */
    public function council(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'COUNCIL',
        ]);
    }
}
