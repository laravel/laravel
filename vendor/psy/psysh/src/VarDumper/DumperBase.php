<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\VarDumper;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\VarDumper\Cloner\Cursor;
use Symfony\Component\VarDumper\Dumper\CliDumper;

abstract class DumperBase extends CliDumper
{
    private const HEREDOC_LABEL = 'EOS';
    private OutputFormatter $formatter;
    private bool $forceArrayIndexes;
    private bool $useDeprecatedMultilineStrings;

    private const ONLY_CONTROL_CHARS = '/^[\x00-\x1F\x7F]+$/';
    private const CONTROL_CHARS = '/([\x00-\x1F\x7F]+)/';
    private const CONTROL_CHARS_MAP = [
        "\0"   => '\0',
        "\t"   => '\t',
        "\n"   => '\n',
        "\v"   => '\v',
        "\f"   => '\f',
        "\r"   => '\r',
        "\033" => '\e',
    ];
    private const STRING_DELIMITERS = [
        '\\'   => '\\\\',
        '"'    => '\\"',
        '$'    => '\\$',
    ];
    private const STRING_ESCAPE_CHARS = '/([\x00-\x1F\x7F\\\\"$])/';
    private const HEREDOC_DELIMITERS = [
        '\\' => '\\\\',
        '$'  => '\\$',
    ];
    private const HEREDOC_ESCAPE_CHARS = '/([\x00-\x1F\x7F\\\\$])/';

    public function __construct(OutputFormatter $formatter, $forceArrayIndexes = false, bool $useDeprecatedMultilineStrings = false)
    {
        $this->formatter = $formatter;
        $this->forceArrayIndexes = $forceArrayIndexes;
        $this->useDeprecatedMultilineStrings = $useDeprecatedMultilineStrings;
        parent::__construct();
        $this->setColors(false);
    }

    protected function doEnterHash(Cursor $cursor, $type, $class, $hasChild): void
    {
        if (Cursor::HASH_INDEXED === $type || Cursor::HASH_ASSOC === $type) {
            $class = 0;
        }
        parent::enterHash($cursor, $type, $class, $hasChild);
    }

    protected function doDumpKey(Cursor $cursor): void
    {
        if (!$this->forceArrayIndexes && Cursor::HASH_INDEXED === $cursor->hashType) {
            return;
        }

        if (null === $key = $cursor->hashKey) {
            return;
        }

        if ($cursor->hashKeyIsBinary) {
            $key = $this->utf8Encode($key);
        }

        $attr = ['binary' => $cursor->hashKeyIsBinary];
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
                    $this->line .= $this->style($style, (string) $key).' => ';
                } else {
                    $this->line .= $bin.$this->style($style, '"');
                    $this->appendEscapedString((string) $key, $style);
                    $this->line .= $this->style($style, '"').' => ';
                }

                break;

