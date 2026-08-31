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

use Symfony\Component\HttpFoundation\Request;

/**
 * A ControllerResolverInterface implementation knows how to determine the
 * controller to execute based on a Request object.
 *
 * A Controller can be any valid PHP callable.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ControllerResolverInterface
{
    /**
     * Returns the Controller instance associated with a Request.
     *
     * As several resolvers can exist for a single application, a resolver must
     * return false when it is not able to determine the controller.
     *
     * The resolver must only throw an exception when it should be able to load a
     * controller but cannot because of some errors made by the developer.
     *
     * @return callable|false A PHP callable representing the Controller,
     *                        or false if this resolver is not able to determine the controller
     *
     * @throws \LogicException If a controller was found based on the request but it is not callable
     */
    public function getController(Request $request): callable|false;
}
