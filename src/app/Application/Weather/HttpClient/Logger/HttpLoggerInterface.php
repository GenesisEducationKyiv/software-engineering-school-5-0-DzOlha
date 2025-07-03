<?php

namespace App\Application\Weather\HttpClient\Logger;

use Illuminate\Http\Client\Response;

interface HttpLoggerInterface
{
    public function logHttpResponse(Response $response, string $url): void;
}
