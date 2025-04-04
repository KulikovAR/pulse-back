<?php

namespace App\Http\Controllers\Api\V1;

use App\Dto\EventDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventRequest;
use App\Http\Resources\Event\EventCollection;
use App\Http\Resources\Event\EventResource;
use App\Http\Responses\ApiJsonResponse;
use App\Http\Services\EventService;
use App\Http\Services\TelegramService;
use App\Repositories\EventDbRepository;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    private EventService $service;
    private TelegramService $telegramService;

    public function __construct()
    {
        $this->service = new EventService(new EventDbRepository);
        $this->telegramService = new TelegramService();
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
        $createdEventDto = $this->service->createEvent($eventDto);
        
        // Send notification with DTO
        $this->telegramService->sendNewEventNotification($createdEventDto);
    
        return new ApiJsonResponse(data: new EventResource($createdEventDto), httpCode: 201);
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
    
        // Проверки прав доступа
        $isClient = $user->client && $event->getClientId() === $user->client->id;
        $isCompanyOwner = isset($event->getCompany()['user_id']) 
            && $event->getCompany()['user_id'] === $user->id;
    
        if (!$isClient && !$isCompanyOwner) {
            return new ApiJsonResponse(
                message: 'You do not have permission to cancel this event.',
                httpCode: 403
            );
        }
    
        $event->setStatus('cancelled');
        $updatedEvent = $this->service->cancelEvent($event);
    
        // Отправка уведомлений
        if ($isClient) {
            $this->telegramService->sendEventCancelledByClientNotification($updatedEvent);
        } elseif ($isCompanyOwner) {
            $this->telegramService->sendEventCancelledByCompanyNotification($updatedEvent);
        }
    
        return new ApiJsonResponse(data: new EventResource($updatedEvent));
    }
}
