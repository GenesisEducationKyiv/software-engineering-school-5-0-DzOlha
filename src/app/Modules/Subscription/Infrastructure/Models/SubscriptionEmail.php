<?php

namespace App\Modules\Subscription\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
