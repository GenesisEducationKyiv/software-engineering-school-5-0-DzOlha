<?php

namespace App\Domains\Frequency\Model;

use App\Domains\Subscription\Model\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Frequency extends Model
{
    protected $fillable = ['name', 'interval_minutes'];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
