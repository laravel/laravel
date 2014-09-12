<?php
namespace Illuminate\Support;

class ClassLoader
{
    protected static $directories = array();
    protected static $registered = false;
    public static function load($class)
    {
        $class = static::normalizeClass($class);
        foreach (static::$directories as $directory) {
            if (file_exists($path = $directory . DIRECTORY_SEPARATOR . $class)) {
                require_once $path;
                return true;
            }
        }
        return false;
    }
    public static function normalizeClass($class)
    {
        if ($class[0] == '\\') {
            $class = substr($class, 1);
        }
        return str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
    }
    public static function register()
    {
        if (!static::$registered) {
            static::$registered = spl_autoload_register(array('\\Illuminate\\Support\\ClassLoader', 'load'));
        }
    }
    public static function addDirectories($directories)
    {
        static::$directories = array_unique(array_merge(static::$directories, (array) $directories));
    }
    public static function removeDirectories($directories = null)
    {
        if (is_null($directories)) {
            static::$directories = array();
        } else {
            static::$directories = array_diff(static::$directories, (array) $directories);
        }
    }
    public static function getDirectories()
    {
        return static::$directories;
    }
}
namespace Illuminate\Container;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionParameter;
class BindingResolutionException extends \Exception
{
    
}
class Container implements ArrayAccess
{
    protected $resolved = array();
    protected $bindings = array();
    protected $instances = array();
    protected $aliases = array();
    protected $reboundCallbacks = array();
    protected $resolvingCallbacks = array();
    protected $globalResolvingCallbacks = array();
    protected function resolvable($abstract)
    {
        return $this->bound($abstract) || $this->isAlias($abstract);
    }
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }
    public function resolved($abstract)
    {
        return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
    }
    public function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_array($abstract)) {
            list($abstract, $alias) = $this->extractAlias($abstract);
            $this->alias($abstract, $alias);
        }
        $this->dropStaleInstances($abstract);
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }
    protected function getClosure($abstract, $concrete)
    {
        return function ($c, $parameters = array()) use($abstract, $concrete) {
            $method = $abstract == $concrete ? 'build' : 'make';
            return $c->{$method}($concrete, $parameters);
        };
    }
    public function bindIf($abstract, $concrete = null, $shared = false)
    {
        if (!$this->bound($abstract)) {
            $this->bind($abstract, $concrete, $shared);
        }
    }
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }
    public function share(Closure $closure)
    {
        return function ($container) use($closure) {
            static $object;
            if (is_null($object)) {
                $object = $closure($container);
            }
            return $object;
        };
    }
    public function bindShared($abstract, Closure $closure)
    {
        $this->bind($abstract, $this->share($closure), true);
    }
    public function extend($abstract, Closure $closure)
    {
        if (!isset($this->bindings[$abstract])) {
            throw new \InvalidArgumentException("Type {$abstract} is not bound.");
        }
        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $closure($this->instances[$abstract], $this);
            $this->rebound($abstract);
        } else {
            $extender = $this->getExtender($abstract, $closure);
            $this->bind($abstract, $extender, $this->isShared($abstract));
        }
    }
    protected function getExtender($abstract, Closure $closure)
    {
        $resolver = $this->bindings[$abstract]['concrete'];
        return function ($container) use($resolver, $closure) {
            return $closure($resolver($container), $container);
        };
    }
    public function instance($abstract, $instance)
    {
        if (is_array($abstract)) {
            list($abstract, $alias) = $this->extractAlias($abstract);
            $this->alias($abstract, $alias);
        }
        unset($this->aliases[$abstract]);
        $bound = $this->bound($abstract);
        $this->instances[$abstract] = $instance;
        if ($bound) {
            $this->rebound($abstract);
        }
    }
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
    }
    protected function extractAlias(array $definition)
    {
        return array(key($definition), current($definition));
    }
    public function rebinding($abstract, Closure $callback)
    {
        $this->reboundCallbacks[$abstract][] = $callback;
        if ($this->bound($abstract)) {
            return $this->make($abstract);
        }
    }
    public function refresh($abstract, $target, $method)
    {
        return $this->rebinding($abstract, function ($app, $instance) use($target, $method) {
            $target->{$method}($instance);
        });
    }
    protected function rebound($abstract)
    {
        $instance = $this->make($abstract);
        foreach ($this->getReboundCallbacks($abstract) as $callback) {
            call_user_func($callback, $this, $instance);
        }
    }
    protected function getReboundCallbacks($abstract)
    {
        if (isset($this->reboundCallbacks[$abstract])) {
            return $this->reboundCallbacks[$abstract];
        } else {
            return array();
        }
    }
    public function make($abstract, $parameters = array())
    {
        $abstract = $this->getAlias($abstract);
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        $concrete = $this->getConcrete($abstract);
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->make($concrete, $parameters);
        }
        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }
        $this->fireResolvingCallbacks($abstract, $object);
        $this->resolved[$abstract] = true;
        return $object;
    }
    protected function getConcrete($abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            if ($this->missingLeadingSlash($abstract) && isset($this->bindings['\\' . $abstract])) {
                $abstract = '\\' . $abstract;
            }
            return $abstract;
        } else {
            return $this->bindings[$abstract]['concrete'];
        }
    }
    protected function missingLeadingSlash($abstract)
    {
        return is_string($abstract) && strpos($abstract, '\\') !== 0;
    }
    public function build($concrete, $parameters = array())
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            $message = "Target [{$concrete}] is not instantiable.";
            throw new BindingResolutionException($message);
        }
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete();
        }
        $dependencies = $constructor->getParameters();
        $parameters = $this->keyParametersByArgument($dependencies, $parameters);
        $instances = $this->getDependencies($dependencies, $parameters);
        return $reflector->newInstanceArgs($instances);
    }
    protected function getDependencies($parameters, array $primitives = array())
    {
        $dependencies = array();
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (array_key_exists($parameter->name, $primitives)) {
                $dependencies[] = $primitives[$parameter->name];
            } elseif (is_null($dependency)) {
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }
        return (array) $dependencies;
    }
    protected function resolveNonClass(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        } else {
            $message = "Unresolvable dependency resolving [{$parameter}] in class {$parameter->getDeclaringClass()->getName()}";
            throw new BindingResolutionException($message);
        }
    }
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            return $this->make($parameter->getClass()->name);
        } catch (BindingResolutionException $e) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            } else {
                throw $e;
            }
        }
    }
    protected function keyParametersByArgument(array $dependencies, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (is_numeric($key)) {
                unset($parameters[$key]);
                $parameters[$dependencies[$key]->name] = $value;
            }
        }
        return $parameters;
    }
    public function resolving($abstract, Closure $callback)
    {
        $this->resolvingCallbacks[$abstract][] = $callback;
    }
    public function resolvingAny(Closure $callback)
    {
        $this->globalResolvingCallbacks[] = $callback;
    }
    protected function fireResolvingCallbacks($abstract, $object)
    {
        if (isset($this->resolvingCallbacks[$abstract])) {
            $this->fireCallbackArray($object, $this->resolvingCallbacks[$abstract]);
        }
        $this->fireCallbackArray($object, $this->globalResolvingCallbacks);
    }
    protected function fireCallbackArray($object, array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $object, $this);
        }
    }
    public function isShared($abstract)
    {
        if (isset($this->bindings[$abstract]['shared'])) {
            $shared = $this->bindings[$abstract]['shared'];
        } else {
            $shared = false;
        }
        return isset($this->instances[$abstract]) || $shared === true;
    }
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }
    protected function getAlias($abstract)
    {
        return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
    }
    public function getBindings()
    {
        return $this->bindings;
    }
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract]);
        unset($this->aliases[$abstract]);
    }
    public function forgetInstance($abstract)
    {
        unset($this->instances[$abstract]);
    }
    public function forgetInstances()
    {
        $this->instances = array();
    }
    public function offsetExists($key)
    {
        return isset($this->bindings[$key]);
    }
    public function offsetGet($key)
    {
        return $this->make($key);
    }
    public function offsetSet($key, $value)
    {
        if (!$value instanceof Closure) {
            $value = function () use($value) {
                return $value;
            };
        }
        $this->bind($key, $value);
    }
    public function offsetUnset($key)
    {
        unset($this->bindings[$key]);
        unset($this->instances[$key]);
    }
}
namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
interface HttpKernelInterface
{
    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true);
}
namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
interface TerminableInterface
{
    public function terminate(Request $request, Response $response);
}
namespace Illuminate\Support\Contracts;

interface ResponsePreparerInterface
{
    public function prepareResponse($value);
    public function readyForResponses();
}
namespace Illuminate\Foundation;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Config\FileLoader;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Exception\ExceptionServiceProvider;
use Illuminate\Config\FileEnvironmentVariablesLoader;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Illuminate\Support\Contracts\ResponsePreparerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class Application extends Container implements HttpKernelInterface, TerminableInterface, ResponsePreparerInterface
{
    const VERSION = '4.2.8';
    protected $booted = false;
    protected $bootingCallbacks = array();
    protected $bootedCallbacks = array();
    protected $finishCallbacks = array();
    protected $shutdownCallbacks = array();
    protected $middlewares = array();
    protected $serviceProviders = array();
    protected $loadedProviders = array();
    protected $deferredServices = array();
    protected static $requestClass = 'Illuminate\\Http\\Request';
    public function __construct(Request $request = null)
    {
        $this->registerBaseBindings($request ?: $this->createNewRequest());
        $this->registerBaseServiceProviders();
        $this->registerBaseMiddlewares();
    }
    protected function createNewRequest()
    {
        return forward_static_call(array(static::$requestClass, 'createFromGlobals'));
    }
    protected function registerBaseBindings($request)
    {
        $this->instance('request', $request);
        $this->instance('Illuminate\\Container\\Container', $this);
    }
    protected function registerBaseServiceProviders()
    {
        foreach (array('Event', 'Exception', 'Routing') as $name) {
            $this->{"register{$name}Provider"}();
        }
    }
    protected function registerExceptionProvider()
    {
        $this->register(new ExceptionServiceProvider($this));
    }
    protected function registerRoutingProvider()
    {
        $this->register(new RoutingServiceProvider($this));
    }
    protected function registerEventProvider()
    {
        $this->register(new EventServiceProvider($this));
    }
    public function bindInstallPaths(array $paths)
    {
        $this->instance('path', realpath($paths['app']));
        foreach (array_except($paths, array('app')) as $key => $value) {
            $this->instance("path.{$key}", realpath($value));
        }
    }
    public static function getBootstrapFile()
    {
        return '/Users/adam/Sites/liferaft/route-contract/vendor/laravel/framework/src/Illuminate/Foundation' . '/start.php';
    }
    public function startExceptionHandling()
    {
        $this['exception']->register($this->environment());
        $this['exception']->setDebug($this['config']['app.debug']);
    }
    public function environment()
    {
        if (count(func_get_args()) > 0) {
            return in_array($this['env'], func_get_args());
        } else {
            return $this['env'];
        }
    }
    public function isLocal()
    {
        return $this['env'] == 'local';
    }
    public function detectEnvironment($envs)
    {
        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;
        return $this['env'] = (new EnvironmentDetector())->detect($envs, $args);
    }
    public function runningInConsole()
    {
        return php_sapi_name() == 'cli';
    }
    public function runningUnitTests()
    {
        return $this['env'] == 'testing';
    }
    public function forceRegister($provider, $options = array())
    {
        return $this->register($provider, $options, true);
    }
    public function register($provider, $options = array(), $force = false)
    {
        if ($registered = $this->getRegistered($provider) && !$force) {
            return $registered;
        }
        if (is_string($provider)) {
            $provider = $this->resolveProviderClass($provider);
        }
        $provider->register();
        foreach ($options as $key => $value) {
            $this[$key] = $value;
        }
        $this->markAsRegistered($provider);
        if ($this->booted) {
            $provider->boot();
        }
        return $provider;
    }
    public function getRegistered($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);
        if (array_key_exists($name, $this->loadedProviders)) {
            return array_first($this->serviceProviders, function ($key, $value) use($name) {
                return get_class($value) == $name;
            });
        }
    }
    public function resolveProviderClass($provider)
    {
        return new $provider($this);
    }
    protected function markAsRegistered($provider)
    {
        $this['events']->fire($class = get_class($provider), array($provider));
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[$class] = true;
    }
    public function loadDeferredProviders()
    {
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }
        $this->deferredServices = array();
    }
    protected function loadDeferredProvider($service)
    {
        $provider = $this->deferredServices[$service];
        if (!isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }
    public function registerDeferredProvider($provider, $service = null)
    {
        if ($service) {
            unset($this->deferredServices[$service]);
        }
        $this->register($instance = new $provider($this));
        if (!$this->booted) {
            $this->booting(function () use($instance) {
                $instance->boot();
            });
        }
    }
    public function make($abstract, $parameters = array())
    {
        $abstract = $this->getAlias($abstract);
        if (isset($this->deferredServices[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }
        return parent::make($abstract, $parameters);
    }
    public function bound($abstract)
    {
        return isset($this->deferredServices[$abstract]) || parent::bound($abstract);
    }
    public function extend($abstract, Closure $closure)
    {
        $abstract = $this->getAlias($abstract);
        if (isset($this->deferredServices[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }
        return parent::extend($abstract, $closure);
    }
    public function before($callback)
    {
        return $this['router']->before($callback);
    }
    public function after($callback)
    {
        return $this['router']->after($callback);
    }
    public function finish($callback)
    {
        $this->finishCallbacks[] = $callback;
    }
    public function shutdown(callable $callback = null)
    {
        if (is_null($callback)) {
            $this->fireAppCallbacks($this->shutdownCallbacks);
        } else {
            $this->shutdownCallbacks[] = $callback;
        }
    }
    public function useArraySessions(Closure $callback)
    {
        $this->bind('session.reject', function () use($callback) {
            return $callback;
        });
    }
    public function isBooted()
    {
        return $this->booted;
    }
    public function boot()
    {
        if ($this->booted) {
            return;
        }
        array_walk($this->serviceProviders, function ($p) {
            $p->boot();
        });
        $this->bootApplication();
    }
    protected function bootApplication()
    {
        $this->fireAppCallbacks($this->bootingCallbacks);
        $this->booted = true;
        $this->fireAppCallbacks($this->bootedCallbacks);
    }
    public function booting($callback)
    {
        $this->bootingCallbacks[] = $callback;
    }
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;
        if ($this->isBooted()) {
            $this->fireAppCallbacks(array($callback));
        }
    }
    public function run(SymfonyRequest $request = null)
    {
        $request = $request ?: $this['request'];
        $response = with($stack = $this->getStackedClient())->handle($request);
        $response->send();
        $stack->terminate($request, $response);
    }
    protected function getStackedClient()
    {
        $sessionReject = $this->bound('session.reject') ? $this['session.reject'] : null;
        $client = (new \Stack\Builder())->push('Illuminate\\Cookie\\Guard', $this['encrypter'])->push('Illuminate\\Cookie\\Queue', $this['cookie'])->push('Illuminate\\Session\\Middleware', $this['session'], $sessionReject);
        $this->mergeCustomMiddlewares($client);
        return $client->resolve($this);
    }
    protected function mergeCustomMiddlewares(\Stack\Builder $stack)
    {
        foreach ($this->middlewares as $middleware) {
            list($class, $parameters) = array_values($middleware);
            array_unshift($parameters, $class);
            call_user_func_array(array($stack, 'push'), $parameters);
        }
    }
    protected function registerBaseMiddlewares()
    {
        
    }
    public function middleware($class, array $parameters = array())
    {
        $this->middlewares[] = compact('class', 'parameters');
        return $this;
    }
    public function forgetMiddleware($class)
    {
        $this->middlewares = array_filter($this->middlewares, function ($m) use($class) {
            return $m['class'] != $class;
        });
    }
    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        try {
            $this->refreshRequest($request = Request::createFromBase($request));
            $this->boot();
            return $this->dispatch($request);
        } catch (\Exception $e) {
            if ($this->runningUnitTests()) {
                throw $e;
            }
            return $this['exception']->handleException($e);
        }
    }
    public function dispatch(Request $request)
    {
        if ($this->isDownForMaintenance()) {
            $response = $this['events']->until('illuminate.app.down');
            if (!is_null($response)) {
                return $this->prepareResponse($response, $request);
            }
        }
        if ($this->runningUnitTests() && !$this['session']->isStarted()) {
            $this['session']->start();
        }
        return $this['router']->dispatch($this->prepareRequest($request));
    }
    public function terminate(SymfonyRequest $request, SymfonyResponse $response)
    {
        $this->callFinishCallbacks($request, $response);
        $this->shutdown();
    }
    protected function refreshRequest(Request $request)
    {
        $this->instance('request', $request);
        Facade::clearResolvedInstance('request');
    }
    public function callFinishCallbacks(SymfonyRequest $request, SymfonyResponse $response)
    {
        foreach ($this->finishCallbacks as $callback) {
            call_user_func($callback, $request, $response);
        }
    }
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }
    public function prepareRequest(Request $request)
    {
        if (!is_null($this['config']['session.driver']) && !$request->hasSession()) {
            $request->setSession($this['session']->driver());
        }
        return $request;
    }
    public function prepareResponse($value)
    {
        if (!$value instanceof SymfonyResponse) {
            $value = new Response($value);
        }
        return $value->prepare($this['request']);
    }
    public function readyForResponses()
    {
        return $this->booted;
    }
    public function isDownForMaintenance()
    {
        return file_exists($this['config']['app.manifest'] . '/down');
    }
    public function down(Closure $callback)
    {
        $this['events']->listen('illuminate.app.down', $callback);
    }
    public function abort($code, $message = '', array $headers = array())
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        } else {
            throw new HttpException($code, $message, null, $headers);
        }
    }
    public function missing(Closure $callback)
    {
        $this->error(function (NotFoundHttpException $e) use($callback) {
            return call_user_func($callback, $e);
        });
    }
    public function error(Closure $callback)
    {
        $this['exception']->error($callback);
    }
    public function pushError(Closure $callback)
    {
        $this['exception']->pushError($callback);
    }
    public function fatal(Closure $callback)
    {
        $this->error(function (FatalErrorException $e) use($callback) {
            return call_user_func($callback, $e);
        });
    }
    public function getConfigLoader()
    {
        return new FileLoader(new Filesystem(), $this['path'] . '/config');
    }
    public function getEnvironmentVariablesLoader()
    {
        return new FileEnvironmentVariablesLoader(new Filesystem(), $this['path.base']);
    }
    public function getProviderRepository()
    {
        $manifest = $this['config']['app.manifest'];
        return new ProviderRepository(new Filesystem(), $manifest);
    }
    public function getLoadedProviders()
    {
        return $this->loadedProviders;
    }
    public function setDeferredServices(array $services)
    {
        $this->deferredServices = $services;
    }
    public function isDeferredService($service)
    {
        return isset($this->deferredServices[$service]);
    }
    public static function requestClass($class = null)
    {
        if (!is_null($class)) {
            static::$requestClass = $class;
        }
        return static::$requestClass;
    }
    public function setRequestForConsoleEnvironment()
    {
        $url = $this['config']->get('app.url', 'http://localhost');
        $parameters = array($url, 'GET', array(), array(), array(), $_SERVER);
        $this->refreshRequest(static::onRequest('create', $parameters));
    }
    public static function onRequest($method, $parameters = array())
    {
        return forward_static_call_array(array(static::requestClass(), $method), $parameters);
    }
    public function getLocale()
    {
        return $this['config']->get('app.locale');
    }
    public function setLocale($locale)
    {
        $this['config']->set('app.locale', $locale);
        $this['translator']->setLocale($locale);
        $this['events']->fire('locale.changed', array($locale));
    }
    public function registerCoreContainerAliases()
    {
        $aliases = array('app' => 'Illuminate\\Foundation\\Application', 'artisan' => 'Illuminate\\Console\\Application', 'auth' => 'Illuminate\\Auth\\AuthManager', 'auth.reminder.repository' => 'Illuminate\\Auth\\Reminders\\ReminderRepositoryInterface', 'blade.compiler' => 'Illuminate\\View\\Compilers\\BladeCompiler', 'cache' => 'Illuminate\\Cache\\CacheManager', 'cache.store' => 'Illuminate\\Cache\\Repository', 'config' => 'Illuminate\\Config\\Repository', 'cookie' => 'Illuminate\\Cookie\\CookieJar', 'encrypter' => 'Illuminate\\Encryption\\Encrypter', 'db' => 'Illuminate\\Database\\DatabaseManager', 'events' => 'Illuminate\\Events\\Dispatcher', 'files' => 'Illuminate\\Filesystem\\Filesystem', 'form' => 'Illuminate\\Html\\FormBuilder', 'hash' => 'Illuminate\\Hashing\\HasherInterface', 'html' => 'Illuminate\\Html\\HtmlBuilder', 'translator' => 'Illuminate\\Translation\\Translator', 'log' => 'Illuminate\\Log\\Writer', 'mailer' => 'Illuminate\\Mail\\Mailer', 'paginator' => 'Illuminate\\Pagination\\Factory', 'auth.reminder' => 'Illuminate\\Auth\\Reminders\\PasswordBroker', 'queue' => 'Illuminate\\Queue\\QueueManager', 'redirect' => 'Illuminate\\Routing\\Redirector', 'redis' => 'Illuminate\\Redis\\Database', 'request' => 'Illuminate\\Http\\Request', 'router' => 'Illuminate\\Routing\\Router', 'session' => 'Illuminate\\Session\\SessionManager', 'session.store' => 'Illuminate\\Session\\Store', 'remote' => 'Illuminate\\Remote\\RemoteManager', 'url' => 'Illuminate\\Routing\\UrlGenerator', 'validator' => 'Illuminate\\Validation\\Factory', 'view' => 'Illuminate\\View\\Factory');
        foreach ($aliases as $key => $alias) {
            $this->alias($key, $alias);
        }
    }
    public function __get($key)
    {
        return $this[$key];
    }
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}
namespace Illuminate\Foundation;

