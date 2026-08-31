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

use Monolog\Level;
use Monolog\LogRecord;

/**
 * Serializes a log message according to RFC 5424
 *
 * @author Dalibor Karlović <dalibor.karlovic@sigwin.hr>
 * @author Renat Gabdullin <renatobyj@gmail.com>
 */
class SyslogFormatter extends LineFormatter
{
    private const SYSLOG_FACILITY_USER = 1;
    private const FORMAT = "<%extra.priority%>1 %datetime% %extra.hostname% %extra.app-name% %extra.procid% %channel% %extra.structured-data% %level_name%: %message% %context% %extra%\n";
    private const NILVALUE = '-';

    private string $hostname;
    private int $procid;

    public function __construct(private string $applicationName = self::NILVALUE)
    {
        parent::__construct(self::FORMAT, 'Y-m-d\TH:i:s.uP', true, true);
        $this->hostname = (string) gethostname();
        $this->procid = (int) getmypid();
    }

    public function format(LogRecord $record): string
    {
        $record->extra = $this->formatExtra($record);

        return parent::format($record);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatExtra(LogRecord $record): array
    {
        $extra = $record->extra;
        $extra['app-name'] = $this->applicationName;
        $extra['hostname'] = $this->hostname;
        $extra['procid'] = $this->procid;
        $extra['priority'] = self::calculatePriority($record->level);
        $extra['structured-data'] = self::NILVALUE;

        return $extra;
    }

    private static function calculatePriority(Level $level): int
    {
        return (self::SYSLOG_FACILITY_USER * 8) + $level->toRFC5424Level();
    }
}
