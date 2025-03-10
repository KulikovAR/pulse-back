<?php

namespace Tests\Feature\Service;

use App\Models\Company;
use App\Models\Service;
use App\Models\User;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_get_services_list()
    {
        // Create test services
        $services = Service::factory()->count(3)->create();

        // Send GET request
        $response = $this->getJson('/api/v1/services');

        // Assert successful response
        $response->assertStatus(200);

        // Assert data structure
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name']
            ]
        ]);

        // Assert all services are present in the response
        foreach ($services as $service) {
            $response->assertJsonFragment([
                'id' => $service->id,
                'name' => $service->name
            ]);
        }
    }

    public function test_get_service_by_id()
    {
        // Create a service
        $service = Service::factory()->create();

        // Send GET request
        $response = $this->getJson("/api/v1/services/{$service->id}");

        // Assert successful response
        $response->assertStatus(200);

        // Assert response data matches the service
        $response->assertJsonFragment([
            'id' => $service->id,
            'name' => $service->name,
        ]);
    }

    public function test_create_service()
    {
        // Prepare test data
        $company = Company::factory()->create();
        $payload = [
            'company_id' => $company->id,
            'name' => 'Test Service',
        ];

        // Send POST request
        $response = $this->postJson('/api/v1/services', $payload);

        // Assert successful creation
        $response->assertStatus(201);

        // Assert service was created with correct data and message
        $response->assertJson([
            'data' => [
                'name' => 'Test Service',
                'company_id' => $company->id
            ],
            'message' => 'Service created successfully'
        ]);

        // Assert service exists in database
        $this->assertDatabaseHas('services', [
            'name' => 'Test Service',
            'company_id' => $company->id,
        ]);
    }

    public function test_create_service_validation_error()
    {
        // Send POST request without required fields
        $response = $this->postJson('/api/v1/services', []);

        // Assert validation error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'company_id']);

        // Send POST request with invalid company_id
        $response = $this->postJson('/api/v1/services', [
            'name' => 'Test Service',
            'company_id' => 'invalid-uuid'
        ]);

        // Assert validation error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_id']);
    }

    public function test_update_service()
    {
        // Create a service
        $service = Service::factory()->create();

        // Prepare update data
        $payload = [
            'company_id' => $service->company_id,
            'name' => 'Updated Service Name',
        ];

        // Send PUT request
        $response = $this->putJson("/api/v1/services/{$service->id}", $payload);

        // Assert successful update
        $response->assertStatus(200);

        // Assert service was updated with correct data and message
        $response->assertJson([
            'data' => [
                'id' => $service->id,
                'name' => 'Updated Service Name',
                'company_id' => $service->company_id
            ],
            'message' => 'Service updated successfully'
        ]);

        // Assert changes in database
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service Name',
        ]);
    
    }

    public function test_delete_service()
    {
        // Create a service
        $service = Service::factory()->create();

        // Send DELETE request
        $response = $this->deleteJson("/api/v1/services/{$service->id}");

        // Assert successful deletion
        $response->assertStatus(200);

        // Assert success message
        $response->assertJson([
            'message' => 'Service deleted successfully'
        ]);

        // Assert service was deleted from database
        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }
}