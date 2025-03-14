<?php

namespace Tests\Feature\Company;

use App\Models\Client;
use App\Models\Company;
use App\Models\Service;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate user
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_can_get_companies_list()
    {
        // Create companies with associated services
        $companies = Company::factory()->count(3)->create();
        foreach ($companies as $company) {
            Service::factory()->count(2)->create([
                'company_id' => $company->id
            ]);
        }

        $response = $this->getJson('/api/v1/companies');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'name',
                        'description',
                        'image',
                        'category',
                        'address',
                        'created_at',
                        'updated_at',
                        'services' => [
                            '*' => [
                                'id',
                                'name',
                                'company_id'
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_create_company()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'name' => 'Test Company',
            'description' => 'Test Description',
            'image' => 'https://example.com/image.jpg',
            'category' => 'Test Category',
            'address' => 'Test Address 123',
        ];

        $response = $this->postJson('/api/v1/companies', $data);

        $response->assertCreated()
            ->assertJsonFragment($data);
    }

    public function test_can_show_company()
    {
        $company = Company::factory()->create();

        $response = $this->getJson("/api/v1/companies/{$company->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $company->id,
                    'user_id' => $company->user_id,
                    'name' => $company->name,
                    'description' => $company->description,
                    'image' => $company->image,
                    'category' => $company->category,
                    'address' => $company->address,
                ]
            ]);
    }

    public function test_can_update_company()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'name' => 'Updated Company',
            'description' => 'Updated Description',
            'image' => 'https://example.com/updated-image.jpg',
            'category' => 'Updated Category',
            'address' => 'Updated Address 456',
        ];

        $response = $this->putJson("/api/v1/companies/{$company->id}", $data);

        $response->assertOk()
            ->assertJsonFragment($data);
    }

    public function test_can_delete_company()
    {
        $company = Company::factory()->create();

        $response = $this->deleteJson("/api/v1/companies/{$company->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_validates_required_fields_for_store()
    {
        $response = $this->postJson('/api/v1/companies', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'user_id',
                'name',
                'description',
                'category',
                'address'
            ]);
    }

    public function test_validates_required_fields_for_update()
    {
        $company = Company::factory()->create();

        $response = $this->putJson("/api/v1/companies/{$company->id}", [
            'user_id' => 'invalid-uuid',
            'name' => '',
            'address' => ''
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'user_id',
                'name',
                'address'
            ]);
    }

    
}