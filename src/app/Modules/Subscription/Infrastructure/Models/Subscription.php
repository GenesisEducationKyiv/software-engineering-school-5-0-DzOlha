<?php

namespace App\Infrastructure\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Subscription\Models\User;
use App\Infrastructure\Subscription\Models\Frequency;
use App\Infrastructure\Subscription\Models\SubscriptionEmail;
use App\Infrastructure\Subscription\Models\SubscriptionToken;

class Subscription extends Model
{
    use TableName;

    public $timestamps = false;

    protected $fillable = ['user_id', 'city_id', 'frequency_id', 'status'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo<Frequency, $this>
     */
    public function frequency(): BelongsTo
    {
        return $this->belongsTo(Frequency::class);
    }

    /**
     * @return HasMany<SubscriptionEmail, $this>
     */
    public function emails(): HasMany
    {
        return $this->hasMany(SubscriptionEmail::class);
    }

    /**
     * @return HasMany<SubscriptionToken, $this>
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(SubscriptionToken::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
