<?php

namespace App\Contracts;

use App\Dto\EventDto;
use App\Dto\EventDtos;

interface EventRepositoryContract
{
    public function list(): EventDtos;

    public function getById(string $id): EventDto;

    public function create(EventDto $eventDto): EventDto;

    public function update(EventDto $eventDto): EventDto;

    public function delete(EventDto $eventDto): void;

    public function cancelEvent(EventDto $eventDto): EventDto;
    
    public function confirmEvent(EventDto $eventDto): EventDto;
    
    public function getByCompanyId(string $companyId): EventDtos;

    public function forceDelete(string $id): void;

    public function softDelete(string $id): void;
}