use Closure;
class EnvironmentDetector
{
    public function detect($environments, $consoleArgs = null)
    {
        if ($consoleArgs) {
            return $this->detectConsoleEnvironment($environments, $consoleArgs);
        } else {
            return $this->detectWebEnvironment($environments);
        }
    }
    protected function detectWebEnvironment($environments)
    {
        if ($environments instanceof Closure) {
            return call_user_func($environments);
        }
        foreach ($environments as $environment => $hosts) {
            foreach ((array) $hosts as $host) {
                if ($this->isMachine($host)) {
                    return $environment;
                }
            }
        }
        return 'production';
    }
    protected function detectConsoleEnvironment($environments, array $args)
    {
        if (!is_null($value = $this->getEnvironmentArgument($args))) {
            return head(array_slice(explode('=', $value), 1));
        } else {
            return $this->detectWebEnvironment($environments);
        }
    }
    protected function getEnvironmentArgument(array $args)
    {
        return array_first($args, function ($k, $v) {
            return starts_with($v, '--env');
        });
    }
    public function isMachine($name)
    {
        return str_is($name, gethostname());
    }
}
namespace Illuminate\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
class Request extends SymfonyRequest
{
    protected $json;
    protected $sessionStore;
    public function instance()
    {
        return $this;
    }
    public function method()
    {
        return $this->getMethod();
    }
    public function root()
    {
        return rtrim($this->getSchemeAndHttpHost() . $this->getBaseUrl(), '/');
    }
    public function url()
    {
        return rtrim(preg_replace('/\\?.*/', '', $this->getUri()), '/');
    }
    public function fullUrl()
    {
        $query = $this->getQueryString();
        return $query ? $this->url() . '?' . $query : $this->url();
    }
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');
        return $pattern == '' ? '/' : $pattern;
    }
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }
    public function segment($index, $default = null)
    {
        return array_get($this->segments(), $index - 1, $default);
    }
    public function segments()
    {
        $segments = explode('/', $this->path());
        return array_values(array_filter($segments, function ($v) {
            return $v != '';
        }));
    }
    public function is()
    {
        foreach (func_get_args() as $pattern) {
            if (str_is($pattern, urldecode($this->path()))) {
                return true;
            }
        }
        return false;
    }
    public function ajax()
    {
        return $this->isXmlHttpRequest();
    }
    public function secure()
    {
        return $this->isSecure();
    }
    public function exists($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
        $input = $this->all();
        foreach ($keys as $value) {
            if (!array_key_exists($value, $input)) {
                return false;
            }
        }
        return true;
    }
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }
        return true;
    }
    protected function isEmptyString($key)
    {
        $boolOrArray = is_bool($this->input($key)) || is_array($this->input($key));
        return !$boolOrArray && trim((string) $this->input($key)) === '';
    }
    public function all()
    {
        return array_replace_recursive($this->input(), $this->files->all());
    }
    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all() + $this->query->all();
        return array_get($input, $key, $default);
    }
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = array();
        $input = $this->all();
        foreach ($keys as $key) {
            array_set($results, $key, array_get($input, $key, null));
        }
        return $results;
    }
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = $this->all();
        array_forget($results, $keys);
        return $results;
    }
    public function query($key = null, $default = null)
    {
        return $this->retrieveItem('query', $key, $default);
    }
    public function hasCookie($key)
    {
        return !is_null($this->cookie($key));
    }
    public function cookie($key = null, $default = null)
    {
        return $this->retrieveItem('cookies', $key, $default);
    }
    public function file($key = null, $default = null)
    {
        return array_get($this->files->all(), $key, $default);
    }
    public function hasFile($key)
    {
        if (is_array($file = $this->file($key))) {
            $file = head($file);
        }
        return $file instanceof \SplFileInfo && $file->getPath() != '';
    }
    public function header($key = null, $default = null)
    {
        return $this->retrieveItem('headers', $key, $default);
    }
    public function server($key = null, $default = null)
    {
        return $this->retrieveItem('server', $key, $default);
    }
    public function old($key = null, $default = null)
    {
        return $this->session()->getOldInput($key, $default);
    }
    public function flash($filter = null, $keys = array())
    {
        $flash = !is_null($filter) ? $this->{$filter}($keys) : $this->input();
        $this->session()->flashInput($flash);
    }
    public function flashOnly($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return $this->flash('only', $keys);
    }
    public function flashExcept($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return $this->flash('except', $keys);
    }
    public function flush()
    {
        $this->session()->flashInput(array());
    }
    protected function retrieveItem($source, $key, $default)
    {
        if (is_null($key)) {
            return $this->{$source}->all();
        } else {
            return $this->{$source}->get($key, $default, true);
        }
    }
    public function merge(array $input)
    {
        $this->getInputSource()->add($input);
    }
    public function replace(array $input)
    {
        $this->getInputSource()->replace($input);
    }
    public function json($key = null, $default = null)
    {
        if (!isset($this->json)) {
            $this->json = new ParameterBag((array) json_decode($this->getContent(), true));
        }
        if (is_null($key)) {
            return $this->json;
        }
        return array_get($this->json->all(), $key, $default);
    }
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }
        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }
    public function isJson()
    {
        return str_contains($this->header('CONTENT_TYPE'), '/json');
    }
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();
        return isset($acceptable[0]) && $acceptable[0] == 'application/json';
    }
    public function format($default = 'html')
    {
        foreach ($this->getAcceptableContentTypes() as $type) {
            if ($format = $this->getFormat($type)) {
                return $format;
            }
        }
        return $default;
    }
    public static function createFromBase(SymfonyRequest $request)
    {
        if ($request instanceof static) {
            return $request;
        }
        return (new static())->duplicate($request->query->all(), $request->request->all(), $request->attributes->all(), $request->cookies->all(), $request->files->all(), $request->server->all());
    }
    public function session()
    {
        if (!$this->hasSession()) {
            throw new \RuntimeException('Session store not set on request.');
        }
        return $this->getSession();
    }
}
namespace Illuminate\Http;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
class FrameGuard implements HttpKernelInterface
{
    protected $app;
    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }
    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = $this->app->handle($request, $type, $catch);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        return $response;
    }
}
namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
class Request
{
    const HEADER_CLIENT_IP = 'client_ip';
    const HEADER_CLIENT_HOST = 'client_host';
    const HEADER_CLIENT_PROTO = 'client_proto';
    const HEADER_CLIENT_PORT = 'client_port';
    protected static $trustedProxies = array();
    protected static $trustedHostPatterns = array();
    protected static $trustedHosts = array();
    protected static $trustedHeaders = array(self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR', self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST', self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO', self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT');
    protected static $httpMethodParameterOverride = false;
    public $attributes;
    public $request;
    public $query;
    public $server;
    public $files;
    public $cookies;
    public $headers;
    protected $content;
    protected $languages;
    protected $charsets;
    protected $encodings;
    protected $acceptableContentTypes;
    protected $pathInfo;
    protected $requestUri;
    protected $baseUrl;
    protected $basePath;
    protected $method;
    protected $format;
    protected $session;
    protected $locale;
    protected $defaultLocale = 'en';
    protected static $formats;
    protected static $requestFactory;
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }
    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->request = new ParameterBag($request);
        $this->query = new ParameterBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new ParameterBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());
        $this->content = $content;
        $this->languages = null;
        $this->charsets = null;
        $this->encodings = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }
    public static function createFromGlobals()
    {
        $request = self::createRequestFromFactory($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded') && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }
        return $request;
    }
    public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        $server = array_replace(array('SERVER_NAME' => 'localhost', 'SERVER_PORT' => 80, 'HTTP_HOST' => 'localhost', 'HTTP_USER_AGENT' => 'Symfony/2.X', 'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5', 'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'REMOTE_ADDR' => '127.0.0.1', 'SCRIPT_NAME' => '', 'SCRIPT_FILENAME' => '', 'SERVER_PROTOCOL' => 'HTTP/1.1', 'REQUEST_TIME' => time()), $server);
        $server['PATH_INFO'] = '';
        $server['REQUEST_METHOD'] = strtoupper($method);
        $components = parse_url($uri);
        if (isset($components['host'])) {
            $server['SERVER_NAME'] = $components['host'];
            $server['HTTP_HOST'] = $components['host'];
        }
        if (isset($components['scheme'])) {
            if ('https' === $components['scheme']) {
                $server['HTTPS'] = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['HTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }
        if (isset($components['port'])) {
            $server['SERVER_PORT'] = $components['port'];
            $server['HTTP_HOST'] = $server['HTTP_HOST'] . ':' . $components['port'];
        }
        if (isset($components['user'])) {
            $server['PHP_AUTH_USER'] = $components['user'];
        }
        if (isset($components['pass'])) {
            $server['PHP_AUTH_PW'] = $components['pass'];
        }
        if (!isset($components['path'])) {
            $components['path'] = '/';
        }
        switch (strtoupper($method)) {
            case 'POST':
            case 'PUT':
            case 'DELETE':
                if (!isset($server['CONTENT_TYPE'])) {
                    $server['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
                }
            case 'PATCH':
                $request = $parameters;
                $query = array();
                break;
            default:
                $request = array();
                $query = $parameters;
                break;
        }
        $queryString = '';
        if (isset($components['query'])) {
            parse_str(html_entity_decode($components['query']), $qs);
            if ($query) {
                $query = array_replace($qs, $query);
                $queryString = http_build_query($query, '', '&');
            } else {
                $query = $qs;
                $queryString = $components['query'];
            }
        } elseif ($query) {
            $queryString = http_build_query($query, '', '&');
        }
        $server['REQUEST_URI'] = $components['path'] . ('' !== $queryString ? '?' . $queryString : '');
        $server['QUERY_STRING'] = $queryString;
        return self::createRequestFromFactory($query, $request, array(), $cookies, $files, $server, $content);
    }
    public static function setFactory($callable)
    {
        self::$requestFactory = $callable;
    }
    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        $dup = clone $this;
        if ($query !== null) {
            $dup->query = new ParameterBag($query);
        }
        if ($request !== null) {
            $dup->request = new ParameterBag($request);
        }
        if ($attributes !== null) {
            $dup->attributes = new ParameterBag($attributes);
        }
        if ($cookies !== null) {
            $dup->cookies = new ParameterBag($cookies);
        }
        if ($files !== null) {
            $dup->files = new FileBag($files);
        }
        if ($server !== null) {
            $dup->server = new ServerBag($server);
            $dup->headers = new HeaderBag($dup->server->getHeaders());
        }
        $dup->languages = null;
        $dup->charsets = null;
        $dup->encodings = null;
        $dup->acceptableContentTypes = null;
        $dup->pathInfo = null;
        $dup->requestUri = null;
        $dup->baseUrl = null;
        $dup->basePath = null;
        $dup->method = null;
        $dup->format = null;
        if (!$dup->get('_format') && $this->get('_format')) {
            $dup->attributes->set('_format', $this->get('_format'));
        }
        if (!$dup->getRequestFormat(null)) {
            $dup->setRequestFormat($format = $this->getRequestFormat(null));
        }
        return $dup;
    }
    public function __clone()
    {
        $this->query = clone $this->query;
        $this->request = clone $this->request;
        $this->attributes = clone $this->attributes;
        $this->cookies = clone $this->cookies;
        $this->files = clone $this->files;
        $this->server = clone $this->server;
        $this->headers = clone $this->headers;
    }
    public function __toString()
    {
        return sprintf('%s %s %s', $this->getMethod(), $this->getRequestUri(), $this->server->get('SERVER_PROTOCOL')) . '
' . $this->headers . '
' . $this->getContent();
    }
    public function overrideGlobals()
    {
        $this->server->set('QUERY_STRING', static::normalizeQueryString(http_build_query($this->query->all(), null, '&')));
        $_GET = $this->query->all();
        $_POST = $this->request->all();
        $_SERVER = $this->server->all();
        $_COOKIE = $this->cookies->all();
        foreach ($this->headers->all() as $key => $value) {
            $key = strtoupper(str_replace('-', '_', $key));
            if (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH'))) {
                $_SERVER[$key] = implode(', ', $value);
            } else {
                $_SERVER['HTTP_' . $key] = implode(', ', $value);
            }
        }
        $request = array('g' => $_GET, 'p' => $_POST, 'c' => $_COOKIE);
        $requestOrder = ini_get('request_order') ?: ini_get('variables_order');
        $requestOrder = preg_replace('#[^cgp]#', '', strtolower($requestOrder)) ?: 'gp';
        $_REQUEST = array();
        foreach (str_split($requestOrder) as $order) {
            $_REQUEST = array_merge($_REQUEST, $request[$order]);
        }
    }
    public static function setTrustedProxies(array $proxies)
    {
        self::$trustedProxies = $proxies;
    }
    public static function getTrustedProxies()
    {
        return self::$trustedProxies;
    }
    public static function setTrustedHosts(array $hostPatterns)
    {
        self::$trustedHostPatterns = array_map(function ($hostPattern) {
            return sprintf('{%s}i', str_replace('}', '\\}', $hostPattern));
        }, $hostPatterns);
        self::$trustedHosts = array();
    }
    public static function getTrustedHosts()
    {
        return self::$trustedHostPatterns;
    }
    public static function setTrustedHeaderName($key, $value)
    {
        if (!array_key_exists($key, self::$trustedHeaders)) {
            throw new \InvalidArgumentException(sprintf('Unable to set the trusted header name for key "%s".', $key));
        }
        self::$trustedHeaders[$key] = $value;
    }
    public static function getTrustedHeaderName($key)
    {
        if (!array_key_exists($key, self::$trustedHeaders)) {
            throw new \InvalidArgumentException(sprintf('Unable to get the trusted header name for key "%s".', $key));
        }
        return self::$trustedHeaders[$key];
    }
    public static function normalizeQueryString($qs)
    {
        if ('' == $qs) {
            return '';
        }
        $parts = array();
        $order = array();
        foreach (explode('&', $qs) as $param) {
            if ('' === $param || '=' === $param[0]) {
                continue;
            }
            $keyValuePair = explode('=', $param, 2);
            $parts[] = isset($keyValuePair[1]) ? rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) : rawurlencode(urldecode($keyValuePair[0]));
            $order[] = urldecode($keyValuePair[0]);
        }
        array_multisort($order, SORT_ASC, $parts);
        return implode('&', $parts);
    }
    public static function enableHttpMethodParameterOverride()
    {
        self::$httpMethodParameterOverride = true;
    }
    public static function getHttpMethodParameterOverride()
    {
        return self::$httpMethodParameterOverride;
    }
    public function get($key, $default = null, $deep = false)
    {
        return $this->query->get($key, $this->attributes->get($key, $this->request->get($key, $default, $deep), $deep), $deep);
    }
    public function getSession()
    {
        return $this->session;
    }
    public function hasPreviousSession()
    {
        return $this->hasSession() && $this->cookies->has($this->session->getName());
    }
    public function hasSession()
    {
        return null !== $this->session;
    }
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }
    public function getClientIps()
    {
        $ip = $this->server->get('REMOTE_ADDR');
        if (!self::$trustedProxies) {
            return array($ip);
        }
        if (!self::$trustedHeaders[self::HEADER_CLIENT_IP] || !$this->headers->has(self::$trustedHeaders[self::HEADER_CLIENT_IP])) {
            return array($ip);
        }
        $clientIps = array_map('trim', explode(',', $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_IP])));
        $clientIps[] = $ip;
        $ip = $clientIps[0];
        foreach ($clientIps as $key => $clientIp) {
            if (IpUtils::checkIp($clientIp, self::$trustedProxies)) {
                unset($clientIps[$key]);
            }
        }
        return $clientIps ? array_reverse($clientIps) : array($ip);
    }
    public function getClientIp()
    {
        $ipAddresses = $this->getClientIps();
        return $ipAddresses[0];
    }
    public function getScriptName()
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }
        return $this->pathInfo;
    }
    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }
        return $this->basePath;
    }
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }
        return $this->baseUrl;
    }
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }
    public function getPort()
    {
        if (self::$trustedProxies) {
            if (self::$trustedHeaders[self::HEADER_CLIENT_PORT] && ($port = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PORT]))) {
                return $port;
            }
            if (self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && 'https' === $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PROTO], 'http')) {
                return 443;
            }
        }
        if ($host = $this->headers->get('HOST')) {
            if ($host[0] === '[') {
                $pos = strpos($host, ':', strrpos($host, ']'));
            } else {
                $pos = strrpos($host, ':');
            }
            if (false !== $pos) {
                return intval(substr($host, $pos + 1));
            }
            return 'https' === $this->getScheme() ? 443 : 80;
        }
        return $this->server->get('SERVER_PORT');
    }
    public function getUser()
    {
        return $this->headers->get('PHP_AUTH_USER');
    }
    public function getPassword()
    {
        return $this->headers->get('PHP_AUTH_PW');
    }
    public function getUserInfo()
    {
        $userinfo = $this->getUser();
        $pass = $this->getPassword();
        if ('' != $pass) {
            $userinfo .= ":{$pass}";
        }
        return $userinfo;
    }
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();
        if ('http' == $scheme && $port == 80 || 'https' == $scheme && $port == 443) {
            return $this->getHost();
        }
        return $this->getHost() . ':' . $port;
    }
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }
        return $this->requestUri;
    }
    public function getSchemeAndHttpHost()
    {
        return $this->getScheme() . '://' . $this->getHttpHost();
    }
    public function getUri()
    {
        if (null !== ($qs = $this->getQueryString())) {
            $qs = '?' . $qs;
        }
        return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $this->getPathInfo() . $qs;
    }
    public function getUriForPath($path)
    {
        return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $path;
    }
    public function getQueryString()
    {
        $qs = static::normalizeQueryString($this->server->get('QUERY_STRING'));
        return '' === $qs ? null : $qs;
    }
    public function isSecure()
    {
        if (self::$trustedProxies && self::$trustedHeaders[self::HEADER_CLIENT_PROTO] && ($proto = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_PROTO]))) {
            return in_array(strtolower(current(explode(',', $proto))), array('https', 'on', 'ssl', '1'));
        }
        $https = $this->server->get('HTTPS');
        return !empty($https) && 'off' !== strtolower($https);
    }
    public function getHost()
    {
        if (self::$trustedProxies && self::$trustedHeaders[self::HEADER_CLIENT_HOST] && ($host = $this->headers->get(self::$trustedHeaders[self::HEADER_CLIENT_HOST]))) {
            $elements = explode(',', $host);
            $host = $elements[count($elements) - 1];
        } elseif (!($host = $this->headers->get('HOST'))) {
            if (!($host = $this->server->get('SERVER_NAME'))) {
                $host = $this->server->get('SERVER_ADDR', '');
            }
        }
        $host = strtolower(preg_replace('/:\\d+$/', '', trim($host)));
        if ($host && '' !== preg_replace('/(?:^\\[)?[a-zA-Z0-9-:\\]_]+\\.?/', '', $host)) {
            throw new \UnexpectedValueException(sprintf('Invalid Host "%s"', $host));
        }
        if (count(self::$trustedHostPatterns) > 0) {
            if (in_array($host, self::$trustedHosts)) {
                return $host;
            }
            foreach (self::$trustedHostPatterns as $pattern) {
                if (preg_match($pattern, $host)) {
                    self::$trustedHosts[] = $host;
                    return $host;
                }
            }
            throw new \UnexpectedValueException(sprintf('Untrusted Host "%s"', $host));
        }
        return $host;
    }
    public function setMethod($method)
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', $method);
    }
    public function getMethod()
    {
        if (null === $this->method) {
            $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
            if ('POST' === $this->method) {
                if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) {
                    $this->method = strtoupper($method);
                } elseif (self::$httpMethodParameterOverride) {
                    $this->method = strtoupper($this->request->get('_method', $this->query->get('_method', 'POST')));
                }
            }
        }
        return $this->method;
    }
    public function getRealMethod()
    {
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }
    public function getMimeType($format)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }
        return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }
    public function getFormat($mimeType)
    {
        if (false !== ($pos = strpos($mimeType, ';'))) {
            $mimeType = substr($mimeType, 0, $pos);
        }
        if (null === static::$formats) {
            static::initializeFormats();
        }
        foreach (static::$formats as $format => $mimeTypes) {
            if (in_array($mimeType, (array) $mimeTypes)) {
                return $format;
            }
        }
    }
    public function setFormat($format, $mimeTypes)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }
        static::$formats[$format] = is_array($mimeTypes) ? $mimeTypes : array($mimeTypes);
    }
    public function getRequestFormat($default = 'html')
    {
        if (null === $this->format) {
            $this->format = $this->get('_format', $default);
        }
        return $this->format;
    }
    public function setRequestFormat($format)
    {
        $this->format = $format;
    }
    public function getContentType()
    {
        return $this->getFormat($this->headers->get('CONTENT_TYPE'));
    }
    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;
        if (null === $this->locale) {
            $this->setPhpDefaultLocale($locale);
        }
    }
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }
    public function setLocale($locale)
    {
        $this->setPhpDefaultLocale($this->locale = $locale);
    }
    public function getLocale()
    {
        return null === $this->locale ? $this->defaultLocale : $this->locale;
    }
    public function isMethod($method)
    {
        return $this->getMethod() === strtoupper($method);
    }
    public function isMethodSafe()
    {
        return in_array($this->getMethod(), array('GET', 'HEAD'));
    }
    public function getContent($asResource = false)
    {
        if (false === $this->content || true === $asResource && null !== $this->content) {
            throw new \LogicException('getContent() can only be called once when using the resource return type.');
        }
        if (true === $asResource) {
            $this->content = false;
            return fopen('php://input', 'rb');
        }
        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }
        return $this->content;
    }
    public function getETags()
    {
        return preg_split('/\\s*,\\s*/', $this->headers->get('if_none_match'), null, PREG_SPLIT_NO_EMPTY);
    }
    public function isNoCache()
    {
        return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
    }
    public function getPreferredLanguage(array $locales = null)
    {
        $preferredLanguages = $this->getLanguages();
        if (empty($locales)) {
            return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null;
        }
        if (!$preferredLanguages) {
            return $locales[0];
        }
        $extendedPreferredLanguages = array();
        foreach ($preferredLanguages as $language) {
            $extendedPreferredLanguages[] = $language;
            if (false !== ($position = strpos($language, '_'))) {
                $superLanguage = substr($language, 0, $position);
                if (!in_array($superLanguage, $preferredLanguages)) {
                    $extendedPreferredLanguages[] = $superLanguage;
                }
            }
        }
        $preferredLanguages = array_values(array_intersect($extendedPreferredLanguages, $locales));
        return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $locales[0];
    }
    public function getLanguages()
    {
        if (null !== $this->languages) {
            return $this->languages;
        }
        $languages = AcceptHeader::fromString($this->headers->get('Accept-Language'))->all();
        $this->languages = array();
        foreach (array_keys($languages) as $lang) {
            if (strstr($lang, '-')) {
                $codes = explode('-', $lang);
                if ($codes[0] == 'i') {
                    if (count($codes) > 1) {
                        $lang = $codes[1];
                    }
                } else {
                    for ($i = 0, $max = count($codes); $i < $max; $i++) {
                        if ($i == 0) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_' . strtoupper($codes[$i]);
                        }
                    }
                }
            }
            $this->languages[] = $lang;
        }
        return $this->languages;
    }
    public function getCharsets()
    {
        if (null !== $this->charsets) {
            return $this->charsets;
        }
        return $this->charsets = array_keys(AcceptHeader::fromString($this->headers->get('Accept-Charset'))->all());
    }
    public function getEncodings()
    {
        if (null !== $this->encodings) {
            return $this->encodings;
        }
        return $this->encodings = array_keys(AcceptHeader::fromString($this->headers->get('Accept-Encoding'))->all());
    }
    public function getAcceptableContentTypes()
    {
        if (null !== $this->acceptableContentTypes) {
            return $this->acceptableContentTypes;
        }
        return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->headers->get('Accept'))->all());
    }
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }
    protected function prepareRequestUri()
    {
        $requestUri = '';
        if ($this->headers->has('X_ORIGINAL_URL')) {
            $requestUri = $this->headers->get('X_ORIGINAL_URL');
            $this->headers->remove('X_ORIGINAL_URL');
            $this->server->remove('HTTP_X_ORIGINAL_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->headers->has('X_REWRITE_URL')) {
            $requestUri = $this->headers->get('X_REWRITE_URL');
            $this->headers->remove('X_REWRITE_URL');
        } elseif ($this->server->get('IIS_WasUrlRewritten') == '1' && $this->server->get('UNENCODED_URL') != '') {
            $requestUri = $this->server->get('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
            $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ('' != $this->server->get('QUERY_STRING')) {
                $requestUri .= '?' . $this->server->get('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }
        $this->server->set('REQUEST_URI', $requestUri);
        return $requestUri;
    }
    protected function prepareBaseUrl()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF')) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME');
        } else {
            $path = $this->server->get('PHP_SELF', '');
            $file = $this->server->get('SCRIPT_FILENAME', '');
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/' . $seg . $baseUrl;
                ++$index;
            } while ($last > $index && false !== ($pos = strpos($path, $baseUrl)) && 0 != $pos);
        }
        $requestUri = $this->getRequestUri();
        if ($baseUrl && false !== ($prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl))) {
            return $prefix;
        }
        if ($baseUrl && false !== ($prefix = $this->getUrlencodedPrefix($requestUri, dirname($baseUrl)))) {
            return rtrim($prefix, '/');
        }
        $truncatedRequestUri = $requestUri;
        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }
        $basename = basename($baseUrl);
        if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            return '';
        }
        if (strlen($requestUri) >= strlen($baseUrl) && false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }
        return rtrim($baseUrl, '/');
    }
    protected function prepareBasePath()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }
        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }
        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }
        return rtrim($basePath, '/');
    }
    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();
        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }
        $pathInfo = '/';
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        if (null !== $baseUrl && false === ($pathInfo = substr($requestUri, strlen($baseUrl)))) {
            return '/';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }
        return (string) $pathInfo;
    }
    protected static function initializeFormats()
    {
        static::$formats = array('html' => array('text/html', 'application/xhtml+xml'), 'txt' => array('text/plain'), 'js' => array('application/javascript', 'application/x-javascript', 'text/javascript'), 'css' => array('text/css'), 'json' => array('application/json', 'application/x-json'), 'xml' => array('text/xml', 'application/xml', 'application/x-xml'), 'rdf' => array('application/rdf+xml'), 'atom' => array('application/atom+xml'), 'rss' => array('application/rss+xml'));
    }
    private function setPhpDefaultLocale($locale)
    {
        try {
            if (class_exists('Locale', false)) {
                \Locale::setDefault($locale);
            }
        } catch (\Exception $e) {
            
        }
    }
    private function getUrlencodedPrefix($string, $prefix)
    {
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }
        $len = strlen($prefix);
        if (preg_match("#^(%[[:xdigit:]]{2}|.){{$len}}#", $string, $match)) {
            return $match[0];
        }
        return false;
    }
    private static function createRequestFromFactory(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        if (self::$requestFactory) {
            $request = call_user_func(self::$requestFactory, $query, $request, $attributes, $cookies, $files, $server, $content);
            if (!$request instanceof Request) {
                throw new \LogicException('The Request factory must return an instance of Symfony\\Component\\HttpFoundation\\Request.');
            }
            return $request;
        }
        return new static($query, $request, $attributes, $cookies, $files, $server, $content);
    }
}
namespace Symfony\Component\HttpFoundation;

class ParameterBag implements \IteratorAggregate, \Countable
{
    protected $parameters;
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }
    public function all()
    {
        return $this->parameters;
    }
    public function keys()
    {
        return array_keys($this->parameters);
    }
    public function replace(array $parameters = array())
    {
        $this->parameters = $parameters;
    }
    public function add(array $parameters = array())
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }
    public function get($path, $default = null, $deep = false)
    {
        if (!$deep || false === ($pos = strpos($path, '['))) {
            return array_key_exists($path, $this->parameters) ? $this->parameters[$path] : $default;
        }
        $root = substr($path, 0, $pos);
        if (!array_key_exists($root, $this->parameters)) {
            return $default;
        }
        $value = $this->parameters[$root];
        $currentKey = null;
        for ($i = $pos, $c = strlen($path); $i < $c; $i++) {
            $char = $path[$i];
            if ('[' === $char) {
                if (null !== $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i));
                }
                $currentKey = '';
            } elseif (']' === $char) {
                if (null === $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i));
                }
                if (!is_array($value) || !array_key_exists($currentKey, $value)) {
                    return $default;
                }
                $value = $value[$currentKey];
                $currentKey = null;
            } else {
                if (null === $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i));
                }
                $currentKey .= $char;
            }
        }
        if (null !== $currentKey) {
            throw new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".'));
        }
        return $value;
    }
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }
    public function remove($key)
    {
        unset($this->parameters[$key]);
    }
    public function getAlpha($key, $default = '', $deep = false)
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default, $deep));
    }
    public function getAlnum($key, $default = '', $deep = false)
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default, $deep));
    }
    public function getDigits($key, $default = '', $deep = false)
    {
        return str_replace(array('-', '+'), '', $this->filter($key, $default, $deep, FILTER_SANITIZE_NUMBER_INT));
    }
    public function getInt($key, $default = 0, $deep = false)
    {
        return (int) $this->get($key, $default, $deep);
    }
    public function filter($key, $default = null, $deep = false, $filter = FILTER_DEFAULT, $options = array())
    {
        $value = $this->get($key, $default, $deep);
        if (!is_array($options) && $options) {
            $options = array('flags' => $options);
        }
        if (is_array($value) && !isset($options['flags'])) {
            $options['flags'] = FILTER_REQUIRE_ARRAY;
        }
        return filter_var($value, $filter, $options);
    }
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }
    public function count()
    {
        return count($this->parameters);
    }
}
namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\File\UploadedFile;
class FileBag extends ParameterBag
{
    private static $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
    public function __construct(array $parameters = array())
    {
        $this->replace($parameters);
    }
    public function replace(array $files = array())
    {
        $this->parameters = array();
        $this->add($files);
    }
    public function set($key, $value)
    {
        if (!is_array($value) && !$value instanceof UploadedFile) {
            throw new \InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
        }
        parent::set($key, $this->convertFileInformation($value));
    }
    public function add(array $files = array())
    {
        foreach ($files as $key => $file) {
            $this->set($key, $file);
        }
    }
    protected function convertFileInformation($file)
    {
        if ($file instanceof UploadedFile) {
            return $file;
        }
        $file = $this->fixPhpFilesArray($file);
        if (is_array($file)) {
            $keys = array_keys($file);
            sort($keys);
            if ($keys == self::$fileKeys) {
                if (UPLOAD_ERR_NO_FILE == $file['error']) {
                    $file = null;
                } else {
                    $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
                }
            } else {
                $file = array_map(array($this, 'convertFileInformation'), $file);
            }
        }
        return $file;
    }
    protected function fixPhpFilesArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        $keys = array_keys($data);
        sort($keys);
        if (self::$fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
            return $data;
        }
        $files = $data;
        foreach (self::$fileKeys as $k) {
            unset($files[$k]);
        }
        foreach (array_keys($data['name']) as $key) {
            $files[$key] = $this->fixPhpFilesArray(array('error' => $data['error'][$key], 'name' => $data['name'][$key], 'type' => $data['type'][$key], 'tmp_name' => $data['tmp_name'][$key], 'size' => $data['size'][$key]));
        }
        return $files;
    }
}
namespace Symfony\Component\HttpFoundation;

class ServerBag extends ParameterBag
{
    public function getHeaders()
    {
        $headers = array();
        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
        foreach ($this->parameters as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }
        if (isset($this->parameters['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $this->parameters['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = isset($this->parameters['PHP_AUTH_PW']) ? $this->parameters['PHP_AUTH_PW'] : '';
        } else {
            $authorizationHeader = null;
            if (isset($this->parameters['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['HTTP_AUTHORIZATION'];
            } elseif (isset($this->parameters['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['REDIRECT_HTTP_AUTHORIZATION'];
            }
            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (count($exploded) == 2) {
                        list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                    }
                } elseif (empty($this->parameters['PHP_AUTH_DIGEST']) && 0 === stripos($authorizationHeader, 'digest ')) {
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $this->parameters['PHP_AUTH_DIGEST'] = $authorizationHeader;
                }
            }
        }
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }
        return $headers;
    }
}
namespace Symfony\Component\HttpFoundation;

class HeaderBag implements \IteratorAggregate, \Countable
{
    protected $headers = array();
    protected $cacheControl = array();
    public function __construct(array $headers = array())
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }
    public function __toString()
    {
        if (!$this->headers) {
            return '';
        }
        $max = max(array_map('strlen', array_keys($this->headers))) + 1;
        $content = '';
        ksort($this->headers);
        foreach ($this->headers as $name => $values) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            foreach ($values as $value) {
                $content .= sprintf("%-{$max}s %s\r\n", $name . ':', $value);
            }
        }
        return $content;
    }
    public function all()
    {
        return $this->headers;
    }
    public function keys()
    {
        return array_keys($this->headers);
    }
    public function replace(array $headers = array())
    {
        $this->headers = array();
        $this->add($headers);
    }
    public function add(array $headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }
    public function get($key, $default = null, $first = true)
    {
        $key = strtr(strtolower($key), '_', '-');
        if (!array_key_exists($key, $this->headers)) {
            if (null === $default) {
                return $first ? null : array();
            }
            return $first ? $default : array($default);
        }
        if ($first) {
            return count($this->headers[$key]) ? $this->headers[$key][0] : $default;
        }
        return $this->headers[$key];
    }
    public function set($key, $values, $replace = true)
    {
        $key = strtr(strtolower($key), '_', '-');
        $values = array_values((array) $values);
        if (true === $replace || !isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }
        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl($values[0]);
        }
    }
    public function has($key)
    {
        return array_key_exists(strtr(strtolower($key), '_', '-'), $this->headers);
    }
    public function contains($key, $value)
    {
        return in_array($value, $this->get($key, null, false));
    }
    public function remove($key)
    {
        $key = strtr(strtolower($key), '_', '-');
        unset($this->headers[$key]);
        if ('cache-control' === $key) {
            $this->cacheControl = array();
        }
    }
    public function getDate($key, \DateTime $default = null)
    {
        if (null === ($value = $this->get($key))) {
            return $default;
        }
        if (false === ($date = \DateTime::createFromFormat(DATE_RFC2822, $value))) {
            throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
        }
        return $date;
    }
    public function addCacheControlDirective($key, $value = true)
    {
        $this->cacheControl[$key] = $value;
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl);
    }
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
    }
    public function removeCacheControlDirective($key)
    {
        unset($this->cacheControl[$key]);
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }
    public function getIterator()
    {
        return new \ArrayIterator($this->headers);
    }
    public function count()
    {
        return count($this->headers);
    }
    protected function getCacheControlHeader()
    {
        $parts = array();
        ksort($this->cacheControl);
        foreach ($this->cacheControl as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                    $value = '"' . $value . '"';
                }
                $parts[] = "{$key}={$value}";
            }
        }
        return implode(', ', $parts);
    }
    protected function parseCacheControl($header)
    {
        $cacheControl = array();
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\\s*(?:=(?:"([^"]*)"|([^ \\t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $cacheControl[strtolower($match[1])] = isset($match[3]) ? $match[3] : (isset($match[2]) ? $match[2] : true);
        }
        return $cacheControl;
    }
}
namespace Symfony\Component\HttpFoundation\Session;

use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
interface SessionInterface
{
    public function start();
    public function getId();
    public function setId($id);
    public function getName();
    public function setName($name);
    public function invalidate($lifetime = null);
    public function migrate($destroy = false, $lifetime = null);
    public function save();
    public function has($name);
    public function get($name, $default = null);
    public function set($name, $value);
    public function all();
    public function replace(array $attributes);
    public function remove($name);
    public function clear();
    public function isStarted();
    public function registerBag(SessionBagInterface $bag);
    public function getBag($name);
    public function getMetadataBag();
}
namespace Symfony\Component\HttpFoundation\Session;

interface SessionBagInterface
{
    public function getName();
    public function initialize(array &$array);
    public function getStorageKey();
    public function clear();
}
namespace Symfony\Component\HttpFoundation\Session\Attribute;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
interface AttributeBagInterface extends SessionBagInterface
{
    public function has($name);
    public function get($name, $default = null);
    public function set($name, $value);
    public function all();
    public function replace(array $attributes);
    public function remove($name);
}
namespace Symfony\Component\HttpFoundation\Session\Attribute;

class AttributeBag implements AttributeBagInterface, \IteratorAggregate, \Countable
{
    private $name = 'attributes';
    private $storageKey;
    protected $attributes = array();
    public function __construct($storageKey = '_sf2_attributes')
    {
        $this->storageKey = $storageKey;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function initialize(array &$attributes)
    {
        $this->attributes =& $attributes;
    }
    public function getStorageKey()
    {
        return $this->storageKey;
    }
    public function has($name)
    {
        return array_key_exists($name, $this->attributes);
    }
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }
    public function set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
    public function all()
    {
        return $this->attributes;
    }
    public function replace(array $attributes)
    {
        $this->attributes = array();
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }
    public function remove($name)
    {
        $retval = null;
        if (array_key_exists($name, $this->attributes)) {
            $retval = $this->attributes[$name];
            unset($this->attributes[$name]);
        }
        return $retval;
    }
    public function clear()
    {
        $return = $this->attributes;
        $this->attributes = array();
        return $return;
    }
    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }
    public function count()
    {
        return count($this->attributes);
    }
}
namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
class MetadataBag implements SessionBagInterface
{
    const CREATED = 'c';
    const UPDATED = 'u';
    const LIFETIME = 'l';
    private $name = '__metadata';
    private $storageKey;
    protected $meta = array(self::CREATED => 0, self::UPDATED => 0, self::LIFETIME => 0);
    private $lastUsed;
    private $updateThreshold;
    public function __construct($storageKey = '_sf2_meta', $updateThreshold = 0)
    {
        $this->storageKey = $storageKey;
        $this->updateThreshold = $updateThreshold;
    }
    public function initialize(array &$array)
    {
        $this->meta =& $array;
        if (isset($array[self::CREATED])) {
            $this->lastUsed = $this->meta[self::UPDATED];
            $timeStamp = time();
            if ($timeStamp - $array[self::UPDATED] >= $this->updateThreshold) {
                $this->meta[self::UPDATED] = $timeStamp;
            }
        } else {
            $this->stampCreated();
        }
    }
    public function getLifetime()
    {
        return $this->meta[self::LIFETIME];
    }
    public function stampNew($lifetime = null)
    {
        $this->stampCreated($lifetime);
    }
    public function getStorageKey()
    {
        return $this->storageKey;
    }
    public function getCreated()
    {
        return $this->meta[self::CREATED];
    }
    public function getLastUsed()
    {
        return $this->lastUsed;
    }
    public function clear()
    {
        
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    private function stampCreated($lifetime = null)
    {
        $timeStamp = time();
        $this->meta[self::CREATED] = $this->meta[self::UPDATED] = $this->lastUsed = $timeStamp;
        $this->meta[self::LIFETIME] = null === $lifetime ? ini_get('session.cookie_lifetime') : $lifetime;
    }
}
namespace Symfony\Component\HttpFoundation;

class AcceptHeaderItem
{
    private $value;
    private $quality = 1.0;
    private $index = 0;
    private $attributes = array();
    public function __construct($value, array $attributes = array())
    {
        $this->value = $value;
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }
    public static function fromString($itemValue)
    {
        $bits = preg_split('/\\s*(?:;*("[^"]+");*|;*(\'[^\']+\');*|;+)\\s*/', $itemValue, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $value = array_shift($bits);
        $attributes = array();
        $lastNullAttribute = null;
        foreach ($bits as $bit) {
            if (($start = substr($bit, 0, 1)) === ($end = substr($bit, -1)) && ($start === '"' || $start === '\'')) {
                $attributes[$lastNullAttribute] = substr($bit, 1, -1);
            } elseif ('=' === $end) {
                $lastNullAttribute = $bit = substr($bit, 0, -1);
                $attributes[$bit] = null;
            } else {
                $parts = explode('=', $bit);
                $attributes[$parts[0]] = isset($parts[1]) && strlen($parts[1]) > 0 ? $parts[1] : '';
            }
        }
        return new self(($start = substr($value, 0, 1)) === ($end = substr($value, -1)) && ($start === '"' || $start === '\'') ? substr($value, 1, -1) : $value, $attributes);
    }
    public function __toString()
    {
        $string = $this->value . ($this->quality < 1 ? ';q=' . $this->quality : '');
        if (count($this->attributes) > 0) {
            $string .= ';' . implode(';', array_map(function ($name, $value) {
                return sprintf(preg_match('/[,;=]/', $value) ? '%s="%s"' : '%s=%s', $name, $value);
            }, array_keys($this->attributes), $this->attributes));
        }
        return $string;
    }
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }
    public function getQuality()
    {
        return $this->quality;
    }
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }
    public function getIndex()
    {
        return $this->index;
    }
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }
    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttribute($name, $value)
    {
        if ('q' === $name) {
            $this->quality = (double) $value;
        } else {
            $this->attributes[$name] = (string) $value;
        }
        return $this;
    }
}
namespace Symfony\Component\HttpFoundation;

