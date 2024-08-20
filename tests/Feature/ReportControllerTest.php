<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Zone;
use App\Models\Report;
use App\Models\Vector;
use App\Models\VectorKey;
use App\Models\ReportItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    // /**
    //  * Test listing reports with filters.
    //  *
    //  * @return void
    //  */
    // public function test_index_reports()
    // {
    //     // **Prepare user and necessary data:**
    //     $user = User::first();

    //     // Si aucun utilisateur n'existe, créez-en un
    //     if (!$user) {
    //         $user = User::factory()->admin()->create();
    //     }
        
    //     $this->actingAs($user, 'sanctum');

    //     // Create a report
    //     $zone = Zone::factory()->create();
    //     $report = Report::factory()
    //             ->withVector()
    //             ->withItems()
    //             ->create([
    //                 'zone_id' => $zone->id,
    //                 'user_id' => $user->id,
    //             ]);

    //     // Fetch reports with filters
    //     $response = $this->getJson('/api/reports?zone_id=' . $zone->id);

    //     $response->assertStatus(200);
    // }
}