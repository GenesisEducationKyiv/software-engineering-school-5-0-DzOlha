<?php

namespace App\Modules\Weather\Infrastructure\Repositories\Chain\Builder;

use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use App\Modules\Weather\Application\HttpClient\HttpClientInterface;
use App\Modules\Weather\Domain\Repositories\Chain\Builder\WeatherChainBuilderInterface;
use App\Modules\Weather\Domain\Repositories\Chain\Handler\WeatherChainHandlerInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Infrastructure\Repositories\Chain\Handler\WeatherChainHandler;
use App\Modules\Weather\Infrastructure\Repositories\Chain\WeatherChain;
use App\Modules\Weather\Infrastructure\Repositories\OpenWeatherRepository;
use App\Modules\Weather\Infrastructure\Repositories\WeatherApiRepository;
use App\Modules\Weather\Infrastructure\Repositories\WeatherStackRepository;

class WeatherChainBuilder implements WeatherChainBuilderInterface
{
    public function getHead(): WeatherChainHandlerInterface
    {
        $httpClient = app(HttpClientInterface::class);
        $monitor = app(ObservabilityModuleInterface::class);

        $weatherApiProvider = new WeatherChainHandler(
            new WeatherApiRepository($httpClient, $monitor)
        );

        $openWeatherProvider = new WeatherChainHandler(
            new OpenWeatherRepository($httpClient, $monitor)
        );

        $weatherStackProvider = new WeatherChainHandler(
            new WeatherStackRepository($httpClient, $monitor)
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
