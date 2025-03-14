<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasEventTime
{
    public static function getActual()
    {        
        return static::where('event_time', '<=', Carbon::now())
                    ->where('event_time', '>=', Carbon::now()->subMinutes(1))
                    ->get();
    }
}