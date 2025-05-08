<?php

namespace App\Services\Telegram\Auth;

class CheckAuth
{
    public function checkTelegramAuthorization(string $rawInitData, string $botToken): bool
    {
        // No array input needed anymore
        $params = explode('&', rawurldecode($rawInitData));
        $hash = null;
        
        foreach ($params as $key => $param) {
            if (str_starts_with($param, 'hash=')) {
                $hash = substr($param, 5);
                unset($params[$key]);
                break;
            }
        }
        
        sort($params);
        $checkString = implode("\n", $params);
        
        $secret = hash_hmac('sha256', $botToken, "WebAppData", true);
        $computedHash = bin2hex(hash_hmac('sha256', $checkString, $secret, true));
        
        return $hash && hash_equals($computedHash, $hash);
    }
}
