<?php

namespace App\Infrastructure\Weather\Repositories\Chain\Builder;

use App\Application\Weather\HttpClient\HttpClientInterface;
use App\Domain\Weather\Repositories\Chain\Builder\WeatherChainBuilderInterface;
use App\Domain\Weather\Repositories\Chain\Handler\WeatherChainHandlerInterface;
use App\Domain\Weather\Repositories\WeatherRepositoryInterface;
use App\Infrastructure\Weather\Repositories\Chain\Handler\WeatherChainHandler;
use App\Infrastructure\Weather\Repositories\Chain\WeatherChain;
use App\Infrastructure\Weather\Repositories\OpenWeatherRepository;
use App\Infrastructure\Weather\Repositories\WeatherApiRepository;
use App\Infrastructure\Weather\Repositories\WeatherStackRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

class WeatherChainBuilder implements WeatherChainBuilderInterface
{
    public function getHead(): WeatherChainHandlerInterface
    {
        $httpClient = app(HttpClientInterface::class);

        $provider1 = new WeatherChainHandler(
            new WeatherApiRepository($httpClient)
        );

        $provider2 = new WeatherChainHandler(
            new OpenWeatherRepository($httpClient)
        );

        $provider3 = new WeatherChainHandler(
            new WeatherStackRepository($httpClient)
        );

        $provider1->setNext($provider2);
        $provider2->setNext($provider3);

        return $provider1;
    }

    public function build(): WeatherRepositoryInterface
    {
        return new WeatherChain($this->getHead());
    }
}
