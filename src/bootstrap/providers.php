<?php

use App\Modules\Observability\Providers\ObservabilityServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventBusServiceProvider::class,
    ObservabilityServiceProvider::class
];
