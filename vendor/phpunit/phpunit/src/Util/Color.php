<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use function array_map;
use function array_walk;
use function count;
use function explode;
use function implode;
use function max;
use function min;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function sprintf;
use function str_pad;
use function strtr;
use function trim;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Color
{
    /**
     * @var array<string,string>
     */
    private const WHITESPACE_MAP = [
        ' '  => '·',
        "\t" => '⇥',
    ];

    /**
     * @var array<string,string>
     */
    private const WHITESPACE_EOL_MAP = [
        ' '  => '·',
        "\t" => '⇥',
        "\n" => '↵',
        "\r" => '⟵',
    ];

    /**
     * @var array<string,string>
     */
    private static array $ansiCodes = [
        'reset'      => '0',
        'bold'       => '1',
        'dim'        => '2',
        'dim-reset'  => '22',
        'underlined' => '4',
        'fg-default' => '39',
        'fg-black'   => '30',
        'fg-red'     => '31',
        'fg-green'   => '32',
        'fg-yellow'  => '33',
        'fg-blue'    => '34',
        'fg-magenta' => '35',
        'fg-cyan'    => '36',
        'fg-white'   => '37',
        'bg-default' => '49',
        'bg-black'   => '40',
        'bg-red'     => '41',
        'bg-green'   => '42',
        'bg-yellow'  => '43',
        'bg-blue'    => '44',
        'bg-magenta' => '45',
        'bg-cyan'    => '46',
        'bg-white'   => '47',
    ];

    public static function colorize(string $color, string $buffer): string
    {
        if (trim($buffer) === '') {
            return $buffer;
        }

        $codes  = array_map('\trim', explode(',', $color));
        $styles = [];

        foreach ($codes as $code) {
            if (isset(self::$ansiCodes[$code])) {
                $styles[] = self::$ansiCodes[$code];
            }
        }

        if (empty($styles)) {
            return $buffer;
        }

        return self::optimizeColor(sprintf("\x1b[%sm", implode(';', $styles)) . $buffer . "\x1b[0m");
    }

    public static function colorizeTextBox(string $color, string $buffer, ?int $columns = null): string
    {
        $lines       = preg_split('/\r\n|\r|\n/', $buffer);
        $maxBoxWidth = max(array_map('\strlen', $lines));

        if ($columns !== null) {
            $maxBoxWidth = min($maxBoxWidth, $columns);
        }

        array_walk($lines, static function (string &$line) use ($color, $maxBoxWidth): void
        {
            $line = self::colorize($color, str_pad($line, $maxBoxWidth));
        });

        return implode(PHP_EOL, $lines);
    }

    public static function colorizePath(string $path, ?string $previousPath = null, bool $colorizeFilename = false): string
    {
        if ($previousPath === null) {
            $previousPath = '';
        }

        $path         = explode(DIRECTORY_SEPARATOR, $path);
        $previousPath = explode(DIRECTORY_SEPARATOR, $previousPath);

        for ($i = 0; $i < min(count($path), count($previousPath)); $i++) {
            if ($path[$i] === $previousPath[$i]) {
                $path[$i] = self::dim($path[$i]);
            }
        }

        if ($colorizeFilename) {
            $last        = count($path) - 1;
            $path[$last] = preg_replace_callback(
                '/([\-_.]+|phpt$)/',
                static fn ($matches) => self::dim($matches[0]),
                $path[$last],
            );
        }

        return self::optimizeColor(implode(self::dim(DIRECTORY_SEPARATOR), $path));
    }

    public static function dim(string $buffer): string
    {
        if (trim($buffer) === '') {
            return $buffer;
        }

        return "\e[2m{$buffer}\e[22m";
    }

    public static function visualizeWhitespace(string $buffer, bool $visualizeEOL = false): string
    {
        $replaceMap = $visualizeEOL ? self::WHITESPACE_EOL_MAP : self::WHITESPACE_MAP;

        return preg_replace_callback(
            '/\s+/',
            static fn ($matches) => self::dim(strtr($matches[0], $replaceMap)),
            $buffer,
        );
    }

    private static function optimizeColor(string $buffer): string
    {
        return preg_replace(
            [
                "/\e\\[22m\e\\[2m/",
                "/\e\\[([^m]*)m\e\\[([1-9][0-9;]*)m/",
                "/(\e\\[[^m]*m)+(\e\\[0m)/",
            ],
            [
                '',
                "\e[$1;$2m",
                '$2',
            ],
            $buffer,
        );
    }
}
