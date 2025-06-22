<?php

namespace App\Domain\Weather\Repositories\Chain\Builder;

use App\Domain\Weather\Repositories\Chain\Handler\WeatherChainHandlerInterface;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;

interface WeatherChainBuilderInterface
{
    public function getHead(): WeatherChainHandlerInterface;
    public function build(): WeatherRepositoryInterface;
}
