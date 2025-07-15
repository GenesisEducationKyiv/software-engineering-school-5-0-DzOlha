<?php

namespace Tests\Unit\Modules\Weather\Application\HttpClient\Decorators;

use App\Modules\Weather\Application\HttpClient\Decorators\HttpClientWithLogger;
use App\Modules\Weather\Application\HttpClient\HttpClientInterface;
use App\Modules\Weather\Application\HttpClient\Logger\HttpLoggerInterface;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

class HttpClientWithLoggerTest extends TestCase
{
    public function test_get_delegates_request_and_logs_response(): void
    {
        $url = 'https://api.example.com/data';
        $params = ['q' => 'Kyiv'];

        $mockResponse = $this->createMock(Response::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('get')
            ->with($url, $params)
            ->willReturn($mockResponse);

        $logger = $this->createMock(HttpLoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('logHttpResponse')
            ->with($mockResponse, $url);

        $client = new HttpClientWithLogger($httpClient, $logger);

        $this->assertSame($mockResponse, $client->get($url, $params));
    }
}