class AcceptHeader
{
    private $items = array();
    private $sorted = true;
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }
    public static function fromString($headerValue)
    {
        $index = 0;
        return new self(array_map(function ($itemValue) use(&$index) {
            $item = AcceptHeaderItem::fromString($itemValue);
            $item->setIndex($index++);
            return $item;
        }, preg_split('/\\s*(?:,*("[^"]+"),*|,*(\'[^\']+\'),*|,+)\\s*/', $headerValue, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)));
    }
    public function __toString()
    {
        return implode(',', $this->items);
    }
    public function has($value)
    {
        return isset($this->items[$value]);
    }
    public function get($value)
    {
        return isset($this->items[$value]) ? $this->items[$value] : null;
    }
    public function add(AcceptHeaderItem $item)
    {
        $this->items[$item->getValue()] = $item;
        $this->sorted = false;
        return $this;
    }
    public function all()
    {
        $this->sort();
        return $this->items;
    }
    public function filter($pattern)
    {
        return new self(array_filter($this->items, function (AcceptHeaderItem $item) use($pattern) {
            return preg_match($pattern, $item->getValue());
        }));
    }
    public function first()
    {
        $this->sort();
        return !empty($this->items) ? reset($this->items) : null;
    }
    private function sort()
    {
        if (!$this->sorted) {
            uasort($this->items, function ($a, $b) {
                $qA = $a->getQuality();
                $qB = $b->getQuality();
                if ($qA === $qB) {
                    return $a->getIndex() > $b->getIndex() ? 1 : -1;
                }
                return $qA > $qB ? -1 : 1;
            });
            $this->sorted = true;
        }
    }
}
namespace Symfony\Component\Debug;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\Exception\OutOfMemoryException;
if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}
class ExceptionHandler
{
    private $debug;
    private $charset;
    private $handler;
    private $caughtBuffer;
    private $caughtLength;
    public function __construct($debug = true, $charset = 'UTF-8')
    {
        $this->debug = $debug;
        $this->charset = $charset;
    }
    public static function register($debug = true)
    {
        $handler = new static($debug);
        set_exception_handler(array($handler, 'handle'));
        return $handler;
    }
    public function setHandler($handler)
    {
        if (null !== $handler && !is_callable($handler)) {
            throw new \LogicException('The exception handler must be a valid PHP callable.');
        }
        $old = $this->handler;
        $this->handler = $handler;
        return $old;
    }
    public function handle(\Exception $exception)
    {
        if (null === $this->handler || $exception instanceof OutOfMemoryException) {
            $this->failSafeHandle($exception);
            return;
        }
        $caughtLength = $this->caughtLength = 0;
        ob_start(array($this, 'catchOutput'));
        $this->failSafeHandle($exception);
        while (null === $this->caughtBuffer && ob_end_flush()) {
            
        }
        if (isset($this->caughtBuffer[0])) {
            ob_start(array($this, 'cleanOutput'));
            echo $this->caughtBuffer;
            $caughtLength = ob_get_length();
        }
        $this->caughtBuffer = null;
        try {
            call_user_func($this->handler, $exception);
            $this->caughtLength = $caughtLength;
        } catch (\Exception $e) {
            if (!$caughtLength) {
                throw $exception;
            }
        }
    }
    private function failSafeHandle(\Exception $exception)
    {
        if (class_exists('Symfony\\Component\\HttpFoundation\\Response', false)) {
            $response = $this->createResponse($exception);
            $response->sendHeaders();
            $response->sendContent();
        } else {
            $this->sendPhpResponse($exception);
        }
    }
    public function sendPhpResponse($exception)
    {
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }
        if (!headers_sent()) {
            header(sprintf('HTTP/1.0 %s', $exception->getStatusCode()));
            foreach ($exception->getHeaders() as $name => $value) {
                header($name . ': ' . $value, false);
            }
        }
        echo $this->decorate($this->getContent($exception), $this->getStylesheet($exception));
    }
    public function createResponse($exception)
    {
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }
        return new Response($this->decorate($this->getContent($exception), $this->getStylesheet($exception)), $exception->getStatusCode(), $exception->getHeaders());
    }
    public function getContent(FlattenException $exception)
    {
        switch ($exception->getStatusCode()) {
            case 404:
                $title = 'Sorry, the page you are looking for could not be found.';
                break;
            default:
                $title = 'Whoops, looks like something went wrong.';
        }
        $content = '';
        if ($this->debug) {
            try {
                $count = count($exception->getAllPrevious());
                $total = $count + 1;
                foreach ($exception->toArray() as $position => $e) {
                    $ind = $count - $position + 1;
                    $class = $this->abbrClass($e['class']);
                    $message = nl2br($e['message']);
                    $content .= sprintf('                        <div class="block_exception clear_fix">
                            <h2><span>%d/%d</span> %s: %s</h2>
                        </div>
                        <div class="block">
                            <ol class="traces list_exception">', $ind, $total, $class, $message);
                    foreach ($e['trace'] as $trace) {
                        $content .= '       <li>';
                        if ($trace['function']) {
                            $content .= sprintf('at %s%s%s(%s)', $this->abbrClass($trace['class']), $trace['type'], $trace['function'], $this->formatArgs($trace['args']));
                        }
                        if (isset($trace['file']) && isset($trace['line'])) {
                            if ($linkFormat = ini_get('xdebug.file_link_format')) {
                                $link = str_replace(array('%f', '%l'), array($trace['file'], $trace['line']), $linkFormat);
                                $content .= sprintf(' in <a href="%s" title="Go to source">%s line %s</a>', $link, $trace['file'], $trace['line']);
                            } else {
                                $content .= sprintf(' in %s line %s', $trace['file'], $trace['line']);
                            }
                        }
                        $content .= '</li>
';
                    }
                    $content .= '    </ol>
</div>
';
                }
            } catch (\Exception $e) {
                if ($this->debug) {
                    $title = sprintf('Exception thrown when handling an exception (%s: %s)', get_class($exception), $exception->getMessage());
                } else {
                    $title = 'Whoops, looks like something went wrong.';
                }
            }
        }
        return "            <div id=\"sf-resetcontent\" class=\"sf-reset\">\n                <h1>{$title}</h1>\n                {$content}\n            </div>";
    }
    public function getStylesheet(FlattenException $exception)
    {
        return '            .sf-reset { font: 11px Verdana, Arial, sans-serif; color: #333 }
            .sf-reset .clear { clear:both; height:0; font-size:0; line-height:0; }
            .sf-reset .clear_fix:after { display:block; height:0; clear:both; visibility:hidden; }
            .sf-reset .clear_fix { display:inline-block; }
            .sf-reset * html .clear_fix { height:1%; }
            .sf-reset .clear_fix { display:block; }
            .sf-reset, .sf-reset .block { margin: auto }
            .sf-reset abbr { border-bottom: 1px dotted #000; cursor: help; }
            .sf-reset p { font-size:14px; line-height:20px; color:#868686; padding-bottom:20px }
            .sf-reset strong { font-weight:bold; }
            .sf-reset a { color:#6c6159; }
            .sf-reset a img { border:none; }
            .sf-reset a:hover { text-decoration:underline; }
            .sf-reset em { font-style:italic; }
            .sf-reset h1, .sf-reset h2 { font: 20px Georgia, "Times New Roman", Times, serif }
            .sf-reset h2 span { background-color: #fff; color: #333; padding: 6px; float: left; margin-right: 10px; }
            .sf-reset .traces li { font-size:12px; padding: 2px 4px; list-style-type:decimal; margin-left:20px; }
            .sf-reset .block { background-color:#FFFFFF; padding:10px 28px; margin-bottom:20px;
                -webkit-border-bottom-right-radius: 16px;
                -webkit-border-bottom-left-radius: 16px;
                -moz-border-radius-bottomright: 16px;
                -moz-border-radius-bottomleft: 16px;
                border-bottom-right-radius: 16px;
                border-bottom-left-radius: 16px;
                border-bottom:1px solid #ccc;
                border-right:1px solid #ccc;
                border-left:1px solid #ccc;
            }
            .sf-reset .block_exception { background-color:#ddd; color: #333; padding:20px;
                -webkit-border-top-left-radius: 16px;
                -webkit-border-top-right-radius: 16px;
                -moz-border-radius-topleft: 16px;
                -moz-border-radius-topright: 16px;
                border-top-left-radius: 16px;
                border-top-right-radius: 16px;
                border-top:1px solid #ccc;
                border-right:1px solid #ccc;
                border-left:1px solid #ccc;
                overflow: hidden;
                word-wrap: break-word;
            }
            .sf-reset li a { background:none; color:#868686; text-decoration:none; }
            .sf-reset li a:hover { background:none; color:#313131; text-decoration:underline; }
            .sf-reset ol { padding: 10px 0; }
            .sf-reset h1 { background-color:#FFFFFF; padding: 15px 28px; margin-bottom: 20px;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
                border: 1px solid #ccc;
            }';
    }
    private function decorate($content, $css)
    {
        return "<!DOCTYPE html>\n<html>\n    <head>\n        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>\n        <meta name=\"robots\" content=\"noindex,nofollow\" />\n        <style>\n            /* Copyright (c) 2010, Yahoo! Inc. All rights reserved. Code licensed under the BSD License: http://developer.yahoo.com/yui/license.html */\n            html{color:#000;background:#FFF;}body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,textarea,p,blockquote,th,td{margin:0;padding:0;}table{border-collapse:collapse;border-spacing:0;}fieldset,img{border:0;}address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:normal;}li{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:normal;}q:before,q:after{content:'';}abbr,acronym{border:0;font-variant:normal;}sup{vertical-align:text-top;}sub{vertical-align:text-bottom;}input,textarea,select{font-family:inherit;font-size:inherit;font-weight:inherit;}input,textarea,select{*font-size:100%;}legend{color:#000;}\n\n            html { background: #eee; padding: 10px }\n            img { border: 0; }\n            #sf-resetcontent { width:970px; margin:0 auto; }\n            {$css}\n        </style>\n    </head>\n    <body>\n        {$content}\n    </body>\n</html>";
    }
    private function abbrClass($class)
    {
        $parts = explode('\\', $class);
        return sprintf('<abbr title="%s">%s</abbr>', $class, array_pop($parts));
    }
    private function formatArgs(array $args)
    {
        $result = array();
        foreach ($args as $key => $item) {
            if ('object' === $item[0]) {
                $formattedValue = sprintf('<em>object</em>(%s)', $this->abbrClass($item[1]));
            } elseif ('array' === $item[0]) {
                $formattedValue = sprintf('<em>array</em>(%s)', is_array($item[1]) ? $this->formatArgs($item[1]) : $item[1]);
            } elseif ('string' === $item[0]) {
                $formattedValue = sprintf('\'%s\'', htmlspecialchars($item[1], ENT_QUOTES | ENT_SUBSTITUTE, $this->charset));
            } elseif ('null' === $item[0]) {
                $formattedValue = '<em>null</em>';
            } elseif ('boolean' === $item[0]) {
                $formattedValue = '<em>' . strtolower(var_export($item[1], true)) . '</em>';
            } elseif ('resource' === $item[0]) {
                $formattedValue = '<em>resource</em>';
            } else {
                $formattedValue = str_replace('
', '', var_export(htmlspecialchars((string) $item[1], ENT_QUOTES | ENT_SUBSTITUTE, $this->charset), true));
            }
            $result[] = is_int($key) ? $formattedValue : sprintf('\'%s\' => %s', $key, $formattedValue);
        }
        return implode(', ', $result);
    }
    public function catchOutput($buffer)
    {
        $this->caughtBuffer = $buffer;
        return '';
    }
    public function cleanOutput($buffer)
    {
        if ($this->caughtLength) {
            $cleanBuffer = substr_replace($buffer, '', 0, $this->caughtLength);
            if (isset($cleanBuffer[0])) {
                $buffer = $cleanBuffer;
            }
        }
        return $buffer;
    }
}
namespace Illuminate\Support;

use ReflectionClass;
abstract class ServiceProvider
{
    protected $app;
    protected $defer = false;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function boot()
    {
        
    }
    public abstract function register();
    public function package($package, $namespace = null, $path = null)
    {
        $namespace = $this->getPackageNamespace($package, $namespace);
        $path = $path ?: $this->guessPackagePath();
        $config = $path . '/config';
        if ($this->app['files']->isDirectory($config)) {
            $this->app['config']->package($package, $config, $namespace);
        }
        $lang = $path . '/lang';
        if ($this->app['files']->isDirectory($lang)) {
            $this->app['translator']->addNamespace($namespace, $lang);
        }
        $appView = $this->getAppViewPath($package);
        if ($this->app['files']->isDirectory($appView)) {
            $this->app['view']->addNamespace($namespace, $appView);
        }
        $view = $path . '/views';
        if ($this->app['files']->isDirectory($view)) {
            $this->app['view']->addNamespace($namespace, $view);
        }
    }
    public function guessPackagePath()
    {
        $path = (new ReflectionClass($this))->getFileName();
        return realpath(dirname($path) . '/../../');
    }
    protected function getPackageNamespace($package, $namespace)
    {
        if (is_null($namespace)) {
            list($vendor, $namespace) = explode('/', $package);
        }
        return $namespace;
    }
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();
        $events = $this->app['events'];
        $events->listen('artisan.start', function ($artisan) use($commands) {
            $artisan->resolveCommands($commands);
        });
    }
    protected function getAppViewPath($package)
    {
        return $this->app['path'] . "/views/packages/{$package}";
    }
    public function provides()
    {
        return array();
    }
    public function when()
    {
        return array();
    }
    public function isDeferred()
    {
        return $this->defer;
    }
}
namespace Illuminate\Exception;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Illuminate\Support\ServiceProvider;
class ExceptionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerDisplayers();
        $this->registerHandler();
    }
    protected function registerDisplayers()
    {
        $this->registerPlainDisplayer();
        $this->registerDebugDisplayer();
    }
    protected function registerHandler()
    {
        $this->app['exception'] = $this->app->share(function ($app) {
            return new Handler($app, $app['exception.plain'], $app['exception.debug']);
        });
    }
    protected function registerPlainDisplayer()
    {
        $this->app['exception.plain'] = $this->app->share(function ($app) {
            if ($app->runningInConsole()) {
                return $app['exception.debug'];
            } else {
                return new PlainDisplayer();
            }
        });
    }
    protected function registerDebugDisplayer()
    {
        $this->registerWhoops();
        $this->app['exception.debug'] = $this->app->share(function ($app) {
            return new WhoopsDisplayer($app['whoops'], $app->runningInConsole());
        });
    }
    protected function registerWhoops()
    {
        $this->registerWhoopsHandler();
        $this->app['whoops'] = $this->app->share(function ($app) {
            with($whoops = new Run())->allowQuit(false);
            $whoops->writeToOutput(false);
            return $whoops->pushHandler($app['whoops.handler']);
        });
    }
    protected function registerWhoopsHandler()
    {
        if ($this->shouldReturnJson()) {
            $this->app['whoops.handler'] = $this->app->share(function () {
                return new JsonResponseHandler();
            });
        } else {
            $this->registerPrettyWhoopsHandler();
        }
    }
    protected function shouldReturnJson()
    {
        return $this->app->runningInConsole() || $this->requestWantsJson();
    }
    protected function requestWantsJson()
    {
        return $this->app['request']->ajax() || $this->app['request']->wantsJson();
    }
    protected function registerPrettyWhoopsHandler()
    {
        $this->app['whoops.handler'] = $this->app->share(function () {
            with($handler = new PrettyPageHandler())->setEditor('sublime');
            return $handler;
        });
    }
}
namespace Illuminate\Routing;

use Illuminate\Support\ServiceProvider;
class RoutingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerRouter();
        $this->registerUrlGenerator();
        $this->registerRedirector();
    }
    protected function registerRouter()
    {
        $this->app['router'] = $this->app->share(function ($app) {
            $router = new Router($app['events'], $app);
            if ($app['env'] == 'testing') {
                $router->disableFilters();
            }
            return $router;
        });
    }
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function ($app) {
            $routes = $app['router']->getRoutes();
            return new UrlGenerator($routes, $app->rebinding('request', function ($app, $request) {
                $app['url']->setRequest($request);
            }));
        });
    }
    protected function registerRedirector()
    {
        $this->app['redirect'] = $this->app->share(function ($app) {
            $redirector = new Redirector($app['url']);
            if (isset($app['session.store'])) {
                $redirector->setSession($app['session.store']);
            }
            return $redirector;
        });
    }
}
namespace Illuminate\Events;

use Illuminate\Support\ServiceProvider;
class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['events'] = $this->app->share(function ($app) {
            return new Dispatcher($app);
        });
    }
}
namespace Illuminate\Support\Facades;

use Mockery\MockInterface;
abstract class Facade
{
    protected static $app;
    protected static $resolvedInstance;
    public static function swap($instance)
    {
        static::$resolvedInstance[static::getFacadeAccessor()] = $instance;
        static::$app->instance(static::getFacadeAccessor(), $instance);
    }
    public static function shouldReceive()
    {
        $name = static::getFacadeAccessor();
        if (static::isMock()) {
            $mock = static::$resolvedInstance[$name];
        } else {
            $mock = static::createFreshMockInstance($name);
        }
        return call_user_func_array(array($mock, 'shouldReceive'), func_get_args());
    }
    protected static function createFreshMockInstance($name)
    {
        static::$resolvedInstance[$name] = $mock = static::createMockByName($name);
        if (isset(static::$app)) {
            static::$app->instance($name, $mock);
        }
        return $mock;
    }
    protected static function createMockByName($name)
    {
        $class = static::getMockableClass($name);
        return $class ? \Mockery::mock($class) : \Mockery::mock();
    }
    protected static function isMock()
    {
        $name = static::getFacadeAccessor();
        return isset(static::$resolvedInstance[$name]) && static::$resolvedInstance[$name] instanceof MockInterface;
    }
    protected static function getMockableClass()
    {
        if ($root = static::getFacadeRoot()) {
            return get_class($root);
        }
    }
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
    }
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }
        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }
        return static::$resolvedInstance[$name] = static::$app[$name];
    }
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstance[$name]);
    }
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = array();
    }
    public static function getFacadeApplication()
    {
        return static::$app;
    }
    public static function setFacadeApplication($app)
    {
        static::$app = $app;
    }
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();
        switch (count($args)) {
            case 0:
                return $instance->{$method}();
            case 1:
                return $instance->{$method}($args[0]);
            case 2:
                return $instance->{$method}($args[0], $args[1]);
            case 3:
                return $instance->{$method}($args[0], $args[1], $args[2]);
            case 4:
                return $instance->{$method}($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }
}
namespace Illuminate\Support\Traits;

trait MacroableTrait
{
    protected static $macros = array();
    public static function macro($name, callable $macro)
    {
        static::$macros[$name] = $macro;
    }
    public static function hasMacro($name)
    {
        return isset(static::$macros[$name]);
    }
    public static function __callStatic($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return call_user_func_array(static::$macros[$method], $parameters);
        }
        throw new \BadMethodCallException("Method {$method} does not exist.");
    }
    public function __call($method, $parameters)
    {
        return static::__callStatic($method, $parameters);
    }
}
namespace Illuminate\Support;

use Closure;
use Illuminate\Support\Traits\MacroableTrait;
class Arr
{
    use MacroableTrait;
    public static function add($array, $key, $value)
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }
        return $array;
    }
    public static function build($array, Closure $callback)
    {
        $results = array();
        foreach ($array as $key => $value) {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);
            $results[$innerKey] = $innerValue;
        }
        return $results;
    }
    public static function divide($array)
    {
        return array(array_keys($array), array_values($array));
    }
    public static function dot($array, $prepend = '')
    {
        $results = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }
    public static function except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }
    public static function fetch($array, $key)
    {
        foreach (explode('.', $key) as $segment) {
            $results = array();
            foreach ($array as $value) {
                $value = (array) $value;
                $results[] = $value[$segment];
            }
            $array = array_values($results);
        }
        return array_values($results);
    }
    public static function first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }
        return value($default);
    }
    public static function last($array, $callback, $default = null)
    {
        return static::first(array_reverse($array), $callback, $default);
    }
    public static function flatten($array)
    {
        $return = array();
        array_walk_recursive($array, function ($x) use(&$return) {
            $return[] = $x;
        });
        return $return;
    }
    public static function forget(&$array, $keys)
    {
        foreach ((array) $keys as $key) {
            $parts = explode('.', $key);
            while (count($parts) > 1) {
                $part = array_shift($parts);
                if (isset($array[$part]) && is_array($array[$part])) {
                    $array =& $array[$part];
                }
            }
            unset($array[array_shift($parts)]);
        }
    }
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }
            $array = $array[$segment];
        }
        return $array;
    }
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
    public static function pluck($array, $value, $key = null)
    {
        $results = array();
        foreach ($array as $item) {
            $itemValue = is_object($item) ? $item->{$value} : $item[$value];
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = is_object($item) ? $item->{$key} : $item[$key];
                $results[$itemKey] = $itemValue;
            }
        }
        return $results;
    }
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);
        static::forget($array, $key);
        return $value;
    }
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }
            $array =& $array[$key];
        }
        $array[array_shift($keys)] = $value;
        return $array;
    }
    public static function sort($array, Closure $callback)
    {
        return Collection::make($array)->sortBy($callback)->all();
    }
    public static function where($array, Closure $callback)
    {
        $filtered = array();
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }
}
namespace Illuminate\Support;

use Illuminate\Support\Traits\MacroableTrait;
class Str
{
    use MacroableTrait;
    public static function ascii($value)
    {
        return \Patchwork\Utf8::toAscii($value);
    }
    public static function camel($value)
    {
        return lcfirst(static::studly($value));
    }
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }
        return false;
    }
    public static function finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');
        return preg_replace('/(?:' . $quoted . ')+$/', '', $value) . $cap;
    }
    public static function is($pattern, $value)
    {
        if ($pattern == $value) {
            return true;
        }
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\\*', '.*', $pattern) . '\\z';
        return (bool) preg_match('#^' . $pattern . '#', $value);
    }
    public static function length($value)
    {
        return mb_strlen($value);
    }
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }
        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }
    public static function lower($value)
    {
        return mb_strtolower($value);
    }
    public static function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\\s*+(?:\\S++\\s*+){1,' . $words . '}/u', $value, $matches);
        if (!isset($matches[0])) {
            return $value;
        }
        if (strlen($value) == strlen($matches[0])) {
            return $value;
        }
        return rtrim($matches[0]) . $end;
    }
    public static function parseCallback($callback, $default)
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : array($callback, $default);
    }
    public static function plural($value, $count = 2)
    {
        return Pluralizer::plural($value, $count);
    }
    public static function random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);
            if ($bytes === false) {
                throw new \RuntimeException('Unable to generate random string.');
            }
            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
        }
        return static::quickRandom($length);
    }
    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
    public static function upper($value)
    {
        return mb_strtoupper($value);
    }
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
    public static function singular($value)
    {
        return Pluralizer::singular($value);
    }
    public static function slug($title, $separator = '-')
    {
        $title = static::ascii($title);
        $flip = $separator == '-' ? '_' : '-';
        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);
        $title = preg_replace('![^' . preg_quote($separator) . '\\pL\\pN\\s]+!u', '', mb_strtolower($title));
        $title = preg_replace('![' . preg_quote($separator) . '\\s]+!u', $separator, $title);
        return trim($title, $separator);
    }
    public static function snake($value, $delimiter = '_')
    {
        $replace = '$1' . $delimiter . '$2';
        return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
    }
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }
        return false;
    }
    public static function studly($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));
        return str_replace(' ', '', $value);
    }
}
namespace Symfony\Component\Debug;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\OutOfMemoryException;
use Symfony\Component\Debug\FatalErrorHandler\UndefinedFunctionFatalErrorHandler;
use Symfony\Component\Debug\FatalErrorHandler\UndefinedMethodFatalErrorHandler;
use Symfony\Component\Debug\FatalErrorHandler\ClassNotFoundFatalErrorHandler;
use Symfony\Component\Debug\FatalErrorHandler\FatalErrorHandlerInterface;
class ErrorHandler
{
    const TYPE_DEPRECATION = -100;
    private $levels = array(E_WARNING => 'Warning', E_NOTICE => 'Notice', E_USER_ERROR => 'User Error', E_USER_WARNING => 'User Warning', E_USER_NOTICE => 'User Notice', E_STRICT => 'Runtime Notice', E_RECOVERABLE_ERROR => 'Catchable Fatal Error', E_DEPRECATED => 'Deprecated', E_USER_DEPRECATED => 'User Deprecated', E_ERROR => 'Error', E_CORE_ERROR => 'Core Error', E_COMPILE_ERROR => 'Compile Error', E_PARSE => 'Parse Error');
    private $level;
    private $reservedMemory;
    private $displayErrors;
    private static $loggers = array();
    private static $stackedErrors = array();
    private static $stackedErrorLevels = array();
    public static function register($level = null, $displayErrors = true)
    {
        $handler = new static();
        $handler->setLevel($level);
        $handler->setDisplayErrors($displayErrors);
        ini_set('display_errors', 0);
        set_error_handler(array($handler, 'handle'));
        register_shutdown_function(array($handler, 'handleFatal'));
        $handler->reservedMemory = str_repeat('x', 10240);
        return $handler;
    }
    public function setLevel($level)
    {
        $this->level = null === $level ? error_reporting() : $level;
    }
    public function setDisplayErrors($displayErrors)
    {
        $this->displayErrors = $displayErrors;
    }
    public static function setLogger(LoggerInterface $logger, $channel = 'deprecation')
    {
        self::$loggers[$channel] = $logger;
    }
    public function handle($level, $message, $file = 'unknown', $line = 0, $context = array())
    {
        if ($level & (E_USER_DEPRECATED | E_DEPRECATED)) {
            if (isset(self::$loggers['deprecation'])) {
                if (self::$stackedErrorLevels) {
                    self::$stackedErrors[] = func_get_args();
                } else {
                    if (version_compare(PHP_VERSION, '5.4', '<')) {
                        $stack = array_map(function ($row) {
                            unset($row['args']);
                            return $row;
                        }, array_slice(debug_backtrace(false), 0, 10));
                    } else {
                        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
                    }
                    self::$loggers['deprecation']->warning($message, array('type' => self::TYPE_DEPRECATION, 'stack' => $stack));
                }
                return true;
            }
        } elseif ($this->displayErrors && error_reporting() & $level && $this->level & $level) {
            if (PHP_VERSION_ID < 50400 && isset($context['GLOBALS']) && is_array($context)) {
                $c = $context;
                unset($c['GLOBALS'], $context);
                $context = $c;
            }
            $exception = sprintf('%s: %s in %s line %d', isset($this->levels[$level]) ? $this->levels[$level] : $level, $message, $file, $line);
            if ($context && class_exists('Symfony\\Component\\Debug\\Exception\\ContextErrorException')) {
                $exception = new ContextErrorException($exception, 0, $level, $file, $line, $context);
            } else {
                $exception = new \ErrorException($exception, 0, $level, $file, $line);
            }
            if (PHP_VERSION_ID <= 50407 && (PHP_VERSION_ID >= 50400 || PHP_VERSION_ID <= 50317)) {
                $exception->errorHandlerCanary = new ErrorHandlerCanary();
            }
            throw $exception;
        }
        if (isset(self::$loggers['scream']) && !(error_reporting() & $level)) {
            if (self::$stackedErrorLevels) {
                self::$stackedErrors[] = func_get_args();
            } else {
                switch ($level) {
                    case E_USER_ERROR:
                    case E_RECOVERABLE_ERROR:
                        $logLevel = LogLevel::ERROR;
                        break;
                    case E_WARNING:
                    case E_USER_WARNING:
                        $logLevel = LogLevel::WARNING;
                        break;
                    default:
                        $logLevel = LogLevel::NOTICE;
                        break;
                }
                self::$loggers['scream']->log($logLevel, $message, array('type' => $level, 'file' => $file, 'line' => $line, 'scream' => error_reporting()));
            }
        }
        return false;
    }
    public static function stackErrors()
    {
        self::$stackedErrorLevels[] = error_reporting(error_reporting() | E_PARSE | E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR);
    }
    public static function unstackErrors()
    {
        $level = array_pop(self::$stackedErrorLevels);
        if (null !== $level) {
            $e = error_reporting($level);
            if ($e !== ($level | E_PARSE | E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR)) {
                error_reporting($e);
            }
        }
        if (empty(self::$stackedErrorLevels)) {
            $errors = self::$stackedErrors;
            self::$stackedErrors = array();
            $errorHandler = set_error_handler('var_dump');
            restore_error_handler();
            if ($errorHandler) {
                foreach ($errors as $e) {
                    call_user_func_array($errorHandler, $e);
                }
            }
        }
    }
    public function handleFatal()
    {
        $this->reservedMemory = '';
        gc_collect_cycles();
        $error = error_get_last();
        $exceptionHandler = set_exception_handler('var_dump');
        restore_exception_handler();
        try {
            while (self::$stackedErrorLevels) {
                static::unstackErrors();
            }
        } catch (\Exception $exception) {
            if ($exceptionHandler) {
                call_user_func($exceptionHandler, $exception);
                return;
            }
            if ($this->displayErrors) {
                ini_set('display_errors', 1);
            }
            throw $exception;
        }
        if (!$error || !$this->level || !($error['type'] & (E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE))) {
            return;
        }
        if (isset(self::$loggers['emergency'])) {
            $fatal = array('type' => $error['type'], 'file' => $error['file'], 'line' => $error['line']);
            self::$loggers['emergency']->emergency($error['message'], $fatal);
        }
        if ($this->displayErrors && $exceptionHandler) {
            $this->handleFatalError($exceptionHandler, $error);
        }
    }
    protected function getFatalErrorHandlers()
    {
        return array(new UndefinedFunctionFatalErrorHandler(), new UndefinedMethodFatalErrorHandler(), new ClassNotFoundFatalErrorHandler());
    }
    private function handleFatalError($exceptionHandler, array $error)
    {
        set_error_handler('var_dump', 0);
        ini_set('display_errors', 1);
        $level = isset($this->levels[$error['type']]) ? $this->levels[$error['type']] : $error['type'];
        $message = sprintf('%s: %s in %s line %d', $level, $error['message'], $error['file'], $error['line']);
        if (0 === strpos($error['message'], 'Allowed memory') || 0 === strpos($error['message'], 'Out of memory')) {
            $exception = new OutOfMemoryException($message, 0, $error['type'], $error['file'], $error['line'], 3, false);
        } else {
            $exception = new FatalErrorException($message, 0, $error['type'], $error['file'], $error['line'], 3, true);
            foreach ($this->getFatalErrorHandlers() as $handler) {
                if ($e = $handler->handleError($error, $exception)) {
                    $exception = $e;
                    break;
                }
            }
        }
        try {
            call_user_func($exceptionHandler, $exception);
        } catch (\Exception $e) {
            throw $exception;
        }
    }
}
class ErrorHandlerCanary
{
    private static $displayErrors = null;
    public function __construct()
    {
        if (null === self::$displayErrors) {
            self::$displayErrors = ini_set('display_errors', 1);
        }
    }
    public function __destruct()
    {
        if (null !== self::$displayErrors) {
            ini_set('display_errors', self::$displayErrors);
            self::$displayErrors = null;
        }
    }
}
namespace Symfony\Component\HttpKernel\Debug;

use Symfony\Component\Debug\ErrorHandler as DebugErrorHandler;
class ErrorHandler extends DebugErrorHandler
{
    
}
namespace Illuminate\Config;

use Closure;
use ArrayAccess;
use Illuminate\Support\NamespacedItemResolver;
class Repository extends NamespacedItemResolver implements ArrayAccess
{
    protected $loader;
    protected $environment;
    protected $items = array();
    protected $packages = array();
    protected $afterLoad = array();
    public function __construct(LoaderInterface $loader, $environment)
    {
        $this->loader = $loader;
        $this->environment = $environment;
    }
    public function has($key)
    {
        $default = microtime(true);
        return $this->get($key, $default) !== $default;
    }
    public function hasGroup($key)
    {
        list($namespace, $group, $item) = $this->parseKey($key);
        return $this->loader->exists($group, $namespace);
    }
    public function get($key, $default = null)
    {
        list($namespace, $group, $item) = $this->parseKey($key);
        $collection = $this->getCollection($group, $namespace);
        $this->load($group, $namespace, $collection);
        return array_get($this->items[$collection], $item, $default);
    }
    public function set($key, $value)
    {
        list($namespace, $group, $item) = $this->parseKey($key);
        $collection = $this->getCollection($group, $namespace);
        $this->load($group, $namespace, $collection);
        if (is_null($item)) {
            $this->items[$collection] = $value;
        } else {
            array_set($this->items[$collection], $item, $value);
        }
    }
    protected function load($group, $namespace, $collection)
    {
        $env = $this->environment;
        if (isset($this->items[$collection])) {
            return;
        }
        $items = $this->loader->load($env, $group, $namespace);
        if (isset($this->afterLoad[$namespace])) {
            $items = $this->callAfterLoad($namespace, $group, $items);
        }
        $this->items[$collection] = $items;
    }
    protected function callAfterLoad($namespace, $group, $items)
    {
        $callback = $this->afterLoad[$namespace];
        return call_user_func($callback, $this, $group, $items);
    }
    protected function parseNamespacedSegments($key)
    {
        list($namespace, $item) = explode('::', $key);
        if (in_array($namespace, $this->packages)) {
            return $this->parsePackageSegments($key, $namespace, $item);
        }
        return parent::parseNamespacedSegments($key);
    }
    protected function parsePackageSegments($key, $namespace, $item)
    {
        $itemSegments = explode('.', $item);
        if (!$this->loader->exists($itemSegments[0], $namespace)) {
            return array($namespace, 'config', $item);
        }
        return parent::parseNamespacedSegments($key);
    }
    public function package($package, $hint, $namespace = null)
    {
        $namespace = $this->getPackageNamespace($package, $namespace);
        $this->packages[] = $namespace;
        $this->addNamespace($namespace, $hint);
        $this->afterLoading($namespace, function ($me, $group, $items) use($package) {
            $env = $me->getEnvironment();
            $loader = $me->getLoader();
            return $loader->cascadePackage($env, $package, $group, $items);
        });
    }
    protected function getPackageNamespace($package, $namespace)
    {
        if (is_null($namespace)) {
            list($vendor, $namespace) = explode('/', $package);
        }
        return $namespace;
    }
    public function afterLoading($namespace, Closure $callback)
    {
        $this->afterLoad[$namespace] = $callback;
    }
    protected function getCollection($group, $namespace = null)
    {
        $namespace = $namespace ?: '*';
        return $namespace . '::' . $group;
    }
    public function addNamespace($namespace, $hint)
    {
        $this->loader->addNamespace($namespace, $hint);
    }
    public function getNamespaces()
    {
        return $this->loader->getNamespaces();
    }
    public function getLoader()
    {
        return $this->loader;
    }
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }
    public function getEnvironment()
    {
        return $this->environment;
    }
    public function getAfterLoadCallbacks()
    {
        return $this->afterLoad;
    }
    public function getItems()
    {
        return $this->items;
    }
    public function offsetExists($key)
    {
        return $this->has($key);
    }
    public function offsetGet($key)
    {
        return $this->get($key);
    }
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
namespace Illuminate\Support;

class NamespacedItemResolver
{
    protected $parsed = array();
    public function parseKey($key)
    {
        if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }
        $segments = explode('.', $key);
        if (strpos($key, '::') === false) {
            $parsed = $this->parseBasicSegments($segments);
        } else {
            $parsed = $this->parseNamespacedSegments($key);
        }
        return $this->parsed[$key] = $parsed;
    }
    protected function parseBasicSegments(array $segments)
    {
        $group = $segments[0];
        if (count($segments) == 1) {
            return array(null, $group, null);
        } else {
            $item = implode('.', array_slice($segments, 1));
            return array(null, $group, $item);
        }
    }
    protected function parseNamespacedSegments($key)
    {
        list($namespace, $item) = explode('::', $key);
        $itemSegments = explode('.', $item);
        $groupAndItem = array_slice($this->parseBasicSegments($itemSegments), 1);
        return array_merge(array($namespace), $groupAndItem);
    }
    public function setParsedKey($key, $parsed)
    {
        $this->parsed[$key] = $parsed;
    }
}
namespace Illuminate\Config;

use Illuminate\Filesystem\Filesystem;
class FileLoader implements LoaderInterface
{
    protected $files;
    protected $defaultPath;
    protected $hints = array();
    protected $exists = array();
    public function __construct(Filesystem $files, $defaultPath)
    {
        $this->files = $files;
        $this->defaultPath = $defaultPath;
    }
    public function load($environment, $group, $namespace = null)
    {
        $items = array();
        $path = $this->getPath($namespace);
        if (is_null($path)) {
            return $items;
        }
        $file = "{$path}/{$group}.php";
        if ($this->files->exists($file)) {
            $items = $this->files->getRequire($file);
        }
        $file = "{$path}/{$environment}/{$group}.php";
        if ($this->files->exists($file)) {
            $items = $this->mergeEnvironment($items, $file);
        }
        return $items;
    }
    protected function mergeEnvironment(array $items, $file)
    {
        return array_replace_recursive($items, $this->files->getRequire($file));
    }
    public function exists($group, $namespace = null)
    {
        $key = $group . $namespace;
        if (isset($this->exists[$key])) {
            return $this->exists[$key];
        }
        $path = $this->getPath($namespace);
        if (is_null($path)) {
            return $this->exists[$key] = false;
        }
        $file = "{$path}/{$group}.php";
        $exists = $this->files->exists($file);
        return $this->exists[$key] = $exists;
    }
    public function cascadePackage($env, $package, $group, $items)
    {
        $file = "packages/{$package}/{$group}.php";
        if ($this->files->exists($path = $this->defaultPath . '/' . $file)) {
            $items = array_merge($items, $this->getRequire($path));
        }
        $path = $this->getPackagePath($env, $package, $group);
        if ($this->files->exists($path)) {
            $items = array_merge($items, $this->getRequire($path));
        }
        return $items;
    }
    protected function getPackagePath($env, $package, $group)
    {
        $file = "packages/{$package}/{$env}/{$group}.php";
        return $this->defaultPath . '/' . $file;
    }
    protected function getPath($namespace)
    {
        if (is_null($namespace)) {
            return $this->defaultPath;
        } elseif (isset($this->hints[$namespace])) {
            return $this->hints[$namespace];
        }
    }
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;
    }
    public function getNamespaces()
    {
        return $this->hints;
    }
    protected function getRequire($path)
    {
        return $this->files->getRequire($path);
    }
    public function getFilesystem()
    {
        return $this->files;
    }
}
namespace Illuminate\Config;

