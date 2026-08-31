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

use Monolog\LogRecord;

/**
 * Interface for formatters
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface FormatterInterface
{
    /**
     * Formats a log record.
     *
     * @param  LogRecord $record A record to format
     * @return mixed     The formatted record
     */
    public function format(LogRecord $record);

    /**
     * Formats a set of log records.
     *
     * @param  array<LogRecord> $records A set of records to format
     * @return mixed            The formatted set of records
     */
    public function formatBatch(array $records);
}
