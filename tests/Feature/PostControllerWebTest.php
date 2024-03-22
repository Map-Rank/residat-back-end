<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Zone;
use App\Models\TypeInteraction;
use Illuminate\Support\Facades\DB;
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
            $user = User::factory()->create();
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
            $user = User::factory()->create();
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
            $user = User::factory()->create();
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

    // D'autres fonctions de test pour les méthodes create(), store(), edit(), update(), destroy() peuvent être ajoutées selon les besoins.
}