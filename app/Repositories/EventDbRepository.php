<?php

namespace App\Repositories;

use App\Contracts\EventRepositoryContract;
use App\Dto\EventDto;
use App\Dto\EventDtos;
use App\Models\Event;
use App\Models\EventService;

class EventDbRepository implements EventRepositoryContract
{
    public function list(): EventDtos
    {
        $events = Event::all();

        return $this->mapToEventDtos($events);
    }

    public function getByClientId(string $clientId): EventDtos
    {
        $events = Event::where('client_id', $clientId)->get();

        return $this->mapToEventDtos($events);
    }

    public function getByCompanyId(string $companyId): EventDtos
    {
        $events = Event::where('company_id', $companyId)->get();

        return $this->mapToEventDtos($events);
    }

    public function getById(string $id): EventDto
    {
        $event = Event::findOrFail($id);

        return $this->mapToEventDto($event);
    }

    public function create(EventDto $eventDto): EventDto
    {
        $event = Event::create($eventDto->toModelEventArray());
        
        if ($eventDto->getServiceIds()) {
            foreach ($eventDto->getServiceIds() as $serviceId) {
                EventService::create([
                    'event_id' => $event->id,
                    'service_id' => $serviceId
                ]);
            }
        }
        
        return $this->mapToEventDto($event->fresh()->load('services'));
    }

    public function update(EventDto $eventDto): EventDto
    {
        $event = Event::findOrFail($eventDto->getId());
        $event->update($eventDto->toModelEventArray());
        
        if ($eventDto->getServiceIds()) {
            $event->services()->delete();
            foreach ($eventDto->getServiceIds() as $serviceId) {
                EventService::create([
                    'event_id' => $event->id,
                    'service_id' => $serviceId
                ]);
            }
        }
        
        return $this->mapToEventDto($event->fresh()->load('services'));
    }

    public function delete(EventDto $eventDto): void
    {
        $event = Event::findOrFail($eventDto->getId());
        $event->delete();
    }

    public function cancelEvent(EventDto $eventDto): EventDto
    {
        $event = Event::findOrFail($eventDto->getId());
        $event->update(['is_cancelled' => true]);
        
        return $this->mapToEventDto($event->fresh()->load('services'));
    }

    private function mapToEventDtos($events): EventDtos
    {
        return new EventDtos($events->map(fn ($event) => $this->mapToEventDto($event)));
    }

    private function mapToEventDto($event): EventDto
    {
        return EventDto::makeFromModelEvent($event);
    }
}
