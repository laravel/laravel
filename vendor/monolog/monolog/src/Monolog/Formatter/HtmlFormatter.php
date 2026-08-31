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
use Monolog\Utils;
use Monolog\LogRecord;

/**
 * Formats incoming records into an HTML table
 *
 * This is especially useful for html email logging
 *
 * @author Tiago Brito <tlfbrito@gmail.com>
 */
class HtmlFormatter extends NormalizerFormatter
{
    /**
     * Translates Monolog log levels to html color priorities.
     */
    protected function getLevelColor(Level $level): string
    {
        return match ($level) {
            Level::Debug     => '#CCCCCC',
            Level::Info      => '#28A745',
            Level::Notice    => '#17A2B8',
            Level::Warning   => '#FFC107',
            Level::Error     => '#FD7E14',
            Level::Critical  => '#DC3545',
            Level::Alert     => '#821722',
            Level::Emergency => '#000000',
        };
    }

    /**
     * @param string|null $dateFormat The format of the timestamp: one supported by DateTime::format
     */
    public function __construct(?string $dateFormat = null)
    {
        parent::__construct($dateFormat);
    }

    /**
     * Creates an HTML table row
     *
     * @param string $th       Row header content
     * @param string $td       Row standard cell content
     * @param bool   $escapeTd false if td content must not be html escaped
     */
    protected function addRow(string $th, string $td = ' ', bool $escapeTd = true): string
    {
        $th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
        if ($escapeTd) {
            $td = '<pre>'.htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8').'</pre>';
        }

        return "<tr style=\"padding: 4px;text-align: left;\">\n<th style=\"vertical-align: top;background: #ccc;color: #000\" width=\"100\">$th:</th>\n<td style=\"padding: 4px;text-align: left;vertical-align: top;background: #eee;color: #000\">".$td."</td>\n</tr>";
    }

    /**
     * Create a HTML h1 tag
     *
     * @param string $title Text to be in the h1
     */
    protected function addTitle(string $title, Level $level): string
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');

        return '<h1 style="background: '.$this->getLevelColor($level).';color: #ffffff;padding: 5px;" class="monolog-output">'.$title.'</h1>';
    }

    /**
     * Formats a log record.
     *
     * @return string The formatted record
     */
    public function format(LogRecord $record): string
    {
        $output = $this->addTitle($record->level->getName(), $record->level);
        $output .= '<table cellspacing="1" width="100%" class="monolog-output">';

        $output .= $this->addRow('Message', $record->message);
        $output .= $this->addRow('Time', $this->formatDate($record->datetime));
        $output .= $this->addRow('Channel', $record->channel);
        if (\count($record->context) > 0) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record->context as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Context', $embeddedTable, false);
        }
        if (\count($record->extra) > 0) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record->extra as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Extra', $embeddedTable, false);
        }

        return $output.'</table>';
    }

    /**
     * Formats a set of log records.
     *
     * @return string The formatted set of records
     */
    public function formatBatch(array $records): string
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    /**
     * @param mixed $data
     */
    protected function convertToString($data): string
    {
        if (null === $data || \is_scalar($data)) {
            return (string) $data;
        }

        $data = $this->normalize($data);

        return Utils::jsonEncode($data, JSON_PRETTY_PRINT | Utils::DEFAULT_JSON_FLAGS, true);
    }
}
