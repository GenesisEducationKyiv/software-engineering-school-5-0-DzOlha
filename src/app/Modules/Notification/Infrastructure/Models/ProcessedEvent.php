<?php

namespace App\Modules\Notification\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedEvent extends Model
{
    use TableName;

    protected $fillable = ['event_key', 'event_name', 'status'];
}
