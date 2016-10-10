<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

/**
 * Encodes message information into JSON in a format compatible with Loggly.
 *
 * @author Adam Pancutt <adam@pancutt.com>
 */
class LogglyFormatter extends JsonFormatter
{
    /**
     * Overrides the default batch mode to new lines for compatibility with the
     * Loggly bulk API.
     *
     * @param int $batchMode
     */
    public function __construct($batchMode = self::BATCH_MODE_NEWLINES, $appendNewline = false)
    {
        parent::__construct($batchMode, $appendNewline);
    }

    /**
     * Appends the 'timestamp' parameter for indexing by Loggly.
     *
     * @see https://www.loggly.com/docs/automated-parsing/#json
     * @see \Monolog\Formatter\JsonFormatter::format()
     */
    public function format(array $record)
    {
        if (isset($record["datetime"]) && ($record["datetime"] instanceof \DateTime)) {
            $record["timestamp"] = $record["datetime"]->format("Y-m-d\TH:i:s.uO");
            // TODO 2.0 unset the 'datetime' parameter, retained for BC
        }

        return parent::format($record);
    }
}
