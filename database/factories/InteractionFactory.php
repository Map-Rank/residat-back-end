<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Interaction;
use App\Models\TypeInteraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class InteractionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Interaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'type_interaction_id' => TypeInteraction::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}