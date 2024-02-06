<?php

namespace Tests\Feature\Http\Controllers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Zone;
use App\Models\Topic;
use App\Models\Interaction;
use Laravel\Sanctum\Sanctum;
use App\Models\TypeInteraction;
use Database\Seeders\PostSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ZoneSeeder;
use Database\Seeders\LevelSeeder;
use Illuminate\Http\UploadedFile;
use Database\Seeders\SectorSeeder;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SubDivisionSeeder;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\TypeInteractionSeeder;
use App\Http\Controllers\Api\PostController;
use App\Models\Level;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        // $this->seed();
        Sanctum::actingAs(
            User::factory()->create($this->dataLogin())
        );
    }

    public function test_index()
    {
        TypeInteraction::factory()->create();
        sleep(2);
        Post::factory()->count(10)->creator()->create();
        sleep(3);

        $this->withoutExceptionHandling();

        // Utiliser la méthode index pour récupérer la liste des ressources
        $response = $this->getJson(route('post.index', ['page' => 1, 'size' => 5]));

        $response->assertStatus(200)
             ->assertJson(['status' => true])
             ->assertJsonCount(5, 'data');
    }

    public function test_store()
    {
        // Vérifiez si la table TypeInteraction est vide
        $typeInteraction = TypeInteraction::where('name', 'created')->first();

        if (!$typeInteraction) {
            // Si le type d'interaction n'existe pas, créez-le
            $typeInteraction = TypeInteraction::factory()->create(['name' => 'created']);
        }
        sleep(3);
        // Créez des fichiers temporaires pour simuler le téléchargement
        $file1 = UploadedFile::fake()->image('image1.jpg');
        $file2 = UploadedFile::fake()->image('image2.jpg');

        $data = [
            'content' => $this->faker->sentence(),
            'published_at' => Carbon::now()->toDateTimeString(),
            'zone_id' => Zone::factory()->create()->id,
            'sectors' => [1, 2],
            'media' => [$file1, $file2],
        ];

        $response = $this->postJson(route('post.store'), $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('posts', ['content' => $data['content']]);

        // Vérifiez également que les médias sont associés au post
        $post = Post::where('content', $data['content'])->first();
        $this->assertNotNull($post);

        foreach ($data['media'] as $mediaFile) {
            $mediaPath = $mediaFile->store('media/' . auth()->user()->email);
            $this->assertDatabaseHas('medias', [
              'url' => Storage::url($mediaPath),
              'post_id' => $post->id,
              'type' => $mediaFile->getClientMimeType(),
            ]);
          }
    }

    public function test_show()
    {
        $post = Post::factory()->create();

        $response = $this->getJson(route('post.show', ['page' => 1, $post->id]) );

        $response->assertStatus(200)
            ->assertJson(['data' => ['content' => $post->content]]);
    }

    public function test_update()
    {
        $typeInteraction = TypeInteraction::factory()->create(['name' => 'created']);
        // Créez un post pour la mise à jour
        $post = Post::factory()->creator()->create();

        sleep(3);

        // Récupérez l'utilisateur créateur du post
        $user = $post->creator->first();

        // Simulez l'authentification de l'utilisateur
        Sanctum::actingAs($user); // Assurez-vous que l'utilisateur est authentifié

        // Créez un nouveau fichier temporaire pour simuler la mise à jour du média
        $newMediaFile = UploadedFile::fake()->image('new_image.jpg');

        $data = [
            'content' => $this->faker->sentence(),
            'published_at' => Carbon::now()->toDateTimeString(),
            'zone_id' => Zone::factory()->create()->id,
            'sectors' => [1, 2],
            'media' => [$newMediaFile], // Ajoutez le nouveau fichier média à la requête de mise à jour
        ];

        $response = $this->putJson(route('post.update', $post->id), $data);

        $response->assertStatus(200);

        // Rafraîchissez l'instance du post depuis la base de données
        $post->refresh();

        // Vérifiez que les données du post ont été mises à jour
        $this->assertEquals($data['content'], $post->content);
        $this->assertEquals($data['published_at'], $post->published_at);
        $this->assertEquals($data['zone_id'], $post->zone_id);

        // Vérifiez également que le nouveau média est associé au post
        $storedMediaUrl = Storage::url($newMediaFile->store('media/' . auth()->user()->email));
        $this->assertDatabaseHas('medias', ['url' => $storedMediaUrl, 'post_id' => $post->id]);
    }

    public function test_destroy()
    {
        // Vérifiez si la table TypeInteraction est vide
        $typeInteraction = TypeInteraction::where('name', 'created')->first();

        if (!$typeInteraction) {
            // Si le type d'interaction n'existe pas, créez-le
            $typeInteraction = TypeInteraction::factory()->create(['name' => 'created']);
        }
        sleep(3);

        Post::factory()->creator()->create();

        $post = Post::with('creator')->first();

        $user = $post->creator->first(); // Récupérer le créateur du post

        // Simuler l'authentification de l'utilisateur
        // Sanctum::actingAs($user);

        $response = $this->deleteJson(route('post.destroy', $post->id));

        sleep(5);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
            'deleted_at' => $post->deleted_at,
        ]);
    }

    /**
     * Test liking a post.
     */
    public function testLike()
    {
        // $interaction = TypeInteraction::factory()->create();

        // // dd($interaction);

        // $typeInteraction = TypeInteraction::where('name', 'created')->first();

        

        // // TypeInteraction::factory()->create(['name' => 'created']);
       
        // Post::factory()->creator()->create();

        // $post = Post::with('creator')->first();

        // $user = $post->creator->first();
        
        // // Authentifier l'utilisateur
        // $this->actingAs($user);

        // // Envoyer une requête POST à la route `/api/post/like/{id}`
        // $response = $this->postJson('/api/post/like/' . $post->id);

        // // Asserter que la relation entre l'utilisateur, le post et le type d'interaction est bien établie
        // $this->assertDatabaseHas('interactions', [
        //     'post_id' => $post->id,
        //     'user_id' => $user->id,
        //     'type_interaction_id' => $typeInteraction->id,
        // ]);
        $typeInteraction = TypeInteraction::where('name', 'created')->firstOrCreate(['name' => 'created']);

        $post = Post::factory()->creator()->create();
        $user = $post->creator->first();

        $this->actingAs($user);

        $response = $this->postJson('/api/post/like/' . $post->id);

        $response->assertStatus(200);

        $typeInteraction = TypeInteraction::where('name', 'created')->first();

        $this->assertDatabaseHas('interactions', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'type_interaction_id' => $typeInteraction->id,
        ]);
    }

    /**
     * Test commenting on a post.
     */
    public function testComment()
    {
        $post = Post::factory()->creator()->create();

        $data = [
            'text' => $this->faker->sentence(), // Ajoutez le texte du commentaire ici
        ];

        $response = $this->postJson('api/post/comment/' . $post->id, $data);

        $response->assertStatus(200);

        $user = auth()->user();
        $this->assertDatabaseHas('interactions', [
            'type_interaction_id' => 3,
            'user_id' => $user->id,
            'post_id' => $post->id,
            'text' => $data['text'],
        ]);
    }

    /**
     * Test sharing a post.
     */
    public function testShare()
    {
        $post = Post::factory()->creator()->create();

        $response = $this->postJson('api/post/share/' . $post->id);

        $response->assertStatus(200);

        $user = auth()->user();
        $this->assertDatabaseHas('interactions', [
            'type_interaction_id' => 4,
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function testDeleteInteraction()
    {
        // Vérifiez si la table TypeInteraction est vide
        $typeInteraction = TypeInteraction::where('name', 'comment')->first();

        if (!$typeInteraction) {
            // Si le type d'interaction n'existe pas, créez-le
            $typeInteraction = TypeInteraction::factory()->create(['name' => 'comment']);
        }
        sleep(3);

        Post::factory()->creator()->create();

        $post = Post::with('creator')->first();

        $user = $post->creator->first(); // Récupérer le créateur du post

        $response = $this->deleteJson(route('delete.interaction', $post->id));

        sleep(5);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('interactions', [
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
