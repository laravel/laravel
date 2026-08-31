<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class RequestDataCollector extends DataCollector implements EventSubscriberInterface, LateDataCollectorInterface
{
    /**
     * @var \SplObjectStorage<Request, callable>
     */
    private \SplObjectStorage $controllers;
    private array $sessionUsages = [];

    public function __construct(
        private ?RequestStack $requestStack = null,
    ) {
        $this->controllers = new \SplObjectStorage();
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        // attributes are serialized and as they can be anything, they need to be converted to strings.
        $attributes = [];
        $route = '';
        foreach ($request->attributes->all() as $key => $value) {
            if ('_route' === $key) {
                $route = \is_object($value) ? $value->getPath() : $value;
                $attributes[$key] = $route;
            } else {
                $attributes[$key] = $value;
            }
        }

        $content = $request->getContent();

        $sessionMetadata = [];
        $sessionAttributes = [];
        $flashes = [];
        if (!$request->attributes->getBoolean('_stateless') && $request->hasSession()) {
            $session = $request->getSession();
            if ($session->isStarted()) {
                $sessionMetadata['Created'] = date(\DATE_RFC822, $session->getMetadataBag()->getCreated());
                $sessionMetadata['Last used'] = date(\DATE_RFC822, $session->getMetadataBag()->getLastUsed());
                $sessionMetadata['Lifetime'] = $session->getMetadataBag()->getLifetime();
                $sessionAttributes = $session->all();
                $flashes = $session->getFlashBag()->peekAll();
            }
        }

        $statusCode = $response->getStatusCode();

        $responseCookies = [];
        foreach ($response->headers->getCookies() as $cookie) {
            $responseCookies[$cookie->getName()] = $cookie;
        }

        $dotenvVars = [];
        foreach (explode(',', $_SERVER['SYMFONY_DOTENV_VARS'] ?? $_ENV['SYMFONY_DOTENV_VARS'] ?? '') as $name) {
            if ('' !== $name && isset($_ENV[$name])) {
                $dotenvVars[$name] = $_ENV[$name];
            }
        }

        $this->data = [
            'method' => $request->getMethod(),
            'format' => $request->getRequestFormat(),
            'content_type' => $response->headers->get('Content-Type', 'text/html'),
            'status_text' => Response::$statusTexts[$statusCode] ?? '',
            'status_code' => $statusCode,
            'request_query' => $request->query->all(),
            'request_request' => $request->request->all(),
            'request_files' => $request->files->all(),
            'request_headers' => $request->headers->all(),
            'request_server' => $request->server->all(),
            'request_cookies' => $request->cookies->all(),
            'request_attributes' => $attributes,
            'route' => $route,
            'response_headers' => $response->headers->all(),
            'response_cookies' => $responseCookies,
            'session_metadata' => $sessionMetadata,
            'session_attributes' => $sessionAttributes,
            'session_usages' => array_values($this->sessionUsages),
            'stateless_check' => $this->requestStack?->getMainRequest()?->attributes->get('_stateless') ?? false,
            'flashes' => $flashes,
            'path_info' => $request->getPathInfo(),
            'controller' => 'n/a',
            'locale' => $request->getLocale(),
            'dotenv_vars' => $dotenvVars,
        ];

        if (isset($this->data['request_headers']['php-auth-pw'])) {
            $this->data['request_headers']['php-auth-pw'] = '******';
        }

        if (isset($this->data['request_server']['PHP_AUTH_PW'])) {
            $this->data['request_server']['PHP_AUTH_PW'] = '******';
        }

        if (isset($this->data['request_request']['_password'])) {
            $encodedPassword = rawurlencode($this->data['request_request']['_password']);
            $content = str_replace('_password='.$encodedPassword, '_password=******', $content);
            $this->data['request_request']['_password'] = '******';
        }

        $this->data['content'] = $content;

        foreach ($this->data as $key => $value) {
            if (!\is_array($value)) {
                continue;
            }
            if ('request_headers' === $key || 'response_headers' === $key) {
                $this->data[$key] = array_map(static fn ($v) => isset($v[0]) && !isset($v[1]) ? $v[0] : $v, $value);
            }
        }

        if (isset($this->controllers[$request])) {
            $this->data['controller'] = $this->parseController($this->controllers[$request]);
            unset($this->controllers[$request]);
        }

        if ($request->attributes->has('_redirected') && $redirectCookie = $request->cookies->get('sf_redirect')) {
            $this->data['redirect'] = json_decode($redirectCookie, true);

            $response->headers->clearCookie('sf_redirect');
        }

        if ($response->isRedirect()) {
            $response->headers->setCookie(new Cookie(
                'sf_redirect',
                json_encode([
                    'token' => $response->headers->get('x-debug-token'),
                    'route' => $request->attributes->get('_route', 'n/a'),
                    'method' => $request->getMethod(),
                    'controller' => $this->parseController($request->attributes->get('_controller')),
                    'status_code' => $statusCode,
                    'status_text' => Response::$statusTexts[$statusCode],
                ]),
                0, '/', null, $request->isSecure(), true, false, 'lax'
            ));
        }

        $this->data['identifier'] = $this->data['route'] ?: (\is_array($this->data['controller']) ? $this->data['controller']['class'].'::'.$this->data['controller']['method'].'()' : $this->data['controller']);

        if ($response->headers->has('x-previous-debug-token')) {
            $this->data['forward_token'] = $response->headers->get('x-previous-debug-token');
        }
    }

    public function lateCollect(): void
    {
        $this->data = $this->cloneVar($this->data);
    }

    public function reset(): void
    {
        parent::reset();
        $this->controllers = new \SplObjectStorage();
        $this->sessionUsages = [];
    }

    public function getMethod(): string
    {
        return $this->data['method'];
    }

    public function getPathInfo(): string
    {
        return $this->data['path_info'];
    }

    public function getRequestRequest(): ParameterBag
    {
        return new ParameterBag($this->data['request_request']->getValue());
    }

    public function getRequestQuery(): ParameterBag
    {
        return new ParameterBag($this->data['request_query']->getValue());
    }

    public function getRequestFiles(): ParameterBag
    {
        return new ParameterBag($this->data['request_files']->getValue());
    }

    public function getRequestHeaders(): ParameterBag
    {
        return new ParameterBag($this->data['request_headers']->getValue());
    }

    public function getRequestServer(bool $raw = false): ParameterBag
    {
        return new ParameterBag($this->data['request_server']->getValue($raw));
    }

    public function getRequestCookies(bool $raw = false): ParameterBag
    {
        return new ParameterBag($this->data['request_cookies']->getValue($raw));
    }

    public function getRequestAttributes(): ParameterBag
    {
        return new ParameterBag($this->data['request_attributes']->getValue());
    }

    public function getResponseHeaders(): ParameterBag
    {
        return new ParameterBag($this->data['response_headers']->getValue());
    }

    public function getResponseCookies(): ParameterBag
    {
        return new ParameterBag($this->data['response_cookies']->getValue());
    }

    public function getSessionMetadata(): array
    {
        return $this->data['session_metadata']->getValue();
    }

    public function getSessionAttributes(): array
    {
        return $this->data['session_attributes']->getValue();
    }

    public function getStatelessCheck(): bool
    {
        return $this->data['stateless_check'];
    }

    public function getSessionUsages(): Data|array
    {
        return $this->data['session_usages'];
    }

    public function getFlashes(): array
    {
        return $this->data['flashes']->getValue();
    }

    /**
     * @return string|resource
     */
    public function getContent()
    {
        return $this->data['content'];
    }

    public function isJsonRequest(): bool
    {
        return 1 === preg_match('{^application/(?:\w+\++)*json$}i', $this->data['request_headers']['content-type']);
    }

    public function getPrettyJson(): ?string
    {
        $decoded = json_decode($this->getContent());

        return \JSON_ERROR_NONE === json_last_error() ? json_encode($decoded, \JSON_PRETTY_PRINT) : null;
    }

    public function getContentType(): string
    {
        return $this->data['content_type'];
    }

    public function getStatusText(): string
    {
        return $this->data['status_text'];
    }

    public function getStatusCode(): int
    {
        return $this->data['status_code'];
    }

    public function getFormat(): string
    {
        return $this->data['format'];
    }

    public function getLocale(): string
    {
        return $this->data['locale'];
    }

    public function getDotenvVars(): ParameterBag
    {
        return new ParameterBag($this->data['dotenv_vars']->getValue());
    }

    /**
     * Gets the route name.
     *
     * The _route request attributes is automatically set by the Router Matcher.
     */
    public function getRoute(): string
    {
        return $this->data['route'];
    }

    public function getIdentifier(): string
    {
        return $this->data['identifier'];
    }

    /**
     * Gets the route parameters.
     *
     * The _route_params request attributes is automatically set by the RouterListener.
     */
    public function getRouteParams(): array
    {
        return isset($this->data['request_attributes']['_route_params']) ? $this->data['request_attributes']['_route_params']->getValue() : [];
    }

    /**
     * Gets the parsed controller.
     *
     * @return array|string|Data The controller as a string or array of data
     *                           with keys 'class', 'method', 'file' and 'line'
     */
    public function getController(): array|string|Data
    {
        return $this->data['controller'];
    }

    /**
     * Gets the previous request attributes.
     *
     * @return array|Data|false A legacy array of data from the previous redirection response
     *                          or false otherwise
     */
    public function getRedirect(): array|Data|false
    {
        return $this->data['redirect'] ?? false;
    }

    public function getForwardToken(): ?string
    {
        return $this->data['forward_token'] ?? null;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $this->controllers[$event->getRequest()] = $event->getController();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->cookies->has('sf_redirect')) {
            $event->getRequest()->attributes->set('_redirected', true);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function getName(): string
    {
        return 'request';
    }

    public function collectSessionUsage(): void
    {
        $trace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);

        $traceEndIndex = \count($trace) - 1;
        for ($i = $traceEndIndex; $i > 0; --$i) {
            if (null !== ($class = $trace[$i]['class'] ?? null) && (is_subclass_of($class, SessionInterface::class) || is_subclass_of($class, SessionBagInterface::class))) {
                $traceEndIndex = $i;
                break;
            }
        }

        if ((\count($trace) - 1) === $traceEndIndex) {
            return;
        }

        // Remove part of the backtrace that belongs to session only
        array_splice($trace, 0, $traceEndIndex);

        // Merge identical backtraces generated by internal call reports
        $name = \sprintf('%s:%s', $trace[1]['class'] ?? $trace[0]['file'], $trace[0]['line']);
        if (!\array_key_exists($name, $this->sessionUsages)) {
            $this->sessionUsages[$name] = [
                'name' => $name,
                'file' => $trace[0]['file'],
                'line' => $trace[0]['line'],
                'trace' => $trace,
            ];
        }
    }

    /**
     * @return array|string An array of controller data or a simple string
     */
    private function parseController(array|object|string|null $controller): array|string
    {
        if (\is_string($controller) && str_contains($controller, '::')) {
            $controller = explode('::', $controller);
        }

        if (\is_array($controller)) {
            try {
                $r = new \ReflectionMethod($controller[0], $controller[1]);

                return [
                    'class' => \is_object($controller[0]) ? get_debug_type($controller[0]) : $controller[0],
                    'method' => $controller[1],
                    'file' => $r->getFileName(),
                    'line' => $r->getStartLine(),
                ];
            } catch (\ReflectionException) {
                if (\is_callable($controller)) {
                    // using __call or  __callStatic
                    return [
                        'class' => \is_object($controller[0]) ? get_debug_type($controller[0]) : $controller[0],
                        'method' => $controller[1],
                        'file' => 'n/a',
                        'line' => 'n/a',
                    ];
                }
            }
        }

        if ($controller instanceof \Closure) {
            $r = new \ReflectionFunction($controller);

            $controller = [
                'class' => $r->getName(),
                'method' => null,
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];

            if ($r->isAnonymous()) {
                return $controller;
            }
            $controller['method'] = $r->name;

            if ($class = $r->getClosureCalledClass()) {
                $controller['class'] = $class->name;
            } else {
                return $r->name;
            }

            return $controller;
        }

        if (\is_object($controller)) {
            $r = new \ReflectionClass($controller);

            return [
                'class' => $r->getName(),
                'method' => null,
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];
        }

        return \is_string($controller) ? $controller : 'n/a';
    }
}
