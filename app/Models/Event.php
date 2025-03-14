<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasEventTime;
use App\Traits\Repeatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory, HasUuids, HasEventTime, Repeatable;

    protected $fillable = [
        'id',
        'client_id',
        'company_id',
        'name',
        'description',
        'event_type',
        'event_time',
        'repeat_type',
        'target_time'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function repeats()
    {
        return $this->hasMany(EventRepeat::class);
    }
}
