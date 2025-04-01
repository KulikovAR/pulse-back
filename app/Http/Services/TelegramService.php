<?php

namespace App\Http\Services;

use App\Models\Client;
use App\Models\TelegramClient;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function sendNewEventNotification($eventDto): void
    {
        $client = Client::find($eventDto->getClientId());
        
        if ($client && $telegramClient = TelegramClient::where('client_id', $client->id)->first()) {
            $eventTime = new \DateTime($eventDto->getEventTime(), new \DateTimeZone('UTC'));
            $eventTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
            
            $this->sendMessage(
                $telegramClient->chat_id,
                "<b>🆕 Новая запись!</b>\nКомпания: {$eventDto->getCompany()['name']}\nУслуги: "
                . implode(', ', array_column($eventDto->getServices(), 'name')) 
                . "\n\nДата: " . $eventTime->format('d.m.Y')
                . "\nВремя: " . $eventTime->format('H:i') . " (МСК, UTC+3)"
                . "\n\nАдрес: {$eventDto->getCompany()['address']}"
            );
        }
    }

    public function sendMessage(int $chatId, string $message): void
    {
        Http::post("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);
    }
}