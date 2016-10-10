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

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\HttpFoundation\Request;

/**
 * TraceableControllerResolver.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableControllerResolver implements ControllerResolverInterface
{
    private $resolver;
    private $stopwatch;

    /**
     * Constructor.
     *
     * @param ControllerResolverInterface $resolver  A ControllerResolverInterface instance
     * @param Stopwatch                   $stopwatch A Stopwatch instance
     */
    public function __construct(ControllerResolverInterface $resolver, Stopwatch $stopwatch)
    {
        $this->resolver = $resolver;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $e = $this->stopwatch->start('controller.get_callable');

        $ret = $this->resolver->getController($request);

        $e->stop();

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(Request $request, $controller)
    {
        $e = $this->stopwatch->start('controller.get_arguments');

        $ret = $this->resolver->getArguments($request, $controller);

        $e->stop();

        return $ret;
    }
}
