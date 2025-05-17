<?php

namespace App\Domains\SubscriptionToken\Model;

use App\Domains\Subscription\Model\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionToken extends Model
{
    protected $fillable = ['subscription_id', 'token', 'type', 'expires_at'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
