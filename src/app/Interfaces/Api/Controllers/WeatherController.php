<?php

namespace App\Interfaces\Api\Controllers;

use App\Application\Weather\DTOs\WeatherRequestDTO;
use App\Application\Weather\Queries\GetCurrentWeatherQuery;
use App\Domain\Weather\ValueObjects\City;
use App\Exceptions\Custom\ApiAccessException;
use App\Exceptions\Custom\CityNotFoundException;
use App\Exceptions\Custom\InvalidRequestException;
use App\Interfaces\Api\Requests\WeatherRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class WeatherController extends Controller
{
    public function __construct(
        private readonly GetCurrentWeatherQuery $getCurrentWeatherQuery
    ) {
    }

    public function getCurrentWeather(WeatherRequest $request): JsonResponse
    {
        $dto = new WeatherRequestDTO(
            new City($request->city)
        );

        try {
            $weatherData = $this->getCurrentWeatherQuery->execute($dto);
        }
        catch (\InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
        catch (CityNotFoundException | ApiAccessException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json($weatherData->toArray());
    }
}
