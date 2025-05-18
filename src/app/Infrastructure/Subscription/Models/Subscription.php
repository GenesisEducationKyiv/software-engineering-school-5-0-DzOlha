<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'city_id', 'frequency_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(Frequency::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(SubscriptionEmail::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(SubscriptionToken::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
