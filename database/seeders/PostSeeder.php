<?php

namespace Database\Seeders;

use App\Models\Interaction;
use Exception;
use App\Models\Post;
use App\Models\User;
use App\Models\TypeInteraction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

    class PostSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            $posts = Post::factory(100)->creator()->create();

            // Iterate through each post
            foreach ($posts as $post) {
                // Like the post 50 times
                // $this->performInteractions($post, 2, 50);

                // Comment on the post 50 times
                $this->performInteractions($post, 3, 50);

                // Share the post 50 times
                $this->performInteractions($post, 4, 50);
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
                    'liked' => $interactionType === 2, // Set liked to true only if it's a like interaction
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
