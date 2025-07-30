<?php

namespace App\Modules\Observability\Domain\Sampling;

class ConfigBasedSamplingStrategy implements LogSamplingStrategyInterface
{
    /**
     * @param string $level
     * @param array<string, mixed> $context
     * @return bool
     */
    public function shouldLog(string $level, array $context = []): bool
    {
        $probability = config("observability.logging.sampling.$level", 1.0);
        return mt_rand() / mt_getrandmax() <= $probability;
    }
}
