<?php

namespace Illuminate\Foundation;

use Illuminate\Container\Container;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class LaravelCloudJsonFormatter extends JsonFormatter
{
    /**
     * {@inheritdoc}
     */
    protected function normalizeRecord(LogRecord $record): array
    {
        $normalized = parent::normalizeRecord($record);

        $app = Container::getInstance();

        if ($app->bound('request')) {
            $requestId = $app->make('request')->header('Cloud-Request-ID');

            if ($requestId !== null) {
                $normalized['cloud_request_id'] = $requestId;
            }
        }

        return $normalized;
    }
}
