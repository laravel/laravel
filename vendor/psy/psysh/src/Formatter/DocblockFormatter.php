<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Formatter;

use Psy\Util\Docblock;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * A pretty-printer for docblocks.
 */
class DocblockFormatter implements ReflectorFormatter
{
    private const VECTOR_PARAM_TEMPLATES = [
        'type' => 'info',
        'var'  => 'strong',
    ];

    /**
     * Format a docblock.
     *
     * @param \Reflector $reflector
     *
     * @return string Formatted docblock
     */
    public static function format(\Reflector $reflector): string
    {
        $docblock = new Docblock($reflector);
        $chunks = [];

        if (!empty($docblock->desc)) {
            $chunks[] = '<comment>Description:</comment>';
            $chunks[] = self::indent(OutputFormatter::escape($docblock->desc), '  ');
            $chunks[] = '';
        }

        if (!empty($docblock->tags)) {
            foreach ($docblock::$vectors as $name => $vector) {
                if (isset($docblock->tags[$name])) {
                    $chunks[] = \sprintf('<comment>%s:</comment>', self::inflect($name));
                    $chunks[] = self::formatVector($vector, $docblock->tags[$name]);
                    $chunks[] = '';
                }
            }

            $tags = self::formatTags(\array_keys($docblock::$vectors), $docblock->tags);
            if (!empty($tags)) {
                $chunks[] = $tags;
                $chunks[] = '';
            }
        }

        return \rtrim(\implode("\n", $chunks));
    }

    /**
     * Format a docblock vector, for example, `@throws`, `@param`, or `@return`.
     *
     * @see DocBlock::$vectors
     *
     * @param array $vector
     * @param array $lines
     */
    private static function formatVector(array $vector, array $lines): string
    {
        $template = [' '];
        foreach ($vector as $type) {
            $max = 0;
            foreach ($lines as $line) {
                $chunk = $line[$type];
                $cur = empty($chunk) ? 0 : \strlen($chunk) + 1;
                if ($cur > $max) {
                    $max = $cur;
                }
            }

            $template[] = self::getVectorParamTemplate($type, $max);
        }
        $template = \implode(' ', $template);

        return \implode("\n", \array_map(function ($line) use ($template) {
            $escaped = \array_map(fn ($l) => ($l === null) ? '' : OutputFormatter::escape($l), $line);

            return \rtrim(\vsprintf($template, $escaped));
        }, $lines));
    }

    /**
     * Format docblock tags.
     *
     * @param array $skip Tags to exclude
     * @param array $tags Tags to format
     *
     * @return string formatted tags
     */
    private static function formatTags(array $skip, array $tags): string
    {
        $chunks = [];

        foreach ($tags as $name => $values) {
            if (\in_array($name, $skip)) {
                continue;
            }

            foreach ($values as $value) {
                $chunks[] = \sprintf('<comment>%s%s</comment> %s', self::inflect($name), empty($value) ? '' : ':', OutputFormatter::escape($value));
            }

            $chunks[] = '';
        }

        return \implode("\n", $chunks);
    }

    /**
     * Get a docblock vector template.
     *
     * @param string $type Vector type
     * @param int    $max  Pad width
     */
    private static function getVectorParamTemplate(string $type, int $max): string
    {
        if (!isset(self::VECTOR_PARAM_TEMPLATES[$type])) {
            return \sprintf('%%-%ds', $max);
        }

        return \sprintf('<%s>%%-%ds</%s>', self::VECTOR_PARAM_TEMPLATES[$type], $max, self::VECTOR_PARAM_TEMPLATES[$type]);
    }

    /**
     * Indent a string.
     *
     * @param string $text   String to indent
     * @param string $indent (default: '  ')
     */
    private static function indent(string $text, string $indent = '  '): string
    {
        return $indent.\str_replace("\n", "\n".$indent, $text);
    }

    /**
     * Convert underscored or whitespace separated words into sentence case.
     *
     * @param string $text
     */
    private static function inflect(string $text): string
    {
        $words = \trim(\preg_replace('/[\s_-]+/', ' ', \preg_replace('/([a-z])([A-Z])/', '$1 $2', $text)));

        return \implode(' ', \array_map('ucfirst', \explode(' ', $words)));
    }
}