interface LoaderInterface
{
    public function load($environment, $group, $namespace = null);
    public function exists($group, $namespace = null);
    public function addNamespace($namespace, $hint);
    public function getNamespaces();
    public function cascadePackage($environment, $package, $group, $items);
}
namespace Illuminate\Config;

interface EnvironmentVariablesLoaderInterface
{
    public function load($environment = null);
}
namespace Illuminate\Config;

use Illuminate\Filesystem\Filesystem;
class FileEnvironmentVariablesLoader implements EnvironmentVariablesLoaderInterface
{
    protected $files;
    protected $path;
    public function __construct(Filesystem $files, $path = null)
    {
        $this->files = $files;
        $this->path = $path ?: base_path();
    }
    public function load($environment = null)
    {
        if ($environment == 'production') {
            $environment = null;
        }
        if (!$this->files->exists($path = $this->getFile($environment))) {
            return array();
        } else {
            return array_dot($this->files->getRequire($path));
        }
    }
    protected function getFile($environment)
    {
        if ($environment) {
            return $this->path . '/.env.' . $environment . '.php';
        } else {
            return $this->path . '/.env.php';
        }
    }
}
namespace Illuminate\Config;

class EnvironmentVariables
{
    protected $loader;
    public function __construct(EnvironmentVariablesLoaderInterface $loader)
    {
        $this->loader = $loader;
    }
    public function load($environment = null)
    {
        foreach ($this->loader->load($environment) as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}
namespace Illuminate\Filesystem;

use FilesystemIterator;
use Symfony\Component\Finder\Finder;
class FileNotFoundException extends \Exception
{
    
}
class Filesystem
{
    public function exists($path)
    {
        return file_exists($path);
    }
    public function get($path)
    {
        if ($this->isFile($path)) {
            return file_get_contents($path);
        }
        throw new FileNotFoundException("File does not exist at path {$path}");
    }
    public function getRequire($path)
    {
        if ($this->isFile($path)) {
            return require $path;
        }
        throw new FileNotFoundException("File does not exist at path {$path}");
    }
    public function requireOnce($file)
    {
        require_once $file;
    }
    public function put($path, $contents)
    {
        return file_put_contents($path, $contents);
    }
    public function prepend($path, $data)
    {
        if ($this->exists($path)) {
            return $this->put($path, $data . $this->get($path));
        } else {
            return $this->put($path, $data);
        }
    }
    public function append($path, $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();
        $success = true;
        foreach ($paths as $path) {
            if (!@unlink($path)) {
                $success = false;
            }
        }
        return $success;
    }
    public function move($path, $target)
    {
        return rename($path, $target);
    }
    public function copy($path, $target)
    {
        return copy($path, $target);
    }
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
    public function type($path)
    {
        return filetype($path);
    }
    public function size($path)
    {
        return filesize($path);
    }
    public function lastModified($path)
    {
        return filemtime($path);
    }
    public function isDirectory($directory)
    {
        return is_dir($directory);
    }
    public function isWritable($path)
    {
        return is_writable($path);
    }
    public function isFile($file)
    {
        return is_file($file);
    }
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }
    public function files($directory)
    {
        $glob = glob($directory . '/*');
        if ($glob === false) {
            return array();
        }
        return array_filter($glob, function ($file) {
            return filetype($file) == 'file';
        });
    }
    public function allFiles($directory)
    {
        return iterator_to_array(Finder::create()->files()->in($directory), false);
    }
    public function directories($directory)
    {
        $directories = array();
        foreach (Finder::create()->in($directory)->directories()->depth(0) as $dir) {
            $directories[] = $dir->getPathname();
        }
        return $directories;
    }
    public function makeDirectory($path, $mode = 493, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        } else {
            return mkdir($path, $mode, $recursive);
        }
    }
    public function copyDirectory($directory, $destination, $options = null)
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }
        $options = $options ?: FilesystemIterator::SKIP_DOTS;
        if (!$this->isDirectory($destination)) {
            $this->makeDirectory($destination, 511, true);
        }
        $items = new FilesystemIterator($directory, $options);
        foreach ($items as $item) {
            $target = $destination . '/' . $item->getBasename();
            if ($item->isDir()) {
                $path = $item->getPathname();
                if (!$this->copyDirectory($path, $target, $options)) {
                    return false;
                }
            } else {
                if (!$this->copy($item->getPathname(), $target)) {
                    return false;
                }
            }
        }
        return true;
    }
    public function deleteDirectory($directory, $preserve = false)
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }
        $items = new FilesystemIterator($directory);
        foreach ($items as $item) {
            if ($item->isDir()) {
                $this->deleteDirectory($item->getPathname());
            } else {
                $this->delete($item->getPathname());
            }
        }
        if (!$preserve) {
            @rmdir($directory);
        }
        return true;
    }
    public function cleanDirectory($directory)
    {
        return $this->deleteDirectory($directory, true);
    }
}
namespace Illuminate\Foundation;

class AliasLoader
{
    protected $aliases;
    protected $registered = false;
    protected static $instance;
    public function __construct(array $aliases = array())
    {
        $this->aliases = $aliases;
    }
    public static function getInstance(array $aliases = array())
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($aliases);
        }
        $aliases = array_merge(static::$instance->getAliases(), $aliases);
        static::$instance->setAliases($aliases);
        return static::$instance;
    }
    public function load($alias)
    {
        if (isset($this->aliases[$alias])) {
            return class_alias($this->aliases[$alias], $alias);
        }
    }
    public function alias($class, $alias)
    {
        $this->aliases[$class] = $alias;
    }
    public function register()
    {
        if (!$this->registered) {
            $this->prependToLoaderStack();
            $this->registered = true;
        }
    }
    protected function prependToLoaderStack()
    {
        spl_autoload_register(array($this, 'load'), true, true);
    }
    public function getAliases()
    {
        return $this->aliases;
    }
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }
    public function isRegistered()
    {
        return $this->registered;
    }
    public function setRegistered($value)
    {
        $this->registered = $value;
    }
    public static function setInstance($loader)
    {
        static::$instance = $loader;
    }
}
namespace Illuminate\Foundation;

use Illuminate\Filesystem\Filesystem;
class ProviderRepository
{
    protected $files;
    protected $manifestPath;
    protected $default = array('when' => array());
    public function __construct(Filesystem $files, $manifestPath)
    {
        $this->files = $files;
        $this->manifestPath = $manifestPath;
    }
    public function load(Application $app, array $providers)
    {
        $manifest = $this->loadManifest();
        if ($this->shouldRecompile($manifest, $providers)) {
            $manifest = $this->compileManifest($app, $providers);
        }
        if ($app->runningInConsole()) {
            $manifest['eager'] = $manifest['providers'];
        }
        foreach ($manifest['when'] as $provider => $events) {
            $this->registerLoadEvents($app, $provider, $events);
        }
        foreach ($manifest['eager'] as $provider) {
            $app->register($this->createProvider($app, $provider));
        }
        $app->setDeferredServices($manifest['deferred']);
    }
    protected function registerLoadEvents(Application $app, $provider, array $events)
    {
        if (count($events) < 1) {
            return;
        }
        $app->make('events')->listen($events, function () use($app, $provider) {
            $app->register($provider);
        });
    }
    protected function compileManifest(Application $app, $providers)
    {
        $manifest = $this->freshManifest($providers);
        foreach ($providers as $provider) {
            $instance = $this->createProvider($app, $provider);
            if ($instance->isDeferred()) {
                foreach ($instance->provides() as $service) {
                    $manifest['deferred'][$service] = $provider;
                }
                $manifest['when'][$provider] = $instance->when();
            } else {
                $manifest['eager'][] = $provider;
            }
        }
        return $this->writeManifest($manifest);
    }
    public function createProvider(Application $app, $provider)
    {
        return new $provider($app);
    }
    public function shouldRecompile($manifest, $providers)
    {
        return is_null($manifest) || $manifest['providers'] != $providers;
    }
    public function loadManifest()
    {
        $path = $this->manifestPath . '/services.json';
        if ($this->files->exists($path)) {
            $manifest = json_decode($this->files->get($path), true);
            return array_merge($this->default, $manifest);
        }
    }
    public function writeManifest($manifest)
    {
        $path = $this->manifestPath . '/services.json';
        $this->files->put($path, json_encode($manifest, JSON_PRETTY_PRINT));
        return $manifest;
    }
    protected function freshManifest(array $providers)
    {
        list($eager, $deferred) = array(array(), array());
        return compact('providers', 'eager', 'deferred');
    }
    public function getFilesystem()
    {
        return $this->files;
    }
}
namespace Illuminate\Cookie;

use Illuminate\Support\ServiceProvider;
class CookieServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindShared('cookie', function ($app) {
            $config = $app['config']['session'];
            return (new CookieJar())->setDefaultPathAndDomain($config['path'], $config['domain']);
        });
    }
}
namespace Illuminate\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connectors\ConnectionFactory;
class DatabaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }
    public function register()
    {
        $this->app->bindShared('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });
        $this->app->bindShared('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });
    }
}
namespace Illuminate\Encryption;

use Illuminate\Support\ServiceProvider;
class EncryptionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindShared('encrypter', function ($app) {
            $encrypter = new Encrypter($app['config']['app.key']);
            if ($app['config']->has('app.cipher')) {
                $encrypter->setCipher($app['config']['app.cipher']);
            }
            return $encrypter;
        });
    }
}
namespace Illuminate\Filesystem;

use Illuminate\Support\ServiceProvider;
class FilesystemServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindShared('files', function () {
            return new Filesystem();
        });
    }
}
namespace Illuminate\Session;

use Illuminate\Support\ServiceProvider;
class SessionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->setupDefaultDriver();
        $this->registerSessionManager();
        $this->registerSessionDriver();
    }
    protected function setupDefaultDriver()
    {
        if ($this->app->runningInConsole()) {
            $this->app['config']['session.driver'] = 'array';
        }
    }
    protected function registerSessionManager()
    {
        $this->app->bindShared('session', function ($app) {
            return new SessionManager($app);
        });
    }
    protected function registerSessionDriver()
    {
        $this->app->bindShared('session.store', function ($app) {
            $manager = $app['session'];
            return $manager->driver();
        });
    }
    protected function getDriver()
    {
        return $this->app['config']['session.driver'];
    }
}
namespace Illuminate\View;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Compilers\BladeCompiler;
class ViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerFactory();
        $this->registerSessionBinder();
    }
    public function registerEngineResolver()
    {
        $this->app->bindShared('view.engine.resolver', function ($app) {
            $resolver = new EngineResolver();
            foreach (array('php', 'blade') as $engine) {
                $this->{'register' . ucfirst($engine) . 'Engine'}($resolver);
            }
            return $resolver;
        });
    }
    public function registerPhpEngine($resolver)
    {
        $resolver->register('php', function () {
            return new PhpEngine();
        });
    }
    public function registerBladeEngine($resolver)
    {
        $app = $this->app;
        $app->bindShared('blade.compiler', function ($app) {
            $cache = $app['path.storage'] . '/views';
            return new BladeCompiler($app['files'], $cache);
        });
        $resolver->register('blade', function () use($app) {
            return new CompilerEngine($app['blade.compiler'], $app['files']);
        });
    }
    public function registerViewFinder()
    {
        $this->app->bindShared('view.finder', function ($app) {
            $paths = $app['config']['view.paths'];
            return new FileViewFinder($app['files'], $paths);
        });
    }
    public function registerFactory()
    {
        $this->app->bindShared('view', function ($app) {
            $resolver = $app['view.engine.resolver'];
            $finder = $app['view.finder'];
            $env = new Factory($resolver, $finder, $app['events']);
            $env->setContainer($app);
            $env->share('app', $app);
            return $env;
        });
    }
    protected function registerSessionBinder()
    {
        list($app, $me) = array($this->app, $this);
        $app->booted(function () use($app, $me) {
            if ($me->sessionHasErrors($app)) {
                $errors = $app['session.store']->get('errors');
                $app['view']->share('errors', $errors);
            } else {
                $app['view']->share('errors', new ViewErrorBag());
            }
        });
    }
    public function sessionHasErrors($app)
    {
        $config = $app['config']['session'];
        if (isset($app['session.store']) && !is_null($config['driver'])) {
            return $app['session.store']->has('errors');
        }
    }
}
namespace Illuminate\Routing;

interface RouteFiltererInterface
{
    public function filter($name, $callback);
    public function callRouteFilter($filter, $parameters, $route, $request, $response = null);
}
namespace Illuminate\Routing;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class Router implements HttpKernelInterface, RouteFiltererInterface
{
    protected $events;
    protected $container;
    protected $routes;
    protected $current;
    protected $currentRequest;
    protected $controllerDispatcher;
    protected $inspector;
    protected $filtering = true;
    protected $patternFilters = array();
    protected $regexFilters = array();
    protected $binders = array();
    protected $patterns = array();
    protected $groupStack = array();
    public static $verbs = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS');
    protected $resourceDefaults = array('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');
    public function __construct(Dispatcher $events, Container $container = null)
    {
        $this->events = $events;
        $this->routes = new RouteCollection();
        $this->container = $container ?: new Container();
        $this->bind('_missing', function ($v) {
            return explode('/', $v);
        });
    }
    public function get($uri, $action)
    {
        return $this->addRoute(array('GET', 'HEAD'), $uri, $action);
    }
    public function post($uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }
    public function put($uri, $action)
    {
        return $this->addRoute('PUT', $uri, $action);
    }
    public function patch($uri, $action)
    {
        return $this->addRoute('PATCH', $uri, $action);
    }
    public function delete($uri, $action)
    {
        return $this->addRoute('DELETE', $uri, $action);
    }
    public function options($uri, $action)
    {
        return $this->addRoute('OPTIONS', $uri, $action);
    }
    public function any($uri, $action)
    {
        $verbs = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE');
        return $this->addRoute($verbs, $uri, $action);
    }
    public function match($methods, $uri, $action)
    {
        return $this->addRoute(array_map('strtoupper', (array) $methods), $uri, $action);
    }
    public function controllers(array $controllers)
    {
        foreach ($controllers as $uri => $name) {
            $this->controller($uri, $name);
        }
    }
    public function controller($uri, $controller, $names = array())
    {
        $prepended = $controller;
        if (!empty($this->groupStack)) {
            $prepended = $this->prependGroupUses($controller);
        }
        $routable = $this->getInspector()->getRoutable($prepended, $uri);
        foreach ($routable as $method => $routes) {
            foreach ($routes as $route) {
                $this->registerInspected($route, $controller, $method, $names);
            }
        }
        $this->addFallthroughRoute($controller, $uri);
    }
    protected function registerInspected($route, $controller, $method, &$names)
    {
        $action = array('uses' => $controller . '@' . $method);
        $action['as'] = array_pull($names, $method);
        $this->{$route['verb']}($route['uri'], $action);
    }
    protected function addFallthroughRoute($controller, $uri)
    {
        $missing = $this->any($uri . '/{_missing}', $controller . '@missingMethod');
        $missing->where('_missing', '(.*)');
    }
    public function resource($name, $controller, array $options = array())
    {
        if (str_contains($name, '/')) {
            $this->prefixedResource($name, $controller, $options);
            return;
        }
        $base = $this->getResourceWildcard(last(explode('.', $name)));
        $defaults = $this->resourceDefaults;
        foreach ($this->getResourceMethods($defaults, $options) as $m) {
            $this->{'addResource' . ucfirst($m)}($name, $base, $controller, $options);
        }
    }
    protected function prefixedResource($name, $controller, array $options)
    {
        list($name, $prefix) = $this->getResourcePrefix($name);
        $callback = function ($me) use($name, $controller, $options) {
            $me->resource($name, $controller, $options);
        };
        return $this->group(compact('prefix'), $callback);
    }
    protected function getResourcePrefix($name)
    {
        $segments = explode('/', $name);
        $prefix = implode('/', array_slice($segments, 0, -1));
        return array(end($segments), $prefix);
    }
    protected function getResourceMethods($defaults, $options)
    {
        if (isset($options['only'])) {
            return array_intersect($defaults, (array) $options['only']);
        } elseif (isset($options['except'])) {
            return array_diff($defaults, (array) $options['except']);
        }
        return $defaults;
    }
    public function getResourceUri($resource)
    {
        if (!str_contains($resource, '.')) {
            return $resource;
        }
        $segments = explode('.', $resource);
        $uri = $this->getNestedResourceUri($segments);
        return str_replace('/{' . $this->getResourceWildcard(last($segments)) . '}', '', $uri);
    }
    protected function getNestedResourceUri(array $segments)
    {
        return implode('/', array_map(function ($s) {
            return $s . '/{' . $this->getResourceWildcard($s) . '}';
        }, $segments));
    }
    protected function getResourceAction($resource, $controller, $method, $options)
    {
        $name = $this->getResourceName($resource, $method, $options);
        return array('as' => $name, 'uses' => $controller . '@' . $method);
    }
    protected function getResourceName($resource, $method, $options)
    {
        if (isset($options['names'][$method])) {
            return $options['names'][$method];
        }
        $prefix = isset($options['as']) ? $options['as'] . '.' : '';
        if (empty($this->groupStack)) {
            return $prefix . $resource . '.' . $method;
        }
        return $this->getGroupResourceName($prefix, $resource, $method);
    }
    protected function getGroupResourceName($prefix, $resource, $method)
    {
        $group = str_replace('/', '.', $this->getLastGroupPrefix());
        return trim("{$prefix}{$group}.{$resource}.{$method}", '.');
    }
    public function getResourceWildcard($value)
    {
        return str_replace('-', '_', $value);
    }
    protected function addResourceIndex($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name);
        $action = $this->getResourceAction($name, $controller, 'index', $options);
        return $this->get($uri, $action);
    }
    protected function addResourceCreate($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/create';
        $action = $this->getResourceAction($name, $controller, 'create', $options);
        return $this->get($uri, $action);
    }
    protected function addResourceStore($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name);
        $action = $this->getResourceAction($name, $controller, 'store', $options);
        return $this->post($uri, $action);
    }
    protected function addResourceShow($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}';
        $action = $this->getResourceAction($name, $controller, 'show', $options);
        return $this->get($uri, $action);
    }
    protected function addResourceEdit($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}/edit';
        $action = $this->getResourceAction($name, $controller, 'edit', $options);
        return $this->get($uri, $action);
    }
    protected function addResourceUpdate($name, $base, $controller, $options)
    {
        $this->addPutResourceUpdate($name, $base, $controller, $options);
        return $this->addPatchResourceUpdate($name, $base, $controller);
    }
    protected function addPutResourceUpdate($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}';
        $action = $this->getResourceAction($name, $controller, 'update', $options);
        return $this->put($uri, $action);
    }
    protected function addPatchResourceUpdate($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}';
        $this->patch($uri, $controller . '@update');
    }
    protected function addResourceDestroy($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}';
        $action = $this->getResourceAction($name, $controller, 'destroy', $options);
        return $this->delete($uri, $action);
    }
    public function group(array $attributes, Closure $callback)
    {
        $this->updateGroupStack($attributes);
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }
    protected function updateGroupStack(array $attributes)
    {
        if (!empty($this->groupStack)) {
            $attributes = $this->mergeGroup($attributes, last($this->groupStack));
        }
        $this->groupStack[] = $attributes;
    }
    public function mergeWithLastGroup($new)
    {
        return $this->mergeGroup($new, last($this->groupStack));
    }
    public static function mergeGroup($new, $old)
    {
        $new['namespace'] = static::formatUsesPrefix($new, $old);
        $new['prefix'] = static::formatGroupPrefix($new, $old);
        if (isset($new['domain'])) {
            unset($old['domain']);
        }
        $new['where'] = array_merge(array_get($old, 'where', array()), array_get($new, 'where', array()));
        return array_merge_recursive(array_except($old, array('namespace', 'prefix', 'where')), $new);
    }
    protected static function formatUsesPrefix($new, $old)
    {
        if (isset($new['namespace']) && isset($old['namespace'])) {
            return trim(array_get($old, 'namespace'), '\\') . '\\' . trim($new['namespace'], '\\');
        } elseif (isset($new['namespace'])) {
            return trim($new['namespace'], '\\');
        }
        return array_get($old, 'namespace');
    }
    protected static function formatGroupPrefix($new, $old)
    {
        if (isset($new['prefix'])) {
            return trim(array_get($old, 'prefix'), '/') . '/' . trim($new['prefix'], '/');
        }
        return array_get($old, 'prefix');
    }
    protected function getLastGroupPrefix()
    {
        if (!empty($this->groupStack)) {
            $last = end($this->groupStack);
            return isset($last['prefix']) ? $last['prefix'] : '';
        }
        return '';
    }
    protected function addRoute($methods, $uri, $action)
    {
        return $this->routes->add($this->createRoute($methods, $uri, $action));
    }
    protected function createRoute($methods, $uri, $action)
    {
        if ($this->routingToController($action)) {
            $action = $this->getControllerAction($action);
        }
        $route = $this->newRoute($methods, $uri = $this->prefix($uri), $action);
        if (!empty($this->groupStack)) {
            $this->mergeController($route);
        }
        $this->addWhereClausesToRoute($route);
        return $route;
    }
    protected function newRoute($methods, $uri, $action)
    {
        return new Route($methods, $uri, $action);
    }
    protected function prefix($uri)
    {
        return trim(trim($this->getLastGroupPrefix(), '/') . '/' . trim($uri, '/'), '/') ?: '/';
    }
    protected function addWhereClausesToRoute($route)
    {
        $route->where(array_merge($this->patterns, array_get($route->getAction(), 'where', array())));
        return $route;
    }
    protected function mergeController($route)
    {
        $action = $this->mergeWithLastGroup($route->getAction());
        $route->setAction($action);
    }
    protected function routingToController($action)
    {
        if ($action instanceof Closure) {
            return false;
        }
        return is_string($action) || is_string(array_get($action, 'uses'));
    }
    protected function getControllerAction($action)
    {
        if (is_string($action)) {
            $action = array('uses' => $action);
        }
        if (!empty($this->groupStack)) {
            $action['uses'] = $this->prependGroupUses($action['uses']);
        }
        $action['controller'] = $action['uses'];
        $closure = $this->getClassClosure($action['uses']);
        return array_set($action, 'uses', $closure);
    }
    protected function getClassClosure($controller)
    {
        $d = $this->getControllerDispatcher();
        return function () use($d, $controller) {
            $route = $this->current();
            $request = $this->getCurrentRequest();
            list($class, $method) = explode('@', $controller);
            return $d->dispatch($route, $request, $class, $method);
        };
    }
    protected function prependGroupUses($uses)
    {
        $group = last($this->groupStack);
        return isset($group['namespace']) ? $group['namespace'] . '\\' . $uses : $uses;
    }
    public function dispatch(Request $request)
    {
        $this->currentRequest = $request;
        $response = $this->callFilter('before', $request);
        if (is_null($response)) {
            $response = $this->dispatchToRoute($request);
        }
        $response = $this->prepareResponse($request, $response);
        $this->callFilter('after', $request, $response);
        return $response;
    }
    public function dispatchToRoute(Request $request)
    {
        $route = $this->findRoute($request);
        $this->events->fire('router.matched', array($route, $request));
        $response = $this->callRouteBefore($route, $request);
        if (is_null($response)) {
            $response = $route->run($request);
        }
        $response = $this->prepareResponse($request, $response);
        $this->callRouteAfter($route, $request, $response);
        return $response;
    }
    protected function findRoute($request)
    {
        $this->current = $route = $this->routes->match($request);
        return $this->substituteBindings($route);
    }
    protected function substituteBindings($route)
    {
        foreach ($route->parameters() as $key => $value) {
            if (isset($this->binders[$key])) {
                $route->setParameter($key, $this->performBinding($key, $value, $route));
            }
        }
        return $route;
    }
    protected function performBinding($key, $value, $route)
    {
        return call_user_func($this->binders[$key], $value, $route);
    }
    public function matched($callback)
    {
        $this->events->listen('router.matched', $callback);
    }
    public function before($callback)
    {
        $this->addGlobalFilter('before', $callback);
    }
    public function after($callback)
    {
        $this->addGlobalFilter('after', $callback);
    }
    protected function addGlobalFilter($filter, $callback)
    {
        $this->events->listen('router.' . $filter, $this->parseFilter($callback));
    }
    public function filter($name, $callback)
    {
        $this->events->listen('router.filter: ' . $name, $this->parseFilter($callback));
    }
    protected function parseFilter($callback)
    {
        if (is_string($callback) && !str_contains($callback, '@')) {
            return $callback . '@filter';
        } else {
            return $callback;
        }
    }
    public function when($pattern, $name, $methods = null)
    {
        if (!is_null($methods)) {
            $methods = array_map('strtoupper', (array) $methods);
        }
        $this->patternFilters[$pattern][] = compact('name', 'methods');
    }
    public function whenRegex($pattern, $name, $methods = null)
    {
        if (!is_null($methods)) {
            $methods = array_map('strtoupper', (array) $methods);
        }
        $this->regexFilters[$pattern][] = compact('name', 'methods');
    }
    public function model($key, $class, Closure $callback = null)
    {
        $this->bind($key, function ($value) use($class, $callback) {
            if (is_null($value)) {
                return null;
            }
            if ($model = (new $class())->find($value)) {
                return $model;
            }
            if ($callback instanceof Closure) {
                return call_user_func($callback);
            }
            throw new NotFoundHttpException();
        });
    }
    public function bind($key, $binder)
    {
        if (is_string($binder)) {
            $binder = $this->createClassBinding($binder);
        }
        $this->binders[str_replace('-', '_', $key)] = $binder;
    }
    public function createClassBinding($binding)
    {
        return function ($value, $route) use($binding) {
            $segments = explode('@', $binding);
            $method = count($segments) == 2 ? $segments[1] : 'bind';
            $callable = array($this->container->make($segments[0]), $method);
            return call_user_func($callable, $value, $route);
        };
    }
    public function pattern($key, $pattern)
    {
        $this->patterns[$key] = $pattern;
    }
    public function patterns($patterns)
    {
        foreach ($patterns as $key => $pattern) {
            $this->pattern($key, $pattern);
        }
    }
    protected function callFilter($filter, $request, $response = null)
    {
        if (!$this->filtering) {
            return null;
        }
        return $this->events->until('router.' . $filter, array($request, $response));
    }
    public function callRouteBefore($route, $request)
    {
        $response = $this->callPatternFilters($route, $request);
        return $response ?: $this->callAttachedBefores($route, $request);
    }
    protected function callPatternFilters($route, $request)
    {
        foreach ($this->findPatternFilters($request) as $filter => $parameters) {
            $response = $this->callRouteFilter($filter, $parameters, $route, $request);
            if (!is_null($response)) {
                return $response;
            }
        }
    }
    public function findPatternFilters($request)
    {
        $results = array();
        list($path, $method) = array($request->path(), $request->getMethod());
        foreach ($this->patternFilters as $pattern => $filters) {
            if (str_is($pattern, $path)) {
                $merge = $this->patternsByMethod($method, $filters);
                $results = array_merge($results, $merge);
            }
        }
        foreach ($this->regexFilters as $pattern => $filters) {
            if (preg_match($pattern, $path)) {
                $merge = $this->patternsByMethod($method, $filters);
                $results = array_merge($results, $merge);
            }
        }
        return $results;
    }
    protected function patternsByMethod($method, $filters)
    {
        $results = array();
        foreach ($filters as $filter) {
            if ($this->filterSupportsMethod($filter, $method)) {
                $parsed = Route::parseFilters($filter['name']);
                $results = array_merge($results, $parsed);
            }
        }
        return $results;
    }
    protected function filterSupportsMethod($filter, $method)
    {
        $methods = $filter['methods'];
        return is_null($methods) || in_array($method, $methods);
    }
    protected function callAttachedBefores($route, $request)
    {
        foreach ($route->beforeFilters() as $filter => $parameters) {
            $response = $this->callRouteFilter($filter, $parameters, $route, $request);
            if (!is_null($response)) {
                return $response;
            }
        }
    }
    public function callRouteAfter($route, $request, $response)
    {
        foreach ($route->afterFilters() as $filter => $parameters) {
            $this->callRouteFilter($filter, $parameters, $route, $request, $response);
        }
    }
    public function callRouteFilter($filter, $parameters, $route, $request, $response = null)
    {
        if (!$this->filtering) {
            return null;
        }
        $data = array_merge(array($route, $request, $response), $parameters);
        return $this->events->until('router.filter: ' . $filter, $this->cleanFilterParameters($data));
    }
    protected function cleanFilterParameters(array $parameters)
    {
        return array_filter($parameters, function ($p) {
            return !is_null($p) && $p !== '';
        });
    }
    protected function prepareResponse($request, $response)
    {
        if (!$response instanceof SymfonyResponse) {
            $response = new Response($response);
        }
        return $response->prepare($request);
    }
    public function withoutFilters(callable $callback)
    {
        $this->disableFilters();
        call_user_func($callback);
        $this->enableFilters();
    }
    public function enableFilters()
    {
        $this->filtering = true;
    }
    public function disableFilters()
    {
        $this->filtering = false;
    }
    public function input($key, $default = null)
    {
        return $this->current()->parameter($key, $default);
    }
    public function getCurrentRoute()
    {
        return $this->current();
    }
    public function current()
    {
        return $this->current;
    }
    public function has($name)
    {
        return $this->routes->hasNamedRoute($name);
    }
    public function currentRouteName()
    {
        return $this->current() ? $this->current()->getName() : null;
    }
    public function is()
    {
        foreach (func_get_args() as $pattern) {
            if (str_is($pattern, $this->currentRouteName())) {
                return true;
            }
        }
        return false;
    }
    public function currentRouteNamed($name)
    {
        return $this->current() ? $this->current()->getName() == $name : false;
    }
    public function currentRouteAction()
    {
        if (!$this->current()) {
            return;
        }
        $action = $this->current()->getAction();
        return isset($action['controller']) ? $action['controller'] : null;
    }
    public function uses()
    {
        foreach (func_get_args() as $pattern) {
            if (str_is($pattern, $this->currentRouteAction())) {
                return true;
            }
        }
        return false;
    }
    public function currentRouteUses($action)
    {
        return $this->currentRouteAction() == $action;
    }
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }
    public function getRoutes()
    {
        return $this->routes;
    }
    public function getControllerDispatcher()
    {
        if (is_null($this->controllerDispatcher)) {
            $this->controllerDispatcher = new ControllerDispatcher($this, $this->container);
        }
        return $this->controllerDispatcher;
    }
    public function setControllerDispatcher(ControllerDispatcher $dispatcher)
    {
        $this->controllerDispatcher = $dispatcher;
    }
    public function getInspector()
    {
        return $this->inspector ?: ($this->inspector = new ControllerInspector());
    }
    public function getPatterns()
    {
        return $this->patterns;
    }
    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->dispatch(Request::createFromBase($request));
    }
}
namespace Illuminate\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\UriValidator;
use Illuminate\Routing\Matching\HostValidator;
use Illuminate\Routing\Matching\MethodValidator;
use Illuminate\Routing\Matching\SchemeValidator;
use Symfony\Component\Routing\Route as SymfonyRoute;
class Route
{
    protected $uri;
    protected $methods;
    protected $action;
    protected $defaults = array();
    protected $wheres = array();
    protected $parameters;
    protected $parameterNames;
    protected $compiled;
    protected static $validators;
    public function __construct($methods, $uri, $action)
    {
        $this->uri = $uri;
        $this->methods = (array) $methods;
        $this->action = $this->parseAction($action);
        if (in_array('GET', $this->methods) && !in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }
        if (isset($this->action['prefix'])) {
            $this->prefix($this->action['prefix']);
        }
    }
    public function run()
    {
        $parameters = array_filter($this->parameters(), function ($p) {
            return isset($p);
        });
        return call_user_func_array($this->action['uses'], $parameters);
    }
    public function matches(Request $request, $includingMethod = true)
    {
        $this->compileRoute();
        foreach ($this->getValidators() as $validator) {
            if (!$includingMethod && $validator instanceof MethodValidator) {
                continue;
            }
            if (!$validator->matches($this, $request)) {
                return false;
            }
        }
        return true;
    }
    protected function compileRoute()
    {
        $optionals = $this->extractOptionalParameters();
        $uri = preg_replace('/\\{(\\w+?)\\?\\}/', '{$1}', $this->uri);
        $this->compiled = with(new SymfonyRoute($uri, $optionals, $this->wheres, array(), $this->domain() ?: ''))->compile();
    }
    protected function extractOptionalParameters()
    {
        preg_match_all('/\\{(\\w+?)\\?\\}/', $this->uri, $matches);
        $optional = array();
        if (isset($matches[1])) {
            foreach ($matches[1] as $key) {
                $optional[$key] = null;
            }
        }
        return $optional;
    }
    public function beforeFilters()
    {
        if (!isset($this->action['before'])) {
            return array();
        }
        return $this->parseFilters($this->action['before']);
    }
    public function afterFilters()
    {
        if (!isset($this->action['after'])) {
            return array();
        }
        return $this->parseFilters($this->action['after']);
    }
    public static function parseFilters($filters)
    {
        return array_build(static::explodeFilters($filters), function ($key, $value) {
            return Route::parseFilter($value);
        });
    }
    protected static function explodeFilters($filters)
    {
        if (is_array($filters)) {
            return static::explodeArrayFilters($filters);
        }
        return array_map('trim', explode('|', $filters));
    }
    protected static function explodeArrayFilters(array $filters)
    {
        $results = array();
        foreach ($filters as $filter) {
            $results = array_merge($results, array_map('trim', explode('|', $filter)));
        }
        return $results;
    }
    public static function parseFilter($filter)
    {
        if (!str_contains($filter, ':')) {
            return array($filter, array());
        }
        return static::parseParameterFilter($filter);
    }
    protected static function parseParameterFilter($filter)
    {
        list($name, $parameters) = explode(':', $filter, 2);
        return array($name, explode(',', $parameters));
    }
    public function getParameter($name, $default = null)
    {
        return $this->parameter($name, $default);
    }
    public function parameter($name, $default = null)
    {
        return array_get($this->parameters(), $name, $default);
    }
    public function setParameter($name, $value)
    {
        $this->parameters();
        $this->parameters[$name] = $value;
    }
    public function forgetParameter($name)
    {
        $this->parameters();
        unset($this->parameters[$name]);
    }
    public function parameters()
    {
        if (isset($this->parameters)) {
            return array_map(function ($value) {
                return is_string($value) ? rawurldecode($value) : $value;
            }, $this->parameters);
        }
        throw new \LogicException('Route is not bound.');
    }
    public function parametersWithoutNulls()
    {
        return array_filter($this->parameters(), function ($p) {
            return !is_null($p);
        });
    }
    public function parameterNames()
    {
        if (isset($this->parameterNames)) {
            return $this->parameterNames;
        }
        return $this->parameterNames = $this->compileParameterNames();
    }
    protected function compileParameterNames()
    {
        preg_match_all('/\\{(.*?)\\}/', $this->domain() . $this->uri, $matches);
        return array_map(function ($m) {
            return trim($m, '?');
        }, $matches[1]);
    }
    public function bind(Request $request)
    {
        $this->compileRoute();
        $this->bindParameters($request);
        return $this;
    }
    public function bindParameters(Request $request)
    {
        $params = $this->matchToKeys(array_slice($this->bindPathParameters($request), 1));
        if (!is_null($this->compiled->getHostRegex())) {
            $params = $this->bindHostParameters($request, $params);
        }
        return $this->parameters = $this->replaceDefaults($params);
    }
    protected function bindPathParameters(Request $request)
    {
        preg_match($this->compiled->getRegex(), '/' . $request->decodedPath(), $matches);
        return $matches;
    }
    protected function bindHostParameters(Request $request, $parameters)
    {
        preg_match($this->compiled->getHostRegex(), $request->getHost(), $matches);
        return array_merge($this->matchToKeys(array_slice($matches, 1)), $parameters);
    }
    protected function matchToKeys(array $matches)
    {
        if (count($this->parameterNames()) == 0) {
            return array();
        }
        $parameters = array_intersect_key($matches, array_flip($this->parameterNames()));
        return array_filter($parameters, function ($value) {
            return is_string($value) && strlen($value) > 0;
        });
    }
    protected function replaceDefaults(array $parameters)
    {
        foreach ($parameters as $key => &$value) {
            $value = isset($value) ? $value : array_get($this->defaults, $key);
        }
        return $parameters;
    }
    protected function parseAction($action)
    {
        if (is_callable($action)) {
            return array('uses' => $action);
        } elseif (!isset($action['uses'])) {
            $action['uses'] = $this->findClosure($action);
        }
        return $action;
    }
    protected function findClosure(array $action)
    {
        return array_first($action, function ($key, $value) {
            return is_callable($value);
        });
    }
    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }
        return static::$validators = array(new MethodValidator(), new SchemeValidator(), new HostValidator(), new UriValidator());
    }
    public function before($filters)
    {
        return $this->addFilters('before', $filters);
    }
    public function after($filters)
    {
        return $this->addFilters('after', $filters);
    }
    protected function addFilters($type, $filters)
    {
        if (isset($this->action[$type])) {
            $this->action[$type] .= '|' . $filters;
        } else {
            $this->action[$type] = $filters;
        }
        return $this;
    }
    public function defaults($key, $value)
    {
        $this->defaults[$key] = $value;
        return $this;
    }
    public function where($name, $expression = null)
    {
        foreach ($this->parseWhere($name, $expression) as $name => $expression) {
            $this->wheres[$name] = $expression;
        }
        return $this;
    }
    protected function parseWhere($name, $expression)
    {
        return is_array($name) ? $name : array($name => $expression);
    }
    protected function whereArray(array $wheres)
    {
        foreach ($wheres as $name => $expression) {
            $this->where($name, $expression);
        }
        return $this;
    }
    public function prefix($prefix)
    {
        $this->uri = trim($prefix, '/') . '/' . trim($this->uri, '/');
        return $this;
    }
    public function getPath()
    {
        return $this->uri();
    }
    public function uri()
    {
        return $this->uri;
    }
    public function getMethods()
    {
        return $this->methods();
    }
    public function methods()
    {
        return $this->methods;
    }
    public function httpOnly()
    {
        return in_array('http', $this->action, true);
    }
    public function httpsOnly()
    {
        return $this->secure();
    }
    public function secure()
    {
        return in_array('https', $this->action, true);
    }
    public function domain()
    {
        return isset($this->action['domain']) ? $this->action['domain'] : null;
    }
    public function getUri()
    {
        return $this->uri;
    }
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }
    public function getPrefix()
    {
        return isset($this->action['prefix']) ? $this->action['prefix'] : null;
    }
    public function getName()
    {
        return isset($this->action['as']) ? $this->action['as'] : null;
    }
    public function getActionName()
    {
        return isset($this->action['controller']) ? $this->action['controller'] : 'Closure';
    }
    public function getAction()
    {
        return $this->action;
    }
    public function setAction(array $action)
    {
        $this->action = $action;
        return $this;
    }
    public function getCompiled()
    {
        return $this->compiled;
    }
}
namespace Illuminate\Routing;

