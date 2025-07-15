<?php

namespace App\Modules\Weather\Infrastructure\Repositories;

use App\Modules\Weather\Application\HttpClient\HttpClientInterface;
use App\Modules\Weather\Domain\Repositories\WeatherRepositoryInterface;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Domain\ValueObjects\WeatherData;

abstract class AbstractWeatherRepository implements WeatherRepositoryInterface
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $currentWeatherEndpoint;
    protected int $cityNotFoundCode = 404;

    public function __construct(
        protected readonly HttpClientInterface $httpClient
    ) {
        /**
         * @var array{
         *      api_key: string|null,
         *      api_url: string|null,
         *      api_endpoint: string|null
         *  } $providerConfig
         */
        $providerConfig = $this->getProviderConfig();

        $key = $providerConfig['api_key'] ?? null;
        $url = $providerConfig['api_url'] ?? null;
        $endpoint = $providerConfig['api_endpoint'] ?? null;

        if (!$key || !$url || !$endpoint) {
            throw new \RuntimeException(
                "Weather provider configuration for '{$this->getProviderName()}' is incomplete."
            );
        }

        $this->apiKey = $key;
        $this->apiUrl = $url;
        $this->currentWeatherEndpoint = $endpoint;
    }

    public function getProviderConfig(): mixed
    {
        return config("services.weather.{$this->getProviderName()}");
    }

    /**
     * @param array<string|int, scalar|null> $error
     * @return bool
     */
    protected function hasNotFoundError(array $error): bool
    {
        /**
         * @var array{code: ?int} $error
         */
        return $error['code'] === $this->cityNotFoundCode;
    }

    abstract public function getProviderName(): string;

    abstract public function getCurrentWeather(City $city): WeatherData;

    abstract public function cityExists(City $city): bool;
}
