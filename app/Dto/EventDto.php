<?php

namespace App\Dto;

use App\Models\Event;

class EventDto
{
    public function __construct(
        private ?string $id = null,               // guid, обязательное поле
        private ?string $clientId = null,         // guid, обязательное поле
        private ?string $companyId = null,        // guid, обязательное поле
        private ?string $name = null,             // Название события
        private ?string $description = null,     // Необязательное описание
        private ?string $eventType = null,       // Необязательный тип события
        private ?string $eventTime = null,        // Время события, обязательное
        private ?string $repeatType = null,      // Частота повторения, не обязательное поле
    ) {}

    // Геттеры и сеттеры

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): EventDto
    {
        $this->id = $id;

        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): EventDto
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    public function setCompanyId(string $companyId): EventDto
    {
        $this->companyId = $companyId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): EventDto
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): EventDto
    {
        $this->description = $description;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): EventDto
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getEventTime(): string
    {
        return $this->eventTime;
    }

    public function setEventTime(string $eventTime): EventDto
    {
        $this->eventTime = $eventTime;

        return $this;
    }

    public function getRepeatType(): ?string
    {
        return $this->repeatType;
    }

    public function setRepeatType(?string $repeatType): EventDto
    {
        $this->repeatType = $repeatType;

        return $this;
    }

    // Метод для преобразования в массив (например, для передачи в репозиторий или возвращения в ответ)
    public function toModelEventArray(): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->clientId,
            'name' => $this->name,
            'description' => $this->description,
            'event_type' => $this->eventType,
            'event_time' => $this->eventTime,
            'repeat_type' => $this->repeatType,
        ];
    }

    public static function makeFromModelEvent(Event $event): EventDto
    {
        return new self(
            $event->id,
            $event->client_id,
            $event->name,
            $event->description,
            $event->event_type,
            $event->event_time,
            $event->repeat_type,
        );
    }
}
