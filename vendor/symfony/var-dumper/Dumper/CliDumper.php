<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Dumper;

use Symfony\Component\ErrorHandler\ErrorRenderer\FileLinkFormatter;
use Symfony\Component\VarDumper\Cloner\Cursor;
use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * CliDumper dumps variables for command line output.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CliDumper extends AbstractDumper
{
    public static bool $defaultColors;
    /** @var callable|resource|string|null */
    public static $defaultOutput = 'php://stdout';

    protected bool $colors;
    protected int $maxStringWidth = 0;
    protected array $styles = [
        // See http://en.wikipedia.org/wiki/ANSI_escape_code#graphics
        'default' => '0;38;5;208',
        'num' => '1;38;5;38',
        'const' => '1;38;5;208',
        'virtual' => '3',
        'str' => '1;38;5;113',
        'note' => '38;5;38',
        'ref' => '38;5;247',
        'public' => '39',
        'protected' => '39',
        'private' => '39',
        'meta' => '38;5;170',
        'key' => '38;5;113',
        'index' => '38;5;38',
    ];

    protected static string $controlCharsRx = '/[\x00-\x1F\x7F]+/';
    protected static array $controlCharsMap = [
        "\t" => '\t',
        "\n" => '\n',
        "\v" => '\v',
        "\f" => '\f',
        "\r" => '\r',
        "\033" => '\e',
    ];
    protected static string $unicodeCharsRx = "/[\u{00A0}\u{00AD}\u{034F}\u{061C}\u{115F}\u{1160}\u{17B4}\u{17B5}\u{180E}\u{2000}-\u{200F}\u{202F}\u{205F}\u{2060}-\u{2064}\u{206A}-\u{206F}\u{3000}\u{2800}\u{3164}\u{FEFF}\u{FFA0}\u{1D159}\u{1D173}-\u{1D17A}]/u";

    protected bool $collapseNextHash = false;
    protected bool $expandNextHash = false;

    private array $displayOptions = [
        'fileLinkFormat' => null,
    ];

    private bool $handlesHrefGracefully;

    public function __construct($output = null, ?string $charset = null, int $flags = 0)
    {
        parent::__construct($output, $charset, $flags);

        if ('\\' === \DIRECTORY_SEPARATOR && !$this->isWindowsTrueColor()) {
            // Use only the base 16 xterm colors when using ANSICON or standard Windows 10 CLI
            $this->setStyles([
                'default' => '31',
                'num' => '1;34',
                'const' => '1;31',
                'str' => '1;32',
                'note' => '34',
                'ref' => '1;30',
                'meta' => '35',
                'key' => '32',
                'index' => '34',
            ]);
        }

        $this->displayOptions['fileLinkFormat'] = class_exists(FileLinkFormatter::class) ? new FileLinkFormatter() : (\ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format') ?: 'file://%f#L%l');
    }

    /**
     * Enables/disables colored output.
     */
    public function setColors(bool $colors): void
    {
        $this->colors = $colors;
    }

    /**
     * Sets the maximum number of characters per line for dumped strings.
     */
    public function setMaxStringWidth(int $maxStringWidth): void
    {
        $this->maxStringWidth = $maxStringWidth;
    }

    /**
     * Configures styles.
     *
     * @param array $styles A map of style names to style definitions
     */
    public function setStyles(array $styles): void
    {
        $this->styles = $styles + $this->styles;
    }

    /**
     * Configures display options.
     *
     * @param array $displayOptions A map of display options to customize the behavior
     */
    public function setDisplayOptions(array $displayOptions): void
    {
        $this->displayOptions = $displayOptions + $this->displayOptions;
    }

    public function dumpScalar(Cursor $cursor, string $type, string|int|float|bool|null $value): void
    {
        $this->dumpKey($cursor);
        $this->collapseNextHash = $this->expandNextHash = false;

        $style = 'const';
        $attr = $cursor->attr;

        switch ($type) {
            case 'default':
                $style = 'default';
                break;

            case 'label':
                $this->styles += ['label' => $this->styles['default']];
                $style = 'label';
                break;

            case 'integer':
                $style = 'num';

                if (isset($this->styles['integer'])) {
                    $style = 'integer';
                }

                break;

            case 'double':
                $style = 'num';

                if (isset($this->styles['float'])) {
                    $style = 'float';
                }

                $value = match (true) {
                    \INF === $value => 'INF',
                    -\INF === $value => '-INF',
                    is_nan($value) => 'NAN',
                    default => !str_contains($value = (string) $value, $this->decimalPoint) ? $value .= $this->decimalPoint.'0' : $value,
                };
                break;

            case 'NULL':
                $value = 'null';
                break;

            case 'boolean':
                $value = $value ? 'true' : 'false';
                break;

            default:
                $attr += ['value' => $this->utf8Encode($value)];
                $value = $this->utf8Encode($type);
                break;
        }

        $this->line .= $this->style($style, $value, $attr);

        $this->endValue($cursor);
    }

    public function dumpString(Cursor $cursor, string $str, bool $bin, int $cut): void
    {
        $this->dumpKey($cursor);
        $this->collapseNextHash = $this->expandNextHash = false;
        $attr = $cursor->attr;

        if ($bin) {
            $str = $this->utf8Encode($str);
        }
        if ('' === $str) {
            $this->line .= '""';
            if ($cut) {
                $this->line .= '…'.$cut;
            }
            $this->endValue($cursor);
        } else {
            $attr += [
                'length' => 0 <= $cut ? mb_strlen($str, 'UTF-8') + $cut : 0,
                'binary' => $bin,
            ];
            $str = $bin && str_contains($str, "\0") ? [$str] : explode("\n", $str);
            if (isset($str[1]) && !isset($str[2]) && !isset($str[1][0])) {
                unset($str[1]);
                $str[0] .= "\n";
            }
            $m = \count($str) - 1;
            $i = $lineCut = 0;

            if (self::DUMP_STRING_LENGTH & $this->flags) {
                $this->line .= '('.$attr['length'].') ';
            }
            if ($bin) {
                $this->line .= 'b';
            }

            if ($m) {
                $this->line .= '"""';
                $this->dumpLine($cursor->depth);
            } else {
                $this->line .= '"';
            }

            foreach ($str as $str) {
                if ($i < $m) {
                    $str .= "\n";
                }
                if (0 < $this->maxStringWidth && $this->maxStringWidth < $len = mb_strlen($str, 'UTF-8')) {
                    $str = mb_substr($str, 0, $this->maxStringWidth, 'UTF-8');
                    $lineCut = $len - $this->maxStringWidth;
                }
                if ($m && 0 < $cursor->depth) {
                    $this->line .= $this->indentPad;
                }
                if ('' !== $str) {
                    $this->line .= $this->style('str', $str, $attr);
                }
                if ($i++ == $m) {
                    if ($m) {
                        if ('' !== $str) {
                            $this->dumpLine($cursor->depth);
                            if (0 < $cursor->depth) {
                                $this->line .= $this->indentPad;
                            }
                        }
                        $this->line .= '"""';
                    } else {
                        $this->line .= '"';
                    }
                    if ($cut < 0) {
                        $this->line .= '…';
                        $lineCut = 0;
                    } elseif ($cut) {
                        $lineCut += $cut;
                    }
                }
                if ($lineCut) {
                    $this->line .= '…'.$lineCut;
                    $lineCut = 0;
                }

                if ($i > $m) {
                    $this->endValue($cursor);
                } else {
                    $this->dumpLine($cursor->depth);
                }
            }
        }
    }

    public function enterHash(Cursor $cursor, int $type, string|int|null $class, bool $hasChild): void
    {
        $this->colors ??= $this->supportsColors();

        $this->dumpKey($cursor);
        $this->expandNextHash = false;
        $attr = $cursor->attr;

        if ($this->collapseNextHash) {
            $cursor->skipChildren = true;
            $this->collapseNextHash = $hasChild = false;
        }

        $class = $this->utf8Encode($class);
        if (Cursor::HASH_OBJECT === $type) {
            $prefix = $class && 'stdClass' !== $class ? $this->style('note', $class, $attr).(empty($attr['cut_hash']) ? ' {' : '') : '{';
        } elseif (Cursor::HASH_RESOURCE === $type) {
            $prefix = $this->style('note', $class.' resource', $attr).($hasChild ? ' {' : ' ');
        } else {
            $prefix = $class && !(self::DUMP_LIGHT_ARRAY & $this->flags) ? $this->style('note', 'array:'.$class).' [' : '[';
        }

        if (($cursor->softRefCount || 0 < $cursor->softRefHandle) && empty($attr['cut_hash'])) {
            $prefix .= $this->style('ref', (Cursor::HASH_RESOURCE === $type ? '@' : '#').(0 < $cursor->softRefHandle ? $cursor->softRefHandle : $cursor->softRefTo), ['count' => $cursor->softRefCount]);
        } elseif ($cursor->hardRefTo && !$cursor->refIndex && $class) {
            $prefix .= $this->style('ref', '&'.$cursor->hardRefTo, ['count' => $cursor->hardRefCount]);
        } elseif (!$hasChild && Cursor::HASH_RESOURCE === $type) {
            $prefix = substr($prefix, 0, -1);
        }

        $this->line .= $prefix;

        if ($hasChild) {
            $this->dumpLine($cursor->depth);
        }
    }

    public function leaveHash(Cursor $cursor, int $type, string|int|null $class, bool $hasChild, int $cut): void
    {
        if (empty($cursor->attr['cut_hash'])) {
            $this->dumpEllipsis($cursor, $hasChild, $cut);
            $this->line .= Cursor::HASH_OBJECT === $type ? '}' : (Cursor::HASH_RESOURCE !== $type ? ']' : ($hasChild ? '}' : ''));
        }

        $this->endValue($cursor);
    }

    /**
     * Dumps an ellipsis for cut children.
     *
     * @param bool $hasChild When the dump of the hash has child item
     * @param int  $cut      The number of items the hash has been cut by
     */
    protected function dumpEllipsis(Cursor $cursor, bool $hasChild, int $cut): void
    {
        if ($cut) {
            $this->line .= ' …';
            if (0 < $cut) {
                $this->line .= $cut;
            }
            if ($hasChild) {
                $this->dumpLine($cursor->depth + 1);
            }
        }
    }

    /**
     * Dumps a key in a hash structure.
     */
    protected function dumpKey(Cursor $cursor): void
    {
        if (null !== $key = $cursor->hashKey) {
            if ($cursor->hashKeyIsBinary) {
                $key = $this->utf8Encode($key);
            }
            $attr = [
                'binary' => $cursor->hashKeyIsBinary,
                'virtual' => $cursor->attr['virtual'] ?? false,
            ];
            $bin = $cursor->hashKeyIsBinary ? 'b' : '';
            $style = 'key';
            switch ($cursor->hashType) {
                default:
                case Cursor::HASH_INDEXED:
                    if (self::DUMP_LIGHT_ARRAY & $this->flags) {
                        break;
                    }
                    $style = 'index';
                    // no break
                case Cursor::HASH_ASSOC:
                    if (\is_int($key)) {
                        $this->line .= $this->style($style, $key).' => ';
                    } else {
                        $this->line .= $bin.'"'.$this->style($style, $key).'" => ';
                    }
                    break;

                case Cursor::HASH_RESOURCE:
                    $key = "\0~\0".$key;
                    // no break
                case Cursor::HASH_OBJECT:
                    if (!isset($key[0]) || "\0" !== $key[0]) {
                        $this->line .= '+'.$bin.$this->style('public', $key, $attr).': ';
                    } elseif (0 < strpos($key, "\0", 1)) {
                        $key = explode("\0", substr($key, 1), 2);

                        switch ($key[0][0]) {
                            case '+': // User inserted keys
                                $attr['dynamic'] = true;
                                $this->line .= '+'.$bin.'"'.$this->style('public', $key[1], $attr).'": ';
                                break 2;
                            case '~':
                                $style = 'meta';
                                if (isset($key[0][1])) {
                                    parse_str(substr($key[0], 1), $attr);
                                    $attr += ['binary' => $cursor->hashKeyIsBinary];
                                }
                                break;
                            case '*':
                                $style = 'protected';
                                $bin = '#'.$bin;
                                break;
                            default:
                                $attr['class'] = $key[0];
                                $style = 'private';
                                $bin = '-'.$bin;
                                break;
                        }

                        if (isset($attr['collapse'])) {
                            if ($attr['collapse']) {
                                $this->collapseNextHash = true;
                            } else {
                                $this->expandNextHash = true;
                            }
                        }

                        $this->line .= $bin.$this->style($style, $key[1], $attr).($attr['separator'] ?? ': ');
                    } else {
                        // This case should not happen
                        $this->line .= '-'.$bin.'"'.$this->style('private', $key, ['class' => '']).'": ';
                    }
                    break;
            }

            if ($cursor->hardRefTo) {
                $this->line .= $this->style('ref', '&'.($cursor->hardRefCount ? $cursor->hardRefTo : ''), ['count' => $cursor->hardRefCount]).' ';
            }
        }
    }

    /**
     * Decorates a value with some style.
     *
     * @param string $style The type of style being applied
     * @param string $value The value being styled
     * @param array  $attr  Optional context information
     */
    protected function style(string $style, string $value, array $attr = []): string
    {
        $this->colors ??= $this->supportsColors();

        $this->handlesHrefGracefully ??= 'JetBrains-JediTerm' !== getenv('TERMINAL_EMULATOR')
            && (!getenv('KONSOLE_VERSION') || (int) getenv('KONSOLE_VERSION') > 201100)
            && !isset($_SERVER['IDEA_INITIAL_DIRECTORY']);

        if (isset($attr['ellipsis'], $attr['ellipsis-type'])) {
            $prefix = substr($value, 0, -$attr['ellipsis']);
            if ('cli' === \PHP_SAPI && 'path' === $attr['ellipsis-type'] && isset($_SERVER[$pwd = '\\' === \DIRECTORY_SEPARATOR ? 'CD' : 'PWD']) && str_starts_with($prefix, $_SERVER[$pwd])) {
                $prefix = '.'.substr($prefix, \strlen($_SERVER[$pwd]));
            }
            if (!empty($attr['ellipsis-tail'])) {
                $prefix .= substr($value, -$attr['ellipsis'], $attr['ellipsis-tail']);
                $value = substr($value, -$attr['ellipsis'] + $attr['ellipsis-tail']);
            } else {
                $value = substr($value, -$attr['ellipsis']);
            }

            $value = $this->style('default', $prefix).$this->style($style, $value);

            goto href;
        }

        $map = static::$controlCharsMap;
        $startCchr = $this->colors ? "\033[m\033[{$this->styles['default']}m" : '';
        $endCchr = $this->colors ? "\033[m\033[{$this->styles[$style]}m" : '';
        $value = preg_replace_callback(static::$controlCharsRx, static function ($c) use ($map, $startCchr, $endCchr) {
            $s = $startCchr;
            $c = $c[$i = 0];
            do {
                $s .= $map[$c[$i]] ?? \sprintf('\x%02X', \ord($c[$i]));
            } while (isset($c[++$i]));

            return $s.$endCchr;
        }, $value, -1, $cchrCount);

        if (!($attr['binary'] ?? false)) {
            $value = preg_replace_callback(static::$unicodeCharsRx, static function ($c) use (&$cchrCount, $startCchr, $endCchr) {
                ++$cchrCount;

                return $startCchr.'\u{'.strtoupper(dechex(mb_ord($c[0]))).'}'.$endCchr;
            }, $value);
        }

        if ($this->colors && '' !== $value) {
            if ($cchrCount && "\033" === $value[0]) {
                $value = substr($value, \strlen($startCchr));
            } else {
                $value = "\033[{$this->styles[$style]}m".$value;
            }
            if ($cchrCount && str_ends_with($value, $endCchr)) {
                $value = substr($value, 0, -\strlen($endCchr));
            } else {
                $value .= "\033[{$this->styles['default']}m";
            }
        }

        href:
        if ($this->colors && $this->handlesHrefGracefully) {
            if (isset($attr['file']) && $href = $this->getSourceLink($attr['file'], $attr['line'] ?? 0)) {
                if ('note' === $style) {
                    $value .= "\033]8;;{$href}\033\\^\033]8;;\033\\";
                } else {
                    $attr['href'] = $href;
                }
            }
            if (isset($attr['href'])) {
                if ('label' === $style) {
                    $value .= '^';
                }
                $value = "\033]8;;{$attr['href']}\033\\{$value}\033]8;;\033\\";
            }
        }

        if ('label' === $style && '' !== $value) {
            $value .= ' ';
        }
        if ($this->colors && ($attr['virtual'] ?? false)) {
            $value = "\033[{$this->styles['virtual']}m".$value;
        }

        return $value;
    }

    protected function supportsColors(): bool
    {
        if ($this->outputStream !== static::$defaultOutput) {
            return $this->hasColorSupport($this->outputStream);
        }
        if (isset(static::$defaultColors)) {
            return static::$defaultColors;
        }
        if (isset($_SERVER['argv'][1])) {
            $colors = $_SERVER['argv'];
            $i = \count($colors);
            while (--$i > 0) {
                if (isset($colors[$i][5])) {
                    switch ($colors[$i]) {
                        case '--ansi':
                        case '--color':
                        case '--color=yes':
                        case '--color=force':
                        case '--color=always':
                        case '--colors=always':
                            return static::$defaultColors = true;

                        case '--no-ansi':
                        case '--color=no':
                        case '--color=none':
                        case '--color=never':
                        case '--colors=never':
                            return static::$defaultColors = false;
                    }
                }
            }
        }

        $h = stream_get_meta_data($this->outputStream) + ['wrapper_type' => null];
        $h = 'Output' === $h['stream_type'] && 'PHP' === $h['wrapper_type'] ? fopen('php://stdout', 'w') : $this->outputStream;

        return static::$defaultColors = $this->hasColorSupport($h);
    }

    protected function dumpLine(int $depth, bool $endOfValue = false): void
    {
        if ($this->colors ??= $this->supportsColors()) {
            $this->line = \sprintf("\033[%sm%s\033[m", $this->styles['default'], $this->line);
        }
        parent::dumpLine($depth);
    }

    protected function endValue(Cursor $cursor): void
    {
        if (-1 === $cursor->hashType) {
            return;
        }

        if (Stub::ARRAY_INDEXED === $cursor->hashType || Stub::ARRAY_ASSOC === $cursor->hashType) {
            if (self::DUMP_TRAILING_COMMA & $this->flags && 0 < $cursor->depth) {
                $this->line .= ',';
            } elseif (self::DUMP_COMMA_SEPARATOR & $this->flags && 1 < $cursor->hashLength - $cursor->hashIndex) {
                $this->line .= ',';
            }
        }

        $this->dumpLine($cursor->depth, true);
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Reference: Composer\XdebugHandler\Process::supportsColor
     * https://github.com/composer/xdebug-handler
     */
    private function hasColorSupport(mixed $stream): bool
    {
        if (!\is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            return false;
        }

        // Follow https://no-color.org/
        if ('' !== (($_SERVER['NO_COLOR'] ?? getenv('NO_COLOR'))[0] ?? '')) {
            return false;
        }

        // Follow https://force-color.org/
        if ('' !== (($_SERVER['FORCE_COLOR'] ?? getenv('FORCE_COLOR'))[0] ?? '')) {
            return true;
        }

        // Detect msysgit/mingw and assume this is a tty because detection
        // does not work correctly, see https://github.com/composer/composer/issues/9690
        if (!@stream_isatty($stream) && !\in_array(strtoupper((string) getenv('MSYSTEM')), ['MINGW32', 'MINGW64'], true)) {
            return false;
        }

        if ('\\' === \DIRECTORY_SEPARATOR && @sapi_windows_vt100_support($stream)) {
            return true;
        }

        if ('Hyper' === getenv('TERM_PROGRAM')
            || false !== getenv('COLORTERM')
            || false !== getenv('ANSICON')
            || 'ON' === getenv('ConEmuANSI')
        ) {
            return true;
        }

        if ('dumb' === $term = (string) getenv('TERM')) {
            return false;
        }

        // See https://github.com/chalk/supports-color/blob/d4f413efaf8da045c5ab440ed418ef02dbb28bf1/index.js#L157
        return preg_match('/^((screen|xterm|vt100|vt220|putty|rxvt|ansi|cygwin|linux).*)|(.*-256(color)?(-bce)?)$/', $term);
    }

    /**
     * Returns true if the Windows terminal supports true color.
     *
     * Note that this does not check an output stream, but relies on environment
     * variables from known implementations, or a PHP and Windows version that
     * supports true color.
     */
    private function isWindowsTrueColor(): bool
    {
        $result = 183 <= getenv('ANSICON_VER')
            || 'ON' === getenv('ConEmuANSI')
            || 'xterm' === getenv('TERM')
            || 'Hyper' === getenv('TERM_PROGRAM');

        if (!$result) {
            $version = \sprintf(
                '%s.%s.%s',
                PHP_WINDOWS_VERSION_MAJOR,
                PHP_WINDOWS_VERSION_MINOR,
                PHP_WINDOWS_VERSION_BUILD
            );
            $result = $version >= '10.0.15063';
        }

        return $result;
    }

    private function getSourceLink(string $file, int $line): string|false
    {
        if ($fmt = $this->displayOptions['fileLinkFormat']) {
            return \is_string($fmt) ? strtr($fmt, ['%f' => $file, '%l' => $line]) : ($fmt->format($file, $line) ?: 'file://'.$file.'#L'.$line);
        }

        return false;
    }
}
