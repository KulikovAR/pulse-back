<?php

namespace App\Http\Services;

use App\Contracts\ClientInterface;
use App\Http\Responses\ApiJsonResponse;
use App\Models\Client;
use App\Models\TelegramClient;
use App\Models\User;
use App\Services\Telegram\Auth\CheckAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TelegramClientService implements ClientInterface
{
    private $authChecker;

    public function __construct(CheckAuth $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    public function login(Request $request): ApiJsonResponse
    {
        $data = $request->all();

        // Проверяем авторизацию Telegram
        if (! $this->authChecker->checkTelegramAuthorization($data, env('TELEGRAM_BOT_TOKEN'))) {
            return new ApiJsonResponse(data: ['error' => 'Unauthorized'], httpCode: 401);
        }

        // Ищем пользователя по chat_id
        $telegramClient = TelegramClient::where('chat_id', $data['id'])->first();

        if ($telegramClient) {

            if (! $telegramClient->client->user_id) {
                return new ApiJsonResponse(
                    httpCode: 500,
                    ok: false,
                    message: 'No associated user found for TelegramClient'
                );
            }

            $token = $telegramClient->client->user->createToken('api-token')->plainTextToken;

            return new ApiJsonResponse(data: ['token' => $token]);
        }

        // Если нет, проверяем phone
        if (isset($data['phone'])) {
            $client = Client::where('phone', $data['phone'])->first();
            if (! $client) {
                $user = User::create([
                    'name' => $data['first_name'] ?? 'User',
                    'email' => $data['username'].'@telegram.local',
                    'password' => Hash::make('defaultpassword'),
                ]);

                $client = Client::create([
                    'user_id' => $user->id,
                    'phone' => $data['phone'],
                    'name' => $data['first_name'] ?? 'User',
                ]);
            }

            $user = $client->user ?: User::create([
                'name' => $data['first_name'] ?? 'User',
                'email' => $data['username'].'@telegram.local',
                'password' => Hash::make('defaultpassword'),
            ]);

            $telegramClient = TelegramClient::create([
                'chat_id' => $data['id'],
                'username' => $data['username'] ?? null,
                'client_id' => $client->id,
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return new ApiJsonResponse(data: ['token' => $token], httpCode: 200);
        }

        // Если phone не указан
        $user = User::create([
            'name' => $data['first_name'] ?? 'User',
            'email' => $data['username'].'@telegram.local',
            'password' => Hash::make('defaultpassword'),
        ]);

        $client = Client::create([
            'user_id' => $user->id,
            'username' => $data['username'],
            'name' => $data['first_name'] ?? 'User',
        ]);

        $telegramClient = TelegramClient::create([
            'chat_id' => $data['id'],
            'username' => $data['username'] ?? null,
            'client_id' => $client->id,
            'user_id' => $user->id,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return new ApiJsonResponse(data: ['token' => $token], httpCode: 200);
    }
}
