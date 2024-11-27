<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transcriptions(): HasMany
    {
        return $this->hasMany(Transcription::class);
    }

     public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
