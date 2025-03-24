<?php

namespace App\Http\Controllers\Api\V1;

use App\Dto\EventDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventRequest;
use App\Http\Resources\Event\EventCollection;
use App\Http\Resources\Event\EventResource;
use App\Http\Responses\ApiJsonResponse;
use App\Http\Services\EventService;
use App\Repositories\EventDbRepository;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    private EventService $service;

    public function __construct()
    {
        $this->service = new EventService(
            new EventDbRepository
        );
    }

    public function getEventsByClientId()
    {
        $clientId = Auth::user()->client->id;
        $events = $this->service->getEventsByClientId($clientId);

        return new ApiJsonResponse(data: new EventCollection($events));
    }

    public function getEventsByCompanyId()
    {
        $companyId = Auth::user()->company->id;
        $events = $this->service->getEventsByCompanyId($companyId);

        return new ApiJsonResponse(data: new EventCollection($events));
    }

    public function getEventById($id)
    {
        $event = $this->service->getEventById($id);
        $user = Auth::user();

        // Check if user has access to this event
        if ($event->getClientId() !== $user->client->id) {
            return new ApiJsonResponse(
                message: 'You do not have permission to access this event.',
                httpCode: 403
            );
        }

        return new ApiJsonResponse(data: new EventResource($event));
    }

    public function createEvent(EventRequest $request)
    {
        $eventDto = $request->toEventDto();
        $event = $this->service->createEvent($eventDto);

        return new ApiJsonResponse(data: new EventResource($event), httpCode: 201);
    }

    public function updateEvent(EventRequest $request, $id)
    {
        $eventDto = $request->toEventDto();
        $eventDto->setId($id);
        $event = $this->service->updateEvent($eventDto);

        return new ApiJsonResponse(data: new EventResource($event));
    }

    public function deleteEvent($id)
    {
        $eventDto = new EventDto;
        $eventDto->setId($id);
        $this->service->deleteEvent($eventDto);

        return new ApiJsonResponse(data: ['message' => 'Event deleted successfully.']);
    }

    public function cancelEvent($id)
    {
        $event = $this->service->getEventById($id);
        $user = Auth::user();

        // Check if user has access to this event through client relationship
        if ($event->getClientId() !== $user->client->id) {
            return new ApiJsonResponse(
                message: 'You do not have permission to cancel this event.',
                httpCode: 403
            );
        }

        $event->setIsCancelled(true);
        $updatedEvent = $this->service->cancelEvent($event);

        return new ApiJsonResponse(data: new EventResource($updatedEvent));
    }
}
