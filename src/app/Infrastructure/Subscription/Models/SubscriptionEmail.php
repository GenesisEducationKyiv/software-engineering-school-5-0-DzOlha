<?php

namespace App\Infrastructure\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Infrastructure\Subscription\Models\Subscription;

class SubscriptionEmail extends Model
{
    use TableName;

    protected $fillable = ['subscription_id', 'last_sent_at', 'next_scheduled_at', 'status'];

    /**
     * @return BelongsTo<Subscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
