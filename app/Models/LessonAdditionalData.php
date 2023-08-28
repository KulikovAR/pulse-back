<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonAdditionalData extends Model
{
    use HasFactory, HasUuids;

    public function lessons(): HasMany
    {
        return $this->hasMany(Slide::class);
    }
}