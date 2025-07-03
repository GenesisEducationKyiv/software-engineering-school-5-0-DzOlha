<?php

namespace App\Infrastructure\Weather\HttpClient;

use App\Application\Weather\HttpClient\HttpClientInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpClient implements HttpClientInterface
{
    /**
     * @param array<string, scalar|null> $params
     */
    public function get(string $url, array $params = []): Response
    {
        return Http::get($url, $params);
    }
}
