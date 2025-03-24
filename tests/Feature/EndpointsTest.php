<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Event;
use App\Models\User;
use App\Models\Client;
use App\Models\CompanyClient;
use App\Models\Service;
use Tests\TestCase;

class EndpointsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_events_by_company_id()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
        ]);
        $anotherCompany = Company::factory()->create();

        $client = Client::factory()->create();
        $companyClient = CompanyClient::factory()->create([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'name' => 'Test GET'
        ]);
        
        // Create events for the first company
        $events = Event::factory()->count(3)->create([
            'company_id' => $company->id,
            'client_id' => $client->id,
        ]);
        
        // Create events for another company
        $anotherEvents = Event::factory()->count(2)->create([
            'company_id' => $anotherCompany->id,
        ]);
       
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/events/company');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'description',
                            'event_type',
                            'event_time',
                            'repeat_type'
                        ]
                    ]
                ])
                ->assertJsonCount(3, 'data');

        // Verify that only events from the user's company are returned
        $eventIds = collect($response->json('data'))->pluck('id');
        foreach ($events as $event) {
            $this->assertTrue($eventIds->contains($event->id));
        }
        foreach ($anotherEvents as $event) {
            $this->assertFalse($eventIds->contains($event->id));
        }
    }

    public function test_can_get_events_by_token()
    {
        $company = Company::factory()->create();
        $client = Client::factory()->create();

        $events = Event::factory()->count(3)->create([
            'company_id' => $company->id,
            'client_id' => $client->id,
        ]);
        $anotherClient = Client::factory()->create();
        $anotherEvents = Event::factory()->count(3)->create([
            'company_id' => $company->id,
            'client_id' => $anotherClient->id,
        ]);
        
        $token = $client->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/events');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'description',
                            'event_type',
                            'event_time',
                            'repeat_type'
                        ]
                    ]
                ]);
    }

    public function test_can_get_events_by_id()
    {
        $company = Company::factory()->create();
        $client = Client::factory()->create();

        $event = Event::factory()->create([
            'company_id' => $company->id,
            'client_id' => $client->id,
        ]);
        
        $token = $client->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/v1/event/{$event->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'description',
                        'event_type',
                        'event_time',
                        'repeat_type'
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'id' => $event->id,
                        'client_id' => $client->id,
                        'company_id' => $company->id
                    ]
                ]);
    }

    public function test_can_get_companies_by_client_id()
    {
        $client = Client::factory()->create();
        $company = Company::factory()->create();
        $anotherCompany = Company::factory()->create();

        // Create events for the client with the first company
        Event::factory()->count(2)->create([
            'company_id' => $company->id,
            'client_id' => $client->id,
        ]);

        // Create events for another client with another company
        $anotherClient = Client::factory()->create();
        Event::factory()->create([
            'company_id' => $anotherCompany->id,
            'client_id' => $anotherClient->id,
        ]);

        $token = $client->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/companies/client');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description'
                        ]
                    ]
                ])
                ->assertJsonCount(1, 'data');

        $this->assertEquals($company->id, $response->json('data.0.id'));
    }

    public function test_can_get_services()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Create services for the user's company
        $services = Service::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);
        
        // Create services for another company
        $anotherCompany = Company::factory()->create();
        $otherServices = Service::factory()->count(2)->create([
            'company_id' => $anotherCompany->id,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/services');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ]
                ])
                ->assertJsonCount(3, 'data');

        // Verify that only services from the user's company are returned
        $serviceIds = collect($response->json('data'))->pluck('id');
        foreach ($services as $service) {
            $this->assertTrue($serviceIds->contains($service->id));
        }
        foreach ($otherServices as $service) {
            $this->assertFalse($serviceIds->contains($service->id));
        }
    }

    public function test_can_create_service()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $serviceData = [
            'name' => 'Test Service',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/services', $serviceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                    ]
                ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Test Service',
            'company_id' => $company->id
        ]);
    }

    public function test_can_delete_service()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $service = Service::factory()->create([
            'company_id' => $company->id,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/v1/services/{$service->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id
        ]);
    }

    public function test_cannot_delete_service_from_another_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Create service for another company
        $anotherCompany = Company::factory()->create();
        $service = Service::factory()->create([
            'company_id' => $anotherCompany->id,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/v1/services/{$service->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('services', [
            'id' => $service->id
        ]);
    }

    public function test_can_create_company()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $companyData = [
            'user_id' => $user->id,
            'name' => 'Test Company',
            'description' => 'Test Company Description',
            'image' => 'https://example.com/image.jpg',
            'category' => 'Test Category',
            'address' => 'Test Address'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'user_id',
                        'name',
                        'description',
                        'image',
                        'category',
                        'address'
                    ]
                ]);

        $this->assertDatabaseHas('companies', [
            'user_id' => $user->id,
            'name' => 'Test Company',
            'description' => 'Test Company Description',
            'category' => 'Test Category',
            'address' => 'Test Address'
        ]);
    }

    public function test_can_update_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Company',
            'description' => 'Updated Description',
            'category' => 'Updated Category',
            'address' => 'Updated Address'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/v1/companies/{$company->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'user_id',
                        'name',
                        'description',
                        'image',
                        'category',
                        'address'
                    ]
                ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company',
            'description' => 'Updated Description',
            'category' => 'Updated Category',
            'address' => 'Updated Address'
        ]);
    }

    public function test_cannot_update_another_users_company()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $anotherUser->id
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Company',
            'description' => 'Updated Description',
            'category' => 'Updated Category',
            'address' => 'Updated Address'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/v1/companies/{$company->id}", $updateData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
            'name' => 'Updated Company'
        ]);
    }

    public function test_can_delete_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id
        ]);
    }

    public function test_cannot_delete_another_users_company()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $anotherUser->id
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id
        ]);
    }

    public function test_can_create_client_when_client_exists()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
        $phone = $this->faker->numerify('+7#########');
        $existingClient = Client::factory()->create([
            'phone' => $phone
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $clientData = [
            'name' => 'Test Client',
            'phone' => $phone
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/v1/clients", $clientData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'verify',
                        'client_id',
                        'company_id'
                    ]
                ]);

        $this->assertDatabaseHas('company_client', [
            'name' => 'Test Client',
            'client_id' => $existingClient->id,
            'company_id' => $company->id
        ]);
    }

    public function test_can_create_client_when_user_exists_with_client()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
        
        $phone = $this->faker->numerify('+7#########');
        $existingUser = User::factory()->create([
            'phone' => $phone
        ]);
        $existingClient = Client::factory()->create([
            'user_id' => $existingUser->id,
            'phone' => null,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $clientData = [
            'name' => 'Test Client',
            'phone' => $phone
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/v1/clients", $clientData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'verify',
                        'client_id',
                        'company_id'
                    ]
                ]);

        $this->assertDatabaseHas('company_client', [
            'name' => 'Test Client',
            'client_id' => $existingClient->id,
            'company_id' => $company->id
        ]);
    }

    public function test_can_create_new_client()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
    
        $token = $user->createToken('test-token')->plainTextToken;
    
        $clientData = [
            'name' => 'New Test Client',
            'phone' => $this->faker->numerify('+7#########')
        ];
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/v1/clients", $clientData);
    
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'verify',
                        'client_id',
                        'company_id'
                    ]
                ]);
    
        $this->assertDatabaseHas('clients', [
            'name' => 'New Test Client',
            'phone' => $clientData['phone'],
            'user_id' => null
        ]);
    
        $client = Client::where('phone', $clientData['phone'])->first();
        $this->assertDatabaseHas('company_client', [
            'name' => 'New Test Client',
            'client_id' => $client->id,
            'company_id' => $company->id
        ]);
    }

    public function test_can_get_clients_by_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);

        // Create clients for the user's company
        $clients = Client::factory()->count(3)->create();
        foreach ($clients as $client) {
            CompanyClient::factory()->create([
                'client_id' => $client->id,
                'company_id' => $company->id,
                'name' => 'Test Client ' . $client->id
            ]);
        }

        // Create clients for another company
        $anotherCompany = Company::factory()->create();
        $otherClients = Client::factory()->count(2)->create();
        foreach ($otherClients as $client) {
            CompanyClient::factory()->create([
                'client_id' => $client->id,
                'company_id' => $anotherCompany->id,
                'name' => 'Other Client ' . $client->id
            ]);
        }

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/clients');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'phone',
                            'created_at',
                            'updated_at',
                            'company_client' => [
                                'custom_name'
                            ]
                        ]
                    ]
                ])
                ->assertJsonCount(3, 'data');

        // Verify that only clients from the user's company are returned
        $clientIds = collect($response->json('data'))->pluck('id');
        foreach ($clients as $client) {
            $this->assertTrue($clientIds->contains($client->id));
        }
        foreach ($otherClients as $client) {
            $this->assertFalse($clientIds->contains($client->id));
        }
    }

    public function test_can_delete_client()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
        $client = Client::factory()->create();
        $companyClient = CompanyClient::factory()->create([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'name' => 'Test Client'
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('company_client', [
            'client_id' => $client->id,
            'company_id' => $company->id
        ]);
    }

    public function test_cannot_delete_client_from_another_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id
        ]);
        
        $anotherCompany = Company::factory()->create();
        $client = Client::factory()->create();
        $companyClient = CompanyClient::factory()->create([
            'client_id' => $client->id,
            'company_id' => $anotherCompany->id,
            'name' => 'Test Client'
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('company_client', [
            'client_id' => $client->id,
            'company_id' => $anotherCompany->id
        ]);
    }

    public function test_can_create_event()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create([
            'user_id' => $user->id,
        ]);
        $client = Client::factory()->create();

        $companyClient = CompanyClient::factory()->create([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'name' => 'Test Client'
        ]);
        
        // Create multiple services
        $services = Service::factory()->count(3)->create([
            'company_id' => $company->id,
            'name' => 'Test Service %d'
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $eventData = [
            'service_ids' => $services->pluck('id')->toArray(),
            'description' => 'Test Event Description',
            'event_type' => 'meeting',
            'event_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'repeat_type' => 'weekly',
            'client_id' => $client->id
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/event', $eventData);
        

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'services' => [
                            '*' => [
                                'id',
                                'name',
                                'company_id'
                            ]
                        ],
                        'description',
                        'event_type',
                        'event_time',
                        'repeat_type',
                        'company_id',
                        'client_id'
                    ]
                ]);

        // Verify the response contains all services data
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData['services']);
        foreach ($services as $index => $service) {
            $this->assertEquals($service->id, $responseData['services'][$index]['id']);
            $this->assertEquals($service->name, $responseData['services'][$index]['name']);
            $this->assertEquals($service->company_id, $responseData['services'][$index]['company_id']);
        }

        $this->assertDatabaseHas('events', [
            'description' => 'Test Event Description',
            'event_type' => 'meeting',
            'repeat_type' => 'weekly',
            'company_id' => $company->id,
            'client_id' => $client->id
        ]);

        // Verify all event_services pivot records were created correctly
        $event = Event::latest()->first();
        foreach ($services as $service) {
            $this->assertDatabaseHas('event_services', [
                'event_id' => $event->id,
                'service_id' => $service->id
            ]);
        }

        // Verify all relationships are loaded correctly
        foreach ($services as $service) {
            $this->assertTrue($event->services->contains($service));
        }
    }

    public function test_cannot_create_event_without_authentication()
    {
        $client = Client::factory()->create();

        $eventData = [
            'name' => 'Test Event',
            'description' => 'Test Event Description',
            'event_type' => 'online',
            'event_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'repeat_type' => 'none',
            'client_id' => $client->id,
        ];

        $response = $this->postJson('/api/v1/event', $eventData);

        $response->assertStatus(401);
    }
}