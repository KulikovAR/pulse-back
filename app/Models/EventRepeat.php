<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasEventTime;
use Illuminate\Database\Eloquent\Model;

class EventRepeat extends Model
{
    use HasFactory, HasUuids, HasEventTime;

    protected $fillable = ['id', 'event_id', 'event_time'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}