<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * Le nom du modèle associé à cette fabrique.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'titre_en' => $this->faker->sentence(),
            'titre_fr' => $this->faker->sentence(),
            'firebase_id' => $this->faker->uuid(),
            'zone_id' => function () {
                return $this->getRandomSubdivisionId();
            },
            'user_id' => User::factory(),
            'content_en' => $this->faker->paragraph(),
            'content_fr' => $this->faker->paragraph(),
            'image' => 'image.jpg', 
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
