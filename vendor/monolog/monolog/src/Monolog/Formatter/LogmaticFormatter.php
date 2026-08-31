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
 * Encodes message information into JSON in a format compatible with Logmatic.
 *
 * @author Julien Breux <julien.breux@gmail.com>
 */
class LogmaticFormatter extends JsonFormatter
{
    protected const MARKERS = ["sourcecode", "php"];

    protected string $hostname = '';

    protected string $appName = '';

    /**
     * @return $this
     */
    public function setHostname(string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return $this
     */
    public function setAppName(string $appName): self
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Appends the 'hostname' and 'appname' parameter for indexing by Logmatic.
     *
     * @see http://doc.logmatic.io/docs/basics-to-send-data
     * @see \Monolog\Formatter\JsonFormatter::format()
     */
    public function normalizeRecord(LogRecord $record): array
    {
        $record = parent::normalizeRecord($record);

        if ($this->hostname !== '') {
            $record["hostname"] = $this->hostname;
        }
        if ($this->appName !== '') {
            $record["appname"] = $this->appName;
        }

        $record["@marker"] = static::MARKERS;

        return $record;
    }
}
