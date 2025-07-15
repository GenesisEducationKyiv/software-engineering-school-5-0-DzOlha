<?php

namespace App\Modules\Weather\Infrastructure\HttpClient\Logger;

use App\Modules\Weather\Application\HttpClient\Logger\HttpLoggerInterface;
use Illuminate\Http\Client\Response;

class FileHttpLogger implements HttpLoggerInterface
{
    private const CONTENT_TYPE_JSON = 'application/json';
    private const LOG_FILE = 'weather.log';
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private const SECONDS_TO_MILLISECONDS = 1000;
    private const MILLISECONDS_PRECISION = 2;

    public function logHttpResponse(Response $response, string $url): void
    {
        $statusCode = $response->status();
        $contentType = $response->header('Content-Type');
        $durationMs = $this->resolveDuration($response);
        $timestamp = now()->format(self::DATE_FORMAT);

        $logEntry = [
            'timestamp' => $timestamp,
            'url' => $url,
            'status' => $statusCode,
            'duration_ms' => $durationMs,
            'content_type' => $contentType,
            'body' => $this->parseBody($response, $contentType),
        ];

        $formatted = sprintf("[%s] HTTP Response:\n%s\n\n", $timestamp, json_encode(
            $logEntry,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));

        file_put_contents(
            storage_path('logs/' . self::LOG_FILE),
            $formatted,
            FILE_APPEND
        );
    }

    private function resolveDuration(Response $response): float|string
    {
        $transferTime = $response->transferStats?->getTransferTime();

        return $transferTime
            ? round(
                $transferTime * self::SECONDS_TO_MILLISECONDS,
                self::MILLISECONDS_PRECISION
            )
            : 'n/a';
    }

    private function parseBody(Response $response, string $contentType): mixed
    {
        if (str_starts_with($contentType, self::CONTENT_TYPE_JSON)) {
            return $response->json();
        }

        return $response->body();
    }
}
