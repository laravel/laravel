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

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\SessionUtils;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\UnexpectedSessionUsageException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Sets the session onto the request on the "kernel.request" event and saves
 * it on the "kernel.response" event.
 *
 * In addition, if the session has been started it overrides the Cache-Control
 * header in such a way that all caching is disabled in that case.
 * If you have a scenario where caching responses with session information in
 * them makes sense, you can disable this behaviour by setting the header
 * AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER on the response.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Tobias Schultze <http://tobion.de>
 */
abstract class AbstractSessionListener implements EventSubscriberInterface, ResetInterface
{
    public const NO_AUTO_CACHE_CONTROL_HEADER = 'Symfony-Session-NoAutoCacheControl';

    /**
     * @param array<string, mixed> $sessionOptions
     *
     * @internal
     */
    public function __construct(
        private ?ContainerInterface $container = null,
        private bool $debug = false,
        private array $sessionOptions = [],
    ) {
    }

    /**
     * @internal
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->hasSession()) {
            $request->setSessionFactory(function () use ($request) {
                // Prevent calling `$this->getSession()` twice in case the Request (and the below factory) is cloned
                static $sess;

                if (!$sess) {
                    $sess = $this->getSession();
                    $request->setSession($sess);

                    /*
                     * For supporting sessions in php runtime with runners like roadrunner or swoole, the session
                     * cookie needs to be read from the cookie bag and set on the session storage.
                     *
                     * Do not set it when a native php session is active.
                     */
                    if ($sess && !$sess->isStarted() && \PHP_SESSION_ACTIVE !== session_status()) {
                        $sessionId = $sess->getId() ?: $request->cookies->get($sess->getName(), '');
                        $sess->setId($sessionId);
                    }
                }

                return $sess;
            });
        }
    }

    /**
     * @internal
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $autoCacheControl = !$response->headers->has(self::NO_AUTO_CACHE_CONTROL_HEADER);
        // Always remove the internal header if present
        $response->headers->remove(self::NO_AUTO_CACHE_CONTROL_HEADER);
        if (!$event->getRequest()->hasSession(true)) {
            return;
        }
        $session = $event->getRequest()->getSession();

        if ($session->isStarted()) {
            /*
             * Saves the session, in case it is still open, before sending the response/headers.
             *
             * This ensures several things in case the developer did not save the session explicitly:
             *
             *  * If a session save handler without locking is used, it ensures the data is available
             *    on the next request, e.g. after a redirect. PHPs auto-save at script end via
             *    session_register_shutdown is executed after fastcgi_finish_request. So in this case
             *    the data could be missing the next request because it might not be saved the moment
             *    the new request is processed.
             *  * A locking save handler (e.g. the native 'files') circumvents concurrency problems like
             *    the one above. But by saving the session before long-running things in the terminate event,
             *    we ensure the session is not blocked longer than needed.
             *  * When regenerating the session ID no locking is involved in PHPs session design. See
             *    https://bugs.php.net/61470 for a discussion. So in this case, the session must
             *    be saved anyway before sending the headers with the new session ID. Otherwise session
             *    data could get lost again for concurrent requests with the new ID. One result could be
             *    that you get logged out after just logging in.
             *
             * This listener should be executed as one of the last listeners, so that previous listeners
             * can still operate on the open session. This prevents the overhead of restarting it.
             * Listeners after closing the session can still work with the session as usual because
             * Symfonys session implementation starts the session on demand. So writing to it after
             * it is saved will just restart it.
             */
            $session->save();

            /*
             * For supporting sessions in php runtime with runners like roadrunner or swoole the session
             * cookie need to be written on the response object and should not be written by PHP itself.
             */
            $sessionName = $session->getName();
            $sessionId = $session->getId();
            $sessionOptions = $this->getSessionOptions($this->sessionOptions);
            $sessionCookiePath = $sessionOptions['cookie_path'] ?? '/';
            $sessionCookieDomain = $sessionOptions['cookie_domain'] ?? null;
            $sessionCookieSecure = $sessionOptions['cookie_secure'] ?? false;
            $sessionCookieHttpOnly = $sessionOptions['cookie_httponly'] ?? true;
            $sessionCookieSameSite = $sessionOptions['cookie_samesite'] ?? Cookie::SAMESITE_LAX;
            $sessionUseCookies = $sessionOptions['use_cookies'] ?? true;

            SessionUtils::popSessionCookie($sessionName, $sessionId);

            if ($sessionUseCookies) {
                $request = $event->getRequest();
                $requestSessionCookieId = $request->cookies->get($sessionName);

                $isSessionEmpty = ($session instanceof Session ? $session->isEmpty() : !$session->all()) && empty($_SESSION); // checking $_SESSION to keep compatibility with native sessions
                if ($requestSessionCookieId && $isSessionEmpty) {
                    // PHP internally sets the session cookie value to "deleted" when setcookie() is called with empty string $value argument
                    // which happens in \Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler::destroy
                    // when the session gets invalidated (for example on logout) so we must handle this case here too
                    // otherwise we would send two Set-Cookie headers back with the response
                    SessionUtils::popSessionCookie($sessionName, 'deleted');
                    $response->headers->clearCookie(
                        $sessionName,
                        $sessionCookiePath,
                        $sessionCookieDomain,
                        $sessionCookieSecure,
                        $sessionCookieHttpOnly,
                        $sessionCookieSameSite
                    );
                } elseif ($sessionId !== $requestSessionCookieId && !$isSessionEmpty) {
                    $expire = 0;
                    $lifetime = $sessionOptions['cookie_lifetime'] ?? null;
                    if ($lifetime) {
                        $expire = time() + $lifetime;
                    }

                    $response->headers->setCookie(
                        Cookie::create(
                            $sessionName,
                            $sessionId,
                            $expire,
                            $sessionCookiePath,
                            $sessionCookieDomain,
                            $sessionCookieSecure,
                            $sessionCookieHttpOnly,
                            false,
                            $sessionCookieSameSite
                        )
                    );
                }
            }
        }

        if ($session instanceof Session ? 0 === $session->getUsageIndex() : !$session->isStarted()) {
            return;
        }

        if ($autoCacheControl) {
            $maxAge = $response->headers->hasCacheControlDirective('public') ? 0 : (int) $response->getMaxAge();
            $response
                ->setExpires(new \DateTimeImmutable('+'.$maxAge.' seconds'))
                ->setPrivate()
                ->setMaxAge($maxAge)
                ->headers->addCacheControlDirective('must-revalidate');
        }

        if (!$event->getRequest()->attributes->get('_stateless', false)) {
            return;
        }

        if ($this->debug) {
            throw new UnexpectedSessionUsageException('Session was used while the request was declared stateless.');
        }

        if ($this->container->has('logger')) {
            $this->container->get('logger')->warning('Session was used while the request was declared stateless.');
        }
    }

    /**
     * @internal
     */
    public function onSessionUsage(): void
    {
        if (!$this->debug) {
            return;
        }

        if ($this->container?->has('session_collector')) {
            $this->container->get('session_collector')();
        }

        if (!$requestStack = $this->container?->has('request_stack') ? $this->container->get('request_stack') : null) {
            return;
        }

        $stateless = false;
        $clonedRequestStack = clone $requestStack;
        while (null !== ($request = $clonedRequestStack->pop()) && !$stateless) {
            $stateless = $request->attributes->get('_stateless');
        }

        if (!$stateless) {
            return;
        }

        if (!$session = $requestStack->getCurrentRequest()->getSession()) {
            return;
        }

        if ($session->isStarted()) {
            $session->save();
        }

        throw new UnexpectedSessionUsageException('Session was used while the request was declared stateless.');
    }

    /**
     * @internal
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 128],
            // low priority to come after regular response listeners
            KernelEvents::RESPONSE => ['onKernelResponse', -1000],
        ];
    }

    /**
     * @internal
     */
    public function reset(): void
    {
        if (\PHP_SESSION_ACTIVE === session_status()) {
            session_abort();
        }

        session_unset();
        $_SESSION = [];

        if (!headers_sent()) { // session id can only be reset when no headers were so we check for headers_sent first
            session_id('');
        }
    }

    /**
     * Gets the session object.
     *
     * @internal
     */
    abstract protected function getSession(): ?SessionInterface;

    private function getSessionOptions(array $sessionOptions): array
    {
        $mergedSessionOptions = [];

        foreach (session_get_cookie_params() as $key => $value) {
            $mergedSessionOptions['cookie_'.$key] = $value;
        }

        foreach ($sessionOptions as $key => $value) {
            // do the same logic as in the NativeSessionStorage
            if ('cookie_secure' === $key && 'auto' === $value) {
                continue;
            }
            $mergedSessionOptions[$key] = $value;
        }

        return $mergedSessionOptions;
    }
}