use Countable;
use ArrayIterator;
use IteratorAggregate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
class RouteCollection implements Countable, IteratorAggregate
{
    protected $routes = array();
    protected $allRoutes = array();
    protected $nameList = array();
    protected $actionList = array();
    public function add(Route $route)
    {
        $this->addToCollections($route);
        $this->addLookups($route);
        return $route;
    }
    protected function addToCollections($route)
    {
        $domainAndUri = $route->domain() . $route->getUri();
        foreach ($route->methods() as $method) {
            $this->routes[$method][$domainAndUri] = $route;
        }
        $this->allRoutes[$method . $domainAndUri] = $route;
    }
    protected function addLookups($route)
    {
        $action = $route->getAction();
        if (isset($action['as'])) {
            $this->nameList[$action['as']] = $route;
        }
        if (isset($action['controller'])) {
            $this->addToActionList($action, $route);
        }
    }
    protected function addToActionList($action, $route)
    {
        if (!isset($this->actionList[$action['controller']])) {
            $this->actionList[$action['controller']] = $route;
        }
    }
    public function match(Request $request)
    {
        $routes = $this->get($request->getMethod());
        $route = $this->check($routes, $request);
        if (!is_null($route)) {
            return $route->bind($request);
        }
        $others = $this->checkForAlternateVerbs($request);
        if (count($others) > 0) {
            return $this->getOtherMethodsRoute($request, $others);
        }
        throw new NotFoundHttpException();
    }
    protected function checkForAlternateVerbs($request)
    {
        $methods = array_diff(Router::$verbs, array($request->getMethod()));
        $others = array();
        foreach ($methods as $method) {
            if (!is_null($this->check($this->get($method), $request, false))) {
                $others[] = $method;
            }
        }
        return $others;
    }
    protected function getOtherMethodsRoute($request, array $others)
    {
        if ($request->method() == 'OPTIONS') {
            return (new Route('OPTIONS', $request->path(), function () use($others) {
                return new Response('', 200, array('Allow' => implode(',', $others)));
            }))->bind($request);
        } else {
            $this->methodNotAllowed($others);
        }
    }
    protected function methodNotAllowed(array $others)
    {
        throw new MethodNotAllowedHttpException($others);
    }
    protected function check(array $routes, $request, $includingMethod = true)
    {
        return array_first($routes, function ($key, $value) use($request, $includingMethod) {
            return $value->matches($request, $includingMethod);
        });
    }
    protected function get($method = null)
    {
        if (is_null($method)) {
            return $this->getRoutes();
        }
        return array_get($this->routes, $method, array());
    }
    public function hasNamedRoute($name)
    {
        return !is_null($this->getByName($name));
    }
    public function getByName($name)
    {
        return isset($this->nameList[$name]) ? $this->nameList[$name] : null;
    }
    public function getByAction($action)
    {
        return isset($this->actionList[$action]) ? $this->actionList[$action] : null;
    }
    public function getRoutes()
    {
        return array_values($this->allRoutes);
    }
    public function getIterator()
    {
        return new ArrayIterator($this->getRoutes());
    }
    public function count()
    {
        return count($this->getRoutes());
    }
}
namespace Illuminate\Routing;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
class ControllerDispatcher
{
    protected $filterer;
    protected $container;
    public function __construct(RouteFiltererInterface $filterer, Container $container = null)
    {
        $this->filterer = $filterer;
        $this->container = $container;
    }
    public function dispatch(Route $route, Request $request, $controller, $method)
    {
        $instance = $this->makeController($controller);
        $this->assignAfter($instance, $route, $request, $method);
        $response = $this->before($instance, $route, $request, $method);
        if (is_null($response)) {
            $response = $this->call($instance, $route, $method);
        }
        return $response;
    }
    protected function makeController($controller)
    {
        Controller::setFilterer($this->filterer);
        return $this->container->make($controller);
    }
    protected function call($instance, $route, $method)
    {
        $parameters = $route->parametersWithoutNulls();
        return $instance->callAction($method, $parameters);
    }
    protected function before($instance, $route, $request, $method)
    {
        foreach ($instance->getBeforeFilters() as $filter) {
            if ($this->filterApplies($filter, $request, $method)) {
                $response = $this->callFilter($filter, $route, $request);
                if (!is_null($response)) {
                    return $response;
                }
            }
        }
    }
    protected function assignAfter($instance, $route, $request, $method)
    {
        foreach ($instance->getAfterFilters() as $filter) {
            if ($this->filterApplies($filter, $request, $method)) {
                $route->after($this->getAssignableAfter($filter));
            }
        }
    }
    protected function getAssignableAfter($filter)
    {
        return $filter['original'] instanceof Closure ? $filter['filter'] : $filter['original'];
    }
    protected function filterApplies($filter, $request, $method)
    {
        foreach (array('Only', 'Except', 'On') as $type) {
            if ($this->{"filterFails{$type}"}($filter, $request, $method)) {
                return false;
            }
        }
        return true;
    }
    protected function filterFailsOnly($filter, $request, $method)
    {
        if (!isset($filter['options']['only'])) {
            return false;
        }
        return !in_array($method, (array) $filter['options']['only']);
    }
    protected function filterFailsExcept($filter, $request, $method)
    {
        if (!isset($filter['options']['except'])) {
            return false;
        }
        return in_array($method, (array) $filter['options']['except']);
    }
    protected function filterFailsOn($filter, $request, $method)
    {
        $on = array_get($filter, 'options.on', null);
        if (is_null($on)) {
            return false;
        }
        if (is_string($on)) {
            $on = explode('|', $on);
        }
        return !in_array(strtolower($request->getMethod()), $on);
    }
    protected function callFilter($filter, $route, $request)
    {
        extract($filter);
        return $this->filterer->callRouteFilter($filter, $parameters, $route, $request);
    }
}
namespace Illuminate\Routing;

use Illuminate\Http\Request;
use InvalidArgumentException;
class UrlGenerator
{
    protected $routes;
    protected $request;
    protected $forcedRoot;
    protected $forceSchema;
    protected $dontEncode = array('%2F' => '/', '%40' => '@', '%3A' => ':', '%3B' => ';', '%2C' => ',', '%3D' => '=', '%2B' => '+', '%21' => '!', '%2A' => '*', '%7C' => '|');
    public function __construct(RouteCollection $routes, Request $request)
    {
        $this->routes = $routes;
        $this->setRequest($request);
    }
    public function full()
    {
        return $this->request->fullUrl();
    }
    public function current()
    {
        return $this->to($this->request->getPathInfo());
    }
    public function previous()
    {
        return $this->to($this->request->headers->get('referer'));
    }
    public function to($path, $extra = array(), $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }
        $scheme = $this->getScheme($secure);
        $tail = implode('/', array_map('rawurlencode', (array) $extra));
        $root = $this->getRootUrl($scheme);
        return $this->trimUrl($root, $path, $tail);
    }
    public function secure($path, $parameters = array())
    {
        return $this->to($path, $parameters, true);
    }
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }
        $root = $this->getRootUrl($this->getScheme($secure));
        return $this->removeIndex($root) . '/' . trim($path, '/');
    }
    protected function removeIndex($root)
    {
        $i = 'index.php';
        return str_contains($root, $i) ? str_replace('/' . $i, '', $root) : $root;
    }
    public function secureAsset($path)
    {
        return $this->asset($path, true);
    }
    protected function getScheme($secure)
    {
        if (is_null($secure)) {
            return $this->forceSchema ?: $this->request->getScheme() . '://';
        } else {
            return $secure ? 'https://' : 'http://';
        }
    }
    public function forceSchema($schema)
    {
        $this->forceSchema = $schema . '://';
    }
    public function route($name, $parameters = array(), $absolute = true, $route = null)
    {
        $route = $route ?: $this->routes->getByName($name);
        $parameters = (array) $parameters;
        if (!is_null($route)) {
            return $this->toRoute($route, $parameters, $absolute);
        } else {
            throw new InvalidArgumentException("Route [{$name}] not defined.");
        }
    }
    protected function toRoute($route, array $parameters, $absolute)
    {
        $domain = $this->getRouteDomain($route, $parameters);
        $uri = strtr(rawurlencode($this->trimUrl($root = $this->replaceRoot($route, $domain, $parameters), $this->replaceRouteParameters($route->uri(), $parameters))), $this->dontEncode) . $this->getRouteQueryString($parameters);
        return $absolute ? $uri : '/' . ltrim(str_replace($root, '', $uri), '/');
    }
    protected function replaceRoot($route, $domain, &$parameters)
    {
        return $this->replaceRouteParameters($this->getRouteRoot($route, $domain), $parameters);
    }
    protected function replaceRouteParameters($path, array &$parameters)
    {
        if (count($parameters)) {
            $path = preg_replace_sub('/\\{.*?\\}/', $parameters, $this->replaceNamedParameters($path, $parameters));
        }
        return trim(preg_replace('/\\{.*?\\?\\}/', '', $path), '/');
    }
    protected function replaceNamedParameters($path, &$parameters)
    {
        return preg_replace_callback('/\\{(.*?)\\??\\}/', function ($m) use(&$parameters) {
            return isset($parameters[$m[1]]) ? array_pull($parameters, $m[1]) : $m[0];
        }, $path);
    }
    protected function getRouteQueryString(array $parameters)
    {
        if (count($parameters) == 0) {
            return '';
        }
        $query = http_build_query($keyed = $this->getStringParameters($parameters));
        if (count($keyed) < count($parameters)) {
            $query .= '&' . implode('&', $this->getNumericParameters($parameters));
        }
        return '?' . trim($query, '&');
    }
    protected function getStringParameters(array $parameters)
    {
        return array_where($parameters, function ($k, $v) {
            return is_string($k);
        });
    }
    protected function getNumericParameters(array $parameters)
    {
        return array_where($parameters, function ($k, $v) {
            return is_numeric($k);
        });
    }
    protected function getRouteDomain($route, &$parameters)
    {
        return $route->domain() ? $this->formatDomain($route, $parameters) : null;
    }
    protected function formatDomain($route, &$parameters)
    {
        return $this->addPortToDomain($this->getDomainAndScheme($route));
    }
    protected function getDomainAndScheme($route)
    {
        return $this->getRouteScheme($route) . $route->domain();
    }
    protected function addPortToDomain($domain)
    {
        if (in_array($this->request->getPort(), array('80', '443'))) {
            return $domain;
        } else {
            return $domain .= ':' . $this->request->getPort();
        }
    }
    protected function getRouteRoot($route, $domain)
    {
        return $this->getRootUrl($this->getRouteScheme($route), $domain);
    }
    protected function getRouteScheme($route)
    {
        if ($route->httpOnly()) {
            return $this->getScheme(false);
        } elseif ($route->httpsOnly()) {
            return $this->getScheme(true);
        } else {
            return $this->getScheme(null);
        }
    }
    public function action($action, $parameters = array(), $absolute = true)
    {
        return $this->route($action, $parameters, $absolute, $this->routes->getByAction($action));
    }
    protected function getRootUrl($scheme, $root = null)
    {
        if (is_null($root)) {
            $root = $this->forcedRoot ?: $this->request->root();
        }
        $start = starts_with($root, 'http://') ? 'http://' : 'https://';
        return preg_replace('~' . $start . '~', $scheme, $root, 1);
    }
    public function forceRootUrl($root)
    {
        $this->forcedRoot = $root;
    }
    public function isValidUrl($path)
    {
        if (starts_with($path, array('#', '//', 'mailto:', 'tel:'))) {
            return true;
        }
        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }
    protected function trimUrl($root, $path, $tail = '')
    {
        return trim($root . '/' . trim($path . '/' . $tail, '/'), '/');
    }
    public function getRequest()
    {
        return $this->request;
    }
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
namespace Illuminate\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
interface ValidatorInterface
{
    public function matches(Route $route, Request $request);
}
namespace Illuminate\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
class HostValidator implements ValidatorInterface
{
    public function matches(Route $route, Request $request)
    {
        if (is_null($route->getCompiled()->getHostRegex())) {
            return true;
        }
        return preg_match($route->getCompiled()->getHostRegex(), $request->getHost());
    }
}
namespace Illuminate\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
class MethodValidator implements ValidatorInterface
{
    public function matches(Route $route, Request $request)
    {
        return in_array($request->getMethod(), $route->methods());
    }
}
namespace Illuminate\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
class SchemeValidator implements ValidatorInterface
{
    public function matches(Route $route, Request $request)
    {
        if ($route->httpOnly()) {
            return !$request->secure();
        } elseif ($route->secure()) {
            return $request->secure();
        }
        return true;
    }
}
namespace Illuminate\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
class UriValidator implements ValidatorInterface
{
    public function matches(Route $route, Request $request)
    {
        $path = $request->path() == '/' ? '/' : '/' . $request->path();
        return preg_match($route->getCompiled()->getRegex(), rawurldecode($path));
    }
}
namespace Illuminate\Workbench;

use Illuminate\Support\ServiceProvider;
use Illuminate\Workbench\Console\WorkbenchMakeCommand;
class WorkbenchServiceProvider extends ServiceProvider
{
    protected $defer = false;
    public function register()
    {
        $this->app->bindShared('package.creator', function ($app) {
            return new PackageCreator($app['files']);
        });
        $this->app->bindShared('command.workbench', function ($app) {
            return new WorkbenchMakeCommand($app['package.creator']);
        });
        $this->commands('command.workbench');
    }
    public function provides()
    {
        return array('package.creator', 'command.workbench');
    }
}
namespace Illuminate\Events;

use Illuminate\Container\Container;
class Dispatcher
{
    protected $container;
    protected $listeners = array();
    protected $wildcards = array();
    protected $sorted = array();
    protected $firing = array();
    public function __construct(Container $container = null)
    {
        $this->container = $container ?: new Container();
    }
    public function listen($events, $listener, $priority = 0)
    {
        foreach ((array) $events as $event) {
            if (str_contains($event, '*')) {
                $this->setupWildcardListen($event, $listener);
            } else {
                $this->listeners[$event][$priority][] = $this->makeListener($listener);
                unset($this->sorted[$event]);
            }
        }
    }
    protected function setupWildcardListen($event, $listener)
    {
        $this->wildcards[$event][] = $this->makeListener($listener);
    }
    public function hasListeners($eventName)
    {
        return isset($this->listeners[$eventName]);
    }
    public function queue($event, $payload = array())
    {
        $this->listen($event . '_queue', function () use($event, $payload) {
            $this->fire($event, $payload);
        });
    }
    public function subscribe($subscriber)
    {
        $subscriber = $this->resolveSubscriber($subscriber);
        $subscriber->subscribe($this);
    }
    protected function resolveSubscriber($subscriber)
    {
        if (is_string($subscriber)) {
            return $this->container->make($subscriber);
        }
        return $subscriber;
    }
    public function until($event, $payload = array())
    {
        return $this->fire($event, $payload, true);
    }
    public function flush($event)
    {
        $this->fire($event . '_queue');
    }
    public function firing()
    {
        return last($this->firing);
    }
    public function fire($event, $payload = array(), $halt = false)
    {
        $responses = array();
        if (!is_array($payload)) {
            $payload = array($payload);
        }
        $this->firing[] = $event;
        foreach ($this->getListeners($event) as $listener) {
            $response = call_user_func_array($listener, $payload);
            if (!is_null($response) && $halt) {
                array_pop($this->firing);
                return $response;
            }
            if ($response === false) {
                break;
            }
            $responses[] = $response;
        }
        array_pop($this->firing);
        return $halt ? null : $responses;
    }
    public function getListeners($eventName)
    {
        $wildcards = $this->getWildcardListeners($eventName);
        if (!isset($this->sorted[$eventName])) {
            $this->sortListeners($eventName);
        }
        return array_merge($this->sorted[$eventName], $wildcards);
    }
    protected function getWildcardListeners($eventName)
    {
        $wildcards = array();
        foreach ($this->wildcards as $key => $listeners) {
            if (str_is($key, $eventName)) {
                $wildcards = array_merge($wildcards, $listeners);
            }
        }
        return $wildcards;
    }
    protected function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();
        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }
    public function makeListener($listener)
    {
        if (is_string($listener)) {
            $listener = $this->createClassListener($listener);
        }
        return $listener;
    }
    public function createClassListener($listener)
    {
        $container = $this->container;
        return function () use($listener, $container) {
            $segments = explode('@', $listener);
            $method = count($segments) == 2 ? $segments[1] : 'handle';
            $callable = array($container->make($segments[0]), $method);
            $data = func_get_args();
            return call_user_func_array($callable, $data);
        };
    }
    public function forget($event)
    {
        unset($this->listeners[$event]);
        unset($this->sorted[$event]);
    }
}
namespace Illuminate\Database\Eloquent;

