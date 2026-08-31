<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Helper;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Debug logging utility for interactive readline.
 *
 * All methods are no-ops unless explicitly enabled at DEBUG verbosity.
 * When enabled, creates a temp file and logs state changes, completion
 * matching, suggestion behavior, etc.
 *
 * Enable via PsySH verbosity level (-vvv):
 *   bin/psysh -vvv
 */
class DebugLog
{
    /** @var resource|null */
    private static $handle = null;
    private static bool $enabled = false;
    private static string $logPath = '';

    /**
     * Enable debug logging if verbosity is at DEBUG level.
     */
    public static function enable(int $verbosity): void
    {
        if ($verbosity < OutputInterface::VERBOSITY_DEBUG) {
            return;
        }

        $path = @\tempnam(\sys_get_temp_dir(), 'psysh-debug-');
        if ($path === false) {
            return;
        }

        $handle = @\fopen($path, 'a');
        if ($handle === false) {
            @\unlink($path);

            return;
        }

        self::$enabled = true;
        self::$logPath = $path;
        self::$handle = $handle;

        \register_shutdown_function(static function () {
            if (self::$handle !== null && \is_resource(self::$handle)) {
                \fclose(self::$handle);
                self::$handle = null;
            }
        });
    }

    /**
     * Log a debug message.
     *
     * @param string $category Category/component (e.g., "Suggestion", "Completion")
     * @param string $message  Concise message describing the event
     * @param array  $context  Optional key-value context data
     */
    public static function log(string $category, string $message, $context = []): void
    {
        if (!self::$enabled) {
            return;
        }

        $contextStr = '';

        if (!empty($context)) {
            if (\is_array($context)) {
                $parts = [];
                foreach ($context as $key => $value) {
                    if (\is_string($value)) {
                        $parts[] = "{$key}=\"".self::escapeValue($value).'"';
                    } elseif (\is_bool($value)) {
                        $parts[] = "{$key}=".($value ? 'true' : 'false');
                    } elseif ($value === null) {
                        $parts[] = "{$key}=null";
                    } else {
                        $parts[] = "{$key}={$value}";
                    }
                }
                $contextStr = ' ('.\implode(', ', $parts).')';
            } else {
                $contextStr = ' ('.self::escapeValue((string) $context).')';
            }
        }

        self::writeLine("{$category}: {$message}{$contextStr}");
    }

    /**
     * Log a separator for readability.
     */
    public static function separator(string $label = ''): void
    {
        if (!self::$enabled) {
            return;
        }

        $sep = \str_repeat('-', 60);
        self::writeLine($label ? "{$sep} {$label} {$sep}" : $sep);
    }

    /**
     * Write a timestamped line to the log file.
     */
    private static function writeLine(string $content): void
    {
        if (self::$handle !== null && \is_resource(self::$handle)) {
            $timestamp = \date('H:i:s');
            \fwrite(self::$handle, "[{$timestamp}] {$content}\n");
            \fflush(self::$handle);
        }
    }

    /**
     * Escape non-printable bytes for safe debug output.
     */
    private static function escapeValue(string $value): string
    {
        $escaped = \addcslashes($value, "\0..\37\177..\377\\\"");

        if (\strlen($escaped) > 200) {
            return \substr($escaped, 0, 200).'...';
        }

        return $escaped;
    }

    /**
     * Get the log file path.
     */
    public static function getLogPath(): string
    {
        return self::$logPath;
    }

    /**
     * Check if debug logging is enabled.
     */
    public static function isEnabled(): bool
    {
        return self::$enabled;
    }
}
