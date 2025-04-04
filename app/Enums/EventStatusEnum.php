<?php

namespace App\Enums;

enum EventStatusEnum: string
{
    case UNREAD = 'unread';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public static function get(): array
    {
        return self::cases();
    }

    public static function default(): self
    {
        return self::UNREAD;
    }
}