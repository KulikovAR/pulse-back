<?php

namespace App\Repositories;

use App\Contracts\EventRepositoryContract;
use App\Dto\EventDto;
use App\Dto\EventDtos;
use App\Models\Event;

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

        return $this->mapToEventDto($event);
    }

    public function update(EventDto $eventDto): EventDto
    {
        $event = Event::findOrFail($eventDto->getId());
        $event->update($eventDto->toModelEventArray());

        return $this->mapToEventDto($event);
    }

    public function delete(EventDto $eventDto): void
    {
        $event = Event::findOrFail($eventDto->getId());
        $event->delete();
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
