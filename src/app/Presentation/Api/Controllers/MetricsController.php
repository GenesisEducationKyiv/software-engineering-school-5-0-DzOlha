<?php

namespace App\Presentation\Api\Controllers;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use  Illuminate\Http\Response;

class MetricsController
{
    /**
     * @throws \Throwable
     */
    public function metrics(CollectorRegistry $registry): Response
    {
        $renderer = new RenderTextFormat();
        $metrics = $registry->getMetricFamilySamples();

        return response($renderer->render($metrics), 200, [
            'Content-Type' => RenderTextFormat::MIME_TYPE,
        ]);
    }
}
