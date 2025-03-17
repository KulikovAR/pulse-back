<?php

namespace App\Http\Services;

use App\Contracts\ClientInterface;
use App\Http\Requests\Traits\CreatesUserWithClient;
use App\Http\Responses\ApiJsonResponse;
use App\Models\Client;
use App\Models\TelegramClient;
use App\Models\User;
use App\Models\Company;
use App\Services\Telegram\Auth\CheckAuth;
use Illuminate\Http\Request;

class TelegramClientService implements ClientInterface
{
    use CreatesUserWithClient;

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

        // Ищем Telegram клиента по chat_id
        $telegramClient = TelegramClient::where('chat_id', $data['id'])->first();
        if ($telegramClient) {
            return $this->handleExistingTelegramClient($telegramClient);
        }

        // Если передан phone
        if (isset($data['phone'])) {
            return $this->handleClientByPhone($data);
        }

        // Если phone не указан
        return $this->createUserClientAndTelegramClient($data, [
            'username' => $data['username'],
            'name' => $data['first_name'] ?? 'User',
        ]);
    }

    private function createCompanyForUser(User $user): void
    {
        if (!$user->company()->exists()) {
            Company::create([
                'name' => 'Company ' . $user->name,
                'user_id' => $user->id
            ]);
        }
    }

    private function getUserFromToken(string $token): ?User
    {
        return User::whereHas('tokens', function($query) use ($token) {
            $query->where('token', hash('sha256', $token));
        })->first();
    }

    public function adminLogin(Request $request): ApiJsonResponse
    {
        $data = $request->all();

        if (!$this->authChecker->checkTelegramAuthorization($data, env('TELEGRAM_BOT_TOKEN'))) {
            return new ApiJsonResponse(data: ['error' => 'Unauthorized'], httpCode: 401);
        }

        $telegramClient = TelegramClient::where('chat_id', $data['id'])->first();
        
        if ($telegramClient) {
            $response = $this->handleExistingTelegramClient($telegramClient);
            
            if ($response->httpCode === 200) {
                $this->createCompanyForUser($telegramClient->client->user);
            }
            
            return $response;
        }

        if (isset($data['phone'])) {
            $response = $this->handleClientByPhone($data);
            
            if ($response->httpCode === 200) {
                $user = $this->getUserFromToken($response->data['token']);
                if ($user) {
                    $this->createCompanyForUser($user);
                }
            }
            
            return $response;
        }

        $response = $this->createUserClientAndTelegramClient($data, [
            'username' => $data['username'],
            'name' => $data['first_name'] ?? 'User',
        ]);
        
        if ($response->httpCode === 200) {
            $user = $this->getUserFromToken($response->data['token']);
            if ($user) {
                $this->createCompanyForUser($user);
            }
        }
        
        return $response;
    }

    private function handleExistingTelegramClient(TelegramClient $telegramClient): ApiJsonResponse
    {
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

    private function handleClientByPhone(array $data): ApiJsonResponse
    {
        $client = Client::where('phone', $data['phone'])->first();

        if (! $client) {
            // Создаем клиента и пользователя, если клиента не существует
            return $this->createUserClientAndTelegramClient($data, [
                'phone' => $data['phone'],
                'name' => $data['first_name'] ?? 'User',
            ]);
        }

        if (! $client->user_id) {
            // Создаем нового пользователя
            $user = $this->createNewUser($data);

            // Сохраняем user_id в клиенте
            $client->update(['user_id' => $user->id]);
        } else {
            $user = $client->user;
        }

        TelegramClient::create([
            'chat_id' => $data['id'],
            'username' => $data['username'] ?? null,
            'client_id' => $client->id,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return new ApiJsonResponse(data: ['token' => $token], httpCode: 200);
    }

    private function createUserClientAndTelegramClient(array $data, array $clientData): ApiJsonResponse
    {
        ['user' => $user, 'client' => $client] = $this->createUserWithClient(
            $data,
            $clientData
        );

        TelegramClient::create([
            'chat_id' => $data['id'],
            'username' => $data['username'] ?? null,
            'client_id' => $client->id,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return new ApiJsonResponse(data: ['token' => $token], httpCode: 200);
    }

    private function createNewUser(array $data): User
    {
        return User::create([
            'name' => $data['first_name'] ?? 'User',
        ]);
    }
}
