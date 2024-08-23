<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use App\Models\Company;
use App\Models\Interaction;
use Illuminate\Support\Str;
use App\Models\TypeInteraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'company_name' => $this->faker->company(),
            'type' => $this->faker->randomElement(['national','regional','divisional','subdivisional']),
            'description' => $this->faker->text(200),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'profile' => $this->faker->filePath(),
            'zone_id' => function () {
                return $this->getRandomSubdivisionId();
            },
        ];
    }
    /**
     * Get a random subdivision ID.
     *
     * @return int
     */
    private function getRandomSubdivisionId(): int
    {
        $subdivision = Zone::where('level_id', Level::query()->latest()->first()->id)->inRandomOrder()->first();
        return $subdivision->id;
    }
}