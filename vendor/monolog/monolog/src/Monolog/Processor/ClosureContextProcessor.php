<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\LogRecord;

/**
 * Generates a context from a Closure if the Closure is the only value
 * in the context
 *
 * It helps reduce the performance impact of debug logs if they do
 * need to create lots of context information. If this processor is added
 * on the correct handler the context data will only be generated
 * when the logs are actually logged to that handler, which is useful when
 * using FingersCrossedHandler or other filtering handlers to conditionally
 * log records.
 */
class ClosureContextProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $record->context;
        if (isset($context[0]) && 1 === \count($context) && $context[0] instanceof \Closure) {
            try {
                $context = $context[0]();
            } catch (\Throwable $e) {
                $context = [
                    'error_on_context_generation' => $e->getMessage(),
                    'exception' => $e,
                ];
            }

            if (!\is_array($context)) {
                $context = [$context];
            }

            $record = $record->with(context: $context);
        }

        return $record;
    }
}
