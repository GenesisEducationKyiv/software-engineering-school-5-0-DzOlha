<?php

namespace App\Infrastructure\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Infrastructure\Subscription\Models\Subscription;

class SubscriptionToken extends Model
{
    use TableName;

    protected $fillable = ['subscription_id', 'token', 'type', 'expires_at'];

    /**
     * @return BelongsTo<Subscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
