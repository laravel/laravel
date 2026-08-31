<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Level;
use Monolog\LogRecord;

/**
 * Logs to syslog service.
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $syslog = new SyslogHandler('myfacility', 'local6');
 *   $formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
 *   $syslog->setFormatter($formatter);
 *   $log->pushHandler($syslog);
 *
 * @author Sven Paulus <sven@karlsruhe.org>
 */
class SyslogHandler extends AbstractSyslogHandler
{
    protected string $ident;
    protected int $logopts;

    /**
     * @param string|int $facility Either one of the names of the keys in $this->facilities, or a LOG_* facility constant
     * @param int        $logopts  Option flags for the openlog() call, defaults to LOG_PID
     */
    public function __construct(string $ident, string|int $facility = LOG_USER, int|string|Level $level = Level::Debug, bool $bubble = true, int $logopts = LOG_PID)
    {
        parent::__construct($facility, $level, $bubble);

        $this->ident = $ident;
        $this->logopts = $logopts;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        closelog();
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        openlog($this->ident, $this->logopts, $this->facility);
        syslog($this->toSyslogPriority($record->level), (string) $record->formatted);
    }
}
