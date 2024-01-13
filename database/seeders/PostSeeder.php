<?php

namespace Database\Seeders;

use App\Models\Interaction;
use App\Models\Media;
use Exception;
use App\Models\Post;
use App\Models\User;
use App\Models\TypeInteraction;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Testing\Fakes\Fake;

    class PostSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            $subDivisions = Zone::query()->where('level_id', 4)->get();
            $user = User::first();
            $faker = \Faker\Factory::create();

            foreach($subDivisions as $subDivision){
                $user = new User();
                $user->first_name = $faker->firstName();
                $user->last_name = $faker->lastName();
                $user->email = mb_strtolower(str_replace(" ", "", $subDivision->name)."@residat.com");
                $user->phone = $faker->phoneNumber();
                $user->password = Hash::make('password!');
                $user->gender = 'male';
                $user->zone()->associate($subDivision);
                $user->date_of_birth = $faker->date();
                $user->active = 1;
                $user->save();

                for($i = 0; $i < 5; $i++){
                    $post = new Post();
                    $post->content = $this->generateFakeComment();
                    $post->published_at = now();
                    $post->created_at = now();
                    $post->zone()->associate($subDivision);
                    $post->save();

                    $interaction = new Interaction([
                        'type_interaction_id' => 1,
                        'user_id' => $user->id,
                        'post_id' => $post->id,
                    ]);

                    $interaction->save();

                    if(($i % 2) == 0){
                        $media = new Media();
                        $media->url = 'storage/media/post_'.$i.'.png';
                        $media->type = 'image/png';
                        $media->post()->associate($post);

                        $media->save();
                    }

                    // Like the post 3 times
                    $this->performInteractions($post, 2, 3);

                    // Comment on the post 3 times
                    $this->performInteractions($post, 3, 3);

                    // Share the post 2 times
                    $this->performInteractions($post, 4, 2);
                }
            }

        }

        /**
         * Perform interactions on a post.
         *
         * @param \App\Models\Post $post
         * @param int $interactionType
         * @param int $count
         */
        private function performInteractions(Post $post, int $interactionType, int $count): void
        {
            // Get users to perform interactions (you may adjust this logic based on your requirements)
            $users = User::inRandomOrder()->limit($count)->get();

            // Attach users to the post with the specified interaction type
            foreach ($users as $user) {
                $interaction = [
                    'type_interaction_id' => $interactionType,
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ];

                // For comments, include a fake text using Faker
                if ($interactionType === 3) {
                    $interaction['text'] = $this->generateFakeComment();
                }

                // Save the interaction
                $post->interactions()->create($interaction);
            }
        }

        /**
         * Generate a fake comment using Faker.
         *
         * @return string
         */
        private function generateFakeComment(): string
        {
            return \Faker\Factory::create()->sentence();
        }

    }
