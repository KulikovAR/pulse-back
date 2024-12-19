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
        return new EventDtos(Event::all()->map(fn ($event) => EventDto::makeFromModelEvent($event)));
    }

    public function getByClientId(string $clientId): EventDtos
    {
        $events = Event::where('client_id', $clientId)->get();

        return new EventDtos($events->map(fn ($event) => EventDto::makeFromModelEvent($event)));
    }

    public function getById(string $id): EventDto
    {
        $event = Event::findOrFail($id);

        return EventDto::makeFromModelEvent($event);
    }

    public function create(EventDto $eventDto): EventDto
    {
        $event = Event::create($eventDto->toModelEventArray());

        return EventDto::makeFromModelEvent($event);
    }

    public function update(EventDto $eventDto): EventDto
    {
        $event = Event::findOrFail($eventDto->id);
        $event->update($eventDto->toModelEventArray());

        return EventDto::makeFromModelEvent($event);
    }

    public function delete(EventDto $eventDto): void
    {
        $event = Event::findOrFail($eventDto->id);
        $event->delete();
    }
}
