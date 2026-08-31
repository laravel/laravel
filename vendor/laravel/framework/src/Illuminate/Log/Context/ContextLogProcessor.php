<?php

namespace Illuminate\Log\Context;

use Illuminate\Container\Container;
use Illuminate\Contracts\Log\ContextLogProcessor as ContextLogProcessorContract;
use Illuminate\Log\Context\Repository as ContextRepository;
use Monolog\LogRecord;

class ContextLogProcessor implements ContextLogProcessorContract
{
    /**
     * Add contextual data to the log's "extra" parameter.
     *
     * @param  \Monolog\LogRecord  $record
     * @return \Monolog\LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $app = Container::getInstance();

        if (! $app->bound(ContextRepository::class)) {
            return $record;
        }

        return $record->with(extra: [
            ...$record->extra,
            ...$app->get(ContextRepository::class)->all(),
        ]);
    }
}
