<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder;

/**
 * Gitignore matches against text.
 *
 * @author Michael Voříšek <vorismi3@fel.cvut.cz>
 * @author Ahmed Abdou <mail@ahmd.io>
 */
class Gitignore
{
    /**
     * Returns a regexp which is the equivalent of the gitignore pattern.
     *
     * Format specification: https://git-scm.com/docs/gitignore#_pattern_format
     */
    public static function toRegex(string $gitignoreFileContent): string
    {
        return self::buildRegex($gitignoreFileContent, false);
    }

    public static function toRegexMatchingNegatedPatterns(string $gitignoreFileContent): string
    {
        return self::buildRegex($gitignoreFileContent, true);
    }

    private static function buildRegex(string $gitignoreFileContent, bool $inverted): string
    {
        $gitignoreFileContent = preg_replace('~(?<!\\\\)#[^\n\r]*~', '', $gitignoreFileContent);
        $gitignoreLines = preg_split('~\r\n?|\n~', $gitignoreFileContent);

        $res = self::lineToRegex('');
        foreach ($gitignoreLines as $line) {
            $line = preg_replace('~(?<!\\\\)[ \t]+$~', '', $line);

            if (str_starts_with($line, '!')) {
                $line = substr($line, 1);
                $isNegative = true;
            } else {
                $isNegative = false;
            }

            if ('' !== $line) {
                if ($isNegative xor $inverted) {
                    $res = '(?!'.self::lineToRegex($line).'$)'.$res;
                } else {
                    $res = '(?:'.$res.'|'.self::lineToRegex($line).')';
                }
            }
        }

        return '~^(?:'.$res.')~s';
    }

    private static function lineToRegex(string $gitignoreLine): string
    {
        if ('' === $gitignoreLine) {
            return '$f'; // always false
        }

        $slashPos = strpos($gitignoreLine, '/');
        if (false !== $slashPos && \strlen($gitignoreLine) - 1 !== $slashPos) {
            if (0 === $slashPos) {
                $gitignoreLine = substr($gitignoreLine, 1);
            }
            $isAbsolute = true;
        } else {
            $isAbsolute = false;
        }

        $regex = preg_quote(str_replace('\\', '', $gitignoreLine), '~');
        $regex = preg_replace_callback('~\\\\\[((?:\\\\!)?)([^\[\]]*)\\\\\]~', static fn (array $matches): string => '['.('' !== $matches[1] ? '^' : '').str_replace('\\-', '-', $matches[2]).']', $regex);
        $regex = preg_replace('~(?:(?:\\\\\*){2,}(/?))+~', '(?:(?:(?!//).(?<!//))+$1)?', $regex);
        $regex = preg_replace('~\\\\\*~', '[^/]*', $regex);
        $regex = preg_replace('~\\\\\?~', '[^/]', $regex);

        return ($isAbsolute ? '' : '(?:[^/]+/)*')
            .$regex
            .(!str_ends_with($gitignoreLine, '/') ? '(?:$|/)' : '');
    }
}
