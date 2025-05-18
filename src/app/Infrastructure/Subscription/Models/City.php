<?php

namespace App\Infrastructure\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'country'];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
