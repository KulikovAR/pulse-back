<?php

namespace App\Dto;

use App\Models\Event;

class EventDto
{
    public function __construct(
        private ?string $id = null,
        private ?string $clientId = null,
        private ?string $companyId = null,
        private ?array $serviceIds = [],
        public ?array $services = [],
        private ?string $description = null,
        private ?string $eventType = null,
        private ?string $eventTime = null,
        private ?string $repeatType = null,
        private ?string $targetTime = null,
        public ?array $companyClient = [],
        public ?array $company = [],
        private ?string $status = 'unread',
        public ?array $client = [],
        public ?array $telegramClient = [],
        public ?array $repeats = [],
    ) {}

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

    public function getServiceIds(): array
    {
        return $this->serviceIds;
    }

    public function setServiceIds(array $serviceIds): EventDto
    {
        $this->serviceIds = $serviceIds;

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

    public function getTargetTime(): ?string
    {
        return $this->targetTime;
    }

    public function setTargetTime(?string $targetTime): EventDto
    {
        $this->targetTime = $targetTime;

        return $this;
    }
    
    public function getServices(): ?array
    {
        return $this->services;
    }

    public function setServices(?array $services): self
    {
        $this->services = $services;
        return $this;
    }

    public function getCompanyClient(): ?array
    {
        return $this->companyClient;
    }

    public function setCompanyClient(?array $companyClient): self
    {
        $this->companyClient = $companyClient;
        return $this;
    }

    public function getCompany(): ?array
    {
        return $this->company;
    }

    public function setCompany(?array $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getClient(): ?array
    {
        return $this->client;
    }

    public function setClient(?array $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getTelegramClient(): ?array
    {
        return $this->telegramClient;
    }

    public function setTelegramClient(?array $telegramClient): self
    {
        $this->telegramClient = $telegramClient;
        return $this;
    }

    public function getRepeats(): ?array
    {
        return $this->repeats;
    }

    public function setRepeats(?array $repeats): self
    {
        $this->repeats = $repeats;
        return $this;
    }

    public function toModelEventArray(): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->clientId,
            'company_id' => $this->companyId,
            'service_ids' => $this->serviceIds,
            'description' => $this->description,
            'event_type' => $this->eventType,
            'event_time' => $this->eventTime,
            'repeat_type' => $this->repeatType,
            'target_time' => $this->targetTime,
            'status' => $this->status,
        ];
    }

    public static function makeFromModelEvent(Event $event): EventDto
    {
        return new self(
            $event->id,
            $event->client_id,
            $event->company_id,
            $event->services->pluck('id')->toArray(),
            $event->services->toArray(),
            $event->description,
            $event->event_type,
            $event->event_time,
            $event->repeat_type,
            $event->target_time,
            $event->companyClient ? $event->companyClient->toArray() : [],
            $event->company ? $event->company->toArray() : [],
            $event->status,
            $event->client ? $event->client->toArray() : [],
            $event->telegramClient ? $event->telegramClient->toArray() : [],
            $event->repeats->toArray(),
        );
    }

}
