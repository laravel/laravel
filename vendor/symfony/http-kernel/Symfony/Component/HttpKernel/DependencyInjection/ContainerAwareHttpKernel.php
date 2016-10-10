<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DependencyInjection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Scope;

/**
 * Adds a managed request scope.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ContainerAwareHttpKernel extends HttpKernel
{
    protected $container;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface    $dispatcher         An EventDispatcherInterface instance
     * @param ContainerInterface          $container          A ContainerInterface instance
     * @param ControllerResolverInterface $controllerResolver A ControllerResolverInterface instance
     * @param RequestStack                $requestStack       A stack for master/sub requests
     */
    public function __construct(EventDispatcherInterface $dispatcher, ContainerInterface $container, ControllerResolverInterface $controllerResolver, RequestStack $requestStack = null)
    {
        parent::__construct($dispatcher, $controllerResolver, $requestStack);

        $this->container = $container;

        // the request scope might have been created before (see FrameworkBundle)
        if (!$container->hasScope('request')) {
            $container->addScope(new Scope('request'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $request->headers->set('X-Php-Ob-Level', ob_get_level());

        $this->container->enterScope('request');
        $this->container->set('request', $request, 'request');

        try {
            $response = parent::handle($request, $type, $catch);
        } catch (\Exception $e) {
            $this->container->set('request', null, 'request');
            $this->container->leaveScope('request');

            throw $e;
        }

        $this->container->set('request', null, 'request');
        $this->container->leaveScope('request');

        return $response;
    }
}