use DateTime;
use ArrayAccess;
use Carbon\Carbon;
use LogicException;
use JsonSerializable;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
abstract class Model implements ArrayAccess, ArrayableInterface, JsonableInterface, JsonSerializable
{
    protected $connection;
    protected $table;
    protected $primaryKey = 'id';
    protected $perPage = 15;
    public $incrementing = true;
    public $timestamps = true;
    protected $attributes = array();
    protected $original = array();
    protected $relations = array();
    protected $hidden = array();
    protected $visible = array();
    protected $appends = array();
    protected $fillable = array();
    protected $guarded = array('*');
    protected $dates = array();
    protected $touches = array();
    protected $observables = array();
    protected $with = array();
    protected $morphClass;
    public $exists = false;
    public static $snakeAttributes = true;
    protected static $resolver;
    protected static $dispatcher;
    protected static $booted = array();
    protected static $globalScopes = array();
    protected static $unguarded = false;
    protected static $mutatorCache = array();
    public static $manyMethods = array('belongsToMany', 'morphToMany', 'morphedByMany');
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    public function __construct(array $attributes = array())
    {
        $this->bootIfNotBooted();
        $this->syncOriginal();
        $this->fill($attributes);
    }
    protected function bootIfNotBooted()
    {
        $class = get_class($this);
        if (!isset(static::$booted[$class])) {
            static::$booted[$class] = true;
            $this->fireModelEvent('booting', false);
            static::boot();
            $this->fireModelEvent('booted', false);
        }
    }
    protected static function boot()
    {
        $class = get_called_class();
        static::$mutatorCache[$class] = array();
        foreach (get_class_methods($class) as $method) {
            if (preg_match('/^get(.+)Attribute$/', $method, $matches)) {
                if (static::$snakeAttributes) {
                    $matches[1] = snake_case($matches[1]);
                }
                static::$mutatorCache[$class][] = lcfirst($matches[1]);
            }
        }
        static::bootTraits();
    }
    protected static function bootTraits()
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'boot' . class_basename($trait))) {
                forward_static_call(array(get_called_class(), $method));
            }
        }
    }
    public static function addGlobalScope(ScopeInterface $scope)
    {
        static::$globalScopes[get_called_class()][get_class($scope)] = $scope;
    }
    public static function hasGlobalScope($scope)
    {
        return !is_null(static::getGlobalScope($scope));
    }
    public static function getGlobalScope($scope)
    {
        return array_first(static::$globalScopes[get_called_class()], function ($key, $value) use($scope) {
            return $scope instanceof $value;
        });
    }
    public function getGlobalScopes()
    {
        return array_get(static::$globalScopes, get_class($this), array());
    }
    public static function observe($class)
    {
        $instance = new static();
        $className = get_class($class);
        foreach ($instance->getObservableEvents() as $event) {
            if (method_exists($class, $event)) {
                static::registerModelEvent($event, $className . '@' . $event);
            }
        }
    }
    public function fill(array $attributes)
    {
        $totallyGuarded = $this->totallyGuarded();
        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            $key = $this->removeTableFromKey($key);
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } elseif ($totallyGuarded) {
                throw new MassAssignmentException($key);
            }
        }
        return $this;
    }
    protected function fillableFromArray(array $attributes)
    {
        if (count($this->fillable) > 0 && !static::$unguarded) {
            return array_intersect_key($attributes, array_flip($this->fillable));
        }
        return $attributes;
    }
    public function newInstance($attributes = array(), $exists = false)
    {
        $model = new static((array) $attributes);
        $model->exists = $exists;
        return $model;
    }
    public function newFromBuilder($attributes = array())
    {
        $instance = $this->newInstance(array(), true);
        $instance->setRawAttributes((array) $attributes, true);
        return $instance;
    }
    public static function hydrate(array $items, $connection = null)
    {
        $collection = with($instance = new static())->newCollection();
        foreach ($items as $item) {
            $model = $instance->newFromBuilder($item);
            if (!is_null($connection)) {
                $model->setConnection($connection);
            }
            $collection->push($model);
        }
        return $collection;
    }
    public static function hydrateRaw($query, $bindings = array(), $connection = null)
    {
        $instance = new static();
        if (!is_null($connection)) {
            $instance->setConnection($connection);
        }
        $items = $instance->getConnection()->select($query, $bindings);
        return static::hydrate($items, $connection);
    }
    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }
    public static function firstOrCreate(array $attributes)
    {
        if (!is_null($instance = static::where($attributes)->first())) {
            return $instance;
        }
        return static::create($attributes);
    }
    public static function firstOrNew(array $attributes)
    {
        if (!is_null($instance = static::where($attributes)->first())) {
            return $instance;
        }
        return new static($attributes);
    }
    public static function updateOrCreate(array $attributes, array $values = array())
    {
        $instance = static::firstOrNew($attributes);
        $instance->fill($values)->save();
        return $instance;
    }
    protected static function firstByAttributes($attributes)
    {
        return static::where($attributes)->first();
    }
    public static function query()
    {
        return (new static())->newQuery();
    }
    public static function on($connection = null)
    {
        $instance = new static();
        $instance->setConnection($connection);
        return $instance->newQuery();
    }
    public static function all($columns = array('*'))
    {
        $instance = new static();
        return $instance->newQuery()->get($columns);
    }
    public static function find($id, $columns = array('*'))
    {
        if (is_array($id) && empty($id)) {
            return new Collection();
        }
        $instance = new static();
        return $instance->newQuery()->find($id, $columns);
    }
    public static function findOrNew($id, $columns = array('*'))
    {
        if (!is_null($model = static::find($id, $columns))) {
            return $model;
        }
        return new static();
    }
    public static function findOrFail($id, $columns = array('*'))
    {
        if (!is_null($model = static::find($id, $columns))) {
            return $model;
        }
        throw (new ModelNotFoundException())->setModel(get_called_class());
    }
    public function load($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }
        $query = $this->newQuery()->with($relations);
        $query->eagerLoadRelations(array($this));
        return $this;
    }
    public static function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }
        $instance = new static();
        return $instance->newQuery()->with($relations);
    }
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $instance = new $related();
        $localKey = $localKey ?: $this->getKeyName();
        return new HasOne($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey);
    }
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = new $related();
        list($type, $id) = $this->getMorphs($name, $type, $id);
        $table = $instance->getTable();
        $localKey = $localKey ?: $this->getKeyName();
        return new MorphOne($instance->newQuery(), $this, $table . '.' . $type, $table . '.' . $id, $localKey);
    }
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            list(, $caller) = debug_backtrace(false);
            $relation = $caller['function'];
        }
        if (is_null($foreignKey)) {
            $foreignKey = snake_case($relation) . '_id';
        }
        $instance = new $related();
        $query = $instance->newQuery();
        $otherKey = $otherKey ?: $instance->getKeyName();
        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }
    public function morphTo($name = null, $type = null, $id = null)
    {
        if (is_null($name)) {
            list(, $caller) = debug_backtrace(false);
            $name = snake_case($caller['function']);
        }
        list($type, $id) = $this->getMorphs($name, $type, $id);
        if (is_null($class = $this->{$type})) {
            return new MorphTo($this->newQuery(), $this, $id, null, $type, $name);
        } else {
            $instance = new $class();
            return new MorphTo($instance->newQuery(), $this, $id, $instance->getKeyName(), $type, $name);
        }
    }
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $instance = new $related();
        $localKey = $localKey ?: $this->getKeyName();
        return new HasMany($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey);
    }
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null)
    {
        $through = new $through();
        $firstKey = $firstKey ?: $this->getForeignKey();
        $secondKey = $secondKey ?: $through->getForeignKey();
        return new HasManyThrough((new $related())->newQuery(), $this, $through, $firstKey, $secondKey);
    }
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = new $related();
        list($type, $id) = $this->getMorphs($name, $type, $id);
        $table = $instance->getTable();
        $localKey = $localKey ?: $this->getKeyName();
        return new MorphMany($instance->newQuery(), $this, $table . '.' . $type, $table . '.' . $id, $localKey);
    }
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $instance = new $related();
        $otherKey = $otherKey ?: $instance->getForeignKey();
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }
        $query = $instance->newQuery();
        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false)
    {
        $caller = $this->getBelongsToManyCaller();
        $foreignKey = $foreignKey ?: $name . '_id';
        $instance = new $related();
        $otherKey = $otherKey ?: $instance->getForeignKey();
        $query = $instance->newQuery();
        $table = $table ?: str_plural($name);
        return new MorphToMany($query, $this, $name, $table, $foreignKey, $otherKey, $caller, $inverse);
    }
    public function morphedByMany($related, $name, $table = null, $foreignKey = null, $otherKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $otherKey = $otherKey ?: $name . '_id';
        return $this->morphToMany($related, $name, $table, $foreignKey, $otherKey, true);
    }
    protected function getBelongsToManyCaller()
    {
        $self = __FUNCTION__;
        $caller = array_first(debug_backtrace(false), function ($key, $trace) use($self) {
            $caller = $trace['function'];
            return !in_array($caller, Model::$manyMethods) && $caller != $self;
        });
        return !is_null($caller) ? $caller['function'] : null;
    }
    public function joiningTable($related)
    {
        $base = snake_case(class_basename($this));
        $related = snake_case(class_basename($related));
        $models = array($related, $base);
        sort($models);
        return strtolower(implode('_', $models));
    }
    public static function destroy($ids)
    {
        $count = 0;
        $ids = is_array($ids) ? $ids : func_get_args();
        $instance = new static();
        $key = $instance->getKeyName();
        foreach ($instance->whereIn($key, $ids)->get() as $model) {
            if ($model->delete()) {
                $count++;
            }
        }
        return $count;
    }
    public function delete()
    {
        if (is_null($this->primaryKey)) {
            throw new \Exception('No primary key defined on model.');
        }
        if ($this->exists) {
            if ($this->fireModelEvent('deleting') === false) {
                return false;
            }
            $this->touchOwners();
            $this->performDeleteOnModel();
            $this->exists = false;
            $this->fireModelEvent('deleted', false);
            return true;
        }
    }
    protected function performDeleteOnModel()
    {
        $this->newQuery()->where($this->getKeyName(), $this->getKey())->delete();
    }
    public static function saving($callback)
    {
        static::registerModelEvent('saving', $callback);
    }
    public static function saved($callback)
    {
        static::registerModelEvent('saved', $callback);
    }
    public static function updating($callback)
    {
        static::registerModelEvent('updating', $callback);
    }
    public static function updated($callback)
    {
        static::registerModelEvent('updated', $callback);
    }
    public static function creating($callback)
    {
        static::registerModelEvent('creating', $callback);
    }
    public static function created($callback)
    {
        static::registerModelEvent('created', $callback);
    }
    public static function deleting($callback)
    {
        static::registerModelEvent('deleting', $callback);
    }
    public static function deleted($callback)
    {
        static::registerModelEvent('deleted', $callback);
    }
    public static function flushEventListeners()
    {
        if (!isset(static::$dispatcher)) {
            return;
        }
        $instance = new static();
        foreach ($instance->getObservableEvents() as $event) {
            static::$dispatcher->forget("eloquent.{$event}: " . get_called_class());
        }
    }
    protected static function registerModelEvent($event, $callback)
    {
        if (isset(static::$dispatcher)) {
            $name = get_called_class();
            static::$dispatcher->listen("eloquent.{$event}: {$name}", $callback);
        }
    }
    public function getObservableEvents()
    {
        return array_merge(array('creating', 'created', 'updating', 'updated', 'deleting', 'deleted', 'saving', 'saved', 'restoring', 'restored'), $this->observables);
    }
    protected function increment($column, $amount = 1)
    {
        return $this->incrementOrDecrement($column, $amount, 'increment');
    }
    protected function decrement($column, $amount = 1)
    {
        return $this->incrementOrDecrement($column, $amount, 'decrement');
    }
    protected function incrementOrDecrement($column, $amount, $method)
    {
        $query = $this->newQuery();
        if (!$this->exists) {
            return $query->{$method}($column, $amount);
        }
        $this->incrementOrDecrementAttributeValue($column, $amount, $method);
        return $query->where($this->getKeyName(), $this->getKey())->{$method}($column, $amount);
    }
    protected function incrementOrDecrementAttributeValue($column, $amount, $method)
    {
        $this->{$column} = $this->{$column} + ($method == 'increment' ? $amount : $amount * -1);
        $this->syncOriginalAttribute($column);
    }
    public function update(array $attributes = array())
    {
        if (!$this->exists) {
            return $this->newQuery()->update($attributes);
        }
        return $this->fill($attributes)->save();
    }
    public function push()
    {
        if (!$this->save()) {
            return false;
        }
        foreach ($this->relations as $models) {
            foreach (Collection::make($models) as $model) {
                if (!$model->push()) {
                    return false;
                }
            }
        }
        return true;
    }
    public function save(array $options = array())
    {
        $query = $this->newQueryWithoutScopes();
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }
        if ($this->exists) {
            $saved = $this->performUpdate($query);
        } else {
            $saved = $this->performInsert($query);
        }
        if ($saved) {
            $this->finishSave($options);
        }
        return $saved;
    }
    protected function finishSave(array $options)
    {
        $this->fireModelEvent('saved', false);
        $this->syncOriginal();
        if (array_get($options, 'touch', true)) {
            $this->touchOwners();
        }
    }
    protected function performUpdate(Builder $query)
    {
        $dirty = $this->getDirty();
        if (count($dirty) > 0) {
            if ($this->fireModelEvent('updating') === false) {
                return false;
            }
            if ($this->timestamps) {
                $this->updateTimestamps();
            }
            $dirty = $this->getDirty();
            if (count($dirty) > 0) {
                $this->setKeysForSaveQuery($query)->update($dirty);
                $this->fireModelEvent('updated', false);
            }
        }
        return true;
    }
    protected function performInsert(Builder $query)
    {
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }
        if ($this->timestamps) {
            $this->updateTimestamps();
        }
        $attributes = $this->attributes;
        if ($this->incrementing) {
            $this->insertAndSetId($query, $attributes);
        } else {
            $query->insert($attributes);
        }
        $this->exists = true;
        $this->fireModelEvent('created', false);
        return true;
    }
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $id = $query->insertGetId($attributes, $keyName = $this->getKeyName());
        $this->setAttribute($keyName, $id);
    }
    public function touchOwners()
    {
        foreach ($this->touches as $relation) {
            $this->{$relation}()->touch();
        }
    }
    public function touches($relation)
    {
        return in_array($relation, $this->touches);
    }
    protected function fireModelEvent($event, $halt = true)
    {
        if (!isset(static::$dispatcher)) {
            return true;
        }
        $event = "eloquent.{$event}: " . get_class($this);
        $method = $halt ? 'until' : 'fire';
        return static::$dispatcher->{$method}($event, $this);
    }
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());
        return $query;
    }
    protected function getKeyForSaveQuery()
    {
        if (isset($this->original[$this->getKeyName()])) {
            return $this->original[$this->getKeyName()];
        } else {
            return $this->getAttribute($this->getKeyName());
        }
    }
    public function touch()
    {
        $this->updateTimestamps();
        return $this->save();
    }
    protected function updateTimestamps()
    {
        $time = $this->freshTimestamp();
        if (!$this->isDirty(static::UPDATED_AT)) {
            $this->setUpdatedAt($time);
        }
        if (!$this->exists && !$this->isDirty(static::CREATED_AT)) {
            $this->setCreatedAt($time);
        }
    }
    public function setCreatedAt($value)
    {
        $this->{static::CREATED_AT} = $value;
    }
    public function setUpdatedAt($value)
    {
        $this->{static::UPDATED_AT} = $value;
    }
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }
    public function freshTimestamp()
    {
        return new Carbon();
    }
    public function freshTimestampString()
    {
        return $this->fromDateTime($this->freshTimestamp());
    }
    public function newQuery()
    {
        $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        return $this->applyGlobalScopes($builder);
    }
    public function newQueryWithoutScope($scope)
    {
        $this->getGlobalScope($scope)->remove($builder = $this->newQuery(), $this);
        return $builder;
    }
    public function newQueryWithoutScopes()
    {
        return $this->removeGlobalScopes($this->newQuery());
    }
    public function applyGlobalScopes($builder)
    {
        foreach ($this->getGlobalScopes() as $scope) {
            $scope->apply($builder, $this);
        }
        return $builder;
    }
    public function removeGlobalScopes($builder)
    {
        foreach ($this->getGlobalScopes() as $scope) {
            $scope->remove($builder, $this);
        }
        return $builder;
    }
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();
        $grammar = $conn->getQueryGrammar();
        return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
    }
    public function newCollection(array $models = array())
    {
        return new Collection($models);
    }
    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        return new Pivot($parent, $attributes, $table, $exists);
    }
    public function getTable()
    {
        if (isset($this->table)) {
            return $this->table;
        }
        return str_replace('\\', '', snake_case(str_plural(class_basename($this))));
    }
    public function setTable($table)
    {
        $this->table = $table;
    }
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }
    public function getKeyName()
    {
        return $this->primaryKey;
    }
    public function getQualifiedKeyName()
    {
        return $this->getTable() . '.' . $this->getKeyName();
    }
    public function usesTimestamps()
    {
        return $this->timestamps;
    }
    protected function getMorphs($name, $type, $id)
    {
        $type = $type ?: $name . '_type';
        $id = $id ?: $name . '_id';
        return array($type, $id);
    }
    public function getMorphClass()
    {
        return $this->morphClass ?: get_class($this);
    }
    public function getPerPage()
    {
        return $this->perPage;
    }
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }
    public function getForeignKey()
    {
        return snake_case(class_basename($this)) . '_id';
    }
    public function getHidden()
    {
        return $this->hidden;
    }
    public function setHidden(array $hidden)
    {
        $this->hidden = $hidden;
    }
    public function setVisible(array $visible)
    {
        $this->visible = $visible;
    }
    public function setAppends(array $appends)
    {
        $this->appends = $appends;
    }
    public function getFillable()
    {
        return $this->fillable;
    }
    public function fillable(array $fillable)
    {
        $this->fillable = $fillable;
        return $this;
    }
    public function guard(array $guarded)
    {
        $this->guarded = $guarded;
        return $this;
    }
    public static function unguard()
    {
        static::$unguarded = true;
    }
    public static function reguard()
    {
        static::$unguarded = false;
    }
    public static function setUnguardState($state)
    {
        static::$unguarded = $state;
    }
    public function isFillable($key)
    {
        if (static::$unguarded) {
            return true;
        }
        if (in_array($key, $this->fillable)) {
            return true;
        }
        if ($this->isGuarded($key)) {
            return false;
        }
        return empty($this->fillable) && !starts_with($key, '_');
    }
    public function isGuarded($key)
    {
        return in_array($key, $this->guarded) || $this->guarded == array('*');
    }
    public function totallyGuarded()
    {
        return count($this->fillable) == 0 && $this->guarded == array('*');
    }
    protected function removeTableFromKey($key)
    {
        if (!str_contains($key, '.')) {
            return $key;
        }
        return last(explode('.', $key));
    }
    public function getTouchedRelations()
    {
        return $this->touches;
    }
    public function setTouchedRelations(array $touches)
    {
        $this->touches = $touches;
    }
    public function getIncrementing()
    {
        return $this->incrementing;
    }
    public function setIncrementing($value)
    {
        $this->incrementing = $value;
    }
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
    public function jsonSerialize()
    {
        return $this->toArray();
    }
    public function toArray()
    {
        $attributes = $this->attributesToArray();
        return array_merge($attributes, $this->relationsToArray());
    }
    public function attributesToArray()
    {
        $attributes = $this->getArrayableAttributes();
        foreach ($this->getDates() as $key) {
            if (!isset($attributes[$key])) {
                continue;
            }
            $attributes[$key] = (string) $this->asDateTime($attributes[$key]);
        }
        foreach ($this->getMutatedAttributes() as $key) {
            if (!array_key_exists($key, $attributes)) {
                continue;
            }
            $attributes[$key] = $this->mutateAttributeForArray($key, $attributes[$key]);
        }
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }
        return $attributes;
    }
    protected function getArrayableAttributes()
    {
        return $this->getArrayableItems($this->attributes);
    }
    protected function getArrayableAppends()
    {
        if (!count($this->appends)) {
            return array();
        }
        return $this->getArrayableItems(array_combine($this->appends, $this->appends));
    }
    public function relationsToArray()
    {
        $attributes = array();
        foreach ($this->getArrayableRelations() as $key => $value) {
            if (in_array($key, $this->hidden)) {
                continue;
            }
            if ($value instanceof ArrayableInterface) {
                $relation = $value->toArray();
            } elseif (is_null($value)) {
                $relation = $value;
            }
            if (static::$snakeAttributes) {
                $key = snake_case($key);
            }
            if (isset($relation) || is_null($value)) {
                $attributes[$key] = $relation;
            }
        }
        return $attributes;
    }
    protected function getArrayableRelations()
    {
        return $this->getArrayableItems($this->relations);
    }
    protected function getArrayableItems(array $values)
    {
        if (count($this->visible) > 0) {
            return array_intersect_key($values, array_flip($this->visible));
        }
        return array_diff_key($values, array_flip($this->hidden));
    }
    public function getAttribute($key)
    {
        $inAttributes = array_key_exists($key, $this->attributes);
        if ($inAttributes || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }
        $camelKey = camel_case($key);
        if (method_exists($this, $camelKey)) {
            return $this->getRelationshipFromMethod($key, $camelKey);
        }
    }
    protected function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        } elseif (in_array($key, $this->getDates())) {
            if ($value) {
                return $this->asDateTime($value);
            }
        }
        return $value;
    }
    protected function getAttributeFromArray($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }
    protected function getRelationshipFromMethod($key, $camelKey)
    {
        $relations = $this->{$camelKey}();
        if (!$relations instanceof Relation) {
            throw new LogicException('Relationship method must return an object of type ' . 'Illuminate\\Database\\Eloquent\\Relations\\Relation');
        }
        return $this->relations[$key] = $relations->getResults();
    }
    public function hasGetMutator($key)
    {
        return method_exists($this, 'get' . studly_case($key) . 'Attribute');
    }
    protected function mutateAttribute($key, $value)
    {
        return $this->{'get' . studly_case($key) . 'Attribute'}($value);
    }
    protected function mutateAttributeForArray($key, $value)
    {
        $value = $this->mutateAttribute($key, $value);
        return $value instanceof ArrayableInterface ? $value->toArray() : $value;
    }
    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set' . studly_case($key) . 'Attribute';
            return $this->{$method}($value);
        } elseif (in_array($key, $this->getDates()) && $value) {
            $value = $this->fromDateTime($value);
        }
        $this->attributes[$key] = $value;
    }
    public function hasSetMutator($key)
    {
        return method_exists($this, 'set' . studly_case($key) . 'Attribute');
    }
    public function getDates()
    {
        $defaults = array(static::CREATED_AT, static::UPDATED_AT);
        return array_merge($this->dates, $defaults);
    }
    public function fromDateTime($value)
    {
        $format = $this->getDateFormat();
        if ($value instanceof DateTime) {
            
        } elseif (is_numeric($value)) {
            $value = Carbon::createFromTimestamp($value);
        } elseif (preg_match('/^(\\d{4})-(\\d{2})-(\\d{2})$/', $value)) {
            $value = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } elseif (!$value instanceof DateTime) {
            $value = Carbon::createFromFormat($format, $value);
        }
        return $value->format($format);
    }
    protected function asDateTime($value)
    {
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        } elseif (preg_match('/^(\\d{4})-(\\d{2})-(\\d{2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } elseif (!$value instanceof DateTime) {
            $format = $this->getDateFormat();
            return Carbon::createFromFormat($format, $value);
        }
        return Carbon::instance($value);
    }
    protected function getDateFormat()
    {
        return $this->getConnection()->getQueryGrammar()->getDateFormat();
    }
    public function replicate(array $except = null)
    {
        $except = $except ?: array($this->getKeyName(), $this->getCreatedAtColumn(), $this->getUpdatedAtColumn());
        $attributes = array_except($this->attributes, $except);
        with($instance = new static())->setRawAttributes($attributes);
        return $instance->setRelations($this->relations);
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;
        if ($sync) {
            $this->syncOriginal();
        }
    }
    public function getOriginal($key = null, $default = null)
    {
        return array_get($this->original, $key, $default);
    }
    public function syncOriginal()
    {
        $this->original = $this->attributes;
        return $this;
    }
    public function syncOriginalAttribute($attribute)
    {
        $this->original[$attribute] = $this->attributes[$attribute];
        return $this;
    }
    public function isDirty($attribute = null)
    {
        $dirty = $this->getDirty();
        if (is_null($attribute)) {
            return count($dirty) > 0;
        } else {
            return array_key_exists($attribute, $dirty);
        }
    }
    public function getDirty()
    {
        $dirty = array();
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key] && !$this->originalIsNumericallyEquivalent($key)) {
                $dirty[$key] = $value;
            }
        }
        return $dirty;
    }
    protected function originalIsNumericallyEquivalent($key)
    {
        $current = $this->attributes[$key];
        $original = $this->original[$key];
        return is_numeric($current) && is_numeric($original) && strcmp((string) $current, (string) $original) === 0;
    }
    public function getRelations()
    {
        return $this->relations;
    }
    public function getRelation($relation)
    {
        return $this->relations[$relation];
    }
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;
        return $this;
    }
    public function setRelations(array $relations)
    {
        $this->relations = $relations;
        return $this;
    }
    public function getConnection()
    {
        return static::resolveConnection($this->connection);
    }
    public function getConnectionName()
    {
        return $this->connection;
    }
    public function setConnection($name)
    {
        $this->connection = $name;
        return $this;
    }
    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }
    public static function getConnectionResolver()
    {
        return static::$resolver;
    }
    public static function setConnectionResolver(Resolver $resolver)
    {
        static::$resolver = $resolver;
    }
    public static function unsetConnectionResolver()
    {
        static::$resolver = null;
    }
    public static function getEventDispatcher()
    {
        return static::$dispatcher;
    }
    public static function setEventDispatcher(Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }
    public static function unsetEventDispatcher()
    {
        static::$dispatcher = null;
    }
    public function getMutatedAttributes()
    {
        $class = get_class($this);
        if (isset(static::$mutatorCache[$class])) {
            return static::$mutatorCache[$class];
        }
        return array();
    }
    public function __get($key)
    {
        return $this->getAttribute($key);
    }
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }
    public function __isset($key)
    {
        return isset($this->attributes[$key]) || isset($this->relations[$key]) || $this->hasGetMutator($key) && !is_null($this->getAttributeValue($key));
    }
    public function __unset($key)
    {
        unset($this->attributes[$key]);
        unset($this->relations[$key]);
    }
    public function __call($method, $parameters)
    {
        if (in_array($method, array('increment', 'decrement'))) {
            return call_user_func_array(array($this, $method), $parameters);
        }
        $query = $this->newQuery();
        return call_user_func_array(array($query, $method), $parameters);
    }
    public static function __callStatic($method, $parameters)
    {
        $instance = new static();
        return call_user_func_array(array($instance, $method), $parameters);
    }
    public function __toString()
    {
        return $this->toJson();
    }
    public function __wakeup()
    {
        $this->bootIfNotBooted();
    }
}
namespace Illuminate\Support\Contracts;

interface ArrayableInterface
{
    public function toArray();
}
namespace Illuminate\Support\Contracts;

interface JsonableInterface
{
    public function toJson($options = 0);
}
namespace Illuminate\Database;

use Illuminate\Database\Connectors\ConnectionFactory;
class DatabaseManager implements ConnectionResolverInterface
{
    protected $app;
    protected $factory;
    protected $connections = array();
    protected $extensions = array();
    public function __construct($app, ConnectionFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();
        if (!isset($this->connections[$name])) {
            $connection = $this->makeConnection($name);
            $this->connections[$name] = $this->prepare($connection);
        }
        return $this->connections[$name];
    }
    public function purge($name = null)
    {
        $this->disconnect($name);
        unset($this->connections[$name]);
    }
    public function disconnect($name = null)
    {
        if (isset($this->connections[$name = $name ?: $this->getDefaultConnection()])) {
            $this->connections[$name]->disconnect();
        }
    }
    public function reconnect($name = null)
    {
        $this->disconnect($name = $name ?: $this->getDefaultConnection());
        if (!isset($this->connections[$name])) {
            return $this->connection($name);
        } else {
            return $this->refreshPdoConnections($name);
        }
    }
    protected function refreshPdoConnections($name)
    {
        $fresh = $this->makeConnection($name);
        return $this->connections[$name]->setPdo($fresh->getPdo())->setReadPdo($fresh->getReadPdo());
    }
    protected function makeConnection($name)
    {
        $config = $this->getConfig($name);
        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config, $name);
        }
        $driver = $config['driver'];
        if (isset($this->extensions[$driver])) {
            return call_user_func($this->extensions[$driver], $config, $name);
        }
        return $this->factory->make($config, $name);
    }
    protected function prepare(Connection $connection)
    {
        $connection->setFetchMode($this->app['config']['database.fetch']);
        if ($this->app->bound('events')) {
            $connection->setEventDispatcher($this->app['events']);
        }
        $app = $this->app;
        $connection->setCacheManager(function () use($app) {
            return $app['cache'];
        });
        $connection->setPaginator(function () use($app) {
            return $app['paginator'];
        });
        $connection->setReconnector(function ($connection) {
            $this->reconnect($connection->getName());
        });
        return $connection;
    }
    protected function getConfig($name)
    {
        $name = $name ?: $this->getDefaultConnection();
        $connections = $this->app['config']['database.connections'];
        if (is_null($config = array_get($connections, $name))) {
            throw new \InvalidArgumentException("Database [{$name}] not configured.");
        }
        return $config;
    }
    public function getDefaultConnection()
    {
        return $this->app['config']['database.default'];
    }
    public function setDefaultConnection($name)
    {
        $this->app['config']['database.default'] = $name;
    }
    public function extend($name, callable $resolver)
    {
        $this->extensions[$name] = $resolver;
    }
    public function getConnections()
    {
        return $this->connections;
    }
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->connection(), $method), $parameters);
    }
}
namespace Illuminate\Database;

interface ConnectionResolverInterface
{
    public function connection($name = null);
    public function getDefaultConnection();
    public function setDefaultConnection($name);
}
namespace Illuminate\Database\Connectors;

use PDO;
use Illuminate\Container\Container;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SqlServerConnection;
class ConnectionFactory
{
    protected $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function make(array $config, $name = null)
    {
        $config = $this->parseConfig($config, $name);
        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        } else {
            return $this->createSingleConnection($config);
        }
    }
    protected function createSingleConnection(array $config)
    {
        $pdo = $this->createConnector($config)->connect($config);
        return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
    }
    protected function createReadWriteConnection(array $config)
    {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));
        return $connection->setReadPdo($this->createReadPdo($config));
    }
    protected function createReadPdo(array $config)
    {
        $readConfig = $this->getReadConfig($config);
        return $this->createConnector($readConfig)->connect($readConfig);
    }
    protected function getReadConfig(array $config)
    {
        $readConfig = $this->getReadWriteConfig($config, 'read');
        return $this->mergeReadWriteConfig($config, $readConfig);
    }
    protected function getWriteConfig(array $config)
    {
        $writeConfig = $this->getReadWriteConfig($config, 'write');
        return $this->mergeReadWriteConfig($config, $writeConfig);
    }
    protected function getReadWriteConfig(array $config, $type)
    {
        if (isset($config[$type][0])) {
            return $config[$type][array_rand($config[$type])];
        } else {
            return $config[$type];
        }
    }
    protected function mergeReadWriteConfig(array $config, array $merge)
    {
        return array_except(array_merge($config, $merge), array('read', 'write'));
    }
    protected function parseConfig(array $config, $name)
    {
        return array_add(array_add($config, 'prefix', ''), 'name', $name);
    }
    public function createConnector(array $config)
    {
        if (!isset($config['driver'])) {
            throw new \InvalidArgumentException('A driver must be specified.');
        }
        if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }
        switch ($config['driver']) {
            case 'mysql':
                return new MySqlConnector();
            case 'pgsql':
                return new PostgresConnector();
            case 'sqlite':
                return new SQLiteConnector();
            case 'sqlsrv':
                return new SqlServerConnector();
        }
        throw new \InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }
    protected function createConnection($driver, PDO $connection, $database, $prefix = '', array $config = array())
    {
        if ($this->container->bound($key = "db.connection.{$driver}")) {
            return $this->container->make($key, array($connection, $database, $prefix, $config));
        }
        switch ($driver) {
            case 'mysql':
                return new MySqlConnection($connection, $database, $prefix, $config);
            case 'pgsql':
                return new PostgresConnection($connection, $database, $prefix, $config);
            case 'sqlite':
                return new SQLiteConnection($connection, $database, $prefix, $config);
            case 'sqlsrv':
                return new SqlServerConnection($connection, $database, $prefix, $config);
        }
        throw new \InvalidArgumentException("Unsupported driver [{$driver}]");
    }
}
namespace Illuminate\Session;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface as BaseSessionInterface;
interface SessionInterface extends BaseSessionInterface
{
    public function getHandler();
    public function handlerNeedsRequest();
    public function setRequestOnHandler(Request $request);
}
namespace Illuminate\Session;

use Closure;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
class Middleware implements HttpKernelInterface
{
    protected $app;
    protected $manager;
    protected $reject;
    public function __construct(HttpKernelInterface $app, SessionManager $manager, Closure $reject = null)
    {
        $this->app = $app;
        $this->reject = $reject;
        $this->manager = $manager;
    }
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->checkRequestForArraySessions($request);
        if ($this->sessionConfigured()) {
            $session = $this->startSession($request);
            $request->setSession($session);
        }
        $response = $this->app->handle($request, $type, $catch);
        if ($this->sessionConfigured()) {
            $this->closeSession($session);
            $this->addCookieToResponse($response, $session);
        }
        return $response;
    }
    public function checkRequestForArraySessions(Request $request)
    {
        if (is_null($this->reject)) {
            return;
        }
        if (call_user_func($this->reject, $request)) {
            $this->manager->setDefaultDriver('array');
        }
    }
    protected function startSession(Request $request)
    {
        with($session = $this->getSession($request))->setRequestOnHandler($request);
        $session->start();
        return $session;
    }
    protected function closeSession(SessionInterface $session)
    {
        $session->save();
        $this->collectGarbage($session);
    }
    protected function getUrl(Request $request)
    {
        $url = rtrim(preg_replace('/\\?.*/', '', $request->getUri()), '/');
        return $request->getQueryString() ? $url . '?' . $request->getQueryString() : $url;
    }
    protected function collectGarbage(SessionInterface $session)
    {
        $config = $this->manager->getSessionConfig();
        if ($this->configHitsLottery($config)) {
            $session->getHandler()->gc($this->getLifetimeSeconds());
        }
    }
    protected function configHitsLottery(array $config)
    {
        return mt_rand(1, $config['lottery'][1]) <= $config['lottery'][0];
    }
    protected function addCookieToResponse(Response $response, SessionInterface $session)
    {
        $s = $session;
        if ($this->sessionIsPersistent($c = $this->manager->getSessionConfig())) {
            $secure = array_get($c, 'secure', false);
            $response->headers->setCookie(new Cookie($s->getName(), $s->getId(), $this->getCookieLifetime(), $c['path'], $c['domain'], $secure));
        }
    }
    protected function getLifetimeSeconds()
    {
        return array_get($this->manager->getSessionConfig(), 'lifetime') * 60;
    }
    protected function getCookieLifetime()
    {
        $config = $this->manager->getSessionConfig();
        return $config['expire_on_close'] ? 0 : Carbon::now()->addMinutes($config['lifetime']);
    }
    protected function sessionConfigured()
    {
        return !is_null(array_get($this->manager->getSessionConfig(), 'driver'));
    }
    protected function sessionIsPersistent(array $config = null)
    {
        $config = $config ?: $this->manager->getSessionConfig();
        return !in_array($config['driver'], array(null, 'array'));
    }
    public function getSession(Request $request)
    {
        $session = $this->manager->driver();
        $session->setId($request->cookies->get($session->getName()));
        return $session;
    }
}
namespace Illuminate\Session;

use SessionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
class Store implements SessionInterface
{
    protected $id;
    protected $name;
    protected $attributes = array();
    protected $bags = array();
    protected $metaBag;
    protected $bagData = array();
    protected $handler;
    protected $started = false;
    public function __construct($name, SessionHandlerInterface $handler, $id = null)
    {
        $this->setId($id);
        $this->name = $name;
        $this->handler = $handler;
        $this->metaBag = new MetadataBag();
    }
    public function start()
    {
        $this->loadSession();
        if (!$this->has('_token')) {
            $this->regenerateToken();
        }
        return $this->started = true;
    }
    protected function loadSession()
    {
        $this->attributes = $this->readFromHandler();
        foreach (array_merge($this->bags, array($this->metaBag)) as $bag) {
            $this->initializeLocalBag($bag);
            $bag->initialize($this->bagData[$bag->getStorageKey()]);
        }
    }
    protected function readFromHandler()
    {
        $data = $this->handler->read($this->getId());
        return $data ? unserialize($data) : array();
    }
    protected function initializeLocalBag($bag)
    {
        $this->bagData[$bag->getStorageKey()] = $this->pull($bag->getStorageKey(), array());
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id ?: $this->generateSessionId();
    }
    protected function generateSessionId()
    {
        return sha1(uniqid('', true) . str_random(25) . microtime(true));
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function invalidate($lifetime = null)
    {
        $this->attributes = array();
        $this->migrate();
        return true;
    }
    public function migrate($destroy = false, $lifetime = null)
    {
        if ($destroy) {
            $this->handler->destroy($this->getId());
        }
        $this->setExists(false);
        $this->id = $this->generateSessionId();
        return true;
    }
    public function regenerate($destroy = false)
    {
        return $this->migrate($destroy);
    }
    public function save()
    {
        $this->addBagDataToSession();
        $this->ageFlashData();
        $this->handler->write($this->getId(), serialize($this->attributes));
        $this->started = false;
    }
    protected function addBagDataToSession()
    {
        foreach (array_merge($this->bags, array($this->metaBag)) as $bag) {
            $this->put($bag->getStorageKey(), $this->bagData[$bag->getStorageKey()]);
        }
    }
    public function ageFlashData()
    {
        foreach ($this->get('flash.old', array()) as $old) {
            $this->forget($old);
        }
        $this->put('flash.old', $this->get('flash.new', array()));
        $this->put('flash.new', array());
    }
    public function has($name)
    {
        return !is_null($this->get($name));
    }
    public function get($name, $default = null)
    {
        return array_get($this->attributes, $name, $default);
    }
    public function pull($key, $default = null)
    {
        return array_pull($this->attributes, $key, $default);
    }
    public function hasOldInput($key = null)
    {
        $old = $this->getOldInput($key);
        return is_null($key) ? count($old) > 0 : !is_null($old);
    }
    public function getOldInput($key = null, $default = null)
    {
        $input = $this->get('_old_input', array());
        if (is_null($key)) {
            return $input;
        }
        return array_get($input, $key, $default);
    }
    public function set($name, $value)
    {
        array_set($this->attributes, $name, $value);
    }
    public function put($key, $value = null)
    {
        if (!is_array($key)) {
            $key = array($key => $value);
        }
        foreach ($key as $arrayKey => $arrayValue) {
            $this->set($arrayKey, $arrayValue);
        }
    }
    public function push($key, $value)
    {
        $array = $this->get($key, array());
        $array[] = $value;
        $this->put($key, $array);
    }
    public function flash($key, $value)
    {
        $this->put($key, $value);
        $this->push('flash.new', $key);
        $this->removeFromOldFlashData(array($key));
    }
    public function flashInput(array $value)
    {
        $this->flash('_old_input', $value);
    }
    public function reflash()
    {
        $this->mergeNewFlashes($this->get('flash.old', array()));
        $this->put('flash.old', array());
    }
    public function keep($keys = null)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $this->mergeNewFlashes($keys);
        $this->removeFromOldFlashData($keys);
    }
    protected function mergeNewFlashes(array $keys)
    {
        $values = array_unique(array_merge($this->get('flash.new', array()), $keys));
        $this->put('flash.new', $values);
    }
    protected function removeFromOldFlashData(array $keys)
    {
        $this->put('flash.old', array_diff($this->get('flash.old', array()), $keys));
    }
    public function all()
    {
        return $this->attributes;
    }
    public function replace(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->put($key, $value);
        }
    }
    public function remove($name)
    {
        return array_pull($this->attributes, $name);
    }
    public function forget($key)
    {
        array_forget($this->attributes, $key);
    }
    public function clear()
    {
        $this->attributes = array();
        foreach ($this->bags as $bag) {
            $bag->clear();
        }
    }
    public function flush()
    {
        $this->clear();
    }
    public function isStarted()
    {
        return $this->started;
    }
    public function registerBag(SessionBagInterface $bag)
    {
        $this->bags[$bag->getStorageKey()] = $bag;
    }
    public function getBag($name)
    {
        return array_get($this->bags, $name, function () {
            throw new \InvalidArgumentException('Bag not registered.');
        });
    }
    public function getMetadataBag()
    {
        return $this->metaBag;
    }
    public function getBagData($name)
    {
        return array_get($this->bagData, $name, array());
    }
    public function token()
    {
        return $this->get('_token');
    }
    public function getToken()
    {
        return $this->token();
    }
    public function regenerateToken()
    {
        $this->put('_token', str_random(40));
    }
    public function setExists($value)
    {
        if ($this->handler instanceof ExistenceAwareInterface) {
            $this->handler->setExists($value);
        }
    }
    public function getHandler()
    {
        return $this->handler;
    }
    public function handlerNeedsRequest()
    {
        return $this->handler instanceof CookieSessionHandler;
    }
    public function setRequestOnHandler(Request $request)
    {
        if ($this->handlerNeedsRequest()) {
            $this->handler->setRequest($request);
        }
    }
}
namespace Illuminate\Session;

use Illuminate\Support\Manager;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;
class SessionManager extends Manager
{
    protected function callCustomCreator($driver)
    {
        return $this->buildSession(parent::callCustomCreator($driver));
    }
    protected function createArrayDriver()
    {
        return new Store($this->app['config']['session.cookie'], new NullSessionHandler());
    }
    protected function createCookieDriver()
    {
        $lifetime = $this->app['config']['session.lifetime'];
        return $this->buildSession(new CookieSessionHandler($this->app['cookie'], $lifetime));
    }
    protected function createFileDriver()
    {
        return $this->createNativeDriver();
    }
    protected function createNativeDriver()
    {
        $path = $this->app['config']['session.files'];
        return $this->buildSession(new FileSessionHandler($this->app['files'], $path));
    }
    protected function createDatabaseDriver()
    {
        $connection = $this->getDatabaseConnection();
        $table = $this->app['config']['session.table'];
        return $this->buildSession(new DatabaseSessionHandler($connection, $table));
    }
    protected function getDatabaseConnection()
    {
        $connection = $this->app['config']['session.connection'];
        return $this->app['db']->connection($connection);
    }
    protected function createApcDriver()
    {
        return $this->createCacheBased('apc');
    }
    protected function createMemcachedDriver()
    {
        return $this->createCacheBased('memcached');
    }
    protected function createWincacheDriver()
    {
        return $this->createCacheBased('wincache');
    }
    protected function createRedisDriver()
    {
        $handler = $this->createCacheHandler('redis');
        $handler->getCache()->getStore()->setConnection($this->app['config']['session.connection']);
        return $this->buildSession($handler);
    }
    protected function createCacheBased($driver)
    {
        return $this->buildSession($this->createCacheHandler($driver));
    }
    protected function createCacheHandler($driver)
    {
        $minutes = $this->app['config']['session.lifetime'];
        return new CacheBasedSessionHandler($this->app['cache']->driver($driver), $minutes);
    }
    protected function buildSession($handler)
    {
        return new Store($this->app['config']['session.cookie'], $handler);
    }
    public function getSessionConfig()
    {
        return $this->app['config']['session'];
    }
    public function getDefaultDriver()
    {
        return $this->app['config']['session.driver'];
    }
    public function setDefaultDriver($name)
    {
        $this->app['config']['session.driver'] = $name;
    }
}
namespace Illuminate\Support;

