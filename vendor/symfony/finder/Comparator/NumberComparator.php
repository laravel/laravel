<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Comparator;

/**
 * NumberComparator compiles a simple comparison to an anonymous
 * subroutine, which you can call with a value to be tested again.
 *
 * Now this would be very pointless, if NumberCompare didn't understand
 * magnitudes.
 *
 * The target value may use magnitudes of kilobytes (k, ki),
 * megabytes (m, mi), or gigabytes (g, gi). Those suffixed
 * with an i use the appropriate 2**n version in accordance with the
 * IEC standard: http://physics.nist.gov/cuu/Units/binary.html
 *
 * Based on the Perl Number::Compare module.
 *
 * @author    Fabien Potencier <fabien@symfony.com> PHP port
 * @author    Richard Clamp <richardc@unixbeard.net> Perl version
 * @copyright 2004-2005 Fabien Potencier <fabien@symfony.com>
 * @copyright 2002 Richard Clamp <richardc@unixbeard.net>
 *
 * @see http://physics.nist.gov/cuu/Units/binary.html
 */
class NumberComparator extends Comparator
{
    /**
     * @param string|null $test A comparison string or null
     *
     * @throws \InvalidArgumentException If the test is not understood
     */
    public function __construct(?string $test)
    {
        if (null === $test || !preg_match('#^\s*(==|!=|[<>]=?)?\s*([0-9\.]+)\s*([kmg]i?)?\s*$#i', $test, $matches)) {
            throw new \InvalidArgumentException(\sprintf('Don\'t understand "%s" as a number test.', $test ?? 'null'));
        }

        $target = $matches[2];
        if (!is_numeric($target)) {
            throw new \InvalidArgumentException(\sprintf('Invalid number "%s".', $target));
        }
        if (isset($matches[3])) {
            // magnitude
            switch (strtolower($matches[3])) {
                case 'k':
                    $target *= 1000;
                    break;
                case 'ki':
                    $target *= 1024;
                    break;
                case 'm':
                    $target *= 1000000;
                    break;
                case 'mi':
                    $target *= 1024 * 1024;
                    break;
                case 'g':
                    $target *= 1000000000;
                    break;
                case 'gi':
                    $target *= 1024 * 1024 * 1024;
                    break;
            }
        }

        parent::__construct($target, $matches[1] ?: '==');
    }
}
