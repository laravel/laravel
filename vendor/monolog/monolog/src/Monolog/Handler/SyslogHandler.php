<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;

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
    protected $ident;
    protected $logopts;

    /**
     * @param string  $ident
     * @param mixed   $facility
     * @param int     $level    The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not
     * @param int     $logopts  Option flags for the openlog() call, defaults to LOG_PID
     */
    public function __construct($ident, $facility = LOG_USER, $level = Logger::DEBUG, $bubble = true, $logopts = LOG_PID)
    {
        parent::__construct($facility, $level, $bubble);

        $this->ident = $ident;
        $this->logopts = $logopts;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        closelog();
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (!openlog($this->ident, $this->logopts, $this->facility)) {
            throw new \LogicException('Can\'t open syslog for ident "'.$this->ident.'" and facility "'.$this->facility.'"');
        }
        syslog($this->logLevels[$record['level']], (string) $record['formatted']);
    }
}
