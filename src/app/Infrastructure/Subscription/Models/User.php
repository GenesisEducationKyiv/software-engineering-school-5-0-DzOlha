<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    public $timestamps = false;

    protected $fillable = ['email'];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