use Closure;
abstract class Manager
{
    protected $app;
    protected $customCreators = array();
    protected $drivers = array();
    public function __construct($app)
    {
        $this->app = $app;
    }
    public abstract function getDefaultDriver();
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();
        if (!isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }
        return $this->drivers[$driver];
    }
    protected function createDriver($driver)
    {
        $method = 'create' . ucfirst($driver) . 'Driver';
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } elseif (method_exists($this, $method)) {
            return $this->{$method}();
        }
        throw new \InvalidArgumentException("Driver [{$driver}] not supported.");
    }
    protected function callCustomCreator($driver)
    {
        return $this->customCreators[$driver]($this->app);
    }
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }
    public function getDrivers()
    {
        return $this->drivers;
    }
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->driver(), $method), $parameters);
    }
}
namespace Illuminate\Cookie;

use Symfony\Component\HttpFoundation\Cookie;
class CookieJar
{
    protected $path = '/';
    protected $domain = null;
    protected $queued = array();
    public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        list($path, $domain) = $this->getPathAndDomain($path, $domain);
        $time = $minutes == 0 ? 0 : time() + $minutes * 60;
        return new Cookie($name, $value, $time, $path, $domain, $secure, $httpOnly);
    }
    public function forever($name, $value, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        return $this->make($name, $value, 2628000, $path, $domain, $secure, $httpOnly);
    }
    public function forget($name, $path = null, $domain = null)
    {
        return $this->make($name, null, -2628000, $path, $domain);
    }
    public function hasQueued($key)
    {
        return !is_null($this->queued($key));
    }
    public function queued($key, $default = null)
    {
        return array_get($this->queued, $key, $default);
    }
    public function queue()
    {
        if (head(func_get_args()) instanceof Cookie) {
            $cookie = head(func_get_args());
        } else {
            $cookie = call_user_func_array(array($this, 'make'), func_get_args());
        }
        $this->queued[$cookie->getName()] = $cookie;
    }
    public function unqueue($name)
    {
        unset($this->queued[$name]);
    }
    protected function getPathAndDomain($path, $domain)
    {
        return array($path ?: $this->path, $domain ?: $this->domain);
    }
    public function setDefaultPathAndDomain($path, $domain)
    {
        list($this->path, $this->domain) = array($path, $domain);
        return $this;
    }
    public function getQueuedCookies()
    {
        return $this->queued;
    }
}
namespace Illuminate\Cookie;

use Illuminate\Encryption\Encrypter;
use Illuminate\Encryption\DecryptException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
class Guard implements HttpKernelInterface
{
    protected $app;
    protected $encrypter;
    public function __construct(HttpKernelInterface $app, Encrypter $encrypter)
    {
        $this->app = $app;
        $this->encrypter = $encrypter;
    }
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->encrypt($this->app->handle($this->decrypt($request), $type, $catch));
    }
    protected function decrypt(Request $request)
    {
        foreach ($request->cookies as $key => $c) {
            try {
                $request->cookies->set($key, $this->decryptCookie($c));
            } catch (DecryptException $e) {
                $request->cookies->set($key, null);
            }
        }
        return $request;
    }
    protected function decryptCookie($cookie)
    {
        return is_array($cookie) ? $this->decryptArray($cookie) : $this->encrypter->decrypt($cookie);
    }
    protected function decryptArray(array $cookie)
    {
        $decrypted = array();
        foreach ($cookie as $key => $value) {
            $decrypted[$key] = $this->encrypter->decrypt($value);
        }
        return $decrypted;
    }
    protected function encrypt(Response $response)
    {
        foreach ($response->headers->getCookies() as $key => $c) {
            $encrypted = $this->encrypter->encrypt($c->getValue());
            $response->headers->setCookie($this->duplicate($c, $encrypted));
        }
        return $response;
    }
    protected function duplicate(Cookie $c, $value)
    {
        return new Cookie($c->getName(), $value, $c->getExpiresTime(), $c->getPath(), $c->getDomain(), $c->isSecure(), $c->isHttpOnly());
    }
}
namespace Illuminate\Cookie;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
class Queue implements HttpKernelInterface
{
    protected $app;
    protected $cookies;
    public function __construct(HttpKernelInterface $app, CookieJar $cookies)
    {
        $this->app = $app;
        $this->cookies = $cookies;
    }
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = $this->app->handle($request, $type, $catch);
        foreach ($this->cookies->getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }
        return $response;
    }
}
namespace Illuminate\Encryption;

use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Security\Core\Util\SecureRandom;
class DecryptException extends \RuntimeException
{
    
}
class Encrypter
{
    protected $key;
    protected $cipher = MCRYPT_RIJNDAEL_128;
    protected $mode = MCRYPT_MODE_CBC;
    protected $block = 16;
    public function __construct($key)
    {
        $this->key = $key;
    }
    public function encrypt($value)
    {
        $iv = mcrypt_create_iv($this->getIvSize(), $this->getRandomizer());
        $value = base64_encode($this->padAndMcrypt($value, $iv));
        $mac = $this->hash($iv = base64_encode($iv), $value);
        return base64_encode(json_encode(compact('iv', 'value', 'mac')));
    }
    protected function padAndMcrypt($value, $iv)
    {
        $value = $this->addPadding(serialize($value));
        return mcrypt_encrypt($this->cipher, $this->key, $value, $this->mode, $iv);
    }
    public function decrypt($payload)
    {
        $payload = $this->getJsonPayload($payload);
        $value = base64_decode($payload['value']);
        $iv = base64_decode($payload['iv']);
        return unserialize($this->stripPadding($this->mcryptDecrypt($value, $iv)));
    }
    protected function mcryptDecrypt($value, $iv)
    {
        try {
            return mcrypt_decrypt($this->cipher, $this->key, $value, $this->mode, $iv);
        } catch (\Exception $e) {
            throw new DecryptException($e->getMessage());
        }
    }
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);
        if (!$payload || $this->invalidPayload($payload)) {
            throw new DecryptException('Invalid data.');
        }
        if (!$this->validMac($payload)) {
            throw new DecryptException('MAC is invalid.');
        }
        return $payload;
    }
    protected function validMac(array $payload)
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new \RuntimeException('OpenSSL extension is required.');
        }
        $bytes = (new SecureRandom())->nextBytes(16);
        $calcMac = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true);
        return StringUtils::equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calcMac);
    }
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv . $value, $this->key);
    }
    protected function addPadding($value)
    {
        $pad = $this->block - strlen($value) % $this->block;
        return $value . str_repeat(chr($pad), $pad);
    }
    protected function stripPadding($value)
    {
        $pad = ord($value[($len = strlen($value)) - 1]);
        return $this->paddingIsValid($pad, $value) ? substr($value, 0, $len - $pad) : $value;
    }
    protected function paddingIsValid($pad, $value)
    {
        $beforePad = strlen($value) - $pad;
        return substr($value, $beforePad) == str_repeat(substr($value, -1), $pad);
    }
    protected function invalidPayload($data)
    {
        return !is_array($data) || !isset($data['iv']) || !isset($data['value']) || !isset($data['mac']);
    }
    protected function getIvSize()
    {
        return mcrypt_get_iv_size($this->cipher, $this->mode);
    }
    protected function getRandomizer()
    {
        if (defined('MCRYPT_DEV_URANDOM')) {
            return MCRYPT_DEV_URANDOM;
        }
        if (defined('MCRYPT_DEV_RANDOM')) {
            return MCRYPT_DEV_RANDOM;
        }
        mt_srand();
        return MCRYPT_RAND;
    }
    public function setKey($key)
    {
        $this->key = $key;
    }
    public function setCipher($cipher)
    {
        $this->cipher = $cipher;
        $this->updateBlockSize();
    }
    public function setMode($mode)
    {
        $this->mode = $mode;
        $this->updateBlockSize();
    }
    protected function updateBlockSize()
    {
        $this->block = mcrypt_get_iv_size($this->cipher, $this->mode);
    }
}
namespace Illuminate\Support\Facades;

class Log extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}
namespace Illuminate\Log;

use Monolog\Logger;
use Illuminate\Support\ServiceProvider;
class LogServiceProvider extends ServiceProvider
{
    protected $defer = true;
    public function register()
    {
        $logger = new Writer(new Logger($this->app['env']), $this->app['events']);
        $this->app->instance('log', $logger);
        if (isset($this->app['log.setup'])) {
            call_user_func($this->app['log.setup'], $logger);
        }
    }
    public function provides()
    {
        return array('log');
    }
}
namespace Illuminate\Log;

use Closure;
use Illuminate\Events\Dispatcher;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;
class Writer
{
    protected $monolog;
    protected $levels = array('debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency');
    protected $dispatcher;
    public function __construct(MonologLogger $monolog, Dispatcher $dispatcher = null)
    {
        $this->monolog = $monolog;
        if (isset($dispatcher)) {
            $this->dispatcher = $dispatcher;
        }
    }
    protected function callMonolog($method, $parameters)
    {
        if (is_array($parameters[0])) {
            $parameters[0] = json_encode($parameters[0]);
        }
        return call_user_func_array(array($this->monolog, $method), $parameters);
    }
    public function useFiles($path, $level = 'debug')
    {
        $level = $this->parseLevel($level);
        $this->monolog->pushHandler($handler = new StreamHandler($path, $level));
        $handler->setFormatter($this->getDefaultFormatter());
    }
    public function useDailyFiles($path, $days = 0, $level = 'debug')
    {
        $level = $this->parseLevel($level);
        $this->monolog->pushHandler($handler = new RotatingFileHandler($path, $days, $level));
        $handler->setFormatter($this->getDefaultFormatter());
    }
    public function useErrorLog($level = 'debug', $messageType = ErrorLogHandler::OPERATING_SYSTEM)
    {
        $level = $this->parseLevel($level);
        $this->monolog->pushHandler($handler = new ErrorLogHandler($messageType, $level));
        $handler->setFormatter($this->getDefaultFormatter());
    }
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, null, true);
    }
    protected function parseLevel($level)
    {
        switch ($level) {
            case 'debug':
                return MonologLogger::DEBUG;
            case 'info':
                return MonologLogger::INFO;
            case 'notice':
                return MonologLogger::NOTICE;
            case 'warning':
                return MonologLogger::WARNING;
            case 'error':
                return MonologLogger::ERROR;
            case 'critical':
                return MonologLogger::CRITICAL;
            case 'alert':
                return MonologLogger::ALERT;
            case 'emergency':
                return MonologLogger::EMERGENCY;
            default:
                throw new \InvalidArgumentException('Invalid log level.');
        }
    }
    public function listen(Closure $callback)
    {
        if (!isset($this->dispatcher)) {
            throw new \RuntimeException('Events dispatcher has not been set.');
        }
        $this->dispatcher->listen('illuminate.log', $callback);
    }
    public function getMonolog()
    {
        return $this->monolog;
    }
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }
    public function setEventDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    protected function fireLogEvent($level, $message, array $context = array())
    {
        if (isset($this->dispatcher)) {
            $this->dispatcher->fire('illuminate.log', compact('level', 'message', 'context'));
        }
    }
    public function write()
    {
        $level = head(func_get_args());
        return call_user_func_array(array($this, $level), array_slice(func_get_args(), 1));
    }
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->levels)) {
            $this->formatParameters($parameters);
            call_user_func_array(array($this, 'fireLogEvent'), array_merge(array($method), $parameters));
            $method = 'add' . ucfirst($method);
            return $this->callMonolog($method, $parameters);
        }
        throw new \BadMethodCallException("Method [{$method}] does not exist.");
    }
    protected function formatParameters(&$parameters)
    {
        if (isset($parameters[0])) {
            if (is_array($parameters[0])) {
                $parameters[0] = var_export($parameters[0], true);
            } elseif ($parameters[0] instanceof JsonableInterface) {
                $parameters[0] = $parameters[0]->toJson();
            } elseif ($parameters[0] instanceof ArrayableInterface) {
                $parameters[0] = var_export($parameters[0]->toArray(), true);
            }
        }
    }
}
namespace Monolog;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;
class Logger implements LoggerInterface
{
    const DEBUG = 100;
    const INFO = 200;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;
    const CRITICAL = 500;
    const ALERT = 550;
    const EMERGENCY = 600;
    const API = 1;
    protected static $levels = array(100 => 'DEBUG', 200 => 'INFO', 250 => 'NOTICE', 300 => 'WARNING', 400 => 'ERROR', 500 => 'CRITICAL', 550 => 'ALERT', 600 => 'EMERGENCY');
    protected static $timezone;
    protected $name;
    protected $handlers;
    protected $processors;
    public function __construct($name, array $handlers = array(), array $processors = array())
    {
        $this->name = $name;
        $this->handlers = $handlers;
        $this->processors = $processors;
    }
    public function getName()
    {
        return $this->name;
    }
    public function pushHandler(HandlerInterface $handler)
    {
        array_unshift($this->handlers, $handler);
    }
    public function popHandler()
    {
        if (!$this->handlers) {
            throw new \LogicException('You tried to pop from an empty handler stack.');
        }
        return array_shift($this->handlers);
    }
    public function getHandlers()
    {
        return $this->handlers;
    }
    public function pushProcessor($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), ' . var_export($callback, true) . ' given');
        }
        array_unshift($this->processors, $callback);
    }
    public function popProcessor()
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }
        return array_shift($this->processors);
    }
    public function getProcessors()
    {
        return $this->processors;
    }
    public function addRecord($level, $message, array $context = array())
    {
        if (!$this->handlers) {
            $this->pushHandler(new StreamHandler('php://stderr', static::DEBUG));
        }
        if (!static::$timezone) {
            static::$timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        }
        $record = array('message' => (string) $message, 'context' => $context, 'level' => $level, 'level_name' => static::getLevelName($level), 'channel' => $this->name, 'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), static::$timezone)->setTimezone(static::$timezone), 'extra' => array());
        $handlerKey = null;
        foreach ($this->handlers as $key => $handler) {
            if ($handler->isHandling($record)) {
                $handlerKey = $key;
                break;
            }
        }
        if (null === $handlerKey) {
            return false;
        }
        foreach ($this->processors as $processor) {
            $record = call_user_func($processor, $record);
        }
        while (isset($this->handlers[$handlerKey]) && false === $this->handlers[$handlerKey]->handle($record)) {
            $handlerKey++;
        }
        return true;
    }
    public function addDebug($message, array $context = array())
    {
        return $this->addRecord(static::DEBUG, $message, $context);
    }
    public function addInfo($message, array $context = array())
    {
        return $this->addRecord(static::INFO, $message, $context);
    }
    public function addNotice($message, array $context = array())
    {
        return $this->addRecord(static::NOTICE, $message, $context);
    }
    public function addWarning($message, array $context = array())
    {
        return $this->addRecord(static::WARNING, $message, $context);
    }
    public function addError($message, array $context = array())
    {
        return $this->addRecord(static::ERROR, $message, $context);
    }
    public function addCritical($message, array $context = array())
    {
        return $this->addRecord(static::CRITICAL, $message, $context);
    }
    public function addAlert($message, array $context = array())
    {
        return $this->addRecord(static::ALERT, $message, $context);
    }
    public function addEmergency($message, array $context = array())
    {
        return $this->addRecord(static::EMERGENCY, $message, $context);
    }
    public static function getLevels()
    {
        return array_flip(static::$levels);
    }
    public static function getLevelName($level)
    {
        if (!isset(static::$levels[$level])) {
            throw new InvalidArgumentException('Level "' . $level . '" is not defined, use one of: ' . implode(', ', array_keys(static::$levels)));
        }
        return static::$levels[$level];
    }
    public function isHandling($level)
    {
        $record = array('level' => $level);
        foreach ($this->handlers as $handler) {
            if ($handler->isHandling($record)) {
                return true;
            }
        }
        return false;
    }
    public function log($level, $message, array $context = array())
    {
        if (is_string($level) && defined(__CLASS__ . '::' . strtoupper($level))) {
            $level = constant(__CLASS__ . '::' . strtoupper($level));
        }
        return $this->addRecord($level, $message, $context);
    }
    public function debug($message, array $context = array())
    {
        return $this->addRecord(static::DEBUG, $message, $context);
    }
    public function info($message, array $context = array())
    {
        return $this->addRecord(static::INFO, $message, $context);
    }
    public function notice($message, array $context = array())
    {
        return $this->addRecord(static::NOTICE, $message, $context);
    }
    public function warn($message, array $context = array())
    {
        return $this->addRecord(static::WARNING, $message, $context);
    }
    public function warning($message, array $context = array())
    {
        return $this->addRecord(static::WARNING, $message, $context);
    }
    public function err($message, array $context = array())
    {
        return $this->addRecord(static::ERROR, $message, $context);
    }
    public function error($message, array $context = array())
    {
        return $this->addRecord(static::ERROR, $message, $context);
    }
    public function crit($message, array $context = array())
    {
        return $this->addRecord(static::CRITICAL, $message, $context);
    }
    public function critical($message, array $context = array())
    {
        return $this->addRecord(static::CRITICAL, $message, $context);
    }
    public function alert($message, array $context = array())
    {
        return $this->addRecord(static::ALERT, $message, $context);
    }
    public function emerg($message, array $context = array())
    {
        return $this->addRecord(static::EMERGENCY, $message, $context);
    }
    public function emergency($message, array $context = array())
    {
        return $this->addRecord(static::EMERGENCY, $message, $context);
    }
}
namespace Psr\Log;

interface LoggerInterface
{
    public function emergency($message, array $context = array());
    public function alert($message, array $context = array());
    public function critical($message, array $context = array());
    public function error($message, array $context = array());
    public function warning($message, array $context = array());
    public function notice($message, array $context = array());
    public function info($message, array $context = array());
    public function debug($message, array $context = array());
    public function log($level, $message, array $context = array());
}
namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
abstract class AbstractHandler implements HandlerInterface
{
    protected $level = Logger::DEBUG;
    protected $bubble = true;
    protected $formatter;
    protected $processors = array();
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        $this->level = $level;
        $this->bubble = $bubble;
    }
    public function isHandling(array $record)
    {
        return $record['level'] >= $this->level;
    }
    public function handleBatch(array $records)
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }
    public function close()
    {
        
    }
    public function pushProcessor($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), ' . var_export($callback, true) . ' given');
        }
        array_unshift($this->processors, $callback);
        return $this;
    }
    public function popProcessor()
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }
        return array_shift($this->processors);
    }
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }
    public function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }
        return $this->formatter;
    }
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
    public function getLevel()
    {
        return $this->level;
    }
    public function setBubble($bubble)
    {
        $this->bubble = $bubble;
        return $this;
    }
    public function getBubble()
    {
        return $this->bubble;
    }
    public function __destruct()
    {
        try {
            $this->close();
        } catch (\Exception $e) {
            
        }
    }
    protected function getDefaultFormatter()
    {
        return new LineFormatter();
    }
}
namespace Monolog\Handler;

abstract class AbstractProcessingHandler extends AbstractHandler
{
    public function handle(array $record)
    {
        if (!$this->isHandling($record)) {
            return false;
        }
        $record = $this->processRecord($record);
        $record['formatted'] = $this->getFormatter()->format($record);
        $this->write($record);
        return false === $this->bubble;
    }
    protected abstract function write(array $record);
    protected function processRecord(array $record)
    {
        if ($this->processors) {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }
        }
        return $record;
    }
}
namespace Monolog\Handler;

use Monolog\Logger;
class StreamHandler extends AbstractProcessingHandler
{
    protected $stream;
    protected $url;
    private $errorMessage;
    protected $filePermission;
    public function __construct($stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null)
    {
        parent::__construct($level, $bubble);
        if (is_resource($stream)) {
            $this->stream = $stream;
        } else {
            $this->url = $stream;
        }
        $this->filePermission = $filePermission;
    }
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }
    protected function write(array $record)
    {
        if (!is_resource($this->stream)) {
            if (!$this->url) {
                throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }
            $this->errorMessage = null;
            set_error_handler(array($this, 'customErrorHandler'));
            $this->stream = fopen($this->url, 'a');
            if ($this->filePermission !== null) {
                @chmod($this->url, $this->filePermission);
            }
            restore_error_handler();
            if (!is_resource($this->stream)) {
                $this->stream = null;
                throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened: ' . $this->errorMessage, $this->url));
            }
        }
        fwrite($this->stream, (string) $record['formatted']);
    }
    private function customErrorHandler($code, $msg)
    {
        $this->errorMessage = preg_replace('{^fopen\\(.*?\\): }', '', $msg);
    }
}
namespace Monolog\Handler;

use Monolog\Logger;
class RotatingFileHandler extends StreamHandler
{
    protected $filename;
    protected $maxFiles;
    protected $mustRotate;
    protected $nextRotation;
    protected $filenameFormat;
    protected $dateFormat;
    public function __construct($filename, $maxFiles = 0, $level = Logger::DEBUG, $bubble = true, $filePermission = null)
    {
        $this->filename = $filename;
        $this->maxFiles = (int) $maxFiles;
        $this->nextRotation = new \DateTime('tomorrow');
        $this->filenameFormat = '{filename}-{date}';
        $this->dateFormat = 'Y-m-d';
        parent::__construct($this->getTimedFilename(), $level, $bubble, $filePermission);
    }
    public function close()
    {
        parent::close();
        if (true === $this->mustRotate) {
            $this->rotate();
        }
    }
    public function setFilenameFormat($filenameFormat, $dateFormat)
    {
        $this->filenameFormat = $filenameFormat;
        $this->dateFormat = $dateFormat;
        $this->url = $this->getTimedFilename();
        $this->close();
    }
    protected function write(array $record)
    {
        if (null === $this->mustRotate) {
            $this->mustRotate = !file_exists($this->url);
        }
        if ($this->nextRotation < $record['datetime']) {
            $this->mustRotate = true;
            $this->close();
        }
        parent::write($record);
    }
    protected function rotate()
    {
        $this->url = $this->getTimedFilename();
        $this->nextRotation = new \DateTime('tomorrow');
        if (0 === $this->maxFiles) {
            return;
        }
        $logFiles = glob($this->getGlobPattern());
        if ($this->maxFiles >= count($logFiles)) {
            return;
        }
        usort($logFiles, function ($a, $b) {
            return strcmp($b, $a);
        });
        foreach (array_slice($logFiles, $this->maxFiles) as $file) {
            if (is_writable($file)) {
                unlink($file);
            }
        }
    }
    protected function getTimedFilename()
    {
        $fileInfo = pathinfo($this->filename);
        $timedFilename = str_replace(array('{filename}', '{date}'), array($fileInfo['filename'], date($this->dateFormat)), $fileInfo['dirname'] . '/' . $this->filenameFormat);
        if (!empty($fileInfo['extension'])) {
            $timedFilename .= '.' . $fileInfo['extension'];
        }
        return $timedFilename;
    }
    protected function getGlobPattern()
    {
        $fileInfo = pathinfo($this->filename);
        $glob = str_replace(array('{filename}', '{date}'), array($fileInfo['filename'], '*'), $fileInfo['dirname'] . '/' . $this->filenameFormat);
        if (!empty($fileInfo['extension'])) {
            $glob .= '.' . $fileInfo['extension'];
        }
        return $glob;
    }
}
namespace Monolog\Handler;

use Monolog\Formatter\FormatterInterface;
interface HandlerInterface
{
    public function isHandling(array $record);
    public function handle(array $record);
    public function handleBatch(array $records);
    public function pushProcessor($callback);
    public function popProcessor();
    public function setFormatter(FormatterInterface $formatter);
    public function getFormatter();
}
namespace Illuminate\Support\Facades;

class App extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'app';
    }
}
namespace Illuminate\Exception;

use Exception;
interface ExceptionDisplayerInterface
{
    public function display(Exception $exception);
}
namespace Illuminate\Exception;

use Exception;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
class SymfonyDisplayer implements ExceptionDisplayerInterface
{
    protected $symfony;
    protected $returnJson;
    public function __construct(ExceptionHandler $symfony, $returnJson = false)
    {
        $this->symfony = $symfony;
        $this->returnJson = $returnJson;
    }
    public function display(Exception $exception)
    {
        if ($this->returnJson) {
            return new JsonResponse(array('error' => $exception->getMessage(), 'file' => $exception->getFile(), 'line' => $exception->getLine()), 500);
        } else {
            return $this->symfony->createResponse($exception);
        }
    }
}
namespace Illuminate\Exception;

use Exception;
use Whoops\Run;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
class WhoopsDisplayer implements ExceptionDisplayerInterface
{
    protected $whoops;
    protected $runningInConsole;
    public function __construct(Run $whoops, $runningInConsole)
    {
        $this->whoops = $whoops;
        $this->runningInConsole = $runningInConsole;
    }
    public function display(Exception $exception)
    {
        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : array();
        return new Response($this->whoops->handleException($exception), $status, $headers);
    }
}
namespace Illuminate\Exception;

use Closure;
use ErrorException;
use ReflectionFunction;
use Illuminate\Support\Contracts\ResponsePreparerInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Debug\Exception\FatalErrorException as FatalError;
class Handler
{
    protected $responsePreparer;
    protected $plainDisplayer;
    protected $debugDisplayer;
    protected $debug;
    protected $handlers = array();
    protected $handled = array();
    public function __construct(ResponsePreparerInterface $responsePreparer, ExceptionDisplayerInterface $plainDisplayer, ExceptionDisplayerInterface $debugDisplayer, $debug = true)
    {
        $this->debug = $debug;
        $this->plainDisplayer = $plainDisplayer;
        $this->debugDisplayer = $debugDisplayer;
        $this->responsePreparer = $responsePreparer;
    }
    public function register($environment)
    {
        $this->registerErrorHandler();
        $this->registerExceptionHandler();
        if ($environment != 'testing') {
            $this->registerShutdownHandler();
        }
    }
    protected function registerErrorHandler()
    {
        set_error_handler(array($this, 'handleError'));
    }
    protected function registerExceptionHandler()
    {
        set_exception_handler(array($this, 'handleUncaughtException'));
    }
    protected function registerShutdownHandler()
    {
        register_shutdown_function(array($this, 'handleShutdown'));
    }
    public function handleError($level, $message, $file = '', $line = 0, $context = array())
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }
    public function handleException($exception)
    {
        $response = $this->callCustomHandlers($exception);
        if (!is_null($response)) {
            return $this->prepareResponse($response);
        }
        return $this->displayException($exception);
    }
    public function handleUncaughtException($exception)
    {
        $this->handleException($exception)->send();
    }
    public function handleShutdown()
    {
        $error = error_get_last();
        if (!is_null($error)) {
            extract($error);
            if (!$this->isFatal($type)) {
                return;
            }
            $this->handleException(new FatalError($message, $type, 0, $file, $line))->send();
        }
    }
    protected function isFatal($type)
    {
        return in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE));
    }
    public function handleConsole($exception)
    {
        return $this->callCustomHandlers($exception, true);
    }
    protected function callCustomHandlers($exception, $fromConsole = false)
    {
        foreach ($this->handlers as $handler) {
            if (!$this->handlesException($handler, $exception)) {
                continue;
            } elseif ($exception instanceof HttpExceptionInterface) {
                $code = $exception->getStatusCode();
            } else {
                $code = 500;
            }
            try {
                $response = $handler($exception, $code, $fromConsole);
            } catch (\Exception $e) {
                $response = $this->formatException($e);
            }
            if (isset($response) && !is_null($response)) {
                return $response;
            }
        }
    }
    protected function displayException($exception)
    {
        $displayer = $this->debug ? $this->debugDisplayer : $this->plainDisplayer;
        return $displayer->display($exception);
    }
    protected function handlesException(Closure $handler, $exception)
    {
        $reflection = new ReflectionFunction($handler);
        return $reflection->getNumberOfParameters() == 0 || $this->hints($reflection, $exception);
    }
    protected function hints(ReflectionFunction $reflection, $exception)
    {
        $parameters = $reflection->getParameters();
        $expected = $parameters[0];
        return !$expected->getClass() || $expected->getClass()->isInstance($exception);
    }
    protected function formatException(\Exception $e)
    {
        if ($this->debug) {
            $location = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
            return 'Error in exception handler: ' . $location;
        }
        return 'Error in exception handler.';
    }
    public function error(Closure $callback)
    {
        array_unshift($this->handlers, $callback);
    }
    public function pushError(Closure $callback)
    {
        $this->handlers[] = $callback;
    }
    protected function prepareResponse($response)
    {
        return $this->responsePreparer->prepareResponse($response);
    }
    public function runningInConsole()
    {
        return php_sapi_name() == 'cli';
    }
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
}
namespace Illuminate\Support\Facades;

class Route extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}
namespace Illuminate\View\Engines;

use Closure;
class EngineResolver
{
    protected $resolvers = array();
    protected $resolved = array();
    public function register($engine, Closure $resolver)
    {
        $this->resolvers[$engine] = $resolver;
    }
    public function resolve($engine)
    {
        if (isset($this->resolved[$engine])) {
            return $this->resolved[$engine];
        }
        if (isset($this->resolvers[$engine])) {
            return $this->resolved[$engine] = call_user_func($this->resolvers[$engine]);
        }
        throw new \InvalidArgumentException("Engine {$engine} not found.");
    }
}
namespace Illuminate\View;

interface ViewFinderInterface
{
    public function find($view);
    public function addLocation($location);
    public function addNamespace($namespace, $hints);
    public function prependNamespace($namespace, $hints);
    public function addExtension($extension);
}
namespace Illuminate\View;

use Illuminate\Filesystem\Filesystem;
class FileViewFinder implements ViewFinderInterface
{
    protected $files;
    protected $paths;
    protected $views = array();
    protected $hints = array();
    protected $extensions = array('blade.php', 'php');
    const HINT_PATH_DELIMITER = '::';
    public function __construct(Filesystem $files, array $paths, array $extensions = null)
    {
        $this->files = $files;
        $this->paths = $paths;
        if (isset($extensions)) {
            $this->extensions = $extensions;
        }
    }
    public function find($name)
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }
        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamedPathView($name);
        }
        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }
    protected function findNamedPathView($name)
    {
        list($namespace, $view) = $this->getNamespaceSegments($name);
        return $this->findInPaths($view, $this->hints[$namespace]);
    }
    protected function getNamespaceSegments($name)
    {
        $segments = explode(static::HINT_PATH_DELIMITER, $name);
        if (count($segments) != 2) {
            throw new \InvalidArgumentException("View [{$name}] has an invalid name.");
        }
        if (!isset($this->hints[$segments[0]])) {
            throw new \InvalidArgumentException("No hint path defined for [{$segments[0]}].");
        }
        return $segments;
    }
    protected function findInPaths($name, $paths)
    {
        foreach ((array) $paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $file) {
                if ($this->files->exists($viewPath = $path . '/' . $file)) {
                    return $viewPath;
                }
            }
        }
        throw new \InvalidArgumentException("View [{$name}] not found.");
    }
    protected function getPossibleViewFiles($name)
    {
        return array_map(function ($extension) use($name) {
            return str_replace('.', '/', $name) . '.' . $extension;
        }, $this->extensions);
    }
    public function addLocation($location)
    {
        $this->paths[] = $location;
    }
    public function addNamespace($namespace, $hints)
    {
        $hints = (array) $hints;
        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }
        $this->hints[$namespace] = $hints;
    }
    public function prependNamespace($namespace, $hints)
    {
        $hints = (array) $hints;
        if (isset($this->hints[$namespace])) {
            $hints = array_merge($hints, $this->hints[$namespace]);
        }
        $this->hints[$namespace] = $hints;
    }
    public function addExtension($extension)
    {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }
        array_unshift($this->extensions, $extension);
    }
    public function hasHintInformation($name)
    {
        return strpos($name, static::HINT_PATH_DELIMITER) > 0;
    }
    public function getFilesystem()
    {
        return $this->files;
    }
    public function getPaths()
    {
        return $this->paths;
    }
    public function getHints()
    {
        return $this->hints;
    }
    public function getExtensions()
    {
        return $this->extensions;
    }
}
namespace Illuminate\Support\Contracts;

interface MessageProviderInterface
{
    public function getMessageBag();
}
namespace Illuminate\Support;

