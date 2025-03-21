<?php

namespace App\Http\Requests\Traits;

use App\Models\Client;
use App\Models\User;

trait CreatesUserWithClient
{
    /**
     * Создает пользователя и клиента.
     *
     * @param  array  $userData  Данные пользователя
     * @param  array  $clientData  Данные клиента
     */
    public static function createUserWithClient(array $userData, array $clientData): array
    {
        $user = User::create([
            'name' => $userData['name'] ?? 'User',
        ]);

        $client = Client::create([
            'user_id' => $user->id,
            'username' => $clientData['username'],
            'name' => $clientData['name'] ?? 'User',
            'phone' => $clientData['phone'] ?? null,
        ]);

        return ['user' => $user, 'client' => $client];
    }
}
