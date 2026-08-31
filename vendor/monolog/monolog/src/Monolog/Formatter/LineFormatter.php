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

use Closure;
use Monolog\Utils;
use Monolog\LogRecord;

/**
 * Formats incoming records into a one-line string
 *
 * This is especially useful for logging to files
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Christophe Coevoet <stof@notk.org>
 */
class LineFormatter extends NormalizerFormatter
{
    public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

    protected string $format;
    protected bool $allowInlineLineBreaks;
    protected bool $ignoreEmptyContextAndExtra;
    protected bool $includeStacktraces;
    protected ?int $maxLevelNameLength = null;
    protected string $indentStacktraces = '';
    protected Closure|null $stacktracesParser = null;
    protected string $basePath = '';

    /**
     * @param string|null $format                The format of the message
     * @param string|null $dateFormat            The format of the timestamp: one supported by DateTime::format
     * @param bool        $allowInlineLineBreaks Whether to allow inline line breaks in log entries
     */
    public function __construct(?string $format = null, ?string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false, bool $includeStacktraces = false)
    {
        $this->format = $format === null ? static::SIMPLE_FORMAT : $format;
        $this->allowInlineLineBreaks = $allowInlineLineBreaks;
        $this->ignoreEmptyContextAndExtra = $ignoreEmptyContextAndExtra;
        $this->includeStacktraces($includeStacktraces);
        parent::__construct($dateFormat);
    }

