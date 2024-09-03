<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition()
    {
        $latestZoneWithLevelFour = Zone::factory()->existingWithLevelFour()->create();

        return [
            'code' => Str::uuid(),
            'user_id' => User::first(),
            'zone_id' => $latestZoneWithLevelFour->id,
            'description' => $this->faker->text(200),
            'type' => $this->faker->randomElement(['DROUGHT', 'FLOOD', 'WATER_STRESS']),
            'image' => $this->faker->filePath(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }
}
