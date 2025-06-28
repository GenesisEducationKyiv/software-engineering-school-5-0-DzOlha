<?php

namespace App\Application\Weather\HttpClient\Decorators;

use App\Application\Weather\HttpClient\HttpClientInterface;
use App\Application\Weather\HttpClient\Logger\HttpLoggerInterface;
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
