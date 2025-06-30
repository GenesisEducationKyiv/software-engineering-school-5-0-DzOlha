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

class WeatherChainBuilder implements WeatherChainBuilderInterface
{
    public function getHead(): WeatherChainHandlerInterface
    {
        $httpClient = app(HttpClientInterface::class);

        $weatherApiProvider = new WeatherChainHandler(
            new WeatherApiRepository($httpClient)
        );

        $openWeatherProvider = new WeatherChainHandler(
            new OpenWeatherRepository($httpClient)
        );

        $weatherStackProvider = new WeatherChainHandler(
            new WeatherStackRepository($httpClient)
        );

        $weatherApiProvider->setNext($openWeatherProvider);
        $openWeatherProvider->setNext($weatherStackProvider);

        return $weatherApiProvider;
    }

    public function build(): WeatherRepositoryInterface
    {
        return new WeatherChain($this->getHead());
    }
}
