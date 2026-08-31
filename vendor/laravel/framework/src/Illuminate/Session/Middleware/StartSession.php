<?php

namespace Illuminate\Session\Middleware;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class StartSession
{
    /**
     * The session manager.
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected $manager;

    /**
     * The callback that can resolve an instance of the cache factory.
     *
     * @var callable|null
     */
    protected $cacheFactoryResolver;

    /**
     * Create a new session middleware.
     *
     * @param  \Illuminate\Session\SessionManager  $manager
     * @param  callable|null  $cacheFactoryResolver
     */
    public function __construct(SessionManager $manager, ?callable $cacheFactoryResolver = null)
    {
        $this->manager = $manager;
        $this->cacheFactoryResolver = $cacheFactoryResolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->sessionConfigured()) {
            return $next($request);
        }

        $session = $this->getSession($request);

        if ($this->manager->shouldBlock() ||
            ($request->route() instanceof Route && $request->route()->locksFor())) {
            return $this->handleRequestWhileBlocking($request, $session, $next);
        }

        return $this->handleStatefulRequest($request, $session, $next);
    }

    /**
     * Handle the given request within session state.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  \Closure  $next
     * @return mixed
     */
    protected function handleRequestWhileBlocking(Request $request, $session, Closure $next)
    {
        if (! $request->route() instanceof Route) {
            return;
        }

        $lockFor = $request->route() && $request->route()->locksFor()
            ? $request->route()->locksFor()
            : $this->manager->defaultRouteBlockLockSeconds();

        $lock = $this->cache($this->manager->blockDriver())
            ->lock('session:'.$session->getId(), $lockFor)
            ->betweenBlockedAttemptsSleepFor(50);

        try {
            $lock->block(
                ! is_null($request->route()->waitsFor())
                    ? $request->route()->waitsFor()
                    : $this->manager->defaultRouteBlockWaitSeconds()
            );

            return $this->handleStatefulRequest($request, $session, $next);
        } finally {
            $lock?->release();
        }
    }

    /**
     * Handle the given request within session state.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  \Closure  $next
     * @return mixed
     */
    protected function handleStatefulRequest(Request $request, $session, Closure $next)
    {
        // If a session driver has been configured, we will need to start the session here
        // so that the data is ready for an application. Note that the Laravel sessions
        // do not make use of PHP "native" sessions in any way since they are crappy.
        $request->setLaravelSession(
            $this->startSession($request, $session)
        );

        $this->collectGarbage($session);

        $response = $next($request);

        $this->storeCurrentUrl($request, $session);

        $this->addCookieToResponse($response, $session);

        // Again, if the session has been configured we will need to close out the session
        // so that the attributes may be persisted to some storage medium. We will also
        // add the session identifier cookie to the application response headers now.
        $this->saveSession($request);

        return $response;
    }

    /**
     * Start the session for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @return \Illuminate\Contracts\Session\Session
     */
    protected function startSession(Request $request, $session)
    {
        return tap($session, function ($session) use ($request) {
            $session->setRequestOnHandler($request);

            $session->start();
        });
    }

    /**
     * Get the session implementation from the manager.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Session\Session
     */
    public function getSession(Request $request)
    {
        return tap($this->manager->driver(), function ($session) use ($request) {
            $session->setId($request->cookies->get($session->getName()));
        });
    }

    /**
     * Remove the garbage from the session if necessary.
     *
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @return void
     */
    protected function collectGarbage(Session $session)
    {
        $config = $this->manager->getSessionConfig();

        // Here we will see if this request hits the garbage collection lottery by hitting
        // the odds needed to perform garbage collection on any given request. If we do
        // hit it, we'll call this handler to let it delete all the expired sessions.
        if ($this->configHitsLottery($config)) {
            $session->getHandler()->gc($this->getSessionLifetimeInSeconds());
        }
    }

    /**
     * Determine if the configuration odds hit the lottery.
     *
     * @param  array  $config
     * @return bool
     */
    protected function configHitsLottery(array $config)
    {
        return random_int(1, $config['lottery'][1]) <= $config['lottery'][0];
    }

    /**
     * Store the current URL for the request if necessary.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @return void
     */
    protected function storeCurrentUrl(Request $request, $session)
    {
        if ($request->isMethod('GET') &&
            $request->route() instanceof Route &&
            ! $request->ajax() &&
            ! $request->prefetch() &&
            ! $request->isPrecognitive()) {
            $session->setPreviousUrl($request->fullUrl());

            if (method_exists($session, 'setPreviousRoute')) {
                $session->setPreviousRoute($request->route()->getName());
            }
        }
    }

    /**
     * Add the session cookie to the application response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @return void
     */
    protected function addCookieToResponse(Response $response, Session $session)
    {
        if ($this->sessionIsPersistent($config = $this->manager->getSessionConfig())) {
            $response->headers->setCookie(new Cookie(
                $session->getName(),
                $session->getId(),
                $this->getCookieExpirationDate(),
                $config['path'],
                $config['domain'],
                $config['secure'],
                $config['http_only'] ?? true,
                false,
                $config['same_site'] ?? null,
                $config['partitioned'] ?? false
            ));
        }
    }

    /**
     * Save the session data to storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function saveSession($request)
    {
        if (! $request->isPrecognitive()) {
            $this->manager->driver()->save();
        }
    }

    /**
     * Get the session lifetime in seconds.
     *
     * @return int
     */
    protected function getSessionLifetimeInSeconds()
    {
        return ($this->manager->getSessionConfig()['lifetime'] ?? null) * 60;
    }

    /**
     * Get the cookie lifetime in seconds.
     *
     * @return \DateTimeInterface|int
     */
    protected function getCookieExpirationDate()
    {
        $expiresOnClose = $this->manager->getSessionConfig()['expire_on_close'];

        return $expiresOnClose ? 0 : Date::instance(
            Carbon::now()->addSeconds($this->getSessionLifetimeInSeconds())
        );
    }

    /**
     * Determine if a session driver has been configured.
     *
     * @return bool
     */
    protected function sessionConfigured()
    {
        return ! is_null($this->manager->getSessionConfig()['driver'] ?? null);
    }

    /**
     * Determine if the configured session driver is persistent.
     *
     * @param  array|null  $config
     * @return bool
     */
    protected function sessionIsPersistent(?array $config = null)
    {
        $config = $config ?: $this->manager->getSessionConfig();

        return ! is_null($config['driver'] ?? null);
    }

    /**
     * Resolve the given cache driver.
     *
     * @param  string  $driver
     * @return \Illuminate\Cache\Store
     */
    protected function cache($driver)
    {
        return call_user_func($this->cacheFactoryResolver)->driver($driver);
    }
}
