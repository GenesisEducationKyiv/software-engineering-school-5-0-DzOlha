<?php

namespace App\Modules\Weather\Domain\Repositories\Chain\Handler;

use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;

interface WeatherChainHandlerInterface extends WeatherRepositoryInterface
{
    public function setNext(?WeatherChainHandlerInterface $handler): void;
}