use Countable;
use JsonSerializable;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\MessageProviderInterface;
class MessageBag implements ArrayableInterface, Countable, JsonableInterface, MessageProviderInterface, JsonSerializable
{
    protected $messages = array();
    protected $format = ':message';
    public function __construct(array $messages = array())
    {
        foreach ($messages as $key => $value) {
            $this->messages[$key] = (array) $value;
        }
    }
    public function add($key, $message)
    {
        if ($this->isUnique($key, $message)) {
            $this->messages[$key][] = $message;
        }
        return $this;
    }
    public function merge($messages)
    {
        if ($messages instanceof MessageProviderInterface) {
            $messages = $messages->getMessageBag()->getMessages();
        }
        $this->messages = array_merge_recursive($this->messages, $messages);
        return $this;
    }
    protected function isUnique($key, $message)
    {
        $messages = (array) $this->messages;
        return !isset($messages[$key]) || !in_array($message, $messages[$key]);
    }
    public function has($key = null)
    {
        return $this->first($key) !== '';
    }
    public function first($key = null, $format = null)
    {
        $messages = is_null($key) ? $this->all($format) : $this->get($key, $format);
        return count($messages) > 0 ? $messages[0] : '';
    }
    public function get($key, $format = null)
    {
        $format = $this->checkFormat($format);
        if (array_key_exists($key, $this->messages)) {
            return $this->transform($this->messages[$key], $format, $key);
        }
        return array();
    }
    public function all($format = null)
    {
        $format = $this->checkFormat($format);
        $all = array();
        foreach ($this->messages as $key => $messages) {
            $all = array_merge($all, $this->transform($messages, $format, $key));
        }
        return $all;
    }
    protected function transform($messages, $format, $messageKey)
    {
        $messages = (array) $messages;
        foreach ($messages as $key => &$message) {
            $replace = array(':message', ':key');
            $message = str_replace($replace, array($message, $messageKey), $format);
        }
        return $messages;
    }
    protected function checkFormat($format)
    {
        return $format === null ? $this->format : $format;
    }
    public function getMessages()
    {
        return $this->messages;
    }
    public function getMessageBag()
    {
        return $this;
    }
    public function getFormat()
    {
        return $this->format;
    }
    public function setFormat($format = ':message')
    {
        $this->format = $format;
        return $this;
    }
    public function isEmpty()
    {
        return !$this->any();
    }
    public function any()
    {
        return $this->count() > 0;
    }
    public function count()
    {
        return count($this->messages, COUNT_RECURSIVE) - count($this->messages);
    }
    public function toArray()
    {
        return $this->getMessages();
    }
    public function jsonSerialize()
    {
        return $this->toArray();
    }
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
    public function __toString()
    {
        return $this->toJson();
    }
}
namespace Illuminate\Support\Facades;

class View extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'view';
    }
}
namespace Illuminate\Support\Contracts;

interface RenderableInterface
{
    public function render();
}
namespace Illuminate\View;

use ArrayAccess;
use Closure;
use Illuminate\Support\MessageBag;
use Illuminate\View\Engines\EngineInterface;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\Contracts\ArrayableInterface as Arrayable;
use Illuminate\Support\Contracts\RenderableInterface as Renderable;
class View implements ArrayAccess, Renderable
{
    protected $factory;
    protected $engine;
    protected $view;
    protected $data;
    protected $path;
    public function __construct(Factory $factory, EngineInterface $engine, $view, $path, $data = array())
    {
        $this->view = $view;
        $this->path = $path;
        $this->engine = $engine;
        $this->factory = $factory;
        $this->data = $data instanceof Arrayable ? $data->toArray() : (array) $data;
    }
    public function render(Closure $callback = null)
    {
        $contents = $this->renderContents();
        $response = isset($callback) ? $callback($this, $contents) : null;
        $this->factory->flushSectionsIfDoneRendering();
        return $response ?: $contents;
    }
    protected function renderContents()
    {
        $this->factory->incrementRender();
        $this->factory->callComposer($this);
        $contents = $this->getContents();
        $this->factory->decrementRender();
        return $contents;
    }
    public function renderSections()
    {
        $env = $this->factory;
        return $this->render(function ($view) use($env) {
            return $env->getSections();
        });
    }
    protected function getContents()
    {
        return $this->engine->get($this->path, $this->gatherData());
    }
    protected function gatherData()
    {
        $data = array_merge($this->factory->getShared(), $this->data);
        foreach ($data as $key => $value) {
            if ($value instanceof Renderable) {
                $data[$key] = $value->render();
            }
        }
        return $data;
    }
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
    public function nest($key, $view, array $data = array())
    {
        return $this->with($key, $this->factory->make($view, $data));
    }
    public function withErrors($provider)
    {
        if ($provider instanceof MessageProviderInterface) {
            $this->with('errors', $provider->getMessageBag());
        } else {
            $this->with('errors', new MessageBag((array) $provider));
        }
        return $this;
    }
    public function getFactory()
    {
        return $this->factory;
    }
    public function getEngine()
    {
        return $this->engine;
    }
    public function getName()
    {
        return $this->view;
    }
    public function getData()
    {
        return $this->data;
    }
    public function getPath()
    {
        return $this->path;
    }
    public function setPath($path)
    {
        $this->path = $path;
    }
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }
    public function offsetGet($key)
    {
        return $this->data[$key];
    }
    public function offsetSet($key, $value)
    {
        $this->with($key, $value);
    }
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }
    public function &__get($key)
    {
        return $this->data[$key];
    }
    public function __set($key, $value)
    {
        $this->with($key, $value);
    }
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }
    public function __unset($key)
    {
        unset($this->data[$key]);
    }
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'with')) {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }
        throw new \BadMethodCallException("Method [{$method}] does not exist on view.");
    }
    public function __toString()
    {
        return $this->render();
    }
}
namespace Illuminate\View\Engines;

interface EngineInterface
{
    public function get($path, array $data = array());
}
namespace Illuminate\View\Engines;

class PhpEngine implements EngineInterface
{
    public function get($path, array $data = array())
    {
        return $this->evaluatePath($path, $data);
    }
    protected function evaluatePath($__path, $__data)
    {
        ob_start();
        extract($__data);
        try {
            include $__path;
        } catch (\Exception $e) {
            $this->handleViewException($e);
        }
        return ltrim(ob_get_clean());
    }
    protected function handleViewException($e)
    {
        ob_get_clean();
        throw $e;
    }
}
namespace Symfony\Component\HttpFoundation;

class Response
{
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;
    const HTTP_ALREADY_REPORTED = 208;
    const HTTP_IM_USED = 226;
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENTLY_REDIRECT = 308;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_I_AM_A_TEAPOT = 418;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_LOCKED = 423;
    const HTTP_FAILED_DEPENDENCY = 424;
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;
    const HTTP_UPGRADE_REQUIRED = 426;
    const HTTP_PRECONDITION_REQUIRED = 428;
    const HTTP_TOO_MANY_REQUESTS = 429;
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;
    const HTTP_INSUFFICIENT_STORAGE = 507;
    const HTTP_LOOP_DETECTED = 508;
    const HTTP_NOT_EXTENDED = 510;
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;
    public $headers;
    protected $content;
    protected $version;
    protected $statusCode;
    protected $statusText;
    protected $charset;
    public static $statusTexts = array(100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 208 => 'Already Reported', 226 => 'IM Used', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Reserved', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Reserved for WebDAV advanced collections expired proposal', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates (Experimental)', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 510 => 'Not Extended', 511 => 'Network Authentication Required');
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->headers = new ResponseHeaderBag($headers);
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
        if (!$this->headers->has('Date')) {
            $this->setDate(new \DateTime(null, new \DateTimeZone('UTC')));
        }
    }
    public static function create($content = '', $status = 200, $headers = array())
    {
        return new static($content, $status, $headers);
    }
    public function __toString()
    {
        return sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText) . '
' . $this->headers . '
' . $this->getContent();
    }
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }
    public function prepare(Request $request)
    {
        $headers = $this->headers;
        if ($this->isInformational() || in_array($this->statusCode, array(204, 304))) {
            $this->setContent(null);
            $headers->remove('Content-Type');
            $headers->remove('Content-Length');
        } else {
            if (!$headers->has('Content-Type')) {
                $format = $request->getRequestFormat();
                if (null !== $format && ($mimeType = $request->getMimeType($format))) {
                    $headers->set('Content-Type', $mimeType);
                }
            }
            $charset = $this->charset ?: 'UTF-8';
            if (!$headers->has('Content-Type')) {
                $headers->set('Content-Type', 'text/html; charset=' . $charset);
            } elseif (0 === stripos($headers->get('Content-Type'), 'text/') && false === stripos($headers->get('Content-Type'), 'charset')) {
                $headers->set('Content-Type', $headers->get('Content-Type') . '; charset=' . $charset);
            }
            if ($headers->has('Transfer-Encoding')) {
                $headers->remove('Content-Length');
            }
            if ($request->isMethod('HEAD')) {
                $length = $headers->get('Content-Length');
                $this->setContent(null);
                if ($length) {
                    $headers->set('Content-Length', $length);
                }
            }
        }
        if ('HTTP/1.0' != $request->server->get('SERVER_PROTOCOL')) {
            $this->setProtocolVersion('1.1');
        }
        if ('1.0' == $this->getProtocolVersion() && 'no-cache' == $this->headers->get('Cache-Control')) {
            $this->headers->set('pragma', 'no-cache');
            $this->headers->set('expires', -1);
        }
        $this->ensureIEOverSSLCompatibility($request);
        return $this;
    }
    public function sendHeaders()
    {
        if (headers_sent()) {
            return $this;
        }
        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);
        foreach ($this->headers->allPreserveCase() as $name => $values) {
            foreach ($values as $value) {
                header($name . ': ' . $value, false, $this->statusCode);
            }
        }
        foreach ($this->headers->getCookies() as $cookie) {
            setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        }
        return $this;
    }
    public function sendContent()
    {
        echo $this->content;
        return $this;
    }
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif ('cli' !== PHP_SAPI) {
            static::closeOutputBuffers(0, true);
            flush();
        }
        return $this;
    }
    public function setContent($content)
    {
        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
            throw new \UnexpectedValueException(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
        }
        $this->content = (string) $content;
        return $this;
    }
    public function getContent()
    {
        return $this->content;
    }
    public function setProtocolVersion($version)
    {
        $this->version = $version;
        return $this;
    }
    public function getProtocolVersion()
    {
        return $this->version;
    }
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = $code = (int) $code;
        if ($this->isInvalid()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }
        if (null === $text) {
            $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : '';
            return $this;
        }
        if (false === $text) {
            $this->statusText = '';
            return $this;
        }
        $this->statusText = $text;
        return $this;
    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }
    public function getCharset()
    {
        return $this->charset;
    }
    public function isCacheable()
    {
        if (!in_array($this->statusCode, array(200, 203, 300, 301, 302, 404, 410))) {
            return false;
        }
        if ($this->headers->hasCacheControlDirective('no-store') || $this->headers->getCacheControlDirective('private')) {
            return false;
        }
        return $this->isValidateable() || $this->isFresh();
    }
    public function isFresh()
    {
        return $this->getTtl() > 0;
    }
    public function isValidateable()
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }
    public function setPrivate()
    {
        $this->headers->removeCacheControlDirective('public');
        $this->headers->addCacheControlDirective('private');
        return $this;
    }
    public function setPublic()
    {
        $this->headers->addCacheControlDirective('public');
        $this->headers->removeCacheControlDirective('private');
        return $this;
    }
    public function mustRevalidate()
    {
        return $this->headers->hasCacheControlDirective('must-revalidate') || $this->headers->has('proxy-revalidate');
    }
    public function getDate()
    {
        return $this->headers->getDate('Date', new \DateTime());
    }
    public function setDate(\DateTime $date)
    {
        $date->setTimezone(new \DateTimeZone('UTC'));
        $this->headers->set('Date', $date->format('D, d M Y H:i:s') . ' GMT');
        return $this;
    }
    public function getAge()
    {
        if (null !== ($age = $this->headers->get('Age'))) {
            return (int) $age;
        }
        return max(time() - $this->getDate()->format('U'), 0);
    }
    public function expire()
    {
        if ($this->isFresh()) {
            $this->headers->set('Age', $this->getMaxAge());
        }
        return $this;
    }
    public function getExpires()
    {
        try {
            return $this->headers->getDate('Expires');
        } catch (\RuntimeException $e) {
            return \DateTime::createFromFormat(DATE_RFC2822, 'Sat, 01 Jan 00 00:00:00 +0000');
        }
    }
    public function setExpires(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Expires');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Expires', $date->format('D, d M Y H:i:s') . ' GMT');
        }
        return $this;
    }
    public function getMaxAge()
    {
        if ($this->headers->hasCacheControlDirective('s-maxage')) {
            return (int) $this->headers->getCacheControlDirective('s-maxage');
        }
        if ($this->headers->hasCacheControlDirective('max-age')) {
            return (int) $this->headers->getCacheControlDirective('max-age');
        }
        if (null !== $this->getExpires()) {
            return $this->getExpires()->format('U') - $this->getDate()->format('U');
        }
    }
    public function setMaxAge($value)
    {
        $this->headers->addCacheControlDirective('max-age', $value);
        return $this;
    }
    public function setSharedMaxAge($value)
    {
        $this->setPublic();
        $this->headers->addCacheControlDirective('s-maxage', $value);
        return $this;
    }
    public function getTtl()
    {
        if (null !== ($maxAge = $this->getMaxAge())) {
            return $maxAge - $this->getAge();
        }
    }
    public function setTtl($seconds)
    {
        $this->setSharedMaxAge($this->getAge() + $seconds);
        return $this;
    }
    public function setClientTtl($seconds)
    {
        $this->setMaxAge($this->getAge() + $seconds);
        return $this;
    }
    public function getLastModified()
    {
        return $this->headers->getDate('Last-Modified');
    }
    public function setLastModified(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Last-Modified');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');
        }
        return $this;
    }
    public function getEtag()
    {
        return $this->headers->get('ETag');
    }
    public function setEtag($etag = null, $weak = false)
    {
        if (null === $etag) {
            $this->headers->remove('Etag');
        } else {
            if (0 !== strpos($etag, '"')) {
                $etag = '"' . $etag . '"';
            }
            $this->headers->set('ETag', (true === $weak ? 'W/' : '') . $etag);
        }
        return $this;
    }
    public function setCache(array $options)
    {
        if ($diff = array_diff(array_keys($options), array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public'))) {
            throw new \InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', array_values($diff))));
        }
        if (isset($options['etag'])) {
            $this->setEtag($options['etag']);
        }
        if (isset($options['last_modified'])) {
            $this->setLastModified($options['last_modified']);
        }
        if (isset($options['max_age'])) {
            $this->setMaxAge($options['max_age']);
        }
        if (isset($options['s_maxage'])) {
            $this->setSharedMaxAge($options['s_maxage']);
        }
        if (isset($options['public'])) {
            if ($options['public']) {
                $this->setPublic();
            } else {
                $this->setPrivate();
            }
        }
        if (isset($options['private'])) {
            if ($options['private']) {
                $this->setPrivate();
            } else {
                $this->setPublic();
            }
        }
        return $this;
    }
    public function setNotModified()
    {
        $this->setStatusCode(304);
        $this->setContent(null);
        foreach (array('Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified') as $header) {
            $this->headers->remove($header);
        }
        return $this;
    }
    public function hasVary()
    {
        return null !== $this->headers->get('Vary');
    }
    public function getVary()
    {
        if (!($vary = $this->headers->get('Vary', null, false))) {
            return array();
        }
        $ret = array();
        foreach ($vary as $item) {
            $ret = array_merge($ret, preg_split('/[\\s,]+/', $item));
        }
        return $ret;
    }
    public function setVary($headers, $replace = true)
    {
        $this->headers->set('Vary', $headers, $replace);
        return $this;
    }
    public function isNotModified(Request $request)
    {
        if (!$request->isMethodSafe()) {
            return false;
        }
        $lastModified = $request->headers->get('If-Modified-Since');
        $notModified = false;
        if ($etags = $request->getEtags()) {
            $notModified = (in_array($this->getEtag(), $etags) || in_array('*', $etags)) && (!$lastModified || $this->headers->get('Last-Modified') == $lastModified);
        } elseif ($lastModified) {
            $notModified = $lastModified == $this->headers->get('Last-Modified');
        }
        if ($notModified) {
            $this->setNotModified();
        }
        return $notModified;
    }
    public function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }
    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }
    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }
    public function isOk()
    {
        return 200 === $this->statusCode;
    }
    public function isForbidden()
    {
        return 403 === $this->statusCode;
    }
    public function isNotFound()
    {
        return 404 === $this->statusCode;
    }
    public function isRedirect($location = null)
    {
        return in_array($this->statusCode, array(201, 301, 302, 303, 307, 308)) && (null === $location ?: $location == $this->headers->get('Location'));
    }
    public function isEmpty()
    {
        return in_array($this->statusCode, array(204, 304));
    }
    public static function closeOutputBuffers($targetLevel, $flush)
    {
        $status = ob_get_status(true);
        $level = count($status);
        while ($level-- > $targetLevel && (!empty($status[$level]['del']) || isset($status[$level]['flags']) && $status[$level]['flags'] & PHP_OUTPUT_HANDLER_REMOVABLE && $status[$level]['flags'] & ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE))) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }
    protected function ensureIEOverSSLCompatibility(Request $request)
    {
        if (false !== stripos($this->headers->get('Content-Disposition'), 'attachment') && preg_match('/MSIE (.*?);/i', $request->server->get('HTTP_USER_AGENT'), $match) == 1 && true === $request->isSecure()) {
            if (intval(preg_replace('/(MSIE )(.*?);/', '$2', $match[0])) < 9) {
                $this->headers->remove('Cache-Control');
            }
        }
    }
}
namespace Illuminate\Http;

use Symfony\Component\HttpFoundation\Cookie;
trait ResponseTrait
{
    public function header($key, $value, $replace = true)
    {
        $this->headers->set($key, $value, $replace);
        return $this;
    }
    public function withCookie(Cookie $cookie)
    {
        $this->headers->setCookie($cookie);
        return $this;
    }
}
namespace Illuminate\Http;

use ArrayObject;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\RenderableInterface;
class Response extends \Symfony\Component\HttpFoundation\Response
{
    use ResponseTrait;
    public $original;
    public function setContent($content)
    {
        $this->original = $content;
        if ($this->shouldBeJson($content)) {
            $this->headers->set('Content-Type', 'application/json');
            $content = $this->morphToJson($content);
        } elseif ($content instanceof RenderableInterface) {
            $content = $content->render();
        }
        return parent::setContent($content);
    }
    protected function morphToJson($content)
    {
        if ($content instanceof JsonableInterface) {
            return $content->toJson();
        }
        return json_encode($content);
    }
    protected function shouldBeJson($content)
    {
        return $content instanceof JsonableInterface || $content instanceof ArrayObject || is_array($content);
    }
    public function getOriginalContent()
    {
        return $this->original;
    }
}
namespace Symfony\Component\HttpFoundation;

class ResponseHeaderBag extends HeaderBag
{
    const COOKIES_FLAT = 'flat';
    const COOKIES_ARRAY = 'array';
    const DISPOSITION_ATTACHMENT = 'attachment';
    const DISPOSITION_INLINE = 'inline';
    protected $computedCacheControl = array();
    protected $cookies = array();
    protected $headerNames = array();
    public function __construct(array $headers = array())
    {
        parent::__construct($headers);
        if (!isset($this->headers['cache-control'])) {
            $this->set('Cache-Control', '');
        }
    }
    public function __toString()
    {
        $cookies = '';
        foreach ($this->getCookies() as $cookie) {
            $cookies .= 'Set-Cookie: ' . $cookie . '
';
        }
        ksort($this->headerNames);
        return parent::__toString() . $cookies;
    }
    public function allPreserveCase()
    {
        return array_combine($this->headerNames, $this->headers);
    }
    public function replace(array $headers = array())
    {
        $this->headerNames = array();
        parent::replace($headers);
        if (!isset($this->headers['cache-control'])) {
            $this->set('Cache-Control', '');
        }
    }
    public function set($key, $values, $replace = true)
    {
        parent::set($key, $values, $replace);
        $uniqueKey = strtr(strtolower($key), '_', '-');
        $this->headerNames[$uniqueKey] = $key;
        if (in_array($uniqueKey, array('cache-control', 'etag', 'last-modified', 'expires'))) {
            $computed = $this->computeCacheControlValue();
            $this->headers['cache-control'] = array($computed);
            $this->headerNames['cache-control'] = 'Cache-Control';
            $this->computedCacheControl = $this->parseCacheControl($computed);
        }
    }
    public function remove($key)
    {
        parent::remove($key);
        $uniqueKey = strtr(strtolower($key), '_', '-');
        unset($this->headerNames[$uniqueKey]);
        if ('cache-control' === $uniqueKey) {
            $this->computedCacheControl = array();
        }
    }
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl);
    }
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl) ? $this->computedCacheControl[$key] : null;
    }
    public function setCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
    }
    public function removeCookie($name, $path = '/', $domain = null)
    {
        if (null === $path) {
            $path = '/';
        }
        unset($this->cookies[$domain][$path][$name]);
        if (empty($this->cookies[$domain][$path])) {
            unset($this->cookies[$domain][$path]);
            if (empty($this->cookies[$domain])) {
                unset($this->cookies[$domain]);
            }
        }
    }
    public function getCookies($format = self::COOKIES_FLAT)
    {
        if (!in_array($format, array(self::COOKIES_FLAT, self::COOKIES_ARRAY))) {
            throw new \InvalidArgumentException(sprintf('Format "%s" invalid (%s).', $format, implode(', ', array(self::COOKIES_FLAT, self::COOKIES_ARRAY))));
        }
        if (self::COOKIES_ARRAY === $format) {
            return $this->cookies;
        }
        $flattenedCookies = array();
        foreach ($this->cookies as $path) {
            foreach ($path as $cookies) {
                foreach ($cookies as $cookie) {
                    $flattenedCookies[] = $cookie;
                }
            }
        }
        return $flattenedCookies;
    }
    public function clearCookie($name, $path = '/', $domain = null)
    {
        $this->setCookie(new Cookie($name, null, 1, $path, $domain));
    }
    public function makeDisposition($disposition, $filename, $filenameFallback = '')
    {
        if (!in_array($disposition, array(self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE))) {
            throw new \InvalidArgumentException(sprintf('The disposition must be either "%s" or "%s".', self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE));
        }
        if ('' == $filenameFallback) {
            $filenameFallback = $filename;
        }
        if (!preg_match('/^[\\x20-\\x7e]*$/', $filenameFallback)) {
            throw new \InvalidArgumentException('The filename fallback must only contain ASCII characters.');
        }
        if (false !== strpos($filenameFallback, '%')) {
            throw new \InvalidArgumentException('The filename fallback cannot contain the "%" character.');
        }
        if (false !== strpos($filename, '/') || false !== strpos($filename, '\\') || false !== strpos($filenameFallback, '/') || false !== strpos($filenameFallback, '\\')) {
            throw new \InvalidArgumentException('The filename and the fallback cannot contain the "/" and "\\" characters.');
        }
        $output = sprintf('%s; filename="%s"', $disposition, str_replace('"', '\\"', $filenameFallback));
        if ($filename !== $filenameFallback) {
            $output .= sprintf('; filename*=utf-8\'\'%s', rawurlencode($filename));
        }
        return $output;
    }
    protected function computeCacheControlValue()
    {
        if (!$this->cacheControl && !$this->has('ETag') && !$this->has('Last-Modified') && !$this->has('Expires')) {
            return 'no-cache';
        }
        if (!$this->cacheControl) {
            return 'private, must-revalidate';
        }
        $header = $this->getCacheControlHeader();
        if (isset($this->cacheControl['public']) || isset($this->cacheControl['private'])) {
            return $header;
        }
        if (!isset($this->cacheControl['s-maxage'])) {
            return $header . ', private';
        }
        return $header;
    }
}
namespace Symfony\Component\HttpFoundation;

class Cookie
{
    protected $name;
    protected $value;
    protected $domain;
    protected $expire;
    protected $path;
    protected $secure;
    protected $httpOnly;
    public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        if (preg_match('/[=,; 	
]/', $name)) {
            throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }
        if (empty($name)) {
            throw new \InvalidArgumentException('The cookie name cannot be empty.');
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);
            if (false === $expire || -1 === $expire) {
                throw new \InvalidArgumentException('The cookie expiration time is not valid.');
            }
        }
        $this->name = $name;
        $this->value = $value;
        $this->domain = $domain;
        $this->expire = $expire;
        $this->path = empty($path) ? '/' : $path;
        $this->secure = (bool) $secure;
        $this->httpOnly = (bool) $httpOnly;
    }
    public function __toString()
    {
        $str = urlencode($this->getName()) . '=';
        if ('' === (string) $this->getValue()) {
            $str .= 'deleted; expires=' . gmdate('D, d-M-Y H:i:s T', time() - 31536001);
        } else {
            $str .= urlencode($this->getValue());
            if ($this->getExpiresTime() !== 0) {
                $str .= '; expires=' . gmdate('D, d-M-Y H:i:s T', $this->getExpiresTime());
            }
        }
        if ($this->path) {
            $str .= '; path=' . $this->path;
        }
        if ($this->getDomain()) {
            $str .= '; domain=' . $this->getDomain();
        }
        if (true === $this->isSecure()) {
            $str .= '; secure';
        }
        if (true === $this->isHttpOnly()) {
            $str .= '; httponly';
        }
        return $str;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getDomain()
    {
        return $this->domain;
    }
    public function getExpiresTime()
    {
        return $this->expire;
    }
    public function getPath()
    {
        return $this->path;
    }
    public function isSecure()
    {
        return $this->secure;
    }
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }
    public function isCleared()
    {
        return $this->expire < time();
    }
}
namespace Whoops;

use Whoops\Handler\HandlerInterface;
use Whoops\Handler\Handler;
use Whoops\Handler\CallbackHandler;
use Whoops\Exception\Inspector;
use Whoops\Exception\ErrorException;
use InvalidArgumentException;
use Exception;
class Run
{
    const EXCEPTION_HANDLER = 'handleException';
    const ERROR_HANDLER = 'handleError';
    const SHUTDOWN_HANDLER = 'handleShutdown';
    protected $isRegistered;
    protected $allowQuit = true;
    protected $sendOutput = true;
    protected $sendHttpCode = 500;
    protected $handlerStack = array();
    protected $silencedPatterns = array();
    public function pushHandler($handler)
    {
        if (is_callable($handler)) {
            $handler = new CallbackHandler($handler);
        }
        if (!$handler instanceof HandlerInterface) {
            throw new InvalidArgumentException('Argument to ' . __METHOD__ . ' must be a callable, or instance of' . 'Whoops\\Handler\\HandlerInterface');
        }
        $this->handlerStack[] = $handler;
        return $this;
    }
    public function popHandler()
    {
        return array_pop($this->handlerStack);
    }
    public function getHandlers()
    {
        return $this->handlerStack;
    }
    public function clearHandlers()
    {
        $this->handlerStack = array();
        return $this;
    }
    protected function getInspector(Exception $exception)
    {
        return new Inspector($exception);
    }
    public function register()
    {
        if (!$this->isRegistered) {
            class_exists('\\Whoops\\Exception\\ErrorException');
            class_exists('\\Whoops\\Exception\\FrameCollection');
            class_exists('\\Whoops\\Exception\\Frame');
            class_exists('\\Whoops\\Exception\\Inspector');
            set_error_handler(array($this, self::ERROR_HANDLER));
            set_exception_handler(array($this, self::EXCEPTION_HANDLER));
            register_shutdown_function(array($this, self::SHUTDOWN_HANDLER));
            $this->isRegistered = true;
        }
        return $this;
    }
    public function unregister()
    {
        if ($this->isRegistered) {
            restore_exception_handler();
            restore_error_handler();
            $this->isRegistered = false;
        }
        return $this;
    }
    public function allowQuit($exit = null)
    {
        if (func_num_args() == 0) {
            return $this->allowQuit;
        }
        return $this->allowQuit = (bool) $exit;
    }
    public function silenceErrorsInPaths($patterns, $levels = 10240)
    {
        $this->silencedPatterns = array_merge($this->silencedPatterns, array_map(function ($pattern) use($levels) {
            return array('pattern' => $pattern, 'levels' => $levels);
        }, (array) $patterns));
        return $this;
    }
    public function sendHttpCode($code = null)
    {
        if (func_num_args() == 0) {
            return $this->sendHttpCode;
        }
        if (!$code) {
            return $this->sendHttpCode = false;
        }
        if ($code === true) {
            $code = 500;
        }
        if ($code < 400 || 600 <= $code) {
            throw new InvalidArgumentException("Invalid status code '{$code}', must be 4xx or 5xx");
        }
        return $this->sendHttpCode = $code;
    }
    public function writeToOutput($send = null)
    {
        if (func_num_args() == 0) {
            return $this->sendOutput;
        }
        return $this->sendOutput = (bool) $send;
    }
    public function handleException(Exception $exception)
    {
        $inspector = $this->getInspector($exception);
        ob_start();
        $handlerResponse = null;
        foreach (array_reverse($this->handlerStack) as $handler) {
            $handler->setRun($this);
            $handler->setInspector($inspector);
            $handler->setException($exception);
            $handlerResponse = $handler->handle($exception);
            if (in_array($handlerResponse, array(Handler::LAST_HANDLER, Handler::QUIT))) {
                break;
            }
        }
        $willQuit = $handlerResponse == Handler::QUIT && $this->allowQuit();
        $output = ob_get_clean();
        if ($this->writeToOutput()) {
            if ($willQuit) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
            }
            $this->writeToOutputNow($output);
        }
        if ($willQuit) {
            die(1);
        }
        return $output;
    }
    public function handleError($level, $message, $file = null, $line = null)
    {
        if ($level & error_reporting()) {
            foreach ($this->silencedPatterns as $entry) {
                $pathMatches = (bool) preg_match($entry['pattern'], $file);
                $levelMatches = $level & $entry['levels'];
                if ($pathMatches && $levelMatches) {
                    return true;
                }
            }
            $exception = new ErrorException($message, $level, 0, $file, $line);
            if ($this->canThrowExceptions) {
                throw $exception;
            } else {
                $this->handleException($exception);
            }
        }
    }
    public function handleShutdown()
    {
        $this->canThrowExceptions = false;
        $error = error_get_last();
        if ($error && $this->isLevelFatal($error['type'])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    private $canThrowExceptions = true;
    private function writeToOutputNow($output)
    {
        if ($this->sendHttpCode() && \Whoops\Util\Misc::canSendHeaders()) {
            $httpCode = $this->sendHttpCode();
            if (function_exists('http_response_code')) {
                http_response_code($httpCode);
            } else {
                header('X-Ignore-This: 1', true, $httpCode);
            }
        }
        echo $output;
        return $this;
    }
    private static function isLevelFatal($level)
    {
        return in_array($level, array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING));
    }
}
namespace Whoops\Handler;

use Whoops\Exception\Inspector;
use Whoops\Run;
use Exception;
interface HandlerInterface
{
    public function handle();
    public function setRun(Run $run);
    public function setException(Exception $exception);
    public function setInspector(Inspector $inspector);
}
namespace Whoops\Handler;

use Whoops\Handler\HandlerInterface;
use Whoops\Exception\Inspector;
use Whoops\Run;
use Exception;
abstract class Handler implements HandlerInterface
{
    const DONE = 16;
    const LAST_HANDLER = 32;
    const QUIT = 48;
    private $run;
    private $inspector;
    private $exception;
    public function setRun(Run $run)
    {
        $this->run = $run;
    }
    protected function getRun()
    {
        return $this->run;
    }
    public function setInspector(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }
    protected function getInspector()
    {
        return $this->inspector;
    }
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }
    protected function getException()
    {
        return $this->exception;
    }
}
namespace Whoops\Handler;

use Whoops\Handler\Handler;
use Whoops\Exception\Formatter;
class JsonResponseHandler extends Handler
{
    private $returnFrames = false;
    private $onlyForAjaxRequests = false;
    public function addTraceToOutput($returnFrames = null)
    {
        if (func_num_args() == 0) {
            return $this->returnFrames;
        }
        $this->returnFrames = (bool) $returnFrames;
        return $this;
    }
    public function onlyForAjaxRequests($onlyForAjaxRequests = null)
    {
        if (func_num_args() == 0) {
            return $this->onlyForAjaxRequests;
        }
        $this->onlyForAjaxRequests = (bool) $onlyForAjaxRequests;
    }
    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    public function handle()
    {
        if ($this->onlyForAjaxRequests() && !$this->isAjaxRequest()) {
            return Handler::DONE;
        }
        $response = array('error' => Formatter::formatExceptionAsDataArray($this->getInspector(), $this->addTraceToOutput()));
        if (\Whoops\Util\Misc::canSendHeaders()) {
            header('Content-Type: application/json');
        }
        echo json_encode($response);
        return Handler::QUIT;
    }
}
namespace Stack;

use Symfony\Component\HttpKernel\HttpKernelInterface;
class Builder
{
    private $specs;
    public function __construct()
    {
        $this->specs = new \SplStack();
    }
    public function unshift()
    {
        if (func_num_args() === 0) {
            throw new \InvalidArgumentException('Missing argument(s) when calling unshift');
        }
        $spec = func_get_args();
        $this->specs->unshift($spec);
        return $this;
    }
    public function push()
    {
        if (func_num_args() === 0) {
            throw new \InvalidArgumentException('Missing argument(s) when calling push');
        }
        $spec = func_get_args();
        $this->specs->push($spec);
        return $this;
    }
    public function resolve(HttpKernelInterface $app)
    {
        $middlewares = array($app);
        foreach ($this->specs as $spec) {
            $args = $spec;
            $firstArg = array_shift($args);
            if (is_callable($firstArg)) {
                $app = $firstArg($app);
            } else {
                $kernelClass = $firstArg;
                array_unshift($args, $app);
                $reflection = new \ReflectionClass($kernelClass);
                $app = $reflection->newInstanceArgs($args);
            }
            array_unshift($middlewares, $app);
        }
        return new StackedHttpKernel($app, $middlewares);
    }
}
namespace Stack;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class StackedHttpKernel implements HttpKernelInterface, TerminableInterface
{
    private $app;
    private $middlewares = array();
    public function __construct(HttpKernelInterface $app, array $middlewares)
    {
        $this->app = $app;
        $this->middlewares = $middlewares;
    }
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->app->handle($request, $type, $catch);
    }
    public function terminate(Request $request, Response $response)
    {
        foreach ($this->middlewares as $kernel) {
            if ($kernel instanceof TerminableInterface) {
                $kernel->terminate($request, $response);
            }
        }
    }
}
