<?php

namespace App\Modules\Weather\Presentation\Http\Controllers;

use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\ValidationException;
use App\Modules\Weather\Application\DTOs\WeatherRequestDTO;
use App\Modules\Weather\Application\Queries\GetCurrentWeatherQuery;
use App\Modules\Weather\Domain\ValueObjects\City\City;
use App\Modules\Weather\Presentation\Http\Requests\WeatherRequest;
use App\Presentation\Api\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    public function __construct(
        private readonly GetCurrentWeatherQuery $getCurrentWeatherQuery
    ) {
    }

    public function getCurrentWeather(WeatherRequest $request): JsonResponse
    {
        $data = $request->validatedTyped();

        try {
            $dto = new WeatherRequestDTO(
                new City($data['city'])
            );

            $weatherData = $this->getCurrentWeatherQuery->execute($dto);
        } catch (ValidationException | CityNotFoundException | ApiAccessException $e) {
            return $this->errorResponse($e);
        }

        return $this->successResponse(
            "Current weather for {$data['city']}",
            $weatherData->toArray()
        );
    }
}
