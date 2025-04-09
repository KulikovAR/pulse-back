<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRepeat;

class EventScheduleService
{
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
                $event->createNewRepeat();
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
                $repeat->event->createNewRepeat();
            }
        }
    }

    protected function sendNotification($event)
    {
        // Здесь реализация отправки уведомления
    }
}