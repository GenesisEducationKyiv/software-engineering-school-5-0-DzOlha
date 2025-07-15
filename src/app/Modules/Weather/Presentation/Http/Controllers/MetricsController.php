<?php

namespace App\Modules\Weather\Presentation\Http\Controllers;

use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

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
