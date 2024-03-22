<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Zone;
use Illuminate\Support\Str;
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
            'zone_id' => $latestZoneWithLevelFour->id,
            'active' => 1,
            'verified' => 1,
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
}
