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
            $this->sendMessage(
                $telegramClient->chat_id,
                "🆕 Новая запись!\nКомпания: {$eventDto->getCompany()['name']}\nУслуги: "
                . implode(', ', array_column($eventDto->getServices(), 'name')) 
                . "\nДата: " . date('d.m.Y', strtotime($eventDto->getEventTime()))
                . "\nВремя: " . date('H:i', strtotime($eventDto->getEventTime()))
                . "\nАдрес: {$eventDto->getCompany()['address']}"
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