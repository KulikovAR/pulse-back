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

    // private function validateTelegramAuth(array $data): ?ApiJsonResponse
    // {
    //     if (!$this->authChecker->checkTelegramAuthorization($data, env('TELEGRAM_BOT_TOKEN'))) {
    //         return new ApiJsonResponse(data: ['error' => 'Unauthorized'], httpCode: 401);
    //     }
    //     return null;
    // }
    private function validateTelegramAuth(string $initData): ?ApiJsonResponse
    {
        if (!$this->authChecker->checkTelegramAuthorization(
            $initData,
            env('TELEGRAM_BOT_TOKEN')
        )) {
            return new ApiJsonResponse(data: ['error' => 'Unauthorized'], httpCode: 401);
        }
        return null;
    }

    private function handleTelegramClientWithUser(TelegramClient $telegramClient): ApiJsonResponse
    {
        $client = $telegramClient->client;
        
        if (!$client->user_id) {
            $user = $this->createNewUser($telegramClient->toArray());
            $client->update(['user_id' => $user->id]);
        }
        
        return $this->handleExistingTelegramClient($telegramClient);
    }

    private function handlePhoneNotProvided(): ApiJsonResponse
    {
        return new ApiJsonResponse(
            data: ['error' => 'phone_required'],
            message: 'Please share your phone number through Telegram bot',
            httpCode: 400
        );
    }

    public function login(Request $request): ApiJsonResponse
    {
        $data = $request->all();
        $telegramInitData = $request->header('X-Telegram-InitData');
    
        // Validate Telegram authorization
        if ($authError = $this->validateTelegramAuth($telegramInitData)) {
            return $authError;
        }
    
        // Find Telegram client by chat_id
        $telegramClient = TelegramClient::where('chat_id', $data['id'])->first();
        if ($telegramClient) {
            return $this->handleTelegramClientWithUser($telegramClient);
        }

        // Handle phone number if provided
        if (isset($data['phone'])) {
            return $this->handleClientByPhone($data);
        }

        return $this->handlePhoneNotProvided();
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

    private function handleAdminTelegramClient(TelegramClient $telegramClient): ApiJsonResponse
    {
        $client = $telegramClient->client;
        
        if (!$client->user_id) {
            $user = $this->createNewUser($telegramClient->toArray());
            $client->update(['user_id' => $user->id]);
            $this->createCompanyForUser($user);
        } else {
            $this->createCompanyForUser($client->user);
        }
        
        return $this->handleExistingTelegramClient($telegramClient);
    }

    private function handleAdminPhoneResponse(ApiJsonResponse $response): ApiJsonResponse
    {
        if ($response->httpCode === 200) {
            $user = $this->getUserFromToken($response->data['token']);
            if ($user) {
                $this->createCompanyForUser($user);
            }
        }
        
        return $response;
    }

    public function adminLogin(Request $request): ApiJsonResponse
    {
        $data = $request->all();

        // if ($authError = $this->validateTelegramAuth($data)) {
        //     return $authError;
        // }

        $telegramClient = TelegramClient::where('chat_id', $data['id'])->first();
        
        if ($telegramClient) {
            return $this->handleAdminTelegramClient($telegramClient);
        }

        if (isset($data['phone'])) {
            $response = $this->handleClientByPhone($data);
            return $this->handleAdminPhoneResponse($response);
        }

        return $this->handlePhoneNotProvided();
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
        // Clean phone number to contain only digits
        $cleanPhone = preg_replace('/[^0-9]/', '', $data['phone']);
        if (empty($cleanPhone)) {
            return new ApiJsonResponse(
                data: ['error' => 'invalid_phone'],
                message: 'Phone number must contain numeric characters',
                httpCode: 400
            );
        }
        $data['phone'] = $cleanPhone;

        $client = Client::where('phone', $data['phone'])->first();

        if (! $client) {
            // Создаем клиента и пользователя, если клиента не существует
            return $this->createUserClientAndTelegramClient($data, [
                'phone' => $data['phone'],
                'username' => $data['username'],
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
