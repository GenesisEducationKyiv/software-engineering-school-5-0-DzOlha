<?php

namespace App\Modules\Subscription\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
