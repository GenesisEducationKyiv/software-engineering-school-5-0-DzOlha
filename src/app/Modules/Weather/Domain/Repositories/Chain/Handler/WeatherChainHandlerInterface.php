<?php

namespace App\Domain\Weather\Repositories\Chain\Handler;

use App\Domain\Weather\Repositories\WeatherRepositoryInterface;

interface WeatherChainHandlerInterface extends WeatherRepositoryInterface
{
    public function setNext(?WeatherChainHandlerInterface $handler): void;
}
