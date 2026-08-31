<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog;

final class Utils
{
    const DEFAULT_JSON_FLAGS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR;

    public static function getClass(object $object): string
    {
        $class = \get_class($object);

        if (false === ($pos = strpos($class, "@anonymous\0"))) {
            return $class;
        }

        if (false === ($parent = get_parent_class($class))) {
            return substr($class, 0, $pos + 10);
        }

        return $parent . '@anonymous';
    }

    public static function substr(string $string, int $start, ?int $length = null): string
    {
        if (\extension_loaded('mbstring')) {
            return mb_strcut($string, $start, $length);
        }

        return substr($string, $start, (null === $length) ? \strlen($string) : $length);
    }

    /**
     * Makes sure if a relative path is passed in it is turned into an absolute path
     *
     * @param string $streamUrl stream URL or path without protocol
     */
    public static function canonicalizePath(string $streamUrl): string
    {
        $prefix = '';
        if ('file://' === substr($streamUrl, 0, 7)) {
            $streamUrl = substr($streamUrl, 7);
            $prefix = 'file://';
        }

        // other type of stream, not supported
        if (false !== strpos($streamUrl, '://')) {
            return $streamUrl;
        }

        // already absolute
        if (substr($streamUrl, 0, 1) === '/' || substr($streamUrl, 1, 1) === ':' || substr($streamUrl, 0, 2) === '\\\\') {
            return $prefix.$streamUrl;
        }

        $streamUrl = getcwd() . '/' . $streamUrl;

        return $prefix.$streamUrl;
    }

    /**
     * Return the JSON representation of a value
     *
     * @param  mixed             $data
     * @param  int               $encodeFlags  flags to pass to json encode, defaults to DEFAULT_JSON_FLAGS
     * @param  bool              $ignoreErrors whether to ignore encoding errors or to throw on error, when ignored and the encoding fails, "null" is returned which is valid json for null
     * @throws \RuntimeException if encoding fails and errors are not ignored
     * @return string            when errors are ignored and the encoding fails, "null" is returned which is valid json for null
     */
    public static function jsonEncode($data, ?int $encodeFlags = null, bool $ignoreErrors = false): string
    {
        if (null === $encodeFlags) {
            $encodeFlags = self::DEFAULT_JSON_FLAGS;
        }

        if ($ignoreErrors) {
            $json = @json_encode($data, $encodeFlags);
            if (false === $json) {
                return 'null';
            }

            return $json;
        }

        $json = json_encode($data, $encodeFlags);
        if (false === $json) {
            $json = self::handleJsonError(json_last_error(), $data);
        }

        return $json;
    }

    /**
     * Handle a json_encode failure.
     *
     * If the failure is due to invalid string encoding, try to clean the
     * input and encode again. If the second encoding attempt fails, the
     * initial error is not encoding related or the input can't be cleaned then
     * raise a descriptive exception.
     *
     * @param  int               $code        return code of json_last_error function
     * @param  mixed             $data        data that was meant to be encoded
     * @param  int               $encodeFlags flags to pass to json encode, defaults to JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
     * @throws \RuntimeException if failure can't be corrected
     * @return string            JSON encoded data after error correction
     */
    public static function handleJsonError(int $code, $data, ?int $encodeFlags = null): string
    {
        if ($code !== JSON_ERROR_UTF8) {
            self::throwEncodeError($code, $data);
        }

        if (\is_string($data)) {
            self::detectAndCleanUtf8($data);
        } elseif (\is_array($data)) {
            array_walk_recursive($data, ['Monolog\Utils', 'detectAndCleanUtf8']);
        } else {
            self::throwEncodeError($code, $data);
        }

        if (null === $encodeFlags) {
            $encodeFlags = self::DEFAULT_JSON_FLAGS;
        }

        $json = json_encode($data, $encodeFlags);

        if ($json === false) {
            self::throwEncodeError(json_last_error(), $data);
        }

        return $json;
    }

    /**
     * Throws an exception according to a given code with a customized message
     *
     * @param  int               $code return code of json_last_error function
     * @param  mixed             $data data that was meant to be encoded
     * @throws \RuntimeException
     */
    private static function throwEncodeError(int $code, $data): never
    {
        $msg = match ($code) {
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            default => 'Unknown error',
        };

        throw new \RuntimeException('JSON encoding failed: '.$msg.'. Encoding: '.var_export($data, true));
    }

    /**
     * Detect invalid UTF-8 string characters and convert to valid UTF-8.
     *
     * Valid UTF-8 input will be left unmodified, but strings containing
     * invalid UTF-8 codepoints will be reencoded as UTF-8 with an assumed
     * original encoding of ISO-8859-15. This conversion may result in
     * incorrect output if the actual encoding was not ISO-8859-15, but it
     * will be clean UTF-8 output and will not rely on expensive and fragile
     * detection algorithms.
     *
     * Function converts the input in place in the passed variable so that it
     * can be used as a callback for array_walk_recursive.
     *
     * @param mixed $data Input to check and convert if needed, passed by ref
     */
    private static function detectAndCleanUtf8(&$data): void
    {
        if (\is_string($data) && preg_match('//u', $data) !== 1) {
            $data = preg_replace_callback(
                '/[\x80-\xFF]+/',
                function (array $m): string {
                    return \function_exists('mb_convert_encoding')
                        ? mb_convert_encoding($m[0], 'UTF-8', 'ISO-8859-1')
                        : (\function_exists('utf8_encode') ? utf8_encode($m[0]) : '');
                },
                $data
            );
            if (!\is_string($data)) {
                $pcreErrorCode = preg_last_error();

                throw new \RuntimeException('Failed to preg_replace_callback: ' . $pcreErrorCode . ' / ' . preg_last_error_msg());
            }
            $data = str_replace(
                ['¤', '¦', '¨', '´', '¸', '¼', '½', '¾'],
                ['€', 'Š', 'š', 'Ž', 'ž', 'Œ', 'œ', 'Ÿ'],
                $data
            );
        }
    }

    /**
     * Converts a string with a valid 'memory_limit' format, to bytes.
     *
     * @param  string|false $val
     * @return int|false    Returns an integer representing bytes. Returns FALSE in case of error.
     */
    public static function expandIniShorthandBytes($val)
    {
        if (!\is_string($val)) {
            return false;
        }

        // support -1
        if ((int) $val < 0) {
            return (int) $val;
        }

        if (!(bool) preg_match('/^\s*(?<val>\d+)(?:\.\d+)?\s*(?<unit>[gmk]?)\s*$/i', $val, $match)) {
            return false;
        }

        $val = (int) $match['val'];
        switch (strtolower($match['unit'])) {
            case 'g':
                $val *= 1024;
                // no break
            case 'm':
                $val *= 1024;
                // no break
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    public static function getRecordMessageForException(LogRecord $record): string
    {
        $context = '';
        $extra = '';

        try {
            if (\count($record->context) > 0) {
                $context = "\nContext: " . json_encode($record->context, JSON_THROW_ON_ERROR);
            }
            if (\count($record->extra) > 0) {
                $extra = "\nExtra: " . json_encode($record->extra, JSON_THROW_ON_ERROR);
            }
        } catch (\Throwable $e) {
            // noop
        }

        return "\nThe exception occurred while attempting to log: " . $record->message . $context . $extra;
    }
}
