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
 * Glob matches globbing patterns against text.
 *
 *     if match_glob("foo.*", "foo.bar") echo "matched\n";
 *
 *     // prints foo.bar and foo.baz
 *     $regex = glob_to_regex("foo.*");
 *     for (['foo.bar', 'foo.baz', 'foo', 'bar'] as $t)
 *     {
 *         if (/$regex/) echo "matched: $car\n";
 *     }
 *
 * Glob implements glob(3) style matching that can be used to match
 * against text, rather than fetching names from a filesystem.
 *
 * Based on the Perl Text::Glob module.
 *
 * @author Fabien Potencier <fabien@symfony.com> PHP port
 * @author     Richard Clamp <richardc@unixbeard.net> Perl version
 * @copyright  2004-2005 Fabien Potencier <fabien@symfony.com>
 * @copyright  2002 Richard Clamp <richardc@unixbeard.net>
 */
class Glob
{
    /**
     * Returns a regexp which is the equivalent of the glob pattern.
     */
    public static function toRegex(string $glob, bool $strictLeadingDot = true, bool $strictWildcardSlash = true, string $delimiter = '#'): string
    {
        $firstByte = true;
        $escaping = false;
        $inCurlies = 0;
        $regex = '';
        if ($unanchored = str_starts_with($glob, '**/')) {
            $glob = '/'.$glob;
        }
        $sizeGlob = \strlen($glob);
        for ($i = 0; $i < $sizeGlob; ++$i) {
            $car = $glob[$i];
            if ($firstByte && $strictLeadingDot && '.' !== $car) {
                $regex .= '(?=[^\.])';
            }

            $firstByte = '/' === $car;

            if ($firstByte && $strictWildcardSlash && isset($glob[$i + 2]) && '**' === $glob[$i + 1].$glob[$i + 2] && (!isset($glob[$i + 3]) || '/' === $glob[$i + 3])) {
                $car = '[^/]++/';
                if (!isset($glob[$i + 3])) {
                    $car .= '?';
                }

                if ($strictLeadingDot) {
                    $car = '(?=[^\.])'.$car;
                }

                $car = '/(?:'.$car.')*';
                $i += 2 + isset($glob[$i + 3]);

                if ('/' === $delimiter) {
                    $car = str_replace('/', '\\/', $car);
                }
            }

            if ($delimiter === $car || '.' === $car || '(' === $car || ')' === $car || '|' === $car || '+' === $car || '^' === $car || '$' === $car) {
                $regex .= "\\$car";
            } elseif ('*' === $car) {
                $regex .= $escaping ? '\\*' : ($strictWildcardSlash ? '[^/]*' : '.*');
            } elseif ('?' === $car) {
                $regex .= $escaping ? '\\?' : ($strictWildcardSlash ? '[^/]' : '.');
            } elseif ('{' === $car) {
                $regex .= $escaping ? '\\{' : '(';
                if (!$escaping) {
                    ++$inCurlies;
                }
            } elseif ('}' === $car && $inCurlies) {
                $regex .= $escaping ? '}' : ')';
                if (!$escaping) {
                    --$inCurlies;
                }
            } elseif (',' === $car && $inCurlies) {
                $regex .= $escaping ? ',' : '|';
            } elseif ('\\' === $car) {
                if ($escaping) {
                    $regex .= '\\\\';
                    $escaping = false;
                } else {
                    $escaping = true;
                }

                continue;
            } else {
                $regex .= $car;
            }
            $escaping = false;
        }

        if ($unanchored) {
            $regex = substr_replace($regex, '?', 1 + ('/' === $delimiter) + ($strictLeadingDot ? \strlen('(?=[^\.])') : 0), 0);
        }

        return $delimiter.'^'.$regex.'$'.$delimiter;
    }
}
