<?php

namespace App\Modules\Observability\Infrastructure\Metrics;

use App\Modules\Observability\Application\Metrics\MetricsServiceInterface;
use OpenTelemetry\API\Metrics\CounterInterface;
use OpenTelemetry\API\Metrics\HistogramInterface;
use OpenTelemetry\API\Metrics\MeterInterface;
use OpenTelemetry\API\Metrics\ObservableGaugeInterface;
use OpenTelemetry\API\Metrics\ObserverInterface;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;

class OpenTelemetryMetricsExporter implements MetricsServiceInterface
{
    private MeterInterface $meter;
    private ExportingReader $reader;

    /**
     * @var array<string, CounterInterface>
     */
    private array $counters = [];
    /**
     * @var array<string, HistogramInterface>
     */
    private array $histograms = [];
    /**
     * @var array<string, ObservableGaugeInterface>
     */
    private array $gauges = [];
    /**
     * @var array<string, array{
     *     value: float,
     *     attributes: array<non-empty-string, array<mixed>|bool|float|int|string|null>
     * }>
     */
    private array $gaugeValues = [];

    public function __construct(string $endpoint)
    {
        $transport = (new OtlpHttpTransportFactory())->create(
            $endpoint,
            'application/x-protobuf'
        );

        $exporter = new MetricExporter($transport);

        $this->reader = new ExportingReader(
            exporter: $exporter
        );

        $meterProvider = MeterProvider::builder()
            ->addReader($this->reader)
            ->build();

        $this->meter = $meterProvider->getMeter('weather-app');
    }

    /**
     * @param string $name
     * @param int|float $value
     * @param array<string, mixed> $attributes
     * @return void
     */
    public function counter(string $name, int|float $value, array $attributes = []): void
    {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = $this->meter->createCounter($name);
        }

        $this->counters[$name]->add($value, $this->normalizeAttributes($attributes));
    }

    /**
     * @param string $name
     * @param float $value
     * @param array<string, mixed> $attributes
     * @return void
     */
    public function gauge(string $name, float $value, array $attributes = []): void
    {
        ksort($attributes);
        $key = $name . '_' . json_encode($attributes);

        $normalizedAttributes = $this->normalizeAttributes($attributes);
        $this->gaugeValues[$key] = ['value' => $value, 'attributes' => $normalizedAttributes];

        if (!isset($this->gauges[$name])) {
            $this->gauges[$name] = $this->meter->createObservableGauge($name);

            $this->gauges[$name]->observe(function (ObserverInterface $observer) use ($name) {
                foreach ($this->gaugeValues as $gaugeKey => $data) {
                    if (str_starts_with($gaugeKey, $name . '_')) {
                        $observer->observe($data['value'], $data['attributes']);
                    }
                }
            });
        }
    }

    /**
     * @param string $name
     * @param float $value
     * @param array<string, mixed> $attributes
     * @return void
     */
    public function histogram(string $name, float $value, array $attributes = []): void
    {
        if (!isset($this->histograms[$name])) {
            $this->histograms[$name] = $this->meter->createHistogram($name);
        }

        $this->histograms[$name]->record($value, $this->normalizeAttributes($attributes));
    }

    public function export(): void
    {
        $this->reader->collect();
    }

    /**
     * Normalize attributes to match OpenTelemetry's expected format
     *
     * @param array<string, mixed> $attributes
     * @return array<non-empty-string, array<mixed>|bool|float|int|string|null>
     */
    private function normalizeAttributes(array $attributes): array
    {
        $normalized = [];

        foreach ($attributes as $key => $value) {
            if ($key === '') {
                continue;
            }

            if (
                is_array($value) || is_bool($value)
                || is_float($value) || is_int($value)
                || is_string($value) || $value === null
            ) {
                $normalized[$key] = $value;
            } else {
                if (is_object($value)) {
                    if (method_exists($value, '__toString')) {
                        $normalized[$key] = (string) $value;
                    } else {
                        $normalized[$key] = get_class($value);
                    }
                } elseif (is_resource($value)) {
                    $normalized[$key] = 'resource';
                } else {
                    $normalized[$key] = 'unknown';
                }
            }
        }

        return $normalized;
    }
}
