<?php

namespace App\Domains\SubscriptionEmail\Model;

use App\Domains\Subscription\Model\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionEmail extends Model
{
    protected $fillable = ['subscription_id', 'last_sent_at', 'next_scheduled_at', 'status'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
