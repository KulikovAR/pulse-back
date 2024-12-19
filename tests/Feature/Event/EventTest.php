<?php

namespace Tests\Feature\Event;

use App\Models\Client;
use App\Models\Company;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем пользователя
        $user = User::factory()->create();

        // Аутентифицируем его
        $this->actingAs($user);
    }

    public function test_it_fetches_events_by_client_id()
    {
        // Arrange: создаем клиента и связанные с ним события
        $client = Client::factory()->create();
        $events = Event::factory()->count(3)->create(['client_id' => $client->id]);

        // Act: отправляем GET-запрос
        $response = $this->getJson("/api/v1/events/{$client->id}");

        //debug
        $responseData = $response->json();
        dd($responseData); // Это выведет структуру данных

        // Assert: проверяем статус ответа
        $response->assertStatus(200);

        // Проверяем, что данные в 'data' — это объекты EventDto
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData);

        // Проверка, что каждый элемент в данных — это объект EventDto
        foreach ($responseData as $eventData) {
            $this->assertArrayHasKey('id', $eventData);
            $this->assertArrayHasKey('client_id', $eventData);
            $this->assertArrayHasKey('name', $eventData);
            $this->assertArrayHasKey('description', $eventData);
            $this->assertArrayHasKey('event_time', $eventData);
            $this->assertArrayHasKey('repeat_type', $eventData);
        }
    }

    // public function test_it_creates_an_event()
    // {
    //     // Arrange: создаем клиента и компанию
    //     $client = Client::factory()->create();
    //     $company = Company::factory()->create();
    //     $payload = [
    //         'id' => \Illuminate\Support\Str::uuid(),
    //         'client_id' => $client->id,
    //         'company_id' => $company->id,
    //         'name' => 'Test Event',
    //         'description' => 'Description for the test event',
    //         'event_type' => 'meeting',
    //         'event_time' => now()->addMinutes(10)->toDateTimeString(),
    //         'repeat_type' => 'daily',
    //     ];

    //     // Act: отправляем POST-запрос
    //     $response = $this->postJson('/api/v1/event', $payload);

    //     // Assert: проверяем успешное создание
    //     $response->assertStatus(201);

    //     // Проверяем, что объект EventDto в ответе
    //     $responseData = $response->json('data');
    //     $this->assertArrayHasKey('id', $responseData);
    //     $this->assertEquals($payload['name'], $responseData['name']);
    //     $this->assertDatabaseHas('events', ['name' => 'Test Event']);
    // }

    // public function test_it_updates_an_event()
    // {
    //     // Arrange: создаем событие
    //     $event = Event::factory()->create();
    //     $payload = ['name' => 'Updated Event Name'];

    //     // Act: отправляем PUT-запрос
    //     $response = $this->putJson("/api/v1/event/{$event->id}", $payload);

    //     // Assert: проверяем обновление
    //     $response->assertStatus(200);

    //     // Проверяем, что объект EventDto в ответе
    //     $responseData = $response->json('data');
    //     $this->assertArrayHasKey('id', $responseData);
    //     $this->assertEquals('Updated Event Name', $responseData['name']);
    //     $this->assertDatabaseHas('events', ['id' => $event->id, 'name' => 'Updated Event Name']);
    // }

    // public function test_it_deletes_an_event()
    // {
    //     // Arrange: создаем событие
    //     $event = Event::factory()->create();

    //     // Act: отправляем DELETE-запрос
    //     $response = $this->deleteJson("/api/v1/event/{$event->id}");

    //     // Assert: проверяем удаление
    //     $response->assertStatus(204);
    //     $this->assertDatabaseMissing('events', ['id' => $event->id]);
    // }
}
