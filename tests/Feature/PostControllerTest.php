<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Zone;
use App\Models\Topic;
use Laravel\Sanctum\Sanctum;
use App\Http\Controllers\Api\PostController;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create($this->dataLogin())
        );
    }

    public function testIndex()
    {
        // $posts = factory(Post::class, 10)->create();

        Post::factory()->count(10)->create();

        $response = $this->getJson('api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data.data');
    }

    public function testStore()
    {
        $data = [
            'content' => $this->faker->sentence(),
            'published_at' => Carbon::now()->toDateTimeString(),
            'zone_id' => Zone::factory()->create()->id,
            'user_id' => auth()->user()->id,
            'topic_id' => Topic::factory()->create()->id,
        ];

        $response = $this->postJson('api/create', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('posts', $data);
    }

    public function testShow()
    {
        $post = Post::factory()->create();

        $response = $this->getJson('api/show/' . $post->id);

        $response->assertStatus(200)
            ->assertJson(['data' => ['content' => $post->content]]);
    }

    public function testUpdate()
    {
        $post = Post::factory()->create();

        $data = [
            'content' => $this->faker->sentence(),
            'published_at' => Carbon::now()->toDateTimeString(),
            'zone_id' => Zone::factory()->create()->id,
            'user_id' => auth()->user()->id,
            'topic_id' => Topic::factory()->create()->id,
        ];

        $response = $this->putJson('api/update/' . $post->id, $data);

        $response->assertStatus(200);

        $post->refresh();
        $this->assertEquals($data['content'], $post->content);
        $this->assertEquals($data['published_at'], $post->published_at);
        $this->assertEquals($data['zone_id'], $post->zone_id);
        $this->assertEquals($data['user_id'], $post->user_id);
        $this->assertEquals($data['topic_id'], $post->topic_id);
    }

    public function testDestroy()
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson('api/delete/' . $post->id);

        sleep(5);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
            'deleted_at' => $post->deleted_at,
        ]);
    }

    /**
     * @return array
     */
    private function dataLogin()
    {
        return [
            'email' => 'users@user.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];
    }
}