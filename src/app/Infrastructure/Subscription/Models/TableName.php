<?php

namespace App\Infrastructure\Subscription\Models;

use Illuminate\Database\Eloquent\Model;

trait TableName
{
    protected static string $resolvedTableName;

    public static function getTableName(): string
    {
        if (isset(static::$resolvedTableName)) {
            return static::$resolvedTableName;
        }

        /** @var Model $model */
        $model = app(static::class);

        return static::$resolvedTableName = $model->getTable();
    }
}
