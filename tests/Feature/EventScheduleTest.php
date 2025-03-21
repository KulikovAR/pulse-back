<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use Carbon\Carbon;

class EventScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_event_creates_repeat()
    {
        // Создаем тестовые данные
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $client = Client::factory()->create(['user_id' => $user->id]);

        // Создаем событие на текущее время с еженедельным повтором
        $event = Event::factory()->create([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'event_time' => Carbon::now(),
            'repeat_type' => 'WEEKLY'
        ]);

        // Запускаем команду
        $this->artisan('events:process');

        // Проверяем, что создался повтор события
        $this->assertDatabaseHas('event_repeats', [
            'event_id' => $event->id,
            'event_time' => Carbon::parse($event->event_time)->addWeek()
        ]);

        // Очищаем после теста
        $event->delete();
        $client->delete();
        $company->delete();
        $user->delete();
    }

    public function test_repeat_event_processing()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $client = Client::factory()->create(['user_id' => $user->id]);

        $event = Event::factory()->create([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'event_time' => Carbon::now()->subWeek(),
            'repeat_type' => 'WEEKLY'
        ]);

        $currentTime = Carbon::now();
        $eventTime = $currentTime->format('Y-m-d H:i:s');

        // Создаем повтор на текущее время
        $repeat = $event->repeats()->create([
            'event_time' => $eventTime
        ]);

        // Запускаем команду
        $this->artisan('events:process');

        // Даем время на обработку
        sleep(1);

        // Проверяем, что старый повтор был удален
        $this->assertDatabaseMissing('event_repeats', [
            'id' => $repeat->id,
            'event_time' => $eventTime
        ]);

        // Проверяем, что создан новый повтор на следующую неделю
        $expectedNextTime = Carbon::parse($event->event_time)->addWeek()->format('Y-m-d H:i:s');
        $this->assertDatabaseHas('event_repeats', [
            'event_id' => $event->id,
            'event_time' => $expectedNextTime
        ]);

        // Очищаем после теста
        $event->repeats()->delete();
        $event->delete();
        $client->delete();
        $company->delete();
        $user->delete();
    }

    public function test_specific_date_event()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $client = Client::factory()->create(['user_id' => $user->id]);
    
        $targetTime = Carbon::now()->addDays(2)->setHour(8)->setMinute(0)->setSecond(0);
        
        $event = Event::factory()->create([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'event_time' => Carbon::now(),
            'target_time' => $targetTime,
            'repeat_type' => null
        ]);
    
        $this->artisan('events:process');

        // Даем время на обработку
        sleep(1);
    
        // Проверяем, что создался повтор на конкретную дату
        $this->assertDatabaseHas('event_repeats', [
            'event_id' => $event->id,
            'event_time' => $targetTime
        ]);
    
        // Очистка
        $event->repeats()->delete();
        $event->delete();
        $client->delete();
        $company->delete();
        $user->delete();
    }
}