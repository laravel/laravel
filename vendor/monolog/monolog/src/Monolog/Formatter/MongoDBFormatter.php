<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use MongoDB\BSON\Type;
use MongoDB\BSON\UTCDateTime;
use Monolog\Utils;
use Monolog\LogRecord;

/**
 * Formats a record for use with the MongoDBHandler.
 *
 * @author Florian Plattner <me@florianplattner.de>
 */
class MongoDBFormatter implements FormatterInterface
{
    private bool $exceptionTraceAsString;
    private int $maxNestingLevel;

    /**
     * @param int  $maxNestingLevel        0 means infinite nesting, the $record itself is level 1, $record->context is 2
     * @param bool $exceptionTraceAsString set to false to log exception traces as a sub documents instead of strings
     */
    public function __construct(int $maxNestingLevel = 3, bool $exceptionTraceAsString = true)
    {
        $this->maxNestingLevel = max($maxNestingLevel, 0);
        $this->exceptionTraceAsString = $exceptionTraceAsString;
    }

    /**
     * @inheritDoc
     *
     * @return mixed[]
     */
    public function format(LogRecord $record): array
    {
        /** @var mixed[] $res */
        $res = $this->formatArray($record->toArray());

        return $res;
    }

    /**
     * @inheritDoc
     *
     * @return array<mixed[]>
     */
    public function formatBatch(array $records): array
    {
        $formatted = [];
        foreach ($records as $key => $record) {
            $formatted[$key] = $this->format($record);
        }

        return $formatted;
    }

    /**
     * @param  mixed[]        $array
     * @return mixed[]|string Array except when max nesting level is reached then a string "[...]"
     */
    protected function formatArray(array $array, int $nestingLevel = 0)
    {
        if ($this->maxNestingLevel > 0 && $nestingLevel > $this->maxNestingLevel) {
            return '[...]';
        }

        foreach ($array as $name => $value) {
            if ($value instanceof \DateTimeInterface) {
                $array[$name] = $this->formatDate($value, $nestingLevel + 1);
            } elseif ($value instanceof \Throwable) {
                $array[$name] = $this->formatException($value, $nestingLevel + 1);
            } elseif (\is_array($value)) {
                $array[$name] = $this->formatArray($value, $nestingLevel + 1);
            } elseif (\is_object($value) && !$value instanceof Type) {
                $array[$name] = $this->formatObject($value, $nestingLevel + 1);
            }
        }

        return $array;
    }

    /**
     * @param  mixed          $value
     * @return mixed[]|string
     */
    protected function formatObject($value, int $nestingLevel)
    {
        $objectVars = get_object_vars($value);
        $objectVars['class'] = Utils::getClass($value);

        return $this->formatArray($objectVars, $nestingLevel);
    }

    /**
     * @return mixed[]|string
     */
    protected function formatException(\Throwable $exception, int $nestingLevel)
    {
        $formattedException = [
            'class' => Utils::getClass($exception),
            'message' => $exception->getMessage(),
            'code' => (int) $exception->getCode(),
            'file' => $exception->getFile() . ':' . $exception->getLine(),
        ];

        if ($this->exceptionTraceAsString === true) {
            $formattedException['trace'] = $exception->getTraceAsString();
        } else {
            $formattedException['trace'] = $exception->getTrace();
        }

        return $this->formatArray($formattedException, $nestingLevel);
    }

    protected function formatDate(\DateTimeInterface $value, int $nestingLevel): UTCDateTime
    {
        return new UTCDateTime((int) floor(((float) $value->format('U.u')) * 1000));
    }
}
