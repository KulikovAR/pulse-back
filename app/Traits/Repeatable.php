<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\EventRepeat;

trait Repeatable
{
    protected static $repeatClass = EventRepeat::class;

    public function createNewRepeat()
    {
        // Если это событие с конкретной датой
        if ($this->target_time) {
            return self::$repeatClass::create([
                'event_id' => $this->id,
                'event_time' => $this->target_time
            ]);
        }

        // Для повторяющихся событий
        if ($this->repeat_type) {
            $nextTime = $this->calculateNextEventTime();
            return self::$repeatClass::create([
                'event_id' => $this->id,
                'event_time' => $nextTime
            ]);
        }
    
        return null;
    }

    public function clearOldRepeats()
    {
        return self::$repeatClass::where('event_id', $this->id)
            ->where('event_time', '<', Carbon::now())
            ->delete();
    }

    protected function calculateNextEventTime()
    {
        $time = Carbon::parse($this->event_time);

        return match($this->repeat_type) {
            'weekly' => $time->addWeek(),
            'biweekly' => $time->addWeeks(2),
            'monthly' => $time->addMonth(),
            default => $time
        };
    }
}