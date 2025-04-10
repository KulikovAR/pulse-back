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