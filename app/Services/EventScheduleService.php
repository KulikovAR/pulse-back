<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRepeat;
use App\Http\Services\TelegramService;
use App\Dto\EventDto;
use App\Models\Client;

class EventScheduleService
{
    public function __construct(
        private TelegramService $telegramService
    ) {}

    public function processEvents()
    {
        $this->processMainEvents();
        $this->processRepeatEvents();
        $this->processReminders();
        $this->processDailySchedule();
    }

    protected function processDailySchedule()
    {
        $mskTime = now()->setTimezone('Europe/Moscow');
        if ($mskTime->format('H:i') !== '09:00') {
            return;
        }

        $clients = Client::all();
        
        foreach ($clients as $client) {
            $events = Event::where('client_id', $client->id)
                ->where('event_time', '>=', now()->startOfDay())
                ->where('event_time', '<=', now()->endOfDay())
                ->where('status', '!=', 'cancelled')
                ->get();

            $repeats = EventRepeat::where('event_time', '>=', now()->startOfDay())
                ->where('event_time', '<=', now()->endOfDay())
                ->whereHas('event', function($query) use ($client) {
                    $query->where('client_id', $client->id)
                          ->where('status', '!=', 'cancelled');
                })
                ->get();

            $todayEvents = [];
            
            foreach ($events as $event) {
                $eventDto = EventDto::makeFromModelEvent($event);
                $todayEvents[] = [
                    'event_time' => $eventDto->getEventTime(),
                    'company' => $eventDto->getCompany(),
                    'services' => $eventDto->getServices()
                ];
            }

            foreach ($repeats as $repeat) {
                $eventDto = EventDto::makeFromModelEvent($repeat->event);
                $eventDto->setEventTime($repeat->event_time);
                $todayEvents[] = [
                    'event_time' => $eventDto->getEventTime(),
                    'company' => $eventDto->getCompany(),
                    'services' => $eventDto->getServices()
                ];
            }

            usort($todayEvents, function($a, $b) {
                return strcmp($a['event_time'], $b['event_time']);
            });

            if (!empty($todayEvents)) {
                $this->telegramService->sendDailySchedule($client, $todayEvents);
            }
        }
    }

    protected function processReminders()
    {
        $this->processDayBeforeReminders();
        $this->processHourBeforeReminders();
    }

    protected function processDayBeforeReminders()
    {
        // Получаем события за 24 часа до начала
        $events = Event::where('event_time', '>', now())
            ->where('event_time', '<', now()->addHours(24)->addMinutes(1))
            ->where('event_time', '>', now()->addHours(24))
            ->where('status', '!=', 'cancelled')
            ->get();

        $repeats = EventRepeat::where('event_time', '>', now())
            ->where('event_time', '<', now()->addHours(24)->addMinutes(1))
            ->where('event_time', '>', now()->addHours(24))
            ->whereHas('event', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->get();

        foreach ($events as $event) {
            $this->telegramService->sendDayBeforeReminder(EventDto::makeFromModelEvent($event));
        }

        foreach ($repeats as $repeat) {
            $eventDto = EventDto::makeFromModelEvent($repeat->event);
            $eventDto->setEventTime($repeat->event_time);
            $this->telegramService->sendDayBeforeReminder($eventDto);
        }
    }

    protected function processHourBeforeReminders()
    {
        // Получаем события за 1 час до начала
        $events = Event::where('event_time', '>', now())
            ->where('event_time', '<', now()->addHour()->addMinutes(1))
            ->where('event_time', '>', now()->addHour())
            ->where('status', '!=', 'cancelled')
            ->get();

        $repeats = EventRepeat::where('event_time', '>', now())
            ->where('event_time', '<', now()->addHour()->addMinutes(1))
            ->where('event_time', '>', now()->addHour())
            ->whereHas('event', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->get();

        foreach ($events as $event) {
            $this->telegramService->sendHourBeforeReminder(EventDto::makeFromModelEvent($event));
        }

        foreach ($repeats as $repeat) {
            $eventDto = EventDto::makeFromModelEvent($repeat->event);
            $eventDto->setEventTime($repeat->event_time);
            $this->telegramService->sendHourBeforeReminder($eventDto);
        }
    }

    protected function processMainEvents()
    {
        $events = Event::getActual();
        
        foreach ($events as $event) {
            $this->sendNotification($event);
            
            if (($event->repeat_type || $event->target_time) && $event->status !== 'cancelled') {
                $newRepeat = $event->createNewRepeat();
                // Создаем DTO из оригинального события, но с датой из повторения
                $eventDto = EventDto::makeFromModelEvent($event);
                $eventDto->setEventTime($newRepeat->event_time);
                $this->telegramService->sendEventRepeatNotification($eventDto);
            }
        }
    }

    protected function processRepeatEvents()
    {
        $repeats = EventRepeat::getActual();
        
        foreach ($repeats as $repeat) {
            $this->sendNotification($repeat->event);
            $repeat->delete();
            
            if ($repeat->event->repeat_type && $repeat->event->status !== 'cancelled') {
                $newRepeat = $repeat->event->createNewRepeat();
                // То же самое для повторений
                $eventDto = EventDto::makeFromModelEvent($repeat->event);
                $eventDto->setEventTime($newRepeat->event_time);
                $this->telegramService->sendEventRepeatNotification($eventDto);
            }
        }
    }

    protected function sendNotification($event)
    {
        // Здесь реализация отправки уведомления
    }
}