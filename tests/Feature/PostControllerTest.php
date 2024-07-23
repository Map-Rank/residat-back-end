<?php

namespace Tests\Feature\Http\Controllers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use App\Models\Topic;
use App\Models\Interaction;
use App\Models\Subscription;
use Laravel\Sanctum\Sanctum;
use App\Models\TypeInteraction;
use Database\Seeders\PostSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ZoneSeeder;
use Database\Seeders\LevelSeeder;
use Illuminate\Http\UploadedFile;
use Database\Seeders\SectorSeeder;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use Database\Seeders\DivisionSeeder;
use App\Http\Resources\TopicResource;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SubDivisionSeeder;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\InteractionResource;
use Database\Seeders\TypeInteractionSeeder;
use App\Http\Controllers\Api\PostController;
use App\Http\Resources\SubscriptionResource;
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
        $interactions =  [
            ['name'=> 'created', 'id'=> 1],
            ['name'=> 'like', 'id'=> 2],
            ['name'=> 'comment', 'id'=> 3],
            ['name'=> 'share', 'id'=> 4],
        ];
        DB::table('type_interactions')->insert($interactions);
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

        // Créez des fichiers temporaires pour simuler le téléchargement
        Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');
        $file1 = UploadedFile::fake()->image('media1.jpg');
        $file2 = UploadedFile::fake()->image('media2.jpg');

        // Créez des zones et récupérez les ID nécessaires
        $zoneId = Zone::factory()->create()->id;

        $data = [
            'content' => $this->faker->sentence(),
            'published_at' => Carbon::now()->toDateTimeString(),
            'zone_id' => $zoneId,
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
            // Créez un nom de fichier unique
            $imageName = time() . '.' . $mediaFile->getClientOriginalExtension();
    
            // Stocker le fichier avec le nom unique dans le disque 'public'
            $mediaPath = $mediaFile->storeAs('images', $imageName, 'public');
    
            // Vérifiez que le fichier est bien stocké dans le disque 'public'
            Storage::disk('public')->assertExists($mediaPath);
    
            // Vérifiez que le chemin correspond à ce qui est dans la base de données
            $this->assertDatabaseHas('medias', [
                'url' => $mediaPath,
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
        $typeInteraction = TypeInteraction::where('name', 'created')->first();

        if(!$typeInteraction){
            $typeInteractions =  [
                ['name'=> 'created', 'id'=> 1],
                ['name'=> 'like', 'id'=> 2],
                ['name'=> 'comment', 'id'=> 3],
                ['name'=> 'share', 'id'=> 4],
            ];

            DB::table('type_interactions')->insert($typeInteractions);
        }

        $typeInteraction = TypeInteraction::where('name', 'created')->first();

        // $typeInteraction = TypeInteraction::factory()->create(['name' => 'created']);
        // Créez un post pour la mise à jour
        $post = Post::factory()->creator()->create();

        sleep(3);

        // Récupérez l'utilisateur créateur du post
        $user = $post->creator->first();

        // Simulez l'authentification de l'utilisateur
        Sanctum::actingAs($user); // Assurez-vous que l'utilisateur est authentifié

        // Créez un nouveau fichier temporaire pour simuler la mise à jour du média
        Storage::fake(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0 ? 'public' : 's3');
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
        foreach ($data['media'] as $mediaFile) {
            // Créez un nom de fichier unique
            $imageName = time() . '.' . $mediaFile->getClientOriginalExtension();
    
            // Stocker le fichier avec le nom unique dans le disque 'public'
            $mediaPath = $mediaFile->storeAs('images', $imageName, 'public');
    
            // Vérifiez que le fichier est bien stocké dans le disque 'public'
            Storage::disk('public')->assertExists($mediaPath);
    
            // Vérifiez que le chemin correspond à ce qui est dans la base de données
            $this->assertDatabaseHas('medias', [
                'url' => Storage::url($mediaPath),
                'post_id' => $post->id,
                'type' => $mediaFile->getClientMimeType(),
            ]);
        }
    }

    public function test_destroy()
    {
        // Vérifiez si la table TypeInteraction est vide
        $typeInteraction = TypeInteraction::where('name', 'created')->first();

        if (!$typeInteraction) {
            // Si le type d'interaction n'existe pas, créez-le
            // $typeInteraction = TypeInteraction::factory()->create(['name' => 'created']);
            $typeInteractions =  [
                ['name'=> 'created', 'id'=> 1],
                ['name'=> 'like', 'id'=> 2],
                ['name'=> 'comment', 'id'=> 3],
                ['name'=> 'share', 'id'=> 4],
            ];

            DB::table('type_interactions')->insert($typeInteractions);
        }
        $typeInteraction = TypeInteraction::where('name', 'created')->first();
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

        $typeInteraction = TypeInteraction::where('name', 'like')->first();

        if(!$typeInteraction){
            $typeInteractions =  [
                ['name'=> 'created', 'id'=> 1],
                ['name'=> 'like', 'id'=> 2],
                ['name'=> 'comment', 'id'=> 3],
                ['name'=> 'share', 'id'=> 4],
            ];

            DB::table('type_interactions')->insert($typeInteractions);
        }

        $typeInteraction = TypeInteraction::where('name', 'like')->first();

        $post = Post::factory()->creator()->create();

        sleep(3);

        // Récupérez l'utilisateur créateur du post
        $user = $post->creator->first();

        // Authentifier l'utilisateur
        // dd(json_encode($post->loadMissing('creator', 'interactions.typeInteraction')));

        $interaction  = $post->interactions->where('user_id', $user->id)
            ->where('post_id', $post->id)->where('type_interaction_id', $typeInteraction->id)->first();

        $hasValue = ($interaction != null );

        $this->actingAs($user);

        // Envoyer une requête POST à la route `/api/post/like/{id}`

        $response = $this->postJson('/api/post/like/' . $post->id);

        // Asserter que la relation entre l'utilisateur, le post et le type d'interaction est bien établie
        if($hasValue){
            $this->assertDatabaseMissing('interactions', [
                'post_id' => $post->id,
                'user_id' => $user->id,
                'type_interaction_id' => $typeInteraction->id,
            ]);
        }else {
            $this->assertDatabaseHas('interactions', [
                'post_id' => $post->id,
                'user_id' => $user->id,
                'type_interaction_id' => $typeInteraction->id,
            ]);
        }

    }

    /**
     * Test commenting on a post.
     */
    public function testComment()
    {
        $typeInteraction = TypeInteraction::where('name', 'comment')->first();

        if(!$typeInteraction){
            $typeInteractions =  [
                ['name'=> 'created', 'id'=> 1],
                ['name'=> 'like', 'id'=> 2],
                ['name'=> 'comment', 'id'=> 3],
                ['name'=> 'share', 'id'=> 4],
            ];

            DB::table('type_interactions')->insert($typeInteractions);
        }

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
        $typeInteraction = TypeInteraction::where('name', 'comment')->first();

        if(!$typeInteraction){
            $typeInteractions =  [
                ['name'=> 'created', 'id'=> 1],
                ['name'=> 'like', 'id'=> 2],
                ['name'=> 'comment', 'id'=> 3],
                ['name'=> 'share', 'id'=> 4],
            ];

            DB::table('type_interactions')->insert($typeInteractions);
        }

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

        // Créer un type d'interaction s'il n'existe pas déjà
        $typeInteraction = TypeInteraction::where('name', 'comment')->first();
        if (!$typeInteraction) {
            $typeInteractions =  [
                ['name'=> 'created', 'id'=> 1],
                ['name'=> 'like', 'id'=> 2],
                ['name'=> 'comment', 'id'=> 3],
                ['name'=> 'share', 'id'=> 4],
            ];
            DB::table('type_interactions')->insert($typeInteractions);
        }

        // Créer un post
        $post = Post::factory()->creator()->create();

        // Créer un commentaire
        $commentData = [
            'text' => $this->faker->sentence(), // Ajoutez le texte du commentaire ici
        ];
        $this->postJson('api/post/comment/' . $post->id, $commentData)->assertStatus(200);
        // Assurez-vous que le commentaire a été correctement ajouté à la base de données
        $user = auth()->user();
        $this->assertDatabaseHas('interactions', [
            'type_interaction_id' => 3,
            'user_id' => $user->id,
            'post_id' => $post->id,
            'text' => $commentData['text'],
        ]);

        $comment = Interaction::where('user_id', auth()->id())
        ->where('type_interaction_id', 3)
        ->latest()
        ->firstOrFail();

        // dd($comment);

        $this->deleteJson(route('delete.interaction', $comment->id))->assertStatus(200);

        // Assurez-vous que le commentaire a été supprimé de la base de données
        $this->assertSoftDeleted('interactions', [
            'id' => $comment->id,
        ]);
    }

    public function testIndexWithZoneId()
    {
        // Créez une zone avec des enfants pour tester la récupération des descendants
        $parentZone = Zone::factory()->create();
        $childZone1 = Zone::factory()->create(['parent_id' => $parentZone->id]);
        $childZone2 = Zone::factory()->create(['parent_id' => $parentZone->id]);

        // Simulez une requête avec un zone_id spécifié
        $zoneId = $parentZone->id;

        $response = $this->getJson(route('post.index', ['zone_id' => $zoneId]));

        $response->assertStatus(200);

        // Assurez-vous que les posts récupérés appartiennent aux descendants de la zone
        $posts = json_decode($response->getContent(), true)['data'];
        foreach ($posts as $post) {
            $this->assertTrue(in_array($post['zone_id'], [$parentZone->id, $childZone1->id, $childZone2->id]));
        }
    }

    public function test_topic_resource()
    {
        // Créez un modèle Topic factice
        $topic = Topic::factory()->create([
            'name' => 'Example Topic',
            'created_at' => '2022-02-18 12:00:00',
        ]);

        // Transformez le modèle Topic en utilisant TopicResource
        $topicResource = new TopicResource($topic);

        // Vérifiez si les données transformées correspondent à ce que vous attendez
        $expectedData = [
            'id' => $topic->id,
            'name' => 'Example Topic',
            'created_at' => '2022-02-18 12:00:00',
        ];

        // Obtenez les données transformées sous forme de tableau
        $transformedData = $topicResource->jsonSerialize();

        // Comparez les données transformées avec les données attendues
        $this->assertEquals($expectedData, $transformedData);
    }

    public function test_interaction_resource()
    {
        // Créez un modèle Interaction factice
        $interaction = Interaction::factory()->create([
            'text' => 'Example Interaction',
            'user_id' => User::first()->id,
            'created_at' => '2022-02-18 12:00:00',
        ]);

        // Transformez le modèle Interaction en utilisant InteractionResource
        $interactionResource = new InteractionResource($interaction);

        // Obtenez les données transformées sous forme de tableau
        $transformedData = $interactionResource->jsonSerialize();

        // Vérifiez si les données transformées correspondent à ce que vous attendez
        $expectedData = [
            'id' => $interaction->id,
            'user_id' => User::first()->id,
            'post_id' => $interaction->post_id,
            'text' => 'Example Interaction',
            'created_at' => '2022-02-18 12:00:00',
            'updated_at' => $interaction->updated_at,
        ];

        // Comparez les données transformées avec les données attendues
        $this->assertEquals($expectedData, $transformedData);
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
