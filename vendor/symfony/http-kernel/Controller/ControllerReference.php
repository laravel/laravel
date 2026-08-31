<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

/**
 * Acts as a marker and a data holder for a Controller.
 *
 * Some methods in Symfony accept both a URI (as a string) or a controller as
 * an argument. In the latter case, instead of passing an array representing
 * the controller, you can use an instance of this class.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @see FragmentRendererInterface
 */
class ControllerReference
{
    public array $attributes = [];
    public array $query = [];

    /**
     * @param string $controller The controller name
     * @param array  $attributes An array of parameters to add to the Request attributes
     * @param array  $query      An array of parameters to add to the Request query string
     */
    public function __construct(
        public string $controller,
        array $attributes = [],
        array $query = [],
    ) {
        $this->attributes = $attributes;
        $this->query = $query;
    }
}
