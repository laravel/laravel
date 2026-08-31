<?php

namespace Illuminate\Routing;

use BackedEnum;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Contracts\CallableDispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Matching\HostValidator;
use Illuminate\Routing\Matching\MethodValidator;
use Illuminate\Routing\Matching\SchemeValidator;
use Illuminate\Routing\Matching\UriValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use LogicException;
use Symfony\Component\Routing\Route as SymfonyRoute;

use function Illuminate\Support\enum_value;

class Route
{
    use Conditionable, CreatesRegularExpressionRouteConstraints, FiltersControllerMiddleware, Macroable, ResolvesRouteDependencies;

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    public $uri;

    /**
     * The HTTP methods the route responds to.
     *
     * @var array
     */
    public $methods;

    /**
     * The route action array.
     *
     * @var array
     */
    public $action;

    /**
     * Indicates whether the route is a fallback route.
     *
     * @var bool
     */
    public $isFallback = false;

    /**
     * The controller instance.
     *
     * @var mixed
     */
    public $controller;

    /**
     * The default values for the route.
     *
     * @var array
     */
    public $defaults = [];

    /**
     * The regular expression requirements.
     *
     * @var array
     */
    public $wheres = [];

    /**
     * The array of matched parameters.
     *
     * @var array|null
     */
    public $parameters;

    /**
     * The parameter names for the route.
     *
     * @var array|null
     */
    public $parameterNames;

    /**
     * The array of the matched parameters' original values.
     *
     * @var array
     */
    protected $originalParameters;

    /**
     * Indicates "trashed" models can be retrieved when resolving implicit model bindings for this route.
     *
     * @var bool
     */
    protected $withTrashedBindings = false;

    /**
     * Indicates the maximum number of seconds the route should acquire a session lock for.
     *
     * @var int|null
     */
    protected $lockSeconds;

    /**
     * Indicates the maximum number of seconds the route should wait while attempting to acquire a session lock.
     *
     * @var int|null
     */
    protected $waitSeconds;

    /**
     * The computed gathered middleware.
     *
     * @var array|null
     */
    public $computedMiddleware;

    /**
     * The compiled version of the route.
     *
     * @var \Symfony\Component\Routing\CompiledRoute
     */
    public $compiled;

    /**
     * The router instance used by the route.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * The container instance used by the route.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The fields that implicit binding should use for a given parameter.
     *
     * @var array
     */
    protected $bindingFields = [];

    /**
     * The validators used by the routes.
     *
     * @var array
     */
    public static $validators;

    /**
     * Create a new Route instance.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array  $action
     */
    public function __construct($methods, $uri, $action)
    {
        $this->uri = $uri;
        $this->methods = (array) $methods;
        $this->action = Arr::except($this->parseAction($action), ['prefix']);

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }

