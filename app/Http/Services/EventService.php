<?php

namespace App\Http\Services;

use App\Contracts\EventRepositoryContract;
use App\Dto\EventDto;
use App\Dto\EventDtos;

class EventService
{
    public function __construct(
        private EventRepositoryContract $repository
    ) {}

    public function listEvents(): EventDtos
    {
        return $this->repository->list();
    }

    public function getEventsByClientId(string $clientId): EventDtos
    {
        return $this->repository->getByClientId($clientId);
    }

    public function getEventsByCompanyId(string $companyId): EventDtos
    {
        return $this->repository->getByCompanyId($companyId);
    }

    public function getEventById(string $id): EventDto
    {
        return $this->repository->getById($id);
    }

    public function createEvent(EventDto $eventDto): EventDto
    {
        return $this->repository->create($eventDto);
    }

    public function updateEvent(EventDto $eventDto): EventDto
    {
        return $this->repository->update($eventDto);
    }

    public function deleteEvent(EventDto $eventDto): void
    {
        $this->repository->delete($eventDto);
    }

    public function cancelEvent(EventDto $eventDto): EventDto
    {
        return $this->repository->cancelEvent($eventDto);
    }
}
