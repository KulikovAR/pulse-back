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

    private function getAppLink(): array
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => '📱 Открыть приложение',
                        'url' => 'https://t.me/PulseAppBot_bot/app'
                    ]
                ]
            ]
        ];
    }

    public function sendMessage(int $chatId, string $message): void
    {
        Http::post("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode($this->getAppLink())
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

    public function sendEventConfirmedByClientNotification($eventDto): void
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
                    "<b>✅ Запись подтверждена клиентом</b>\n\n"
                    . "Клиент: {$companyClient->name}\n"
                    . "Услуги: " . implode(', ', array_column($eventDto->getServices(), 'name')) . "\n\n"
                    . "Дата: " . $eventTime->format('d.m.Y') . "\n"
                    . "Время: " . $eventTime->format('H:i') . " (МСК, UTC+3)"
                );
            }
        }
    }

    public function sendEventRepeatNotification($eventDto): void
    {
        $client = Client::find($eventDto->getClientId());
        
        if ($client && $telegramClient = TelegramClient::where('client_id', $client->id)->first()) {
            $eventTime = new \DateTime($eventDto->getEventTime(), new \DateTimeZone('UTC'));
            $eventTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
            
            // Формируем текст о типе повторения
            $repeatText = match($eventDto->getRepeatType()) {
                'weekly' => 'еженедельно',
                'biweekly' => 'раз в две недели',
                'monthly' => 'ежемесячно',
                default => 'разово'
            };
            
            $this->sendMessage(
                $telegramClient->chat_id,
                "<b>🔄 Создано повторение записи</b>\n\n"
                . "Компания: {$eventDto->getCompany()['name']}\n"
                . "Услуги: " . implode(', ', array_column($eventDto->getServices(), 'name')) . "\n\n"
                . "Дата: " . $eventTime->format('d.m.Y') . "\n"
                . "Время: " . $eventTime->format('H:i') . " (МСК, UTC+3)\n\n"
                . "Тип повторения: " . $repeatText . "\n"
                . "Адрес: {$eventDto->getCompany()['address']}"
            );
        }
    }

    public function sendDayBeforeReminder($eventDto): void
    {
        $client = Client::find($eventDto->getClientId());
        
        if ($client && $telegramClient = TelegramClient::where('client_id', $client->id)->first()) {
            $eventTime = new \DateTime($eventDto->getEventTime(), new \DateTimeZone('UTC'));
            $eventTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
            
            $this->sendMessage(
                $telegramClient->chat_id,
                "<b>⏰ Напоминание о записи (24 часа)</b>\n\n"
                . "Компания: {$eventDto->getCompany()['name']}\n"
                . "Услуги: " . implode(', ', array_column($eventDto->getServices(), 'name')) . "\n\n"
                . "Дата: " . $eventTime->format('d.m.Y') . "\n"
                . "Время: " . $eventTime->format('H:i') . " (МСК, UTC+3)\n\n"
                . "Адрес: {$eventDto->getCompany()['address']}"
            );
        }
    }

    public function sendHourBeforeReminder($eventDto): void
    {
        $client = Client::find($eventDto->getClientId());
        
        if ($client && $telegramClient = TelegramClient::where('client_id', $client->id)->first()) {
            $eventTime = new \DateTime($eventDto->getEventTime(), new \DateTimeZone('UTC'));
            $eventTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
            
            $this->sendMessage(
                $telegramClient->chat_id,
                "<b>⏰ Напоминание о записи (1 час)</b>\n\n"
                . "Компания: {$eventDto->getCompany()['name']}\n"
                . "Услуги: " . implode(', ', array_column($eventDto->getServices(), 'name')) . "\n\n"
                . "Дата: " . $eventTime->format('d.m.Y') . "\n"
                . "Время: " . $eventTime->format('H:i') . " (МСК, UTC+3)\n\n"
                . "Адрес: {$eventDto->getCompany()['address']}"
            );
        }
    }

    public function sendDailySchedule($client, $events): void
    {
        if ($telegramClient = TelegramClient::where('client_id', $client->id)->first()) {
            $message = "<b>📅 Ваши записи на сегодня:</b>\n\n";
            
            foreach ($events as $event) {
                $eventTime = new \DateTime($event['event_time'], new \DateTimeZone('UTC'));
                $eventTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
                
                $message .= "🕐 " . $eventTime->format('H:i') . "\n"
                    . "📍 {$event['company']['name']}\n"
                    . "✨ Услуги: " . implode(', ', array_column($event['services'], 'name')) . "\n"
                    . "📌 Адрес: {$event['company']['address']}\n\n";
            }
            
            $this->sendMessage($telegramClient->chat_id, $message);
        }
    }
}