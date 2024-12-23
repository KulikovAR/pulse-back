<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitrixIntegration extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bitrix_integration';

    protected $fillable = ['id', 'user_id', 'token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
