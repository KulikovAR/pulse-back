<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventRequest;
use App\Http\Responses\ApiJsonResponse;
use App\Http\Services\EventService;
use App\Repositories\EventDbRepository;

class EventController extends Controller
{
    private EventService $service;

    public function __construct()
    {
        $this->service = new EventService(
            new EventDbRepository
        );
    }

    // Получение всех событий по client_id
    // public function getEventsByClientId($clientId)
    // {
    //     return new ApiJsonResponse(data: $this->service->getEventsByClientId($clientId));
    // }

    public function getEventsByClientId($clientId)
    {
        $events = $this->service->getEventsByClientId($clientId);
        // Печать логов для всех событий
        foreach ($events as $event) {
            \Log::info('Детали события', [
                'event_id' => $event->getId(),
                'event_name' => $event->getName(),
                // другие свойства события...
            ]);
        }

        return new ApiJsonResponse(data: $events);
    }

    // Получение события по id
    public function getEventById($id)
    {
        return new ApiJsonResponse(data: $this->service->getEventById($id));
    }

    public function createEvent(EventRequest $request)
    {
        $eventDto = $request->toEventDto();

        return new ApiJsonResponse(data: $this->service->createEvent($eventDto));
    }

    public function updateEvent(EventRequest $request, $id)
    {
        $eventDto = $request->toEventDto();
        $eventDto->id = $id;

        return new ApiJsonResponse(data: $this->service->updateEvent($eventDto));
    }

    // Удаление события
    public function deleteEvent($id)
    {
        return new ApiJsonResponse(data: $this->service->deleteEvent($id));
    }
}
