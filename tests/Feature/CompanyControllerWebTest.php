<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyControllerWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method to ensure it returns the view with companies.
     *
     * @return void
     */
    public function test_index_returns_companies_view()
    {
        // Crée un utilisateur avec le rôle admin
        $user = User::factory()->admin()->create();
        
        // Agit en tant que cet utilisateur
        $this->actingAs($user);

        // Crée quelques entreprises
        Company::factory()->count(5)->create();

        $response = $this->get('/companies');

        $response->assertStatus(200)
                 ->assertViewIs('companies.index')
                 ->assertViewHas('companies');
    }

    /**
     * Test the show method to ensure it displays a specific company.
     *
     * @return void
     */
    public function test_show_displays_specific_company()
    {
        // Crée un utilisateur avec le rôle admin
        $user = User::factory()->admin()->create();
        
        // Agit en tant que cet utilisateur
        $this->actingAs($user);

        // Crée une entreprise
        $company = Company::factory()->create();

        $response = $this->get("/companies/{$company->id}");

        $response->assertStatus(200)
                 ->assertViewIs('companies.show')
                 ->assertViewHas('company', $company);
    }

    /**
     * Test the destroy method to ensure it deletes a company.
     *
     * @return void
     */
    public function test_destroy_deletes_company()
    {
        // Crée un utilisateur avec le rôle admin
        $user = User::factory()->admin()->create();
        
        // Agit en tant que cet utilisateur
        $this->actingAs($user);

        // Crée une entreprise
        $company = Company::factory()->create();

        $response = $this->delete("/companies/{$company->id}");

        $response->assertRedirect()
                 ->assertSessionHas('success', 'Company deleted successfully');

       // Vérifie que l'entreprise est marquée comme supprimée dans la base de données
       $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    // /**
    //  * Test the destroy method to ensure it handles unauthorized deletion.
    //  *
    //  * @return void
    //  */
    // public function test_destroy_handles_unauthorized_deletion()
    // {
    //     // Crée un utilisateur sans le rôle admin
    //     $user = User::factory()->create();
        
    //     // Agit en tant que cet utilisateur
    //     $this->actingAs($user);

    //     // Crée une entreprise
    //     $company = Company::factory()->create();

    //     $response = $this->delete("/companies/{$company->id}");
    //     dd($response);

    //     $response->assertSessionHas('error', 'Unauthorized deletion to this resource');

    //     // Assurez-vous que l'entreprise n'a pas été supprimée
    //     $this->assertDatabaseHas('companies', ['id' => $company->id]);
    // }

    /**
     * Test the destroy method to handle company not found.
     *
     * @return void
     */
    public function test_destroy_handles_company_not_found()
    {
        // Crée un utilisateur avec le rôle admin
        $user = User::factory()->admin()->create();
        
        // Agit en tant que cet utilisateur
        $this->actingAs($user);

        $response = $this->delete('/companies/999'); // ID qui n'existe pas

        $response->assertRedirect()
                 ->assertSessionHas('error', 'Company not found');
    }
}
