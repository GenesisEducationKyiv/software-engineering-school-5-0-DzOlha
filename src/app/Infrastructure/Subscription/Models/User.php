<?php

namespace App\Infrastructure\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Subscription\Models\Subscription;

class User extends Model
{
    public $timestamps = false;

    protected $fillable = ['email'];

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
