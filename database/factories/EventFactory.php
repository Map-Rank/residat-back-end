<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sector;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Préparez l'utilisateur et les données nécessaires :
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        Sector::factory()->create();
        // Récupérer un ID aléatoire d'un secteur
        $sectorId = Sector::inRandomOrder()->first()->id;

        Storage::fake('public');
        $file = UploadedFile::fake()->image('media.jpg');

        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
            'location' => $this->faker->address(),
            'organized_by' => $this->faker->company(),
            'media' => $file,
            'user_id' => $user->id,
            'sector_id' => $sectorId,
            'published_at' => Carbon::now()->toDateTimeString(),
            'date_debut' => Carbon::now()->addDays(10)->toDateTimeString(),
            'date_fin' => Carbon::now()->addDays(12)->toDateTimeString(),
            'is_valid' => $this->faker->boolean(),
        ];
    }
}
