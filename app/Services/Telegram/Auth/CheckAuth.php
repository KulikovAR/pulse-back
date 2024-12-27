<?php

namespace App\Services\Telegram\Auth;

class CheckAuth
{
    public function checkTelegramAuthorization(array $data, string $botToken): bool
    {
        $checkHash = $data['hash'];
        unset($data['hash']);

        ksort($data);
        $dataCheckString = implode("\n", array_map(
            fn ($key, $value) => "$key=$value",
            array_keys($data),
            $data
        ));

        $secretKey = hash('sha256', $botToken, true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($hash, $checkHash);
    }
}
