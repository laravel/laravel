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

/**
 * PathFilterIterator filters files by path patterns (e.g. some/special/dir).
 *
 * @author Fabien Potencier  <fabien@symfony.com>
 * @author Włodzimierz Gajda <gajdaw@gajdaw.pl>
 */
class PathFilterIterator extends MultiplePcreFilterIterator
{
    /**
     * Filters the iterator values.
     *
     * @return bool    true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $filename = $this->current()->getRelativePathname();

        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $filename = strtr($filename, '\\', '/');
        }

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
     * Converts strings to regexp.
     *
     * PCRE patterns are left unchanged.
     *
     * Default conversion:
     *     'lorem/ipsum/dolor'  ==>  'lorem\/ipsum\/dolor/'
     *
     * Use only / as directory separator (on Windows also).
     *
     * @param string $str Pattern: regexp or dirname.
     *
     * @return string regexp corresponding to a given string or regexp
     */
    protected function toRegex($str)
    {
        return $this->isRegex($str) ? $str : '/'.preg_quote($str, '/').'/';
    }
}
