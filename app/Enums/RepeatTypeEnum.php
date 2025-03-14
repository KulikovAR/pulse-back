<?php

namespace App\Enums;

enum RepeatTypeEnum: string
{
    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case MONTHLY = 'monthly';

    public static function get(): array
    {
        return self::cases();
    }
}
