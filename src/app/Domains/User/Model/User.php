<?php

namespace App\Domains\User\Model;

use App\Domains\Subscription\Model\Subscription;
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
