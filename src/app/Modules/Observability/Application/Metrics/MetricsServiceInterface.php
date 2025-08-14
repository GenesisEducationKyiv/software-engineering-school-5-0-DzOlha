<?php

namespace App\Modules\Observability\Application\Metrics;

interface MetricsServiceInterface
{
    /**
     * @param string $name
     * @param int|float $value
     * @param array<string, mixed> $attributes
     * @return void
     */
    public function counter(string $name, int|float $value, array $attributes = []): void;

    /**
     * @param string $name
     * @param float $value
     * @param array<string, mixed> $attributes
     * @return void
     */
    public function gauge(string $name, float $value, array $attributes = []): void;

    /**
     * @param string $name
     * @param float $value
     * @param array<string, mixed> $attributes
     * @return void
     */
    public function histogram(string $name, float $value, array $attributes = []): void;
    public function export(): void;
}
