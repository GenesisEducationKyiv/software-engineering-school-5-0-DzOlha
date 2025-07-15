<?php

namespace App\Modules\Weather\Application\HttpClient;

use Illuminate\Http\Client\Response;

interface HttpClientInterface
{
    /**
     * @param array<string, scalar|null> $params
     */
    public function get(string $url, array $params = []): Response;
}
