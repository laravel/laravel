<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RequestContextAwareInterface;

/**
 * Initializes the context from the request and sets request attributes based on a matching route.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 *
 * @final
 */
class RouterListener implements EventSubscriberInterface
{
    private RequestContext $context;

    /**
     * @param RequestContext|null $context The RequestContext (can be null when $matcher implements RequestContextAwareInterface)
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private UrlMatcherInterface|RequestMatcherInterface $matcher,
        private RequestStack $requestStack,
        ?RequestContext $context = null,
        private ?LoggerInterface $logger = null,
        private ?string $projectDir = null,
        private bool $debug = true,
    ) {
        if (null === $context && !$matcher instanceof RequestContextAwareInterface) {
            throw new \InvalidArgumentException('You must either pass a RequestContext or the matcher must implement RequestContextAwareInterface.');
        }

        $this->context = $context ?? $matcher->getContext();
    }

    private function setCurrentRequest(?Request $request): void
    {
        if (null !== $request) {
            try {
                $this->context->fromRequest($request);
            } catch (\UnexpectedValueException $e) {
                throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
            }
        }
    }

    /**
     * After a sub-request is done, we need to reset the routing context to the parent request so that the URL generator
     * operates on the correct context again.
     */
    public function onKernelFinishRequest(): void
    {
        $this->setCurrentRequest($this->requestStack->getParentRequest());
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $this->setCurrentRequest($request);

        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }

        // add attributes based on the request (routing)
        try {
            // matching a request is more powerful than matching a URL path + context, so try that first
            if ($this->matcher instanceof RequestMatcherInterface) {
                $parameters = $this->matcher->matchRequest($request);
            } else {
                $parameters = $this->matcher->match($request->getPathInfo());
            }

            $this->logger?->info('Matched route "{route}".', [
                'route' => $parameters['_route'] ?? 'n/a',
                'route_parameters' => $parameters,
                'request_uri' => $request->getUri(),
                'method' => $request->getMethod(),
            ]);

            $attributes = $parameters;
            if ($mapping = $parameters['_route_mapping'] ?? false) {
                unset($parameters['_route_mapping']);
                $mappedAttributes = [];
                $attributes = [];

                foreach ($parameters as $parameter => $value) {
                    if (!isset($mapping[$parameter])) {
                        $attribute = $parameter;
                    } elseif (\is_array($mapping[$parameter])) {
                        [$attribute, $parameter] = $mapping[$parameter];
                        $mappedAttributes[$attribute] = '';
                    } else {
                        $attribute = $mapping[$parameter];
                    }

                    if (!isset($mappedAttributes[$attribute])) {
                        $attributes[$attribute] = $value;
                        $mappedAttributes[$attribute] = $parameter;
                    } elseif ('' !== $mappedAttributes[$attribute]) {
                        $attributes[$attribute] = [
                            $mappedAttributes[$attribute] => $attributes[$attribute],
                            $parameter => $value,
                        ];
                        $mappedAttributes[$attribute] = '';
                    } else {
                        $attributes[$attribute][$parameter] = $value;
                    }
                }

                $attributes['_route_mapping'] = $mapping;
            }

            $request->attributes->add($attributes);
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);
        } catch (ResourceNotFoundException $e) {
            $message = \sprintf('No route found for "%s %s"', $request->getMethod(), $request->getUriForPath($request->getPathInfo()));

            if ($referer = $request->headers->get('referer')) {
                $message .= \sprintf(' (from "%s")', $referer);
            }

            throw new NotFoundHttpException($message, $e);
        } catch (MethodNotAllowedException $e) {
            $message = \sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getUriForPath($request->getPathInfo()), implode(', ', $e->getAllowedMethods()));

            throw new MethodNotAllowedHttpException($e->getAllowedMethods(), $message, $e);
        }
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->debug || !($e = $event->getThrowable()) instanceof NotFoundHttpException) {
            return;
        }

        if ($e->getPrevious() instanceof NoConfigurationException) {
            $event->setResponse($this->createWelcomeResponse());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 32]],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
            KernelEvents::EXCEPTION => ['onKernelException', -64],
        ];
    }

    private function createWelcomeResponse(): Response
    {
        $projectDir = realpath((string) $this->projectDir).\DIRECTORY_SEPARATOR;
        $version = $docVersion = Kernel::MAJOR_VERSION.'.'.Kernel::MINOR_VERSION;

        ob_start();
        include \dirname(__DIR__).'/Resources/welcome.html.php';

        return new Response(ob_get_clean(), Response::HTTP_NOT_FOUND);
    }
}
