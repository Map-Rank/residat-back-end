<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Zone;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = (clone $startDate)->modify('+1 year');

        return [
            'user_id' => User::factory(),
            'package_id' => Package::factory(),
            'zone_id' => Zone::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
