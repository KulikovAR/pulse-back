<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyClient extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'company_client';

    protected $fillable = [
        'company_id',
        'client_id',
        'name',
        'verify',
    ];

    /**
     * Get the company that owns the pivot.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the client that owns the pivot.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}