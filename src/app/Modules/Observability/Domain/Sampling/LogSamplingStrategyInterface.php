<?php

namespace App\Modules\Observability\Domain\Sampling;

interface LogSamplingStrategyInterface
{
    /**
     * @param string $level
     * @param array<string, mixed> $context
     * @return bool
     */
    public function shouldLog(string $level, array $context = []): bool;
}
