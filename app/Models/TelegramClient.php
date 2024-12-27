<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramClient extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['id', 'client_id', 'chat_id', 'username'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // public function user()
    // {
    //     return $this->client->user();
    // }
}
