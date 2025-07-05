<?php

namespace Tests\Feature\Presentation\Api\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\RenderTextFormat;

class MetricsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_endpoint_returns_prometheus_metrics()
    {
        $adapter = new InMemory();
        $registry = new CollectorRegistry($adapter);


        $counter = $registry->registerCounter(
            'test',
            'example_counter',
            'An example counter',
            ['label']
        );
        $counter->inc(['value']);

        $this->app->instance(CollectorRegistry::class, $registry);

        $response = $this->get('/metrics');

        $response->assertStatus(200);

        $contentType = $response->headers->get('Content-Type');
        $this->assertStringStartsWith(RenderTextFormat::MIME_TYPE, $contentType);

        $response->assertSee('example_counter');
        $response->assertSee('value');
    }
}
