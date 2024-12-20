<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['id', 'user_id', 'name', 'phone'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function telegramClients(): HasMany
    {
        return $this->hasMany(TelegramClient::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
