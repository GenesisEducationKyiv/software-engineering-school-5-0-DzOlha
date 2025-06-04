<?php

namespace App\Interfaces\Api\Controllers;

use App\Application\Weather\DTOs\WeatherRequestDTO;
use App\Application\Weather\Queries\GetCurrentWeatherQuery;
use App\Domain\Weather\ValueObjects\City;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\ValidationException;
use App\Interfaces\Api\Requests\WeatherRequest;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    public function __construct(
        private readonly GetCurrentWeatherQuery $getCurrentWeatherQuery
    ) {
    }

    public function getCurrentWeather(WeatherRequest $request): JsonResponse
    {
        try {
            $dto = new WeatherRequestDTO(
                new City($request->city)
            );

            $weatherData = $this->getCurrentWeatherQuery->execute($dto);
        } catch (ValidationException | CityNotFoundException | ApiAccessException $e) {
            return $this->errorResponse($e);
        }

        return $this->successResponse(
            "Current weather for {$request->city}",
            $weatherData->toArray()
        );
    }
}
