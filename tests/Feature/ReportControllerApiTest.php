<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Report;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ReportControllerApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fetches_reports_without_filters()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);
        
        Report::factory()->count(15)->create();

        $response = $this->getJson(route('list.reports'));

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data'); // Default size = 10
    }

    /** @test */
    public function it_filters_reports_by_zone_id()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        $zone = Zone::factory()->create();

        Report::factory()->count(5)->create(['zone_id' => $zone->id]);
        Report::factory()->count(5)->create();

        $response = $this->getJson(route('list.reports', ['zone_id' => $zone->id]));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'description',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_filters_reports_by_start_date()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); 

        $startDate = Carbon::today();

        Report::factory()->create(['start_date' => $startDate]);
        Report::factory()->create(['start_date' => $startDate->copy()->subDay()]);

        $response = $this->getJson(route('list.reports', ['start_date' => $startDate->toDateString()]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_filters_reports_by_end_date()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        $endDate = Carbon::today();

        Report::factory()->create(['end_date' => $endDate]);
        Report::factory()->create(['end_date' => $endDate->copy()->addDay()]);

        $response = $this->getJson(route('list.reports', ['end_date' => $endDate->toDateString()]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_filters_reports_by_type()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);


        Report::factory()->create(['type' => 'DROUGHT']);
        Report::factory()->create(['type' => 'FLOOD']);

        $response = $this->getJson(route('list.reports', ['type' => 'DROUGHT']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_paginates_reports_correctly()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        Report::factory()->count(25)->create();

        // First page
        $response = $this->getJson(route('list.reports', ['page' => 0, 'size' => 10]));

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');

        // Second page
        $response = $this->getJson(route('list.reports', ['page' => 1, 'size' => 10]));

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');

        // Third page (remaining 5)
        $response = $this->getJson(route('list.reports', ['page' => 2, 'size' => 10]));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_returns_validation_errors_for_invalid_parameters()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        $response = $this->getJson(route('list.reports', ['page' => 'invalid', 'zone_id' => 99999]));

        $response->assertStatus(400)
            ->assertJsonStructure(['errors']);
    }
}
