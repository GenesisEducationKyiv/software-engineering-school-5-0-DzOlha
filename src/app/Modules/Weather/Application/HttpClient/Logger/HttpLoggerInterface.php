<?php

namespace App\Modules\Weather\Application\HttpClient\Logger;

use Illuminate\Http\Client\Response;

interface HttpLoggerInterface
{
    public function logHttpResponse(Response $response, string $url): void;
}
