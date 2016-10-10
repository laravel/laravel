<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http;

use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * AccessMap allows configuration of different access control rules for
 * specific parts of the website.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AccessMap implements AccessMapInterface
{
    private $map = array();

    /**
     * Constructor.
     *
     * @param RequestMatcherInterface $requestMatcher A RequestMatcherInterface instance
     * @param array                   $attributes     An array of attributes to pass to the access decision manager (like roles)
     * @param string|null             $channel        The channel to enforce (http, https, or null)
     */
    public function add(RequestMatcherInterface $requestMatcher, array $attributes = array(), $channel = null)
    {
        $this->map[] = array($requestMatcher, $attributes, $channel);
    }

    /**
     * {@inheritdoc}
     */
    public function getPatterns(Request $request)
    {
        foreach ($this->map as $elements) {
            if (null === $elements[0] || $elements[0]->matches($request)) {
                return array($elements[1], $elements[2]);
            }
        }

        return array(null, null);
    }
}
