<?php

namespace App\Modules\Observability\Application\Logging;

use Monolog\Level;

interface LoggingServiceInterface
{
    /**
     * @param Level $level
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function log(Level $level, string $message, array $context = []): void;

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logInfo(string $message, array $context = []): void;

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logDebug(string $message, array $context = []): void;

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logWarn(string $message, array $context = []): void;

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logError(string $message, array $context = []): void;
}
