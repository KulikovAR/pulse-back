<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TelegramClient;

class TelegramController extends Controller
{
    public function getClientByChatId($chatId)
    {
        $telegramClient = TelegramClient::where('chat_id', $chatId)->first();

        if ($telegramClient) {
            return response()->json([
                'id' => $telegramClient->id,
                'client_id' => $telegramClient->client_id,
                'chat_id' => $telegramClient->chat_id,
            ]);
        }

        return response()->json(['message' => 'Client not found'], 404);
    }
}
