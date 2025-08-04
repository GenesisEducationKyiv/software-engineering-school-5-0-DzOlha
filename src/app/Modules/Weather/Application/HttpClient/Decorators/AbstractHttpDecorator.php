<?php

namespace App\Modules\Weather\Application\HttpClient\Decorators;

use App\Modules\Weather\Application\HttpClient\HttpClientInterface;
use Illuminate\Http\Client\Response;

abstract class AbstractHttpDecorator implements HttpClientInterface
{
    public function __construct(protected HttpClientInterface $httpClient)
    {
    }

    /**
     * @param array<string, scalar|null> $params
     */
    abstract public function get(string $url, array $params = []): Response;
}