    /**
     * Setting a base path will hide the base path from exception and stack trace file names to shorten them
     * @return $this
     */
    public function setBasePath(string $path = ''): self
    {
        if ($path !== '') {
            $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        $this->basePath = $path;

        return $this;
    }

    /**
     * @return $this
     */
    public function includeStacktraces(bool $include = true, ?Closure $parser = null): self
    {
        $this->includeStacktraces = $include;
        if ($this->includeStacktraces) {
            $this->allowInlineLineBreaks = true;
            $this->stacktracesParser = $parser;
        }

        return $this;
    }

    /**
     * Indent stack traces to separate them a bit from the main log record messages
     *
     * @param  string $indent The string used to indent, for example "    "
     * @return $this
     */
    public function indentStacktraces(string $indent): self
    {
        $this->indentStacktraces = $indent;

        return $this;
    }

    /**
     * @return $this
     */
    public function allowInlineLineBreaks(bool $allow = true): self
    {
        $this->allowInlineLineBreaks = $allow;

        return $this;
    }

    /**
     * @return $this
     */
    public function ignoreEmptyContextAndExtra(bool $ignore = true): self
    {
        $this->ignoreEmptyContextAndExtra = $ignore;

        return $this;
    }

    /**
     * Allows cutting the level name to get fixed-length levels like INF for INFO, ERR for ERROR if you set this to 3 for example
     *
     * @param  int|null $maxLevelNameLength Maximum characters for the level name. Set null for infinite length (default)
     * @return $this
     */
    public function setMaxLevelNameLength(?int $maxLevelNameLength = null): self
    {
        $this->maxLevelNameLength = $maxLevelNameLength;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function format(LogRecord $record): string
    {
        $vars = parent::format($record);

        if ($this->maxLevelNameLength !== null) {
            $vars['level_name'] = substr($vars['level_name'], 0, $this->maxLevelNameLength);
        }

        $output = $this->format;
        foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }

        foreach ($vars['context'] as $var => $val) {
            if (false !== strpos($output, '%context.'.$var.'%')) {
                $output = str_replace('%context.'.$var.'%', $this->stringify($val), $output);
                unset($vars['context'][$var]);
            }
        }

        if ($this->ignoreEmptyContextAndExtra) {
            if (\count($vars['context']) === 0) {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            if (\count($vars['extra']) === 0) {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }
        }

        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->stringify($val), $output);
            }
        }

        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
            if (null === $output) {
                $pcreErrorCode = preg_last_error();

                throw new \RuntimeException('Failed to run preg_replace: ' . $pcreErrorCode . ' / ' . preg_last_error_msg());
            }
        }

        return $output;
    }

    public function formatBatch(array $records): string
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    /**
     * @param mixed $value
     */
    public function stringify($value): string
    {
        return $this->replaceNewlines($this->convertToString($value));
    }

    protected function normalizeException(\Throwable $e, int $depth = 0): string
    {
        $str = $this->formatException($e);

        $previous = $e->getPrevious();
        while ($previous instanceof \Throwable) {
            $depth++;
            if ($depth > $this->maxNormalizeDepth) {
                $str .= "\n[previous exception] Over " . $this->maxNormalizeDepth . ' levels deep, aborting normalization';
                break;
            }
            $str .= "\n[previous exception] " . $this->formatException($previous);
            $previous = $previous->getPrevious();
        }

        return $str;
    }

    /**
     * @param mixed $data
     */
    protected function convertToString($data): string
    {
        if (null === $data || \is_bool($data)) {
            return var_export($data, true);
        }

        if (\is_scalar($data)) {
            return (string) $data;
        }

        return $this->toJson($data, true);
    }

    protected function replaceNewlines(string $str): string
    {
        if ($this->allowInlineLineBreaks) {
            if (0 === strpos($str, '{') || 0 === strpos($str, '[')) {
                $str = preg_replace('/(?<!\\\\)\\\\[rn]/', "\n", $str);
                if (null === $str) {
                    $pcreErrorCode = preg_last_error();

                    throw new \RuntimeException('Failed to run preg_replace: ' . $pcreErrorCode . ' / ' . preg_last_error_msg());
                }
            }

            return $str;
        }

        return str_replace(["\r\n", "\r", "\n"], ' ', $str);
    }

    private function formatException(\Throwable $e): string
    {
        $str = '[object] (' . Utils::getClass($e) . '(code: ' . $e->getCode();
        if ($e instanceof \SoapFault) {
            if (isset($e->faultcode)) {
                $str .= ' faultcode: ' . $e->faultcode;
            }

            if (isset($e->faultactor)) {
                $str .= ' faultactor: ' . $e->faultactor;
            }

            if (isset($e->detail)) {
                if (\is_string($e->detail)) {
                    $str .= ' detail: ' . $e->detail;
                } elseif (\is_object($e->detail) || \is_array($e->detail)) {
                    $str .= ' detail: ' . $this->toJson($e->detail, true);
                }
            }
        }

        $file = $e->getFile();
        if ($this->basePath !== '') {
            $file = preg_replace('{^'.preg_quote($this->basePath).'}', '', $file);
        }

        $str .= '): ' . $e->getMessage() . ' at ' . strtr((string) $file, DIRECTORY_SEPARATOR, '/') . ':' . $e->getLine() . ')';

        if ($this->includeStacktraces) {
            $str .= $this->stacktracesParser($e);
        }

        return $str;
    }

    private function stacktracesParser(\Throwable $e): string
    {
        $trace = $e->getTraceAsString();

        if ($this->basePath !== '') {
            $trace = preg_replace('{^(#\d+ )' . preg_quote($this->basePath) . '}m', '$1', $trace) ?? $trace;
        }

        if ($this->stacktracesParser !== null) {
            $trace = $this->stacktracesParserCustom($trace);
        }

        if ($this->indentStacktraces !== '') {
            $trace = str_replace("\n", "\n{$this->indentStacktraces}", $trace);
        }

        if (trim($trace) === '') {
            return '';
        }

        return "\n{$this->indentStacktraces}[stacktrace]\n{$this->indentStacktraces}" . strtr($trace, DIRECTORY_SEPARATOR, '/') . "\n";
    }

    private function stacktracesParserCustom(string $trace): string
    {
        return implode("\n", array_filter(array_map($this->stacktracesParser, explode("\n", $trace)), fn ($line) => is_string($line) && trim($line) !== ''));
    }
}
