<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasEventTime;
use App\Traits\Repeatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\CompanyClient;

class Event extends Model
{
    use HasFactory, HasUuids, HasEventTime, Repeatable;

    protected $fillable = [
        'id',
        'client_id',
        'company_id',
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

    public function companyClient(): BelongsTo
    {
        return $this->belongsTo(CompanyClient::class, 'client_id', 'client_id')
            ->where('company_id', $this->company_id);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'event_services')->withTimestamps();
    }

    public function repeats()
    {
        return $this->hasMany(EventRepeat::class);
    }
}
