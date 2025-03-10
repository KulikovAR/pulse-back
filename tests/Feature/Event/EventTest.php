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

    public function test_get_events_by_client_id()
    {
        // Создание клиента и событий для него
        $client = Client::factory()->create();
        $events = Event::factory()->count(3)->create(['client_id' => $client->id]);

        // Отправляем GET-запрос
        $response = $this->getJson("/api/v1/events/{$client->id}");

        // Проверяем, что запрос прошел успешно
        $response->assertStatus(200);

        // Проверяем структуру данных
        $response->assertJsonCount(3, 'data');  // должно быть 3 события
    }

    public function test_get_events_by_company_id()
    {
        // Создание компании и событий для нее
        $company = Company::factory()->create();
        $events = Event::factory()->count(3)->create(['company_id' => $company->id]);

        // Отправляем GET-запрос
        $response = $this->getJson("/api/v1/events/company/{$company->id}");
        
        // Проверяем успешный статус ответа
        $response->assertStatus(200);

        // Проверяем структуру данных
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'event_type',
                    'event_time',
                    'repeat_type'
                ]
            ]
        ]);

        // Проверяем количество событий
        $response->assertJsonCount(3, 'data');
    }

    public function test_get_event_by_id()
    {
        // Создание события
        $event = Event::factory()->create();

        // Отправляем GET-запрос
        $response = $this->getJson("/api/v1/event/{$event->id}");

        // Проверяем, что запрос прошел успешно
        $response->assertStatus(200);

        // Проверяем, что данные в ответе соответствуют событию
        $response->assertJsonFragment([
            'id' => $event->id,
            'name' => $event->name,
        ]);
    }

    public function test_create_event()
    {
        // Подготовка данных для создания события
        $client = Client::factory()->create();
        $payload = [
            'client_id' => $client->id,
            'company_id' => Company::factory()->create()->id,
            'name' => 'Test Event',
            'event_type' => 'meeting',
            'event_time' => now()->addDay()->toDateTimeString(),
            'repeat_type' => 'daily',
        ];

        // Отправляем POST-запрос
        $response = $this->postJson('/api/v1/event', $payload);

        // Проверяем, что запрос прошел успешно
        $response->assertStatus(201);

        // Проверяем, что событие создано
        $response->assertJsonFragment([
            'name' => 'Test Event',
            'event_type' => 'meeting',
        ]);
    }

    public function test_update_event()
    {
        // Создание события
        $event = Event::factory()->create();

        // Данные для обновления
        $payload = [
            'client_id' => $event->client_id,
            'company_id' => $event->company_id,
            'name' => 'Updated Event',
            'event_type' => 'task',
            'event_time' => now()->addDays(2)->toDateTimeString(),
            'repeat_type' => 'weekly',
        ];

        // Отправляем PUT-запрос
        $response = $this->putJson("/api/v1/event/{$event->id}", $payload);

        // Проверяем, что запрос прошел успешно
        $response->assertStatus(200);

        // Проверяем, что данные обновлены
        $response->assertJsonFragment([
            'name' => 'Updated Event',
            'event_type' => 'task',
        ]);
    }

    public function test_delete_event()
    {
        // Создание события
        $event = Event::factory()->create();

        // Отправляем DELETE-запрос
        $response = $this->deleteJson("/api/v1/event/{$event->id}");

        // Проверяем, что запрос прошел успешно
        $response->assertStatus(200);

        // Проверяем, что событие удалено
        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }
}
