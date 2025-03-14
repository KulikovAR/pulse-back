<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Telegram\TelegramClientLoginRequest;
use App\Http\Responses\ApiJsonResponse;
use App\Http\Services\TelegramClientService;
use App\Models\TelegramClient;

class TelegramController extends Controller
{
    private $service;

    public function __construct(TelegramClientService $service)
    {
        $this->service = $service;
    }

    public function login(TelegramClientLoginRequest $request): ApiJsonResponse
    {
        return $this->service->login($request);
    }

    public function adminLogin(TelegramClientLoginRequest $request): ApiJsonResponse
    {
        return $this->service->adminLogin($request);
    }

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
