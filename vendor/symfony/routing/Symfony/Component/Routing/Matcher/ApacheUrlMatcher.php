<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 * ApacheUrlMatcher matches URL based on Apache mod_rewrite matching (see ApacheMatcherDumper).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class ApacheUrlMatcher extends UrlMatcher
{
    /**
     * Tries to match a URL based on Apache mod_rewrite matching.
     *
     * Returns false if no route matches the URL.
     *
     * @param string $pathinfo The pathinfo to be parsed
     *
     * @return array An array of parameters
     *
     * @throws MethodNotAllowedException If the current method is not allowed
     */
    public function match($pathinfo)
    {
        $parameters = array();
        $defaults = array();
        $allow = array();
        $route = null;

        foreach ($this->denormalizeValues($_SERVER) as $key => $value) {
            $name = $key;

            // skip non-routing variables
            // this improves performance when $_SERVER contains many usual
            // variables like HTTP_*, DOCUMENT_ROOT, REQUEST_URI, ...
            if (false === strpos($name, '_ROUTING_')) {
                continue;
            }

            while (0 === strpos($name, 'REDIRECT_')) {
                $name = substr($name, 9);
            }

            // expect _ROUTING_<type>_<name>
            // or _ROUTING_<type>

            if (0 !== strpos($name, '_ROUTING_')) {
                continue;
            }
            if (false !== $pos = strpos($name, '_', 9)) {
                $type = substr($name, 9, $pos-9);
                $name = substr($name, $pos+1);
            } else {
                $type = substr($name, 9);
            }

            if ('param' === $type) {
                if ('' !== $value) {
                    $parameters[$name] = $value;
                }
            } elseif ('default' === $type) {
                $defaults[$name] = $value;
            } elseif ('route' === $type) {
                $route = $value;
            } elseif ('allow' === $type) {
                $allow[] = $name;
            }

            unset($_SERVER[$key]);
        }

        if (null !== $route) {
            $parameters['_route'] = $route;

            return $this->mergeDefaults($parameters, $defaults);
        } elseif (0 < count($allow)) {
            throw new MethodNotAllowedException($allow);
        } else {
            return parent::match($pathinfo);
        }
    }

    /**
     * Denormalizes an array of values.
     *
     * @param string[] $values
     *
     * @return array
     */
    private function denormalizeValues(array $values)
    {
        $normalizedValues = array();
        foreach ($values as $key => $value) {
            if (preg_match('~^(.*)\[(\d+)\]$~', $key, $matches)) {
                if (!isset($normalizedValues[$matches[1]])) {
                    $normalizedValues[$matches[1]] = array();
                }
                $normalizedValues[$matches[1]][(int) $matches[2]] = $value;
            } else {
                $normalizedValues[$key] = $value;
            }
        }

        return $normalizedValues;
    }
}
