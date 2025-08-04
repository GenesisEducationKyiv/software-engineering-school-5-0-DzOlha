<?php

namespace App\Modules\Weather\Domain\Repositories\Chain\Builder;

use App\Modules\Weather\Domain\Repositories\Chain\Handler\WeatherChainHandlerInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;

interface WeatherChainBuilderInterface
{
    public function getHead(): WeatherChainHandlerInterface;
    public function build(): WeatherRepositoryInterface;
}