            case Cursor::HASH_RESOURCE:
            case Cursor::HASH_OBJECT:
                parent::dumpKey($cursor);
        }

        if ($cursor->hardRefTo) {
            $this->line .= $this->style('ref', '&'.($cursor->hardRefCount ? $cursor->hardRefTo : ''), ['count' => $cursor->hardRefCount]).' ';
        }
    }

    protected function doDumpString(Cursor $cursor, $str, $bin, $cut): void
    {
        $this->doDumpKey($cursor);
        $this->collapseNextHash = $this->expandNextHash = false;
        $attr = $cursor->attr;

        if ($bin) {
            $str = (string) $this->utf8Encode($str);
        }

        if ('' === $str) {
            $this->line .= $this->style('str', '""');
            if ($cut) {
                $this->line .= '…'.$cut;
            }
            $this->endValue($cursor);

            return;
        }

        $attr += [
            'length' => 0 <= $cut ? \mb_strlen($str, 'UTF-8') + $cut : 0,
            'binary' => $bin,
        ];

        $parts = $bin && false !== \strpos($str, "\0") ? [$str] : \explode("\n", $str);
        if (isset($parts[1]) && !isset($parts[2]) && !isset($parts[1][0])) {
            unset($parts[1]);
            $parts[0] .= "\n";
        }
        $last = \count($parts) - 1;
        $index = $lineCut = 0;
        $useHeredocMultilineStrings = $last && $this->useHeredocMultilineStrings();
        $heredocLabel = $useHeredocMultilineStrings ? $this->getHeredocLabel($parts) : null;

        if (self::DUMP_STRING_LENGTH & $this->flags) {
            $this->line .= '('.$attr['length'].') ';
        }
        if ($bin) {
            $this->line .= 'b';
        }

        if ($last) {
            if ($useHeredocMultilineStrings) {
                $this->line .= '<<<'.$this->style('str', (string) $heredocLabel);
            } else {
                $this->line .= $this->style('str', '"""');
            }
            $this->dumpLine($cursor->depth);
        } else {
            $this->line .= $this->style('str', '"');
        }

        foreach ($parts as $part) {
            $endsWithNewline = $index < $last;
            $displayPart = $part;
            $measurePart = $displayPart;
            if ($useHeredocMultilineStrings && $endsWithNewline) {
                $measurePart .= "\n";
            } elseif ($last && $endsWithNewline) {
                $displayPart .= "\n";
                $measurePart = $displayPart;
            }
            if (0 < $this->maxStringWidth && $this->maxStringWidth < $len = \mb_strlen($measurePart, 'UTF-8')) {
                $displayPart = (string) \mb_substr($displayPart, 0, $this->maxStringWidth, 'UTF-8');
                $lineCut = $len - $this->maxStringWidth;
            }
            if ($last && $cursor->depth > 0) {
                $this->line .= $this->indentPad;
            }
            if ($displayPart !== '') {
                if ($useHeredocMultilineStrings) {
                    $this->appendEscaped($displayPart, 'str', self::HEREDOC_ESCAPE_CHARS, self::HEREDOC_DELIMITERS);
                } else {
                    $this->appendEscapedString($displayPart, 'str');
                }
            }
            if ($index++ === $last) {
                if ($last) {
                    if ($displayPart !== '') {
                        $this->dumpLine($cursor->depth);
                        if ($cursor->depth > 0) {
                            $this->line .= $this->indentPad;
                        }
                    }
                    $this->line .= $useHeredocMultilineStrings ? $this->style('str', (string) $heredocLabel) : $this->style('str', '"""');
                } else {
                    $this->line .= $this->style('str', '"');
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

            if ($index > $last) {
                $this->endValue($cursor);
            } else {
                $this->dumpLine($cursor->depth);
            }
        }
    }

    protected function doStyle($style, $value, $attr = [])
    {
        if ($attr['if_links'] ?? false) {
            return '';
        }

        if ('ref' === $style) {
            $value = \strtr($value, '@', '#');
        }

        $styled = '';
        $baseStyle = $this->styles[$style] ?? null;
        $controlStyle = $this->styles['cchr'] ?? null;

        $chunks = \preg_split(self::CONTROL_CHARS, $value, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
        foreach ($chunks as $chunk) {
            if (\preg_match(self::ONLY_CONTROL_CHARS, $chunk)) {
                $chars = '';
                $i = 0;
                do {
                    $chars .= isset(self::CONTROL_CHARS_MAP[$chunk[$i]]) ? self::CONTROL_CHARS_MAP[$chunk[$i]] : \sprintf('\x%02X', \ord($chunk[$i]));
                } while (isset($chunk[++$i]));

                $styled .= $this->applyStyle($controlStyle, $chars);
            } else {
                $styled .= $this->applyStyle($baseStyle, $chunk);
            }
        }

        return $styled;
    }

    protected function doDumpLine($depth, $endOfValue = false): void
    {
        if ($endOfValue && 0 < $depth) {
            $this->line .= ',';
        }

        parent::dumpLine($depth, $endOfValue);
    }

    private function applyStyle(?string $style, string $value): string
    {
        if ($style === null || !$this->formatter->hasStyle($style)) {
            return $value;
        }

        if (!$this->formatter->isDecorated()) {
            return $value;
        }

        return $this->formatter->getStyle($style)->apply($value);
    }

    private function appendEscapedString(string $value, string $style): void
    {
        $this->appendEscaped($value, $style, self::STRING_ESCAPE_CHARS, self::STRING_DELIMITERS);
    }

    private function appendEscaped(string $value, string $style, string $escapePattern, array $delimiters): void
    {
        $baseStyle = $this->styles[$style] ?? null;
        $controlStyle = $this->styles['cchr'] ?? null;

        $chunks = \preg_split($escapePattern, $value, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
        foreach ($chunks as $chunk) {
            $escaped = $this->escapeChar($chunk, $delimiters);
            if ($escaped !== null) {
                $this->line .= $this->applyStyle($controlStyle, $escaped);
            } else {
                $this->line .= $this->applyStyle($baseStyle, $chunk);
            }
        }
    }

    private function escapeChar(string $char, array $delimiters): ?string
    {
        if (isset($delimiters[$char])) {
            return $delimiters[$char];
        }

        if (isset(self::CONTROL_CHARS_MAP[$char])) {
            return self::CONTROL_CHARS_MAP[$char];
        }

        if (\preg_match(self::ONLY_CONTROL_CHARS, $char)) {
            return \sprintf('\x%02X', \ord($char));
        }

        return null;
    }

    /**
     * Pick a heredoc label that won't be mistaken for a closing marker.
     *
     * @param string[] $parts
     */
    private function getHeredocLabel(array $parts): string
    {
        $label = self::HEREDOC_LABEL;
        $suffix = 1;

        while ($this->partsContainHeredocLabel($parts, $label)) {
            ++$suffix;
            $label = self::HEREDOC_LABEL.'_'.$suffix;
        }

        return $label;
    }

    /**
     * @param string[] $parts
     */
    private function partsContainHeredocLabel(array $parts, string $label): bool
    {
        foreach ($parts as $part) {
            if (\preg_match('/^\s*'.\preg_quote($label, '/').'(?:$|[;,])/', $this->escapeHeredocString($part))) {
                return true;
            }
        }

        return false;
    }

    private function escapeHeredocString(string $value): string
    {
        $escaped = '';
        $chunks = \preg_split(self::HEREDOC_ESCAPE_CHARS, $value, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);

        foreach ($chunks as $chunk) {
            $escaped .= $this->escapeChar($chunk, self::HEREDOC_DELIMITERS) ?? $chunk;
        }

        return $escaped;
    }

    private function useHeredocMultilineStrings(): bool
    {
        return !$this->useDeprecatedMultilineStrings;
    }
}
