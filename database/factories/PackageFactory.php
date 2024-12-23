<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * Le nom du modèle associé à ce factory.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Définir l'état par défaut pour le modèle.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name_fr' => $this->faker->words(3, true),
            'name_en' => $this->faker->words(3, true),
            'level' => $this->faker->randomElement(['National', 'Regional', 'Divisional', 'Subdivisional']),
            'periodicity' => $this->faker->randomElement(['Month', 'Quarter', 'Half', 'Annual']),
            'price' => $this->faker->numberBetween(100, 10000),
            'description_fr' => $this->faker->optional()->sentence(),
            'description_en' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(100),
        ];
    }
}
