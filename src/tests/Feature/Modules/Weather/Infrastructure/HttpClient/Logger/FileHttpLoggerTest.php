<?php

namespace Tests\Feature\Infrastructure\Weather\HttpClient\Logger;

use App\Modules\Weather\Infrastructure\HttpClient\Logger\FileHttpLogger;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

class FileHttpLoggerTest extends TestCase
{
    private string $logPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logPath = storage_path('logs/weather.log');
        @unlink($this->logPath); // Safe delete
    }

    public function test_it_logs_response_to_file(): void
    {
        $url = 'https://api.weather.com/data';
        $data = ['temp' => 20];
        $body = json_encode($data);
        $duration = 0.1234;

        $response = $this->createMock(Response::class);
        $response->method('status')->willReturn(200);
        $response->method('header')->with('Content-Type')->willReturn('application/json');
        $response->method('json')->willReturn($data);
        $response->method('body')->willReturn($body);
        $response->transferStats = new class ($duration) {
            public function __construct(private float $time)
            {
            }
            public function getTransferTime(): float
            {
                return $this->time;
            }
        };

        $logger = new FileHttpLogger();
        $logger->logHttpResponse($response, $url);

        $this->assertFileExists($this->logPath);
        $contents = file_get_contents($this->logPath);

        $this->assertStringContainsString('HTTP Response', $contents);
        $this->assertStringContainsString($url, $contents);
        $this->assertStringContainsString('"status": 200', $contents);
        $this->assertStringContainsString('"temp": 20', $contents);
        $this->assertStringContainsString('"duration_ms": 123.4', $contents);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        @unlink($this->logPath);
    }
}
