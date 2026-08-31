<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ControllerDoesNotReturnResponseException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

// Help opcache.preload discover always-needed symbols
class_exists(ControllerArgumentsEvent::class);
class_exists(ControllerEvent::class);
class_exists(ExceptionEvent::class);
class_exists(FinishRequestEvent::class);
class_exists(RequestEvent::class);
class_exists(ResponseEvent::class);
class_exists(TerminateEvent::class);
class_exists(ViewEvent::class);
class_exists(KernelEvents::class);

/**
 * HttpKernel notifies events to convert a Request object to a Response one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HttpKernel implements HttpKernelInterface, TerminableInterface
{
    protected RequestStack $requestStack;
    private ArgumentResolverInterface $argumentResolver;
    private bool $terminating = false;

    public function __construct(
        protected EventDispatcherInterface $dispatcher,
        protected ControllerResolverInterface $resolver,
        ?RequestStack $requestStack = null,
        ?ArgumentResolverInterface $argumentResolver = null,
        private bool $handleAllThrowables = false,
    ) {
        $this->requestStack = $requestStack ?? new RequestStack();
        $this->argumentResolver = $argumentResolver ?? new ArgumentResolver();
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        $request->headers->set('X-Php-Ob-Level', (string) ob_get_level());

        $this->requestStack->push($request);
        $response = null;
        try {
            return $response = $this->handleRaw($request, $type);
        } catch (\Throwable $e) {
            if ($e instanceof \Error && !$this->handleAllThrowables) {
                throw $e;
            }

            if ($e instanceof RequestExceptionInterface) {
                $e = new BadRequestHttpException($e->getMessage(), $e);
            }
            if (false === $catch) {
                $this->finishRequest($request, $type);

                throw $e;
            }

            return $response = $this->handleThrowable($e, $request, $type);
        } finally {
            $this->requestStack->pop();

            if ($response instanceof StreamedResponse && $callback = $response->getCallback()) {
                $requestStack = $this->requestStack;

                $response->setCallback(static function () use ($request, $callback, $requestStack) {
                    $requestStack->push($request);
                    try {
                        $callback();
                    } finally {
                        $requestStack->pop();
                    }
                });
            }
        }
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            $this->terminating = true;
            $this->dispatcher->dispatch(new TerminateEvent($this, $request, $response), KernelEvents::TERMINATE);
        } finally {
            $this->terminating = false;
        }
    }

    /**
     * @internal
     */
    public function terminateWithException(\Throwable $exception, ?Request $request = null): void
    {
        if (!$request ??= $this->requestStack->getMainRequest()) {
            throw $exception;
        }

        if ($pop = $request !== $this->requestStack->getMainRequest()) {
            $this->requestStack->push($request);
        }

        try {
            $response = $this->handleThrowable($exception, $request, self::MAIN_REQUEST);
        } finally {
            if ($pop) {
                $this->requestStack->pop();
            }
        }

        $response->sendHeaders();
        $response->sendContent();

        $this->terminate($request, $response);
    }

    /**
     * Handles a request to convert it to a response.
     *
     * Exceptions are not caught.
     *
     * @throws \LogicException       If one of the listener does not behave as expected
     * @throws NotFoundHttpException When controller cannot be found
     */
    private function handleRaw(Request $request, int $type = self::MAIN_REQUEST): Response
    {
        // request
        $event = new RequestEvent($this, $request, $type);
        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

        // load controller
        if (false === $controller = $this->resolver->getController($request)) {
            throw new NotFoundHttpException(\sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $request->getPathInfo()));
        }

        $event = new ControllerEvent($this, $controller, $request, $type);
        $this->dispatcher->dispatch($event, KernelEvents::CONTROLLER);
        $controller = $event->getController();

        // controller arguments
        $arguments = $this->argumentResolver->getArguments($request, $controller, $event->getControllerReflector());

        $event = new ControllerArgumentsEvent($this, $event, $arguments, $request, $type);
        $this->dispatcher->dispatch($event, KernelEvents::CONTROLLER_ARGUMENTS);
        $controller = $event->getController();
        $arguments = $event->getArguments();

        // call controller
        $response = $controller(...$arguments);

        // view
        if (!$response instanceof Response) {
            $event = new ViewEvent($this, $request, $type, $response, $event);
            $this->dispatcher->dispatch($event, KernelEvents::VIEW);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            } else {
                $msg = \sprintf('The controller must return a "Symfony\Component\HttpFoundation\Response" object but it returned %s.', $this->varToString($response));

                // the user may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }

                throw new ControllerDoesNotReturnResponseException($msg, $controller, __FILE__, __LINE__ - 17);
            }
        }

        return $this->filterResponse($response, $request, $type);
    }

    /**
     * Filters a response object.
     *
     * @throws \RuntimeException if the passed object is not a Response instance
     */
    private function filterResponse(Response $response, Request $request, int $type): Response
    {
        $event = new ResponseEvent($this, $request, $type, $response);

        $this->dispatcher->dispatch($event, KernelEvents::RESPONSE);

        $this->finishRequest($request, $type);

        return $event->getResponse();
    }

    /**
     * Publishes the finish request event, then pop the request from the stack.
     *
     * Note that the order of the operations is important here, otherwise
     * operations such as {@link RequestStack::getParentRequest()} can lead to
     * weird results.
     */
    private function finishRequest(Request $request, int $type): void
    {
        $this->dispatcher->dispatch(new FinishRequestEvent($this, $request, $type), KernelEvents::FINISH_REQUEST);
    }

    /**
     * Handles a throwable by trying to convert it to a Response.
     */
    private function handleThrowable(\Throwable $e, Request $request, int $type): Response
    {
        $event = new ExceptionEvent($this, $request, $type, $e, isKernelTerminating: $this->terminating);
        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        // a listener might have replaced the exception
        $e = $event->getThrowable();

        if (!$event->hasResponse()) {
            $this->finishRequest($request, $type);

            throw $e;
        }

        $response = $event->getResponse();

        // the developer asked for a specific status code
        if (!$event->isAllowingCustomResponseCode() && !$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            // ensure that we actually have an error response
            if ($e instanceof HttpExceptionInterface) {
                // keep the HTTP status code and headers
                $response->setStatusCode($e->getStatusCode());
                $response->headers->add($e->getHeaders());
            } else {
                $response->setStatusCode(500);
            }
        }

        try {
            return $this->filterResponse($response, $request, $type);
        } catch (\Throwable $e) {
            if ($e instanceof \Error && !$this->handleAllThrowables) {
                throw $e;
            }

            return $response;
        }
    }

    /**
     * Returns a human-readable string for the specified variable.
     */
    private function varToString(mixed $var): string
    {
        if (\is_object($var)) {
            return \sprintf('an object of type %s', $var::class);
        }

        if (\is_array($var)) {
            $a = [];
            foreach ($var as $k => $v) {
                $a[] = \sprintf('%s => ...', $k);
            }

            return \sprintf('an array ([%s])', mb_substr(implode(', ', $a), 0, 255));
        }

        if (\is_resource($var)) {
            return \sprintf('a resource (%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'a boolean value (false)';
        }

        if (true === $var) {
            return 'a boolean value (true)';
        }

        if (\is_string($var)) {
            return \sprintf('a string ("%s%s")', mb_substr($var, 0, 255), mb_strlen($var) > 255 ? '...' : '');
        }

        if (is_numeric($var)) {
            return \sprintf('a number (%s)', (string) $var);
        }

        return (string) $var;
    }
}
