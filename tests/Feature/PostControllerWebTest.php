<?php

namespace Tests\Feature\Http\Controllers;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Zone;
use App\Models\TypeInteraction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerWebTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_can_display_a_listing_of_posts()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        // Créez une zone avec des enfants pour tester la récupération des descendants
        $parentZone = Zone::factory()->create();
        $childZone1 = Zone::factory()->create(['parent_id' => $parentZone->id]);
        $childZone2 = Zone::factory()->create(['parent_id' => $parentZone->id]);
        
        $interactions =  [
            ['name'=> 'created', 'id'=> 1],
            ['name'=> 'like', 'id'=> 2],
            ['name'=> 'comment', 'id'=> 3],
            ['name'=> 'share', 'id'=> 4],
        ];
        DB::table('type_interactions')->insert($interactions);
        sleep(2);
        $posts = Post::factory()->count(10)->creator()->create();
        sleep(3);

        $this->withoutExceptionHandling();

        // Appelez la méthode index() du PostController
        $response = $this->get(route('posts.index'));

        // Vérifiez que la réponse contient la vue 'posts.index'
        $response->assertViewIs('posts.index');

        // Vérifiez que les posts sont présents dans la vue
        foreach ($posts as $post) {
            $response->assertSee($post->title);
        }
    }

    /** @test */
    public function it_can_show_a_specific_post()
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
        
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $parentZone = Zone::factory()->create();
        $childZone1 = Zone::factory()->create(['parent_id' => $parentZone->id]);
        $childZone2 = Zone::factory()->create(['parent_id' => $parentZone->id]);

        TypeInteraction::where('name', 'created')->first();

        // Créez un post fictif pour les données de test
        $posts = Post::factory()->creator()->create();

        $postId = $posts->first()->id;

        $response = $this->get('/post/' . $postId . '/detail');

        // Vérifiez que la réponse contient la vue 'posts.show'
        $response->assertViewIs('posts.show');

        // Vérifiez que le post est présent dans la vue
        $response->assertSee($posts->first()->title);
    }

    /** @test */
    public function it_can_allow_or_disallow_a_post()
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
        
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $parentZone = Zone::factory()->create();
        $childZone1 = Zone::factory()->create(['parent_id' => $parentZone->id]);
        $childZone2 = Zone::factory()->create(['parent_id' => $parentZone->id]);

        TypeInteraction::where('name', 'created')->first();

        // Créez un post fictif pour les données de test
        $posts = Post::factory()->creator()->create();

        // Sélectionner le premier post pour obtenir son ID
        $firstPostId = $posts->first()->id;

        // Récupérer la valeur de active avant d'appeler allowPost
        $originalActiveValue = $posts->first()->active;

        // Envoyer une requête POST pour activer le post
        $response = $this->post(route('allow.post', ['id' => $firstPostId]));

        // Vérifier que la redirection a réussi vers la page de détail du post
        $response->assertRedirect(route('post.detail', ['id' => $firstPostId]));

        // Rafraîchir le post depuis la base de données pour obtenir sa dernière valeur active
        $posts->first()->refresh();

        // Vérifier que la redirection a réussi vers la page de détail du post
        $response->assertRedirect(route('post.detail', ['id' => $firstPostId]));

        // Vérifier que la valeur de active a changé
        $this->assertNotEquals($originalActiveValue, $posts->first()->active);
    }

    /** @test */
    public function it_can_delete_a_post_with_media_and_associations()
    {
        // Préparer les données nécessaires
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authentification en tant qu'utilisateur admin

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
        

        // Récupérez le post créé depuis la réponse
        $postId = $response->json('data.id');
        // dd($postId);
        
        $post = Post::find($postId);
        

        // Simuler la suppression du post via la route destroy
        $deleteResponse = $this->delete(route('posts.destroy', $post->id));

        // Vérifier que le post a été supprimé
        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        // Vérifier que les médias associés ont été supprimés
        foreach ($post->medias as $media) {
            $this->assertDatabaseMissing('media', ['id' => $media->id]);
        }

        // Vérifier que la redirection s'est faite correctement
        $deleteResponse->assertRedirect(route('posts.index'));

        // Vérifier le message de succès
        $deleteResponse->assertSessionHas('success', 'Post deleted successfully !');
    }
    
}