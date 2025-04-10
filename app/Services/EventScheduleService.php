<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRepeat;
use App\Http\Services\TelegramService;
use App\Dto\EventDto;

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