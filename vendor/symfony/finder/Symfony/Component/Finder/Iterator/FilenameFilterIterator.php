<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\Expression\Expression;

/**
 * FilenameFilterIterator filters files by patterns (a regexp, a glob, or a string).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FilenameFilterIterator extends MultiplePcreFilterIterator
{
    /**
     * Filters the iterator values.
     *
     * @return bool    true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $filename = $this->current()->getFilename();

        // should at least not match one rule to exclude
        foreach ($this->noMatchRegexps as $regex) {
            if (preg_match($regex, $filename)) {
                return false;
            }
        }

        // should at least match one rule
        $match = true;
        if ($this->matchRegexps) {
            $match = false;
            foreach ($this->matchRegexps as $regex) {
                if (preg_match($regex, $filename)) {
                    return true;
                }
            }
        }

        return $match;
    }

    /**
     * Converts glob to regexp.
     *
     * PCRE patterns are left unchanged.
     * Glob strings are transformed with Glob::toRegex().
     *
     * @param string $str Pattern: glob or regexp
     *
     * @return string regexp corresponding to a given glob or regexp
     */
    protected function toRegex($str)
    {
        return Expression::create($str)->getRegex()->render();
    }
}
