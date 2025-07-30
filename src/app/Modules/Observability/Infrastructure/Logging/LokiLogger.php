<?php

namespace App\Modules\Observability\Infrastructure\Logging;

use App\Modules\Observability\Application\Logging\LoggingServiceInterface;
use App\Modules\Observability\Domain\Sampling\LogSamplingStrategyInterface;
use Exception;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class LokiLogger implements LoggingServiceInterface
{
    private Logger $logger;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly LogSamplingStrategyInterface $samplingStrategy
    ) {
        $this->logger = new Logger('loki');

        /**
         * @var string $logPath
         */
        $logPath = config("observability.logging.loki_path");
        $this->ensureLogFileExists($logPath);

        $handler = new StreamHandler($logPath);
        $formatter = new class extends JsonFormatter {
            protected function toJson(mixed $data, bool $ignoreErrors = false): string
            {
                /**
                 * @var non-empty-string $encoded
                 */
                $encoded = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                return $encoded;
            }
        };
        $handler->setFormatter($formatter);

        $this->logger->pushHandler($handler);
    }

    /**
     * Ensures the log file and its parent directory exist
     *
     * @param string $logPath
     * @throws \RuntimeException
     * @throws Exception
     */
    private function ensureLogFileExists(string $logPath): void
    {
        $logDir = dirname($logPath);

        try {
            if (!is_dir($logDir)) {
                if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                    throw new \RuntimeException("Cannot create directory: {$logDir}");
                }
            }

            if (!file_exists($logPath)) {
                if (touch($logPath) === false) {
                    throw new \RuntimeException("Cannot create log file: {$logPath}");
                }
                chmod($logPath, 0644);
            }

            if (!is_writable($logPath)) {
                throw new \RuntimeException("Log file is not writable: {$logPath}");
            }
        } catch (\Exception $e) {
            error_log("LokiLogger file creation failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param Level $level
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function log(Level $level, string $message, array $context = []): void
    {
        if (!$this->samplingStrategy->shouldLog($level->getName(), $context)) {
            return;
        }
        $enrichedContext = ContextEnricher::enrich($context);
        $this->logger->log($level, $message, $enrichedContext);
    }

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logInfo(string $message, array $context = []): void
    {
        $this->log(Level::Info, $message, $context);
    }

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logDebug(string $message, array $context = []): void
    {
        $this->log(Level::Debug, $message, $context);
    }

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logWarn(string $message, array $context = []): void
    {
        $this->log(Level::Warning, $message, $context);
    }

    /**
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function logError(string $message, array $context = []): void
    {
        $this->log(Level::Error, $message, $context);
    }
}