        $this->prefix(is_array($action) ? Arr::get($action, 'prefix') : '');
    }

    /**
     * Parse the route action into a standard array.
     *
     * @param  callable|array|null  $action
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    protected function parseAction($action)
    {
        return RouteAction::parse($this->uri, $action);
    }

    /**
     * Run the route action and return the response.
     *
     * @return mixed
     */
    public function run()
    {
        $this->container = $this->container ?: new Container;

        try {
            if ($this->isControllerAction()) {
                return $this->runController();
            }

            return $this->runCallable();
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Checks whether the route's action is a controller.
     *
     * @return bool
     */
    protected function isControllerAction()
    {
        return is_string($this->action['uses']) && ! $this->isSerializedClosure();
    }

    /**
     * Run the route action and return the response.
     *
     * @return mixed
     */
    protected function runCallable()
    {
        $callable = $this->action['uses'];

        if ($this->isSerializedClosure()) {
            $callable = unserialize($this->action['uses'])->getClosure();
        }

        return $this->container[CallableDispatcher::class]->dispatch($this, $callable);
    }

    /**
     * Determine if the route action is a serialized Closure.
     *
     * @return bool
     */
    protected function isSerializedClosure()
    {
        return RouteAction::containsSerializedClosure($this->action);
    }

    /**
     * Run the route action and return the response.
     *
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function runController()
    {
        return $this->controllerDispatcher()->dispatch(
            $this, $this->getController(), $this->getControllerMethod()
        );
    }

    /**
     * Get the controller instance for the route.
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getController()
    {
        if (! $this->isControllerAction()) {
            return null;
        }

        if (! $this->controller) {
            $class = $this->getControllerClass();

            $this->controller = $this->container->make(ltrim($class, '\\'));
        }

        return $this->controller;
    }

    /**
     * Get the controller class used for the route.
     *
     * @return string|null
     */
    public function getControllerClass()
    {
        return $this->isControllerAction() ? $this->parseControllerCallback()[0] : null;
    }

    /**
     * Get the controller method used for the route.
     *
     * @return string
     */
    protected function getControllerMethod()
    {
        return $this->parseControllerCallback()[1];
    }

    /**
     * Parse the controller.
     *
     * @return array
     */
    protected function parseControllerCallback()
    {
        return Str::parseCallback($this->action['uses']);
    }

    /**
     * Flush the cached container instance on the route.
     *
     * @return void
     */
    public function flushController()
    {
        $this->computedMiddleware = null;
        $this->controller = null;
    }

    /**
     * Determine if the route matches a given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $includingMethod
     * @return bool
     */
    public function matches(Request $request, $includingMethod = true)
    {
        $this->compileRoute();

        foreach (self::getValidators() as $validator) {
            if (! $includingMethod && $validator instanceof MethodValidator) {
                continue;
            }

            if (! $validator->matches($this, $request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compile the route into a Symfony CompiledRoute instance.
     *
     * @return \Symfony\Component\Routing\CompiledRoute
     */
    protected function compileRoute()
    {
        if (! $this->compiled) {
            $this->compiled = $this->toSymfonyRoute()->compile();
        }

        return $this->compiled;
    }

    /**
     * Bind the route to a given request for execution.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $this
     */
    public function bind(Request $request)
    {
        $this->compileRoute();

        $this->parameters = (new RouteParameterBinder($this))
            ->parameters($request);

        $this->originalParameters = $this->parameters;

        return $this;
    }

    /**
     * Determine if the route has parameters.
     *
     * @return bool
     */
    public function hasParameters()
    {
        return isset($this->parameters);
    }

    /**
     * Determine a given parameter exists from the route.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasParameter($name)
    {
        if ($this->hasParameters()) {
            return array_key_exists($name, $this->parameters());
        }

        return false;
    }

    /**
     * Get a given parameter from the route.
     *
     * @param  string  $name
     * @param  string|object|null  $default
     * @return string|object|null
     */
    public function parameter($name, $default = null)
    {
        return Arr::get($this->parameters(), $name, $default);
    }

    /**
     * Get original value of a given parameter from the route.
     *
     * @param  string  $name
     * @param  string|null  $default
     * @return string|null
     */
    public function originalParameter($name, $default = null)
    {
        return Arr::get($this->originalParameters(), $name, $default);
    }

    /**
     * Set a parameter to the given value.
     *
     * @param  string  $name
     * @param  string|object|null  $value
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters();

        $this->parameters[$name] = $value;
    }

    /**
     * Unset a parameter on the route if it is set.
     *
     * @param  string  $name
     * @return void
     */
    public function forgetParameter($name)
    {
        $this->parameters();

        unset($this->parameters[$name]);
    }

    /**
     * Get the key / value list of parameters for the route.
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function parameters()
    {
        if (isset($this->parameters)) {
            return $this->parameters;
        }

        throw new LogicException('Route is not bound.');
    }

    /**
     * Get the key / value list of original parameters for the route.
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function originalParameters()
    {
        if (isset($this->originalParameters)) {
            return $this->originalParameters;
        }

        throw new LogicException('Route is not bound.');
    }

    /**
     * Get the key / value list of parameters without null values.
     *
     * @return array
     */
    public function parametersWithoutNulls()
    {
        return array_filter($this->parameters(), fn ($p) => ! is_null($p));
    }

    /**
     * Get all of the parameter names for the route.
     *
     * @return array
     */
    public function parameterNames()
    {
        if (isset($this->parameterNames)) {
            return $this->parameterNames;
        }

        return $this->parameterNames = $this->compileParameterNames();
    }

    /**
     * Get the parameter names for the route.
     *
     * @return array
     */
    protected function compileParameterNames()
    {
        preg_match_all('/\{(.*?)\}/', $this->getDomain().$this->uri, $matches);

        return array_map(fn ($m) => trim($m, '?'), $matches[1]);
    }

    /**
     * Get the parameters that are listed in the route / controller signature.
     *
     * @param  array  $conditions
     * @return array
     */
    public function signatureParameters($conditions = [])
    {
        if (is_string($conditions)) {
            $conditions = ['subClass' => $conditions];
        }

        return RouteSignatureParameters::fromAction($this->action, $conditions);
    }

    /**
     * Get the binding field for the given parameter.
     *
     * @param  string|int  $parameter
     * @return string|null
     */
    public function bindingFieldFor($parameter)
    {
        $fields = is_int($parameter) ? array_values($this->bindingFields) : $this->bindingFields;

        return $fields[$parameter] ?? null;
    }

    /**
     * Get the binding fields for the route.
     *
     * @return array
     */
    public function bindingFields()
    {
        return $this->bindingFields ?? [];
    }

    /**
     * Set the binding fields for the route.
     *
     * @param  array  $bindingFields
     * @return $this
     */
    public function setBindingFields(array $bindingFields)
    {
        $this->bindingFields = $bindingFields;

        return $this;
    }

    /**
     * Get the parent parameter of the given parameter.
     *
     * @param  string  $parameter
     * @return string|null
     */
    public function parentOfParameter($parameter)
    {
        $key = array_search($parameter, array_keys($this->parameters));

        if ($key === 0 || $key === false) {
            return;
        }

        return array_values($this->parameters)[$key - 1];
    }

    /**
     * Allow "trashed" models to be retrieved when resolving implicit model bindings for this route.
     *
     * @param  bool  $withTrashed
     * @return $this
     */
    public function withTrashed($withTrashed = true)
    {
        $this->withTrashedBindings = $withTrashed;

        return $this;
    }

    /**
     * Determines if the route allows "trashed" models to be retrieved when resolving implicit model bindings.
     *
     * @return bool
     */
    public function allowsTrashedBindings()
    {
        return $this->withTrashedBindings;
    }

    /**
     * Set a default value for the route.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function defaults($key, $value)
    {
        $this->defaults[$key] = $value;

        return $this;
    }

    /**
     * Set the default values for the route.
     *
     * @param  array  $defaults
     * @return $this
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Set a regular expression requirement on the route.
     *
     * @param  array|string  $name
     * @param  string|null  $expression
     * @return $this
     */
    public function where($name, $expression = null)
    {
        foreach ($this->parseWhere($name, $expression) as $name => $expression) {
            $this->wheres[$name] = $expression;
        }

        return $this;
    }

    /**
     * Parse arguments to the where method into an array.
     *
     * @param  array|string  $name
     * @param  string  $expression
     * @return array
     */
    protected function parseWhere($name, $expression)
    {
        return is_array($name) ? $name : [$name => $expression];
    }

    /**
     * Set a list of regular expression requirements on the route.
     *
     * @param  array  $wheres
     * @return $this
     */
    public function setWheres(array $wheres)
    {
        foreach ($wheres as $name => $expression) {
            $this->where($name, $expression);
        }

        return $this;
    }

    /**
     * Mark this route as a fallback route.
     *
     * @return $this
     */
    public function fallback()
    {
        $this->isFallback = true;

        return $this;
    }

    /**
     * Set the fallback value.
     *
     * @param  bool  $isFallback
     * @return $this
     */
    public function setFallback($isFallback)
    {
        $this->isFallback = $isFallback;

        return $this;
    }

    /**
     * Get the HTTP verbs the route responds to.
     *
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * Determine if the route only responds to HTTP requests.
     *
     * @return bool
     */
    public function httpOnly()
    {
        return in_array('http', $this->action, true);
    }

    /**
     * Determine if the route only responds to HTTPS requests.
     *
     * @return bool
     */
    public function httpsOnly()
    {
        return $this->secure();
    }

    /**
     * Determine if the route only responds to HTTPS requests.
     *
     * @return bool
     */
    public function secure()
    {
        return in_array('https', $this->action, true);
    }

    /**
     * Get or set the domain for the route.
     *
     * @param  \BackedEnum|string|null  $domain
     * @return $this|string|null
     *
     * @throws \InvalidArgumentException
     */
    public function domain($domain = null)
    {
        if (is_null($domain)) {
            return $this->getDomain();
        }

        if ($domain instanceof BackedEnum && ! is_string($domain = $domain->value)) {
            throw new InvalidArgumentException('Enum must be string backed.');
        }

        $parsed = RouteUri::parse($domain);

        $this->action['domain'] = $parsed->uri;

        $this->bindingFields = array_merge(
            $this->bindingFields, $parsed->bindingFields
        );

        return $this;
    }

    /**
     * Get the domain defined for the route.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return isset($this->action['domain'])
            ? str_replace(['http://', 'https://'], '', $this->action['domain'])
            : null;
    }

    /**
     * Get the prefix of the route instance.
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->action['prefix'] ?? null;
    }

    /**
     * Add a prefix to the route URI.
     *
     * @param  string|null  $prefix
     * @return $this
     */
    public function prefix($prefix)
    {
        $prefix ??= '';

        $this->updatePrefixOnAction($prefix);

        $uri = rtrim($prefix, '/').'/'.ltrim($this->uri, '/');

        return $this->setUri($uri !== '/' ? trim($uri, '/') : $uri);
    }

    /**
     * Update the "prefix" attribute on the action array.
     *
     * @param  string  $prefix
     * @return void
     */
    protected function updatePrefixOnAction($prefix)
    {
        if (! empty($newPrefix = trim(rtrim($prefix, '/').'/'.ltrim($this->action['prefix'] ?? '', '/'), '/'))) {
            $this->action['prefix'] = $newPrefix;
        }
    }

    /**
     * Get the URI associated with the route.
     *
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Set the URI that the route responds to.
     *
     * @param  string  $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $this->parseUri($uri);

        return $this;
    }

    /**
     * Parse the route URI and normalize / store any implicit binding fields.
     *
     * @param  string  $uri
     * @return string
     */
    protected function parseUri($uri)
    {
        $this->bindingFields = [];

        return tap(RouteUri::parse($uri), function ($uri) {
            $this->bindingFields = $uri->bindingFields;
        })->uri;
    }

    /**
     * Get the name of the route instance.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->action['as'] ?? null;
    }

    /**
     * Add or change the route name.
     *
     * @param  \BackedEnum|string  $name
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function name($name)
    {
        if ($name instanceof BackedEnum && ! is_string($name = $name->value)) {
            throw new InvalidArgumentException('Enum must be string backed.');
        }

        $this->action['as'] = isset($this->action['as']) ? $this->action['as'].$name : $name;

        return $this;
    }

    /**
     * Determine whether the route's name matches the given patterns.
     *
     * @param  mixed  ...$patterns
     * @return bool
     */
    public function named(...$patterns)
    {
        if (is_null($routeName = $this->getName())) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the handler for the route.
     *
     * @param  \Closure|array|string  $action
     * @return $this
     */
    public function uses($action)
    {
        if (is_array($action)) {
            $action = $action[0].'@'.$action[1];
        }

        $action = is_string($action) ? $this->addGroupNamespaceToStringUses($action) : $action;

        return $this->setAction(array_merge($this->action, $this->parseAction([
            'uses' => $action,
            'controller' => $action,
        ])));
    }

    /**
     * Parse a string based action for the "uses" fluent method.
     *
     * @param  string  $action
     * @return string
     */
    protected function addGroupNamespaceToStringUses($action)
    {
        $groupStack = last($this->router->getGroupStack());

        if (isset($groupStack['namespace']) && ! str_starts_with($action, '\\')) {
            return $groupStack['namespace'].'\\'.$action;
        }

        return $action;
    }

    /**
     * Get the action name for the route.
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->action['controller'] ?? 'Closure';
    }

    /**
     * Get the method name of the route action.
     *
     * @return string
     */
    public function getActionMethod()
    {
        return Arr::last(explode('@', $this->getActionName()));
    }

    /**
     * Get the action array or one of its properties for the route.
     *
     * @param  string|null  $key
     * @return mixed
     */
    public function getAction($key = null)
    {
        return Arr::get($this->action, $key);
    }

    /**
     * Set the action array for the route.
     *
     * @param  array  $action
     * @return $this
     */
    public function setAction(array $action)
    {
        $this->action = $action;

        if (isset($this->action['domain'])) {
            $this->domain($this->action['domain']);
        }

        if (isset($this->action['can'])) {
            foreach ($this->action['can'] as $can) {
                $this->can($can[0], $can[1] ?? []);
            }
        }

        return $this;
    }

    /**
     * Get the value of the action that should be taken on a missing model exception.
     *
     * @return \Closure|null
     */
    public function getMissing()
    {
        $missing = $this->action['missing'] ?? null;

        return is_string($missing) &&
            Str::startsWith($missing, [
                'O:47:"Laravel\\SerializableClosure\\SerializableClosure',
                'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure',
            ]) ? unserialize($missing) : $missing;
    }

    /**
     * Define the callable that should be invoked on a missing model exception.
     *
     * @param  \Closure  $missing
     * @return $this
     */
    public function missing($missing)
    {
        $this->action['missing'] = $missing;

        return $this;
    }

    /**
     * Get all middleware, including the ones from the controller.
     *
     * @return array
     */
    public function gatherMiddleware()
    {
        if (! is_null($this->computedMiddleware)) {
            return $this->computedMiddleware;
        }

        $this->computedMiddleware = [];

        return $this->computedMiddleware = Router::uniqueMiddleware(array_merge(
            $this->middleware(), $this->controllerMiddleware()
        ));
    }

    /**
     * Get or set the middlewares attached to the route.
     *
     * @param  array|string|null  $middleware
     * @return ($middleware is null ? array : $this)
     */
    public function middleware($middleware = null)
    {
        if (is_null($middleware)) {
            return (array) ($this->action['middleware'] ?? []);
        }

        if (! is_array($middleware)) {
            $middleware = func_get_args();
        }

        foreach ($middleware as $index => $value) {
            $middleware[$index] = (string) $value;
        }

        $this->action['middleware'] = array_merge(
            (array) ($this->action['middleware'] ?? []), $middleware
        );

        return $this;
    }

    /**
     * Specify that the "Authorize" / "can" middleware should be applied to the route with the given options.
     *
     * @param  \UnitEnum|string  $ability
     * @param  array|string  $models
     * @return $this
     */
    public function can($ability, $models = [])
    {
        $ability = enum_value($ability);

        return empty($models)
            ? $this->middleware(['can:'.$ability])
            : $this->middleware(['can:'.$ability.','.implode(',', Arr::wrap($models))]);
    }

    /**
     * Get the middleware for the route's controller.
     *
     * @return array
     */
    public function controllerMiddleware()
    {
        if (! $this->isControllerAction()) {
            return [];
        }

        [$controllerClass, $controllerMethod] = [
            $this->getControllerClass(),
            $this->getControllerMethod(),
        ];

        if (is_a($controllerClass, HasMiddleware::class, true)) {
            return $this->staticallyProvidedControllerMiddleware(
                $controllerClass, $controllerMethod
            );
        }

        if (method_exists($controllerClass, 'getMiddleware')) {
            return $this->controllerDispatcher()->getMiddleware(
                $this->getController(), $controllerMethod
            );
        }

        return [];
    }

    /**
     * Get the statically provided controller middleware for the given class and method.
     *
     * @param  string  $class
     * @param  string  $method
     * @return array
     */
    protected function staticallyProvidedControllerMiddleware(string $class, string $method)
    {
        return (new Collection($class::middleware()))
            ->map(function ($middleware) {
                return $middleware instanceof Middleware
                    ? $middleware
                    : new Middleware($middleware);
            })
            ->reject(function ($middleware) use ($method) {
                return static::methodExcludedByOptions(
                    $method, ['only' => $middleware->only, 'except' => $middleware->except],
                );
            })
            ->map
            ->middleware
            ->flatten()
            ->values()
            ->all();
    }

    /**
     * Specify middleware that should be removed from the given route.
     *
     * @param  array|string  $middleware
     * @return $this
     */
    public function withoutMiddleware($middleware)
    {
        $this->action['excluded_middleware'] = array_merge(
            (array) ($this->action['excluded_middleware'] ?? []), Arr::wrap($middleware)
        );

        return $this;
    }

    /**
     * Get the middleware that should be removed from the route.
     *
     * @return array
     */
    public function excludedMiddleware()
    {
        return (array) ($this->action['excluded_middleware'] ?? []);
    }

    /**
     * Indicate that the route should enforce scoping of multiple implicit Eloquent bindings.
     *
     * @return $this
     */
    public function scopeBindings()
    {
        $this->action['scope_bindings'] = true;

        return $this;
    }

    /**
     * Indicate that the route should not enforce scoping of multiple implicit Eloquent bindings.
     *
     * @return $this
     */
    public function withoutScopedBindings()
    {
        $this->action['scope_bindings'] = false;

        return $this;
    }

    /**
     * Determine if the route should enforce scoping of multiple implicit Eloquent bindings.
     *
     * @return bool
     */
    public function enforcesScopedBindings()
    {
        return (bool) ($this->action['scope_bindings'] ?? false);
    }

    /**
     * Determine if the route should prevent scoping of multiple implicit Eloquent bindings.
     *
     * @return bool
     */
    public function preventsScopedBindings()
    {
        return isset($this->action['scope_bindings']) && $this->action['scope_bindings'] === false;
    }

    /**
     * Specify that the route should not allow concurrent requests from the same session.
     *
     * @param  int|null  $lockSeconds
     * @param  int|null  $waitSeconds
     * @return $this
     */
    public function block($lockSeconds = 10, $waitSeconds = 10)
    {
        $this->lockSeconds = $lockSeconds;
        $this->waitSeconds = $waitSeconds;

        return $this;
    }

    /**
     * Specify that the route should allow concurrent requests from the same session.
     *
     * @return $this
     */
    public function withoutBlocking()
    {
        return $this->block(null, null);
    }

    /**
     * Get the maximum number of seconds the route's session lock should be held for.
     *
     * @return int|null
     */
    public function locksFor()
    {
        return $this->lockSeconds;
    }

    /**
     * Get the maximum number of seconds to wait while attempting to acquire a session lock.
     *
     * @return int|null
     */
    public function waitsFor()
    {
        return $this->waitSeconds;
    }

    /**
     * Get the dispatcher for the route's controller.
     *
     * @return \Illuminate\Routing\Contracts\ControllerDispatcher
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function controllerDispatcher()
    {
        if ($this->container->bound(ControllerDispatcherContract::class)) {
            return $this->container->make(ControllerDispatcherContract::class);
        }

        return new ControllerDispatcher($this->container);
    }

    /**
     * Get the route validators for the instance.
     *
     * @return array
     */
    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }

        // To match the route, we will use a chain of responsibility pattern with the
        // validator implementations. We will spin through each one making sure it
        // passes and then we will know if the route as a whole matches request.
        return static::$validators = [
            new UriValidator, new MethodValidator,
            new SchemeValidator, new HostValidator,
        ];
    }

    /**
     * Convert the route to a Symfony route.
     *
     * @return \Symfony\Component\Routing\Route
     */
    public function toSymfonyRoute()
    {
        return new SymfonyRoute(
            preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->uri()), $this->getOptionalParameterNames(),
            $this->wheres, ['utf8' => true],
            $this->getDomain() ?: '', [], $this->methods
        );
    }

    /**
     * Get the optional parameter names for the route.
     *
     * @return array<string, null>
     */
    public function getOptionalParameterNames()
    {
        preg_match_all('/\{(\w+?)\?\}/', $this->uri(), $matches);

        return isset($matches[1]) ? array_fill_keys($matches[1], null) : [];
    }

    /**
     * Get the compiled version of the route.
     *
     * @return \Symfony\Component\Routing\CompiledRoute
     */
    public function getCompiled()
    {
        return $this->compiled;
    }

    /**
     * Set the router instance on the route.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Set the container instance on the route.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Prepare the route instance for serialization.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function prepareForSerialization()
    {
        if ($this->action['uses'] instanceof Closure) {
            $this->action['uses'] = serialize(
                SerializableClosure::unsigned($this->action['uses'])
            );
        }

        if (isset($this->action['missing']) && $this->action['missing'] instanceof Closure) {
            $this->action['missing'] = serialize(
                SerializableClosure::unsigned($this->action['missing'])
            );
        }

        $this->compileRoute();

        unset($this->router, $this->container);
    }

    /**
     * Dynamically access route parameters.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->parameter($key);
    }
}
