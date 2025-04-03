<?php

namespace App\Http\Services;

use App\Models\Client;
use App\Models\CompanyClient;
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
                "<b>🆕 Новая запись!</b>\n\nКомпания: {$eventDto->getCompany()['name']}\nУслуги: "
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

    public function sendEventCancelledByClientNotification($eventDto): void
    {
        $company = $eventDto->getCompany();
        $client = Client::find($eventDto->getClientId());
        $companyClient = CompanyClient::where([
            'company_id' => $company['id'],
            'client_id' => $client->id
        ])->first();
        
        if ($client && isset($company['user_id'])) {
            $companyOwnerClient = Client::where('user_id', $company['user_id'])->first();
            
            if ($companyOwnerClient && $telegramClient = TelegramClient::where('client_id', $companyOwnerClient->id)->first()) {
                $eventTime = new \DateTime($eventDto->getEventTime(), new \DateTimeZone('UTC'));
                $eventTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
                
                $this->sendMessage(
                    $telegramClient->chat_id,
                    "<b>❌ Запись отменена клиентом</b>\n\n"
                    . "Клиент: {$companyClient->name}\n"
                    . "Услуги: " . implode(', ', array_column($eventDto->getServices(), 'name')) . "\n\n"
                    . "Дата: " . $eventTime->format('d.m.Y') . "\n"
                    . "Время: " . $eventTime->format('H:i') . " (МСК, UTC+3)"
                );
            }
        }
    }

    public function sendEventCancelledByCompanyNotification($eventDto): void
    {
        $client = Client::find($eventDto->getClientId());
        
        if ($client && $telegramClient = TelegramClient::where('client_id', $client->id)->first()) {
            $eventTime = new \DateTime($eventDto->getEventTime(), new \DateTimeZone('UTC'));
            $eventTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
            
            $this->sendMessage(
                $telegramClient->chat_id,
                "<b>❌ Запись отменена компанией</b>\n\n"
                . "Компания: {$eventDto->getCompany()['name']}\n"
                . "Услуги: " . implode(', ', array_column($eventDto->getServices(), 'name')) . "\n\n"
                . "Дата: " . $eventTime->format('d.m.Y') . "\n"
                . "Время: " . $eventTime->format('H:i') . " (МСК, UTC+3)\n\n"
                . "Пожалуйста, свяжитесь с компанией для уточнения деталей"
            );
        }
    }
}