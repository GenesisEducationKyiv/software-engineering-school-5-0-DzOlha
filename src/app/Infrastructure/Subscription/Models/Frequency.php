<?php

namespace App\Models;

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
