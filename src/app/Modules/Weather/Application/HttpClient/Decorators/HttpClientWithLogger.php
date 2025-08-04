<?php

namespace App\Modules\Weather\Application\HttpClient\Decorators;

use App\Modules\Weather\Application\HttpClient\HttpClientInterface;
use App\Modules\Weather\Application\HttpClient\Logger\HttpLoggerInterface;
use Illuminate\Http\Client\Response;

class HttpClientWithLogger extends AbstractHttpDecorator
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        private readonly HttpLoggerInterface $logger
    ) {
        parent::__construct($httpClient);
    }

    /**
     * @param array<string, scalar|null> $params
     */
    public function get(string $url, array $params = []): Response
    {
        $response = $this->httpClient->get($url, $params);

        $this->logger->logHttpResponse($response, $url);

        return $response;
    }
}
