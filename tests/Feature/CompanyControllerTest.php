<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Company;
use App\Models\Zone;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyCreated;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_a_company_with_files()
    {
        Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');

        Zone::factory()->create();
        $zoneId = Zone::inRandomOrder()->first()->id;

        $profileImage = UploadedFile::fake()->image('profile.jpg');
        $officialDocument = UploadedFile::fake()->create('document.pdf');

        $data = [
            'company_name' => 'Test Company',
            'owner_name' => 'Test Owner',
            'description' => 'Test description',
            'email' => 'test@example.com',
            'phone' => '123-456-7890',
            'profile' => $profileImage,
            'official_document' => $officialDocument,
            'zone_id' => $zoneId,
        ];

        Mail::fake();

        $response = $this->postJson('/api/create/request', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('companies', [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
        ]);

        $profilePath = 'company_profile_pictures/' . time() . '.' . $profileImage->getClientOriginalExtension();
        $documentPath = 'company_official_document/' . time() . '.' . $officialDocument->getClientOriginalExtension();

        Storage::disk(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3')->assertExists($profilePath);
        Storage::disk(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3')->assertExists($documentPath);

        Mail::assertSent(CompanyCreated::class, function ($mail) use ($data) {
            return $mail->hasTo($data['email']);
        });
    }
}