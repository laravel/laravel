<?php
/**
 * An helper file for Laravel 4, to provide autocomplete information to your IDE
 * Generated with https://github.com/barryvdh/laravel-ide-helper
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace {
	exit('Only to be used as an helper for your IDE');

	class App extends \Illuminate\Support\Facades\App{
		/**
		 * Create a new Illuminate application instance.
		 *
		 * @param \Illuminate\Http\Request  $request
		 * @return void
		 * @static 
		 */
		 public static function __construct($request = null){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::__construct($request);
		 }

		/**
		 * Bind the installation paths to the application.
		 *
		 * @param array  $paths
		 * @return void
		 * @static 
		 */
		 public static function bindInstallPaths($paths){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::bindInstallPaths($paths);
		 }

		/**
		 * Get the application bootstrap file.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getBootstrapFile(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::getBootstrapFile();
		 }

		/**
		 * Start the exception handling for the request.
		 *
		 * @return void
		 * @static 
		 */
		 public static function startExceptionHandling(){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::startExceptionHandling();
		 }

		/**
		 * Get or check the current application environment.
		 *
		 * @param dynamic
		 * @return string
		 * @static 
		 */
		 public static function environment(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::environment();
		 }

		/**
		 * Determine if application is in local environment.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isLocal(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::isLocal();
		 }

		/**
		 * Detect the application's current environment.
		 *
		 * @param array|string  $envs
		 * @return string
		 * @static 
		 */
		 public static function detectEnvironment($envs){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::detectEnvironment($envs);
		 }

		/**
		 * Determine if we are running in the console.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function runningInConsole(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::runningInConsole();
		 }

		/**
		 * Determine if we are running unit tests.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function runningUnitTests(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::runningUnitTests();
		 }

		/**
		 * Force register a service provider with the application.
		 *
		 * @param \Illuminate\Support\ServiceProvider|string  $provider
		 * @param array  $options
		 * @return \Illuminate\Support\ServiceProvider
		 * @static 
		 */
		 public static function forceRegister($provider, $options = array()){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::forceRegister($provider, $options);
		 }

		/**
		 * Register a service provider with the application.
		 *
		 * @param \Illuminate\Support\ServiceProvider|string  $provider
		 * @param array  $options
		 * @param bool   $force
		 * @return \Illuminate\Support\ServiceProvider
		 * @static 
		 */
		 public static function register($provider, $options = array(), $force = false){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::register($provider, $options, $force);
		 }

		/**
		 * Get the registered service provider instance if it exists.
		 *
		 * @param \Illuminate\Support\ServiceProvider|string  $provider
		 * @return \Illuminate\Support\ServiceProvider|null
		 * @static 
		 */
		 public static function getRegistered($provider){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::getRegistered($provider);
		 }

		/**
		 * Resolve a service provider instance from the class name.
		 *
		 * @param string  $provider
		 * @return \Illuminate\Support\ServiceProvider
		 * @static 
		 */
		 public static function resolveProviderClass($provider){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::resolveProviderClass($provider);
		 }

		/**
		 * Load and boot all of the remaining deferred providers.
		 *
		 * @return void
		 * @static 
		 */
		 public static function loadDeferredProviders(){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::loadDeferredProviders();
		 }

		/**
		 * Register a deferred provider and service.
		 *
		 * @param string  $provider
		 * @param string  $service
		 * @return void
		 * @static 
		 */
		 public static function registerDeferredProvider($provider, $service = null){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::registerDeferredProvider($provider, $service);
		 }

		/**
		 * Resolve the given type from the container.
		 * 
		 * (Overriding Container::make)
		 *
		 * @param string  $abstract
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function make($abstract, $parameters = array()){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::make($abstract, $parameters);
		 }

		/**
		 * Determine if the given abstract type has been bound.
		 * 
		 * (Overriding Container::bound)
		 *
		 * @param string  $abstract
		 * @return bool
		 * @static 
		 */
		 public static function bound($abstract){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::bound($abstract);
		 }

		/**
		 * "Extend" an abstract type in the container.
		 * 
		 * (Overriding Container::extend)
		 *
		 * @param string   $abstract
		 * @param Closure  $closure
		 * @return void
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function extend($abstract, $closure){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::extend($abstract, $closure);
		 }

		/**
		 * Register a "before" application filter.
		 *
		 * @param \Closure|string  $callback
		 * @return void
		 * @static 
		 */
		 public static function before($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::before($callback);
		 }

		/**
		 * Register an "after" application filter.
		 *
		 * @param \Closure|string  $callback
		 * @return void
		 * @static 
		 */
		 public static function after($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::after($callback);
		 }

		/**
		 * Register a "finish" application filter.
		 *
		 * @param \Closure|string  $callback
		 * @return void
		 * @static 
		 */
		 public static function finish($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::finish($callback);
		 }

		/**
		 * Register a "shutdown" callback.
		 *
		 * @param callable  $callback
		 * @return void
		 * @static 
		 */
		 public static function shutdown($callback = null){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::shutdown($callback);
		 }

		/**
		 * Register a function for determining when to use array sessions.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function useArraySessions($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::useArraySessions($callback);
		 }

		/**
		 * Determine if the application has booted.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isBooted(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::isBooted();
		 }

		/**
		 * Boot the application's service providers.
		 *
		 * @return void
		 * @static 
		 */
		 public static function boot(){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::boot();
		 }

		/**
		 * Register a new boot listener.
		 *
		 * @param mixed  $callback
		 * @return void
		 * @static 
		 */
		 public static function booting($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::booting($callback);
		 }

		/**
		 * Register a new "booted" listener.
		 *
		 * @param mixed  $callback
		 * @return void
		 * @static 
		 */
		 public static function booted($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::booted($callback);
		 }

		/**
		 * Run the application and send the response.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @return void
		 * @static 
		 */
		 public static function run($request = null){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::run($request);
		 }

		/**
		 * Add a HttpKernel middleware onto the stack.
		 *
		 * @param string  $class
		 * @param array  $parameters
		 * @return \Illuminate\Foundation\Application
		 * @static 
		 */
		 public static function middleware($class, $parameters = array()){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::middleware($class, $parameters);
		 }

		/**
		 * Remove a custom middleware from the application.
		 *
		 * @param string  $class
		 * @return void
		 * @static 
		 */
		 public static function forgetMiddleware($class){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::forgetMiddleware($class);
		 }

		/**
		 * Handle the given request and get the response.
		 * 
		 * Provides compatibility with BrowserKit functional testing.
		 *
		 * @implements HttpKernelInterface::handle
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @param int   $type
		 * @param bool  $catch
		 * @return \Symfony\Component\HttpFoundation\Response
		 * @throws \Exception
		 * @static 
		 */
		 public static function handle($request, $type = 1, $catch = true){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::handle($request, $type, $catch);
		 }

		/**
		 * Handle the given request and get the response.
		 *
		 * @param \Illuminate\Http\Request  $request
		 * @return \Symfony\Component\HttpFoundation\Response
		 * @static 
		 */
		 public static function dispatch($request){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::dispatch($request);
		 }

		/**
		 * Terminate the request and send the response to the browser.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @param \Symfony\Component\HttpFoundation\Response  $response
		 * @return void
		 * @static 
		 */
		 public static function terminate($request, $response){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::terminate($request, $response);
		 }

		/**
		 * Call the "finish" callbacks assigned to the application.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @param \Symfony\Component\HttpFoundation\Response  $response
		 * @return void
		 * @static 
		 */
		 public static function callFinishCallbacks($request, $response){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::callFinishCallbacks($request, $response);
		 }

		/**
		 * Prepare the request by injecting any services.
		 *
		 * @param \Illuminate\Http\Request  $request
		 * @return \Illuminate\Http\Request
		 * @static 
		 */
		 public static function prepareRequest($request){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::prepareRequest($request);
		 }

		/**
		 * Prepare the given value as a Response object.
		 *
		 * @param mixed  $value
		 * @return \Symfony\Component\HttpFoundation\Response
		 * @static 
		 */
		 public static function prepareResponse($value){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::prepareResponse($value);
		 }

		/**
		 * Determine if the application is ready for responses.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function readyForResponses(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::readyForResponses();
		 }

		/**
		 * Determine if the application is currently down for maintenance.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isDownForMaintenance(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::isDownForMaintenance();
		 }

		/**
		 * Register a maintenance mode event listener.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function down($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::down($callback);
		 }

		/**
		 * Throw an HttpException with the given data.
		 *
		 * @param int     $code
		 * @param string  $message
		 * @param array   $headers
		 * @return void
		 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
		 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
		 * @static 
		 */
		 public static function abort($code, $message = '', $headers = array()){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::abort($code, $message, $headers);
		 }

		/**
		 * Register a 404 error handler.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function missing($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::missing($callback);
		 }

		/**
		 * Register an application error handler.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function error($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::error($callback);
		 }

		/**
		 * Register an error handler at the bottom of the stack.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function pushError($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::pushError($callback);
		 }

		/**
		 * Register an error handler for fatal errors.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function fatal($callback){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::fatal($callback);
		 }

		/**
		 * Get the configuration loader instance.
		 *
		 * @return \Illuminate\Config\LoaderInterface
		 * @static 
		 */
		 public static function getConfigLoader(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::getConfigLoader();
		 }

		/**
		 * Get the environment variables loader instance.
		 *
		 * @return \Illuminate\Config\EnvironmentVariablesLoaderInterface
		 * @static 
		 */
		 public static function getEnvironmentVariablesLoader(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::getEnvironmentVariablesLoader();
		 }

		/**
		 * Get the service provider repository instance.
		 *
		 * @return \Illuminate\Foundation\ProviderRepository
		 * @static 
		 */
		 public static function getProviderRepository(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::getProviderRepository();
		 }

		/**
		 * Get the service providers that have been loaded.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getLoadedProviders(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::getLoadedProviders();
		 }

		/**
		 * Set the application's deferred services.
		 *
		 * @param array  $services
		 * @return void
		 * @static 
		 */
		 public static function setDeferredServices($services){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::setDeferredServices($services);
		 }

		/**
		 * Determine if the given service is a deferred service.
		 *
		 * @param string  $service
		 * @return bool
		 * @static 
		 */
		 public static function isDeferredService($service){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::isDeferredService($service);
		 }

		/**
		 * Get or set the request class for the application.
		 *
		 * @param string  $class
		 * @return string
		 * @static 
		 */
		 public static function requestClass($class = null){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::requestClass($class);
		 }

		/**
		 * Set the application request for the console environment.
		 *
		 * @return void
		 * @static 
		 */
		 public static function setRequestForConsoleEnvironment(){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::setRequestForConsoleEnvironment();
		 }

		/**
		 * Call a method on the default request class.
		 *
		 * @param string  $method
		 * @param array  $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function onRequest($method, $parameters = array()){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::onRequest($method, $parameters);
		 }

		/**
		 * Get the current application locale.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getLocale(){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::getLocale();
		 }

		/**
		 * Set the current application locale.
		 *
		 * @param string  $locale
		 * @return void
		 * @static 
		 */
		 public static function setLocale($locale){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::setLocale($locale);
		 }

		/**
		 * Register the core class aliases in the container.
		 *
		 * @return void
		 * @static 
		 */
		 public static function registerCoreContainerAliases(){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::registerCoreContainerAliases();
		 }

		/**
		 * Dynamically access application services.
		 *
		 * @param string  $key
		 * @return mixed
		 * @static 
		 */
		 public static function __get($key){
			//Method inherited from \Illuminate\Foundation\Application
			return \Illuminate\Foundation\Application::__get($key);
		 }

		/**
		 * Dynamically set application services.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function __set($key, $value){
			//Method inherited from \Illuminate\Foundation\Application
			 \Illuminate\Foundation\Application::__set($key, $value);
		 }

		/**
		 * Determine if the given abstract type has been resolved.
		 *
		 * @param string $abstract
		 * @return bool
		 * @static 
		 */
		 public static function resolved($abstract){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::resolved($abstract);
		 }

		/**
		 * Determine if a given string is an alias.
		 *
		 * @param string  $name
		 * @return bool
		 * @static 
		 */
		 public static function isAlias($name){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::isAlias($name);
		 }

		/**
		 * Register a binding with the container.
		 *
		 * @param string  $abstract
		 * @param \Closure|string|null  $concrete
		 * @param bool  $shared
		 * @return void
		 * @static 
		 */
		 public static function bind($abstract, $concrete = null, $shared = false){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::bind($abstract, $concrete, $shared);
		 }

		/**
		 * Register a binding if it hasn't already been registered.
		 *
		 * @param string  $abstract
		 * @param \Closure|string|null  $concrete
		 * @param bool  $shared
		 * @return void
		 * @static 
		 */
		 public static function bindIf($abstract, $concrete = null, $shared = false){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::bindIf($abstract, $concrete, $shared);
		 }

		/**
		 * Register a shared binding in the container.
		 *
		 * @param string  $abstract
		 * @param \Closure|string|null  $concrete
		 * @return void
		 * @static 
		 */
		 public static function singleton($abstract, $concrete = null){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::singleton($abstract, $concrete);
		 }

		/**
		 * Wrap a Closure such that it is shared.
		 *
		 * @param \Closure  $closure
		 * @return Closure
		 * @static 
		 */
		 public static function share($closure){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::share($closure);
		 }

		/**
		 * Bind a shared Closure into the container.
		 *
		 * @param string    $abstract
		 * @param \Closure  $closure
		 * @return void
		 * @static 
		 */
		 public static function bindShared($abstract, $closure){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::bindShared($abstract, $closure);
		 }

		/**
		 * Register an existing instance as shared in the container.
		 *
		 * @param string  $abstract
		 * @param mixed   $instance
		 * @return void
		 * @static 
		 */
		 public static function instance($abstract, $instance){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::instance($abstract, $instance);
		 }

		/**
		 * Alias a type to a shorter name.
		 *
		 * @param string  $abstract
		 * @param string  $alias
		 * @return void
		 * @static 
		 */
		 public static function alias($abstract, $alias){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::alias($abstract, $alias);
		 }

		/**
		 * Bind a new callback to an abstract's rebind event.
		 *
		 * @param string    $abstract
		 * @param \Closure  $callback
		 * @return mixed
		 * @static 
		 */
		 public static function rebinding($abstract, $callback){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::rebinding($abstract, $callback);
		 }

		/**
		 * Refresh an instance on the given target and method.
		 *
		 * @param string  $abstract
		 * @param mixed   $target
		 * @param string  $method
		 * @return mixed
		 * @static 
		 */
		 public static function refresh($abstract, $target, $method){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::refresh($abstract, $target, $method);
		 }

		/**
		 * Instantiate a concrete instance of the given type.
		 *
		 * @param string  $concrete
		 * @param array   $parameters
		 * @return mixed
		 * @throws BindingResolutionException
		 * @static 
		 */
		 public static function build($concrete, $parameters = array()){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::build($concrete, $parameters);
		 }

		/**
		 * Register a new resolving callback.
		 *
		 * @param string    $abstract
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function resolving($abstract, $callback){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::resolving($abstract, $callback);
		 }

		/**
		 * Register a new resolving callback for all types.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function resolvingAny($callback){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::resolvingAny($callback);
		 }

		/**
		 * Determine if a given type is shared.
		 *
		 * @param string  $abstract
		 * @return bool
		 * @static 
		 */
		 public static function isShared($abstract){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::isShared($abstract);
		 }

		/**
		 * Get the container's bindings.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getBindings(){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::getBindings();
		 }

		/**
		 * Remove a resolved instance from the instance cache.
		 *
		 * @param string  $abstract
		 * @return void
		 * @static 
		 */
		 public static function forgetInstance($abstract){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::forgetInstance($abstract);
		 }

		/**
		 * Clear all of the instances from the container.
		 *
		 * @return void
		 * @static 
		 */
		 public static function forgetInstances(){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::forgetInstances();
		 }

		/**
		 * Determine if a given offset exists.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function offsetExists($key){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::offsetExists($key);
		 }

		/**
		 * Get the value at a given offset.
		 *
		 * @param string  $key
		 * @return mixed
		 * @static 
		 */
		 public static function offsetGet($key){
			//Method inherited from \Illuminate\Container\Container
			return \Illuminate\Foundation\Application::offsetGet($key);
		 }

		/**
		 * Set the value at a given offset.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function offsetSet($key, $value){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::offsetSet($key, $value);
		 }

		/**
		 * Unset the value at a given offset.
		 *
		 * @param string  $key
		 * @return void
		 * @static 
		 */
		 public static function offsetUnset($key){
			//Method inherited from \Illuminate\Container\Container
			 \Illuminate\Foundation\Application::offsetUnset($key);
		 }

	}
	class Artisan extends \Illuminate\Support\Facades\Artisan{
		/**
		 * Create and boot a new Console application.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @return \Illuminate\Console\Application
		 * @static 
		 */
		 public static function start($app){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::start($app);
		 }

		/**
		 * Create a new Console application.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @return \Illuminate\Console\Application
		 * @static 
		 */
		 public static function make($app){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::make($app);
		 }

		/**
		 * Boot the Console application.
		 *
		 * @return \Illuminate\Console\Application
		 * @static 
		 */
		 public static function boot(){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::boot();
		 }

		/**
		 * Run an Artisan console command by name.
		 *
		 * @param string  $command
		 * @param array   $parameters
		 * @param \Symfony\Component\Console\Output\OutputInterface  $output
		 * @return void
		 * @static 
		 */
		 public static function call($command, $parameters = array(), $output = null){
			//Method inherited from \Illuminate\Console\Application
			 \Illuminate\Console\Application::call($command, $parameters, $output);
		 }

		/**
		 * Add a command to the console.
		 *
		 * @param \Symfony\Component\Console\Command\Command  $command
		 * @return \Symfony\Component\Console\Command\Command
		 * @static 
		 */
		 public static function add($command){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::add($command);
		 }

		/**
		 * Add a command, resolving through the application.
		 *
		 * @param string  $command
		 * @return \Symfony\Component\Console\Command\Command
		 * @static 
		 */
		 public static function resolve($command){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::resolve($command);
		 }

		/**
		 * Resolve an array of commands through the application.
		 *
		 * @param array|dynamic  $commands
		 * @return void
		 * @static 
		 */
		 public static function resolveCommands($commands){
			//Method inherited from \Illuminate\Console\Application
			 \Illuminate\Console\Application::resolveCommands($commands);
		 }

		/**
		 * Render the given exception.
		 *
		 * @param \Exception  $e
		 * @param \Symfony\Component\Console\Output\OutputInterface  $output
		 * @return void
		 * @static 
		 */
		 public static function renderException($e, $output){
			//Method inherited from \Illuminate\Console\Application
			 \Illuminate\Console\Application::renderException($e, $output);
		 }

		/**
		 * Set the exception handler instance.
		 *
		 * @param \Illuminate\Exception\Handler  $handler
		 * @return \Illuminate\Console\Application
		 * @static 
		 */
		 public static function setExceptionHandler($handler){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::setExceptionHandler($handler);
		 }

		/**
		 * Set the Laravel application instance.
		 *
		 * @param \Illuminate\Foundation\Application  $laravel
		 * @return \Illuminate\Console\Application
		 * @static 
		 */
		 public static function setLaravel($laravel){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::setLaravel($laravel);
		 }

		/**
		 * Set whether the Console app should auto-exit when done.
		 *
		 * @param bool  $boolean
		 * @return \Illuminate\Console\Application
		 * @static 
		 */
		 public static function setAutoExit($boolean){
			//Method inherited from \Illuminate\Console\Application
			return \Illuminate\Console\Application::setAutoExit($boolean);
		 }

		/**
		 * Constructor.
		 *
		 * @param string $name    The name of the application
		 * @param string $version The version of the application
		 * @api 
		 * @static 
		 */
		 public static function __construct($name = 'UNKNOWN', $version = 'UNKNOWN'){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::__construct($name, $version);
		 }

		/**
		 * 
		 *
		 * @static 
		 */
		 public static function setDispatcher($dispatcher){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::setDispatcher($dispatcher);
		 }

		/**
		 * Runs the current application.
		 *
		 * @param InputInterface  $input  An Input instance
		 * @param OutputInterface $output An Output instance
		 * @return int 0 if everything went fine, or an error code
		 * @throws \Exception When doRun returns Exception
		 * @api 
		 * @static 
		 */
		 public static function run($input = null, $output = null){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::run($input, $output);
		 }

		/**
		 * Runs the current application.
		 *
		 * @param InputInterface  $input  An Input instance
		 * @param OutputInterface $output An Output instance
		 * @return int 0 if everything went fine, or an error code
		 * @static 
		 */
		 public static function doRun($input, $output){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::doRun($input, $output);
		 }

		/**
		 * Set a helper set to be used with the command.
		 *
		 * @param HelperSet $helperSet The helper set
		 * @api 
		 * @static 
		 */
		 public static function setHelperSet($helperSet){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::setHelperSet($helperSet);
		 }

		/**
		 * Get the helper set associated with the command.
		 *
		 * @return HelperSet The HelperSet instance associated with this command
		 * @api 
		 * @static 
		 */
		 public static function getHelperSet(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getHelperSet();
		 }

		/**
		 * Set an input definition set to be used with this application
		 *
		 * @param InputDefinition $definition The input definition
		 * @api 
		 * @static 
		 */
		 public static function setDefinition($definition){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::setDefinition($definition);
		 }

		/**
		 * Gets the InputDefinition related to this Application.
		 *
		 * @return InputDefinition The InputDefinition instance
		 * @static 
		 */
		 public static function getDefinition(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getDefinition();
		 }

		/**
		 * Gets the help message.
		 *
		 * @return string A help message.
		 * @static 
		 */
		 public static function getHelp(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getHelp();
		 }

		/**
		 * Sets whether to catch exceptions or not during commands execution.
		 *
		 * @param bool $boolean Whether to catch exceptions or not during commands execution
		 * @api 
		 * @static 
		 */
		 public static function setCatchExceptions($boolean){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::setCatchExceptions($boolean);
		 }

		/**
		 * Gets the name of the application.
		 *
		 * @return string The application name
		 * @api 
		 * @static 
		 */
		 public static function getName(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getName();
		 }

		/**
		 * Sets the application name.
		 *
		 * @param string $name The application name
		 * @api 
		 * @static 
		 */
		 public static function setName($name){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::setName($name);
		 }

		/**
		 * Gets the application version.
		 *
		 * @return string The application version
		 * @api 
		 * @static 
		 */
		 public static function getVersion(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getVersion();
		 }

		/**
		 * Sets the application version.
		 *
		 * @param string $version The application version
		 * @api 
		 * @static 
		 */
		 public static function setVersion($version){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::setVersion($version);
		 }

		/**
		 * Returns the long version of the application.
		 *
		 * @return string The long application version
		 * @api 
		 * @static 
		 */
		 public static function getLongVersion(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getLongVersion();
		 }

		/**
		 * Registers a new command.
		 *
		 * @param string $name The command name
		 * @return Command The newly created command
		 * @api 
		 * @static 
		 */
		 public static function register($name){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::register($name);
		 }

		/**
		 * Adds an array of command objects.
		 *
		 * @param Command[] $commands An array of commands
		 * @api 
		 * @static 
		 */
		 public static function addCommands($commands){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::addCommands($commands);
		 }

		/**
		 * Returns a registered command by name or alias.
		 *
		 * @param string $name The command name or alias
		 * @return Command A Command object
		 * @throws \InvalidArgumentException When command name given does not exist
		 * @api 
		 * @static 
		 */
		 public static function get($name){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::get($name);
		 }

		/**
		 * Returns true if the command exists, false otherwise.
		 *
		 * @param string $name The command name or alias
		 * @return bool true if the command exists, false otherwise
		 * @api 
		 * @static 
		 */
		 public static function has($name){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::has($name);
		 }

		/**
		 * Returns an array of all unique namespaces used by currently registered commands.
		 * 
		 * It does not returns the global namespace which always exists.
		 *
		 * @return array An array of namespaces
		 * @static 
		 */
		 public static function getNamespaces(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getNamespaces();
		 }

		/**
		 * Finds a registered namespace by a name or an abbreviation.
		 *
		 * @param string $namespace A namespace or abbreviation to search for
		 * @return string A registered namespace
		 * @throws \InvalidArgumentException When namespace is incorrect or ambiguous
		 * @static 
		 */
		 public static function findNamespace($namespace){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::findNamespace($namespace);
		 }

		/**
		 * Finds a command by name or alias.
		 * 
		 * Contrary to get, this command tries to find the best
		 * match if you give it an abbreviation of a name or alias.
		 *
		 * @param string $name A command name or a command alias
		 * @return Command A Command instance
		 * @throws \InvalidArgumentException When command name is incorrect or ambiguous
		 * @api 
		 * @static 
		 */
		 public static function find($name){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::find($name);
		 }

		/**
		 * Gets the commands (registered in the given namespace if provided).
		 * 
		 * The array keys are the full names and the values the command instances.
		 *
		 * @param string $namespace A namespace name
		 * @return Command[] An array of Command instances
		 * @api 
		 * @static 
		 */
		 public static function all($namespace = null){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::all($namespace);
		 }

		/**
		 * Returns an array of possible abbreviations given a set of names.
		 *
		 * @param array $names An array of names
		 * @return array An array of abbreviations
		 * @static 
		 */
		 public static function getAbbreviations($names){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getAbbreviations($names);
		 }

		/**
		 * Returns a text representation of the Application.
		 *
		 * @param string $namespace An optional namespace name
		 * @param bool   $raw       Whether to return raw command list
		 * @return string A string representing the Application
		 * @deprecated Deprecated since version 2.3, to be removed in 3.0.
		 * @static 
		 */
		 public static function asText($namespace = null, $raw = false){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::asText($namespace, $raw);
		 }

		/**
		 * Returns an XML representation of the Application.
		 *
		 * @param string $namespace An optional namespace name
		 * @param bool   $asDom     Whether to return a DOM or an XML string
		 * @return string|\DOMDocument An XML string representing the Application
		 * @deprecated Deprecated since version 2.3, to be removed in 3.0.
		 * @static 
		 */
		 public static function asXml($namespace = null, $asDom = false){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::asXml($namespace, $asDom);
		 }

		/**
		 * Tries to figure out the terminal dimensions based on the current environment
		 *
		 * @return array Array containing width and height
		 * @static 
		 */
		 public static function getTerminalDimensions(){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::getTerminalDimensions();
		 }

		/**
		 * Sets terminal dimensions.
		 * 
		 * Can be useful to force terminal dimensions for functional tests.
		 *
		 * @param int     $width  The width
		 * @param int     $height The height
		 * @return Application The current application
		 * @static 
		 */
		 public static function setTerminalDimensions($width, $height){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::setTerminalDimensions($width, $height);
		 }

		/**
		 * Returns the namespace part of the command name.
		 * 
		 * This method is not part of public API and should not be used directly.
		 *
		 * @param string $name  The full name of the command
		 * @param string $limit The maximum number of parts of the namespace
		 * @return string The namespace of the command
		 * @static 
		 */
		 public static function extractNamespace($name, $limit = null){
			//Method inherited from \Symfony\Component\Console\Application
			return \Illuminate\Console\Application::extractNamespace($name, $limit);
		 }

		/**
		 * Sets the default Command name.
		 *
		 * @param string $commandName The Command name
		 * @static 
		 */
		 public static function setDefaultCommand($commandName){
			//Method inherited from \Symfony\Component\Console\Application
			 \Illuminate\Console\Application::setDefaultCommand($commandName);
		 }

		/**
		 * Dynamically pass all missing methods to console Artisan.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Foundation\Artisan
			return \Illuminate\Foundation\Artisan::__call($method, $parameters);
		 }

	}
	class Auth extends \Illuminate\Support\Facades\Auth{
		/**
		 * Create an instance of the database driver.
		 *
		 * @return \Illuminate\Auth\Guard
		 * @static 
		 */
		 public static function createDatabaseDriver(){
			//Method inherited from \Illuminate\Auth\AuthManager
			return \Illuminate\Auth\AuthManager::createDatabaseDriver();
		 }

		/**
		 * Create an instance of the Eloquent driver.
		 *
		 * @return \Illuminate\Auth\Guard
		 * @static 
		 */
		 public static function createEloquentDriver(){
			//Method inherited from \Illuminate\Auth\AuthManager
			return \Illuminate\Auth\AuthManager::createEloquentDriver();
		 }

		/**
		 * Get the default authentication driver name.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDefaultDriver(){
			//Method inherited from \Illuminate\Auth\AuthManager
			return \Illuminate\Auth\AuthManager::getDefaultDriver();
		 }

		/**
		 * Set the default authentication driver name.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function setDefaultDriver($name){
			//Method inherited from \Illuminate\Auth\AuthManager
			 \Illuminate\Auth\AuthManager::setDefaultDriver($name);
		 }

		/**
		 * Create a new manager instance.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @return void
		 * @static 
		 */
		 public static function __construct($app){
			//Method inherited from \Illuminate\Support\Manager
			 \Illuminate\Auth\AuthManager::__construct($app);
		 }

		/**
		 * Get a driver instance.
		 *
		 * @param string  $driver
		 * @return mixed
		 * @static 
		 */
		 public static function driver($driver = null){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Auth\AuthManager::driver($driver);
		 }

		/**
		 * Register a custom driver creator Closure.
		 *
		 * @param string    $driver
		 * @param \Closure  $callback
		 * @return \Illuminate\Support\Manager|static
		 * @static 
		 */
		 public static function extend($driver, $callback){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Auth\AuthManager::extend($driver, $callback);
		 }

		/**
		 * Get all of the created "drivers".
		 *
		 * @return array
		 * @static 
		 */
		 public static function getDrivers(){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Auth\AuthManager::getDrivers();
		 }

		/**
		 * Dynamically call the default driver instance.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Auth\AuthManager::__call($method, $parameters);
		 }

		/**
		 * Determine if the current user is authenticated.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function check(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::check();
		 }

		/**
		 * Determine if the current user is a guest.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function guest(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::guest();
		 }

		/**
		 * Get the currently authenticated user.
		 *
		 * @return \Illuminate\Auth\UserInterface|null
		 * @static 
		 */
		 public static function user(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::user();
		 }

		/**
		 * Get the ID for the currently authenticated user.
		 *
		 * @return int|null
		 * @static 
		 */
		 public static function id(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::id();
		 }

		/**
		 * Log a user into the application without sessions or cookies.
		 *
		 * @param array  $credentials
		 * @return bool
		 * @static 
		 */
		 public static function once($credentials = array()){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::once($credentials);
		 }

		/**
		 * Validate a user's credentials.
		 *
		 * @param array  $credentials
		 * @return bool
		 * @static 
		 */
		 public static function validate($credentials = array()){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::validate($credentials);
		 }

		/**
		 * Attempt to authenticate using HTTP Basic Auth.
		 *
		 * @param string  $field
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @return \Symfony\Component\HttpFoundation\Response|null
		 * @static 
		 */
		 public static function basic($field = 'email', $request = null){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::basic($field, $request);
		 }

		/**
		 * Perform a stateless HTTP Basic login attempt.
		 *
		 * @param string  $field
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @return \Symfony\Component\HttpFoundation\Response|null
		 * @static 
		 */
		 public static function onceBasic($field = 'email', $request = null){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::onceBasic($field, $request);
		 }

		/**
		 * Attempt to authenticate a user using the given credentials.
		 *
		 * @param array  $credentials
		 * @param bool   $remember
		 * @param bool   $login
		 * @return bool
		 * @static 
		 */
		 public static function attempt($credentials = array(), $remember = false, $login = true){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::attempt($credentials, $remember, $login);
		 }

		/**
		 * Register an authentication attempt event listener.
		 *
		 * @param mixed  $callback
		 * @return void
		 * @static 
		 */
		 public static function attempting($callback){
			//Method inherited from \Illuminate\Auth\Guard
			 \Illuminate\Auth\Guard::attempting($callback);
		 }

		/**
		 * Log a user into the application.
		 *
		 * @param \Illuminate\Auth\UserInterface  $user
		 * @param bool  $remember
		 * @return void
		 * @static 
		 */
		 public static function login($user, $remember = false){
			//Method inherited from \Illuminate\Auth\Guard
			 \Illuminate\Auth\Guard::login($user, $remember);
		 }

		/**
		 * Log the given user ID into the application.
		 *
		 * @param mixed  $id
		 * @param bool   $remember
		 * @return \Illuminate\Auth\UserInterface
		 * @static 
		 */
		 public static function loginUsingId($id, $remember = false){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::loginUsingId($id, $remember);
		 }

		/**
		 * Log the given user ID into the application without sessions or cookies.
		 *
		 * @param mixed  $id
		 * @return bool
		 * @static 
		 */
		 public static function onceUsingId($id){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::onceUsingId($id);
		 }

		/**
		 * Log the user out of the application.
		 *
		 * @return void
		 * @static 
		 */
		 public static function logout(){
			//Method inherited from \Illuminate\Auth\Guard
			 \Illuminate\Auth\Guard::logout();
		 }

		/**
		 * Get the cookie creator instance used by the guard.
		 *
		 * @return \Illuminate\Cookie\CookieJar
		 * @throws \RuntimeException
		 * @static 
		 */
		 public static function getCookieJar(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getCookieJar();
		 }

		/**
		 * Set the cookie creator instance used by the guard.
		 *
		 * @param \Illuminate\Cookie\CookieJar  $cookie
		 * @return void
		 * @static 
		 */
		 public static function setCookieJar($cookie){
			//Method inherited from \Illuminate\Auth\Guard
			 \Illuminate\Auth\Guard::setCookieJar($cookie);
		 }

		/**
		 * Get the event dispatcher instance.
		 *
		 * @return \Illuminate\Events\Dispatcher
		 * @static 
		 */
		 public static function getDispatcher(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getDispatcher();
		 }

		/**
		 * Set the event dispatcher instance.
		 *
		 * @param \Illuminate\Events\Dispatcher
		 * @static 
		 */
		 public static function setDispatcher($events){
			//Method inherited from \Illuminate\Auth\Guard
			 \Illuminate\Auth\Guard::setDispatcher($events);
		 }

		/**
		 * Get the session store used by the guard.
		 *
		 * @return \Illuminate\Session\Store
		 * @static 
		 */
		 public static function getSession(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getSession();
		 }

		/**
		 * Get the user provider used by the guard.
		 *
		 * @return \Illuminate\Auth\UserProviderInterface
		 * @static 
		 */
		 public static function getProvider(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getProvider();
		 }

		/**
		 * Set the user provider used by the guard.
		 *
		 * @param \Illuminate\Auth\UserProviderInterface  $provider
		 * @return void
		 * @static 
		 */
		 public static function setProvider($provider){
			//Method inherited from \Illuminate\Auth\Guard
			 \Illuminate\Auth\Guard::setProvider($provider);
		 }

		/**
		 * Return the currently cached user of the application.
		 *
		 * @return \Illuminate\Auth\UserInterface|null
		 * @static 
		 */
		 public static function getUser(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getUser();
		 }

		/**
		 * Set the current user of the application.
		 *
		 * @param \Illuminate\Auth\UserInterface  $user
		 * @return void
		 * @static 
		 */
		 public static function setUser($user){
			//Method inherited from \Illuminate\Auth\Guard
			 \Illuminate\Auth\Guard::setUser($user);
		 }

		/**
		 * Get the current request instance.
		 *
		 * @return \Symfony\Component\HttpFoundation\Request
		 * @static 
		 */
		 public static function getRequest(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getRequest();
		 }

		/**
		 * Set the current request instance.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request
		 * @return \Illuminate\Auth\Guard
		 * @static 
		 */
		 public static function setRequest($request){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::setRequest($request);
		 }

		/**
		 * Get the last user we attempted to authenticate.
		 *
		 * @return \Illuminate\Auth\UserInterface
		 * @static 
		 */
		 public static function getLastAttempted(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getLastAttempted();
		 }

		/**
		 * Get a unique identifier for the auth session value.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getName(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getName();
		 }

		/**
		 * Get the name of the cookie used to store the "recaller".
		 *
		 * @return string
		 * @static 
		 */
		 public static function getRecallerName(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::getRecallerName();
		 }

		/**
		 * Determine if the user was authenticated via "remember me" cookie.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function viaRemember(){
			//Method inherited from \Illuminate\Auth\Guard
			return \Illuminate\Auth\Guard::viaRemember();
		 }

	}
	class Blade extends \Illuminate\Support\Facades\Blade{
		/**
		 * Compile the view at the given path.
		 *
		 * @param string  $path
		 * @return void
		 * @static 
		 */
		 public static function compile($path = null){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			 \Illuminate\View\Compilers\BladeCompiler::compile($path);
		 }

		/**
		 * Get the path currently being compiled.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getPath(){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::getPath();
		 }

		/**
		 * Set the path currently being compiled.
		 *
		 * @param string  $path
		 * @return void
		 * @static 
		 */
		 public static function setPath($path){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			 \Illuminate\View\Compilers\BladeCompiler::setPath($path);
		 }

		/**
		 * Compile the given Blade template contents.
		 *
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function compileString($value){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::compileString($value);
		 }

		/**
		 * Compile the default values for the echo statement.
		 *
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function compileEchoDefaults($value){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::compileEchoDefaults($value);
		 }

		/**
		 * Register a custom Blade compiler.
		 *
		 * @param \Closure  $compiler
		 * @return void
		 * @static 
		 */
		 public static function extend($compiler){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			 \Illuminate\View\Compilers\BladeCompiler::extend($compiler);
		 }

		/**
		 * Get the regular expression for a generic Blade function.
		 *
		 * @param string  $function
		 * @return string
		 * @static 
		 */
		 public static function createMatcher($function){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::createMatcher($function);
		 }

		/**
		 * Get the regular expression for a generic Blade function.
		 *
		 * @param string  $function
		 * @return string
		 * @static 
		 */
		 public static function createOpenMatcher($function){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::createOpenMatcher($function);
		 }

		/**
		 * Create a plain Blade matcher.
		 *
		 * @param string  $function
		 * @return string
		 * @static 
		 */
		 public static function createPlainMatcher($function){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::createPlainMatcher($function);
		 }

		/**
		 * Sets the content tags used for the compiler.
		 *
		 * @param string  $openTag
		 * @param string  $closeTag
		 * @param bool    $escaped
		 * @return void
		 * @static 
		 */
		 public static function setContentTags($openTag, $closeTag, $escaped = false){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			 \Illuminate\View\Compilers\BladeCompiler::setContentTags($openTag, $closeTag, $escaped);
		 }

		/**
		 * Sets the escaped content tags used for the compiler.
		 *
		 * @param string  $openTag
		 * @param string  $closeTag
		 * @return void
		 * @static 
		 */
		 public static function setEscapedContentTags($openTag, $closeTag){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			 \Illuminate\View\Compilers\BladeCompiler::setEscapedContentTags($openTag, $closeTag);
		 }

		/**
		 * Gets the content tags used for the compiler.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getContentTags(){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::getContentTags();
		 }

		/**
		 * Gets the escaped content tags used for the compiler.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getEscapedContentTags(){
			//Method inherited from \Illuminate\View\Compilers\BladeCompiler
			return \Illuminate\View\Compilers\BladeCompiler::getEscapedContentTags();
		 }

		/**
		 * Create a new compiler instance.
		 *
		 * @param \Illuminate\Filesystem\Filesystem  $files
		 * @param string  $cachePath
		 * @return void
		 * @static 
		 */
		 public static function __construct($files, $cachePath){
			//Method inherited from \Illuminate\View\Compilers\Compiler
			 \Illuminate\View\Compilers\BladeCompiler::__construct($files, $cachePath);
		 }

		/**
		 * Get the path to the compiled version of a view.
		 *
		 * @param string  $path
		 * @return string
		 * @static 
		 */
		 public static function getCompiledPath($path){
			//Method inherited from \Illuminate\View\Compilers\Compiler
			return \Illuminate\View\Compilers\BladeCompiler::getCompiledPath($path);
		 }

		/**
		 * Determine if the view at the given path is expired.
		 *
		 * @param string  $path
		 * @return bool
		 * @static 
		 */
		 public static function isExpired($path){
			//Method inherited from \Illuminate\View\Compilers\Compiler
			return \Illuminate\View\Compilers\BladeCompiler::isExpired($path);
		 }

	}
	class Cache extends \Illuminate\Support\Facades\Cache{
		/**
		 * Get the cache "prefix" value.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getPrefix(){
			//Method inherited from \Illuminate\Cache\CacheManager
			return \Illuminate\Cache\CacheManager::getPrefix();
		 }

		/**
		 * Set the cache "prefix" value.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function setPrefix($name){
			//Method inherited from \Illuminate\Cache\CacheManager
			 \Illuminate\Cache\CacheManager::setPrefix($name);
		 }

		/**
		 * Get the default cache driver name.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDefaultDriver(){
			//Method inherited from \Illuminate\Cache\CacheManager
			return \Illuminate\Cache\CacheManager::getDefaultDriver();
		 }

		/**
		 * Set the default cache driver name.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function setDefaultDriver($name){
			//Method inherited from \Illuminate\Cache\CacheManager
			 \Illuminate\Cache\CacheManager::setDefaultDriver($name);
		 }

		/**
		 * Create a new manager instance.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @return void
		 * @static 
		 */
		 public static function __construct($app){
			//Method inherited from \Illuminate\Support\Manager
			 \Illuminate\Cache\CacheManager::__construct($app);
		 }

		/**
		 * Get a driver instance.
		 *
		 * @param string  $driver
		 * @return mixed
		 * @static 
		 */
		 public static function driver($driver = null){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Cache\CacheManager::driver($driver);
		 }

		/**
		 * Register a custom driver creator Closure.
		 *
		 * @param string    $driver
		 * @param \Closure  $callback
		 * @return \Illuminate\Support\Manager|static
		 * @static 
		 */
		 public static function extend($driver, $callback){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Cache\CacheManager::extend($driver, $callback);
		 }

		/**
		 * Get all of the created "drivers".
		 *
		 * @return array
		 * @static 
		 */
		 public static function getDrivers(){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Cache\CacheManager::getDrivers();
		 }

		/**
		 * Dynamically call the default driver instance.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Cache\CacheManager::__call($method, $parameters);
		 }

		/**
		 * Determine if an item exists in the cache.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function has($key){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::has($key);
		 }

		/**
		 * Retrieve an item from the cache by key.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function get($key, $default = null){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::get($key, $default);
		 }

		/**
		 * Retrieve an item from the cache and delete it.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function pull($key, $default = null){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::pull($key, $default);
		 }

		/**
		 * Store an item in the cache.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @param \DateTime|int  $minutes
		 * @return void
		 * @static 
		 */
		 public static function put($key, $value, $minutes){
			//Method inherited from \Illuminate\Cache\Repository
			 \Illuminate\Cache\Repository::put($key, $value, $minutes);
		 }

		/**
		 * Store an item in the cache if the key does not exist.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @param \DateTime|int  $minutes
		 * @return bool
		 * @static 
		 */
		 public static function add($key, $value, $minutes){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::add($key, $value, $minutes);
		 }

		/**
		 * Get an item from the cache, or store the default value.
		 *
		 * @param string  $key
		 * @param \DateTime|int  $minutes
		 * @param \Closure  $callback
		 * @return mixed
		 * @static 
		 */
		 public static function remember($key, $minutes, $callback){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::remember($key, $minutes, $callback);
		 }

		/**
		 * Get an item from the cache, or store the default value forever.
		 *
		 * @param string   $key
		 * @param \Closure  $callback
		 * @return mixed
		 * @static 
		 */
		 public static function sear($key, $callback){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::sear($key, $callback);
		 }

		/**
		 * Get an item from the cache, or store the default value forever.
		 *
		 * @param string   $key
		 * @param \Closure  $callback
		 * @return mixed
		 * @static 
		 */
		 public static function rememberForever($key, $callback){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::rememberForever($key, $callback);
		 }

		/**
		 * Get the default cache time.
		 *
		 * @return int
		 * @static 
		 */
		 public static function getDefaultCacheTime(){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::getDefaultCacheTime();
		 }

		/**
		 * Set the default cache time in minutes.
		 *
		 * @param int   $minutes
		 * @return void
		 * @static 
		 */
		 public static function setDefaultCacheTime($minutes){
			//Method inherited from \Illuminate\Cache\Repository
			 \Illuminate\Cache\Repository::setDefaultCacheTime($minutes);
		 }

		/**
		 * Get the cache store implementation.
		 *
		 * @return \Illuminate\Cache\StoreInterface
		 * @static 
		 */
		 public static function getStore(){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::getStore();
		 }

		/**
		 * Determine if a cached value exists.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function offsetExists($key){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::offsetExists($key);
		 }

		/**
		 * Retrieve an item from the cache by key.
		 *
		 * @param string  $key
		 * @return mixed
		 * @static 
		 */
		 public static function offsetGet($key){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::offsetGet($key);
		 }

		/**
		 * Store an item in the cache for the default time.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function offsetSet($key, $value){
			//Method inherited from \Illuminate\Cache\Repository
			 \Illuminate\Cache\Repository::offsetSet($key, $value);
		 }

		/**
		 * Remove an item from the cache.
		 *
		 * @param string  $key
		 * @return void
		 * @static 
		 */
		 public static function offsetUnset($key){
			//Method inherited from \Illuminate\Cache\Repository
			 \Illuminate\Cache\Repository::offsetUnset($key);
		 }

		/**
		 * Register a custom macro.
		 *
		 * @param string    $name
		 * @param callable  $macro
		 * @return void
		 * @static 
		 */
		 public static function macro($name, $macro){
			//Method inherited from \Illuminate\Cache\Repository
			 \Illuminate\Cache\Repository::macro($name, $macro);
		 }

		/**
		 * Checks if macro is registered
		 *
		 * @param string    $name
		 * @return boolean
		 * @static 
		 */
		 public static function hasMacro($name){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::hasMacro($name);
		 }

		/**
		 * Dynamically handle calls to the class.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @throws \BadMethodCallException
		 * @static 
		 */
		 public static function __callStatic($method, $parameters){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::__callStatic($method, $parameters);
		 }

		/**
		 * Dynamically handle calls to the form builder.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @throws \BadMethodCallException
		 * @static 
		 */
		 public static function macroCall($method, $parameters){
			//Method inherited from \Illuminate\Cache\Repository
			return \Illuminate\Cache\Repository::macroCall($method, $parameters);
		 }

		/**
		 * Increment the value of an item in the cache.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return int
		 * @static 
		 */
		 public static function increment($key, $value = 1){
			//Method inherited from \Illuminate\Cache\FileStore
			return \Illuminate\Cache\FileStore::increment($key, $value);
		 }

		/**
		 * Decrement the value of an item in the cache.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return int
		 * @static 
		 */
		 public static function decrement($key, $value = 1){
			//Method inherited from \Illuminate\Cache\FileStore
			return \Illuminate\Cache\FileStore::decrement($key, $value);
		 }

		/**
		 * Store an item in the cache indefinitely.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function forever($key, $value){
			//Method inherited from \Illuminate\Cache\FileStore
			 \Illuminate\Cache\FileStore::forever($key, $value);
		 }

		/**
		 * Remove an item from the cache.
		 *
		 * @param string  $key
		 * @return void
		 * @static 
		 */
		 public static function forget($key){
			//Method inherited from \Illuminate\Cache\FileStore
			 \Illuminate\Cache\FileStore::forget($key);
		 }

		/**
		 * Remove all items from the cache.
		 *
		 * @return void
		 * @static 
		 */
		 public static function flush(){
			//Method inherited from \Illuminate\Cache\FileStore
			 \Illuminate\Cache\FileStore::flush();
		 }

		/**
		 * Get the Filesystem instance.
		 *
		 * @return \Illuminate\Filesystem\Filesystem
		 * @static 
		 */
		 public static function getFilesystem(){
			//Method inherited from \Illuminate\Cache\FileStore
			return \Illuminate\Cache\FileStore::getFilesystem();
		 }

		/**
		 * Get the working directory of the cache.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDirectory(){
			//Method inherited from \Illuminate\Cache\FileStore
			return \Illuminate\Cache\FileStore::getDirectory();
		 }

	}
	class ClassLoader extends \Illuminate\Support\ClassLoader{
	}
	class Config extends \Illuminate\Support\Facades\Config{
		/**
		 * Create a new configuration repository.
		 *
		 * @param \Illuminate\Config\LoaderInterface  $loader
		 * @param string  $environment
		 * @return void
		 * @static 
		 */
		 public static function __construct($loader, $environment){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::__construct($loader, $environment);
		 }

		/**
		 * Determine if the given configuration value exists.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function has($key){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::has($key);
		 }

		/**
		 * Determine if a configuration group exists.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function hasGroup($key){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::hasGroup($key);
		 }

		/**
		 * Get the specified configuration value.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function get($key, $default = null){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::get($key, $default);
		 }

		/**
		 * Set a given configuration value.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function set($key, $value){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::set($key, $value);
		 }

		/**
		 * Register a package for cascading configuration.
		 *
		 * @param string  $package
		 * @param string  $hint
		 * @param string  $namespace
		 * @return void
		 * @static 
		 */
		 public static function package($package, $hint, $namespace = null){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::package($package, $hint, $namespace);
		 }

		/**
		 * Register an after load callback for a given namespace.
		 *
		 * @param string   $namespace
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function afterLoading($namespace, $callback){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::afterLoading($namespace, $callback);
		 }

		/**
		 * Add a new namespace to the loader.
		 *
		 * @param string  $namespace
		 * @param string  $hint
		 * @return void
		 * @static 
		 */
		 public static function addNamespace($namespace, $hint){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::addNamespace($namespace, $hint);
		 }

		/**
		 * Returns all registered namespaces with the config
		 * loader.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getNamespaces(){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::getNamespaces();
		 }

		/**
		 * Get the loader implementation.
		 *
		 * @return \Illuminate\Config\LoaderInterface
		 * @static 
		 */
		 public static function getLoader(){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::getLoader();
		 }

		/**
		 * Set the loader implementation.
		 *
		 * @param \Illuminate\Config\LoaderInterface  $loader
		 * @return void
		 * @static 
		 */
		 public static function setLoader($loader){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::setLoader($loader);
		 }

		/**
		 * Get the current configuration environment.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getEnvironment(){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::getEnvironment();
		 }

		/**
		 * Get the after load callback array.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getAfterLoadCallbacks(){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::getAfterLoadCallbacks();
		 }

		/**
		 * Get all of the configuration items.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getItems(){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::getItems();
		 }

		/**
		 * Determine if the given configuration option exists.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function offsetExists($key){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::offsetExists($key);
		 }

		/**
		 * Get a configuration option.
		 *
		 * @param string  $key
		 * @return mixed
		 * @static 
		 */
		 public static function offsetGet($key){
			//Method inherited from \Illuminate\Config\Repository
			return \Illuminate\Config\Repository::offsetGet($key);
		 }

		/**
		 * Set a configuration option.
		 *
		 * @param string  $key
		 * @param mixed  $value
		 * @return void
		 * @static 
		 */
		 public static function offsetSet($key, $value){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::offsetSet($key, $value);
		 }

		/**
		 * Unset a configuration option.
		 *
		 * @param string  $key
		 * @return void
		 * @static 
		 */
		 public static function offsetUnset($key){
			//Method inherited from \Illuminate\Config\Repository
			 \Illuminate\Config\Repository::offsetUnset($key);
		 }

		/**
		 * Parse a key into namespace, group, and item.
		 *
		 * @param string  $key
		 * @return array
		 * @static 
		 */
		 public static function parseKey($key){
			//Method inherited from \Illuminate\Support\NamespacedItemResolver
			return \Illuminate\Config\Repository::parseKey($key);
		 }

		/**
		 * Set the parsed value of a key.
		 *
		 * @param string  $key
		 * @param array   $parsed
		 * @return void
		 * @static 
		 */
		 public static function setParsedKey($key, $parsed){
			//Method inherited from \Illuminate\Support\NamespacedItemResolver
			 \Illuminate\Config\Repository::setParsedKey($key, $parsed);
		 }

	}
	class Controller extends \Illuminate\Routing\Controller{
	}
	class Cookie extends \Illuminate\Support\Facades\Cookie{
		/**
		 * Create a new cookie instance.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param int     $minutes
		 * @param string  $path
		 * @param string  $domain
		 * @param bool    $secure
		 * @param bool    $httpOnly
		 * @return \Symfony\Component\HttpFoundation\Cookie
		 * @static 
		 */
		 public static function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true){
			//Method inherited from \Illuminate\Cookie\CookieJar
			return \Illuminate\Cookie\CookieJar::make($name, $value, $minutes, $path, $domain, $secure, $httpOnly);
		 }

		/**
		 * Create a cookie that lasts "forever" (five years).
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param string  $path
		 * @param string  $domain
		 * @param bool    $secure
		 * @param bool    $httpOnly
		 * @return \Symfony\Component\HttpFoundation\Cookie
		 * @static 
		 */
		 public static function forever($name, $value, $path = null, $domain = null, $secure = false, $httpOnly = true){
			//Method inherited from \Illuminate\Cookie\CookieJar
			return \Illuminate\Cookie\CookieJar::forever($name, $value, $path, $domain, $secure, $httpOnly);
		 }

		/**
		 * Expire the given cookie.
		 *
		 * @param string  $name
		 * @param string  $path
		 * @param string  $domain
		 * @return \Symfony\Component\HttpFoundation\Cookie
		 * @static 
		 */
		 public static function forget($name, $path = null, $domain = null){
			//Method inherited from \Illuminate\Cookie\CookieJar
			return \Illuminate\Cookie\CookieJar::forget($name, $path, $domain);
		 }

		/**
		 * Determine if a cookie has been queued.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function hasQueued($key){
			//Method inherited from \Illuminate\Cookie\CookieJar
			return \Illuminate\Cookie\CookieJar::hasQueued($key);
		 }

		/**
		 * Get a queued cookie instance.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return \Symfony\Component\HttpFoundation\Cookie
		 * @static 
		 */
		 public static function queued($key, $default = null){
			//Method inherited from \Illuminate\Cookie\CookieJar
			return \Illuminate\Cookie\CookieJar::queued($key, $default);
		 }

		/**
		 * Queue a cookie to send with the next response.
		 *
		 * @param dynamic
		 * @return void
		 * @static 
		 */
		 public static function queue(){
			//Method inherited from \Illuminate\Cookie\CookieJar
			 \Illuminate\Cookie\CookieJar::queue();
		 }

		/**
		 * Remove a cookie from the queue.
		 *
		 * @param string  $name
		 * @static 
		 */
		 public static function unqueue($name){
			//Method inherited from \Illuminate\Cookie\CookieJar
			 \Illuminate\Cookie\CookieJar::unqueue($name);
		 }

		/**
		 * Set the default path and domain for the jar.
		 *
		 * @param string  $path
		 * @param string  $domain
		 * @return self
		 * @static 
		 */
		 public static function setDefaultPathAndDomain($path, $domain){
			//Method inherited from \Illuminate\Cookie\CookieJar
			return \Illuminate\Cookie\CookieJar::setDefaultPathAndDomain($path, $domain);
		 }

		/**
		 * Get the cookies which have been queued for the next request
		 *
		 * @return array
		 * @static 
		 */
		 public static function getQueuedCookies(){
			//Method inherited from \Illuminate\Cookie\CookieJar
			return \Illuminate\Cookie\CookieJar::getQueuedCookies();
		 }

	}
	class Crypt extends \Illuminate\Support\Facades\Crypt{
		/**
		 * Create a new encrypter instance.
		 *
		 * @param string  $key
		 * @return void
		 * @static 
		 */
		 public static function __construct($key){
			//Method inherited from \Illuminate\Encryption\Encrypter
			 \Illuminate\Encryption\Encrypter::__construct($key);
		 }

		/**
		 * Encrypt the given value.
		 *
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function encrypt($value){
			//Method inherited from \Illuminate\Encryption\Encrypter
			return \Illuminate\Encryption\Encrypter::encrypt($value);
		 }

		/**
		 * Decrypt the given value.
		 *
		 * @param string  $payload
		 * @return string
		 * @static 
		 */
		 public static function decrypt($payload){
			//Method inherited from \Illuminate\Encryption\Encrypter
			return \Illuminate\Encryption\Encrypter::decrypt($payload);
		 }

		/**
		 * Set the encryption key.
		 *
		 * @param string  $key
		 * @return void
		 * @static 
		 */
		 public static function setKey($key){
			//Method inherited from \Illuminate\Encryption\Encrypter
			 \Illuminate\Encryption\Encrypter::setKey($key);
		 }

		/**
		 * Set the encryption cipher.
		 *
		 * @param string  $cipher
		 * @return void
		 * @static 
		 */
		 public static function setCipher($cipher){
			//Method inherited from \Illuminate\Encryption\Encrypter
			 \Illuminate\Encryption\Encrypter::setCipher($cipher);
		 }

		/**
		 * Set the encryption mode.
		 *
		 * @param string  $mode
		 * @return void
		 * @static 
		 */
		 public static function setMode($mode){
			//Method inherited from \Illuminate\Encryption\Encrypter
			 \Illuminate\Encryption\Encrypter::setMode($mode);
		 }

	}
	class DB extends \Illuminate\Support\Facades\DB{
		/**
		 * Create a new database manager instance.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @param \Illuminate\Database\Connectors\ConnectionFactory  $factory
		 * @return void
		 * @static 
		 */
		 public static function __construct($app, $factory){
			//Method inherited from \Illuminate\Database\DatabaseManager
			 \Illuminate\Database\DatabaseManager::__construct($app, $factory);
		 }

		/**
		 * Get a database connection instance.
		 *
		 * @param string  $name
		 * @return \Illuminate\Database\Connection
		 * @static 
		 */
		 public static function connection($name = null){
			//Method inherited from \Illuminate\Database\DatabaseManager
			return \Illuminate\Database\DatabaseManager::connection($name);
		 }

		/**
		 * Reconnect to the given database.
		 *
		 * @param string  $name
		 * @return \Illuminate\Database\Connection
		 * @static 
		 */
		 public static function reconnect($name = null){
			//Method inherited from \Illuminate\Database\DatabaseManager
			return \Illuminate\Database\DatabaseManager::reconnect($name);
		 }

		/**
		 * Disconnect from the given database.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function disconnect($name = null){
			//Method inherited from \Illuminate\Database\DatabaseManager
			 \Illuminate\Database\DatabaseManager::disconnect($name);
		 }

		/**
		 * Get the default connection name.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDefaultConnection(){
			//Method inherited from \Illuminate\Database\DatabaseManager
			return \Illuminate\Database\DatabaseManager::getDefaultConnection();
		 }

		/**
		 * Set the default connection name.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function setDefaultConnection($name){
			//Method inherited from \Illuminate\Database\DatabaseManager
			 \Illuminate\Database\DatabaseManager::setDefaultConnection($name);
		 }

		/**
		 * Register an extension connection resolver.
		 *
		 * @param string    $name
		 * @param callable  $resolver
		 * @return void
		 * @static 
		 */
		 public static function extend($name, $resolver){
			//Method inherited from \Illuminate\Database\DatabaseManager
			 \Illuminate\Database\DatabaseManager::extend($name, $resolver);
		 }

		/**
		 * Return all of the created connections.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getConnections(){
			//Method inherited from \Illuminate\Database\DatabaseManager
			return \Illuminate\Database\DatabaseManager::getConnections();
		 }

		/**
		 * Dynamically pass methods to the default connection.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Database\DatabaseManager
			return \Illuminate\Database\DatabaseManager::__call($method, $parameters);
		 }

		/**
		 * Get a schema builder instance for the connection.
		 *
		 * @return \Illuminate\Database\Schema\MySqlBuilder
		 * @static 
		 */
		 public static function getSchemaBuilder(){
			//Method inherited from \Illuminate\Database\MySqlConnection
			return \Illuminate\Database\MySqlConnection::getSchemaBuilder();
		 }

		/**
		 * Set the query grammar to the default implementation.
		 *
		 * @return void
		 * @static 
		 */
		 public static function useDefaultQueryGrammar(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::useDefaultQueryGrammar();
		 }

		/**
		 * Set the schema grammar to the default implementation.
		 *
		 * @return void
		 * @static 
		 */
		 public static function useDefaultSchemaGrammar(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::useDefaultSchemaGrammar();
		 }

		/**
		 * Set the query post processor to the default implementation.
		 *
		 * @return void
		 * @static 
		 */
		 public static function useDefaultPostProcessor(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::useDefaultPostProcessor();
		 }

		/**
		 * Begin a fluent query against a database table.
		 *
		 * @param string  $table
		 * @return \Illuminate\Database\Query\Builder
		 * @static 
		 */
		 public static function table($table){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::table($table);
		 }

		/**
		 * Get a new raw query expression.
		 *
		 * @param mixed  $value
		 * @return \Illuminate\Database\Query\Expression
		 * @static 
		 */
		 public static function raw($value){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::raw($value);
		 }

		/**
		 * Run a select statement and return a single result.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @return mixed
		 * @static 
		 */
		 public static function selectOne($query, $bindings = array()){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::selectOne($query, $bindings);
		 }

		/**
		 * Run a select statement against the database.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @return array
		 * @static 
		 */
		 public static function select($query, $bindings = array()){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::select($query, $bindings);
		 }

		/**
		 * Run an insert statement against the database.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @return bool
		 * @static 
		 */
		 public static function insert($query, $bindings = array()){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::insert($query, $bindings);
		 }

		/**
		 * Run an update statement against the database.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @return int
		 * @static 
		 */
		 public static function update($query, $bindings = array()){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::update($query, $bindings);
		 }

		/**
		 * Run a delete statement against the database.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @return int
		 * @static 
		 */
		 public static function delete($query, $bindings = array()){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::delete($query, $bindings);
		 }

		/**
		 * Execute an SQL statement and return the boolean result.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @return bool
		 * @static 
		 */
		 public static function statement($query, $bindings = array()){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::statement($query, $bindings);
		 }

		/**
		 * Run an SQL statement and get the number of rows affected.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @return int
		 * @static 
		 */
		 public static function affectingStatement($query, $bindings = array()){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::affectingStatement($query, $bindings);
		 }

		/**
		 * Run a raw, unprepared query against the PDO connection.
		 *
		 * @param string  $query
		 * @return bool
		 * @static 
		 */
		 public static function unprepared($query){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::unprepared($query);
		 }

		/**
		 * Prepare the query bindings for execution.
		 *
		 * @param array  $bindings
		 * @return array
		 * @static 
		 */
		 public static function prepareBindings($bindings){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::prepareBindings($bindings);
		 }

		/**
		 * Execute a Closure within a transaction.
		 *
		 * @param \Closure  $callback
		 * @return mixed
		 * @throws \Exception
		 * @static 
		 */
		 public static function transaction($callback){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::transaction($callback);
		 }

		/**
		 * Start a new database transaction.
		 *
		 * @return void
		 * @static 
		 */
		 public static function beginTransaction(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::beginTransaction();
		 }

		/**
		 * Commit the active database transaction.
		 *
		 * @return void
		 * @static 
		 */
		 public static function commit(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::commit();
		 }

		/**
		 * Rollback the active database transaction.
		 *
		 * @return void
		 * @static 
		 */
		 public static function rollBack(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::rollBack();
		 }

		/**
		 * Get the number of active transactions.
		 *
		 * @return int
		 * @static 
		 */
		 public static function transactionLevel(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::transactionLevel();
		 }

		/**
		 * Execute the given callback in "dry run" mode.
		 *
		 * @param \Closure  $callback
		 * @return array
		 * @static 
		 */
		 public static function pretend($callback){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::pretend($callback);
		 }

		/**
		 * Log a query in the connection's query log.
		 *
		 * @param string  $query
		 * @param array   $bindings
		 * @param $time
		 * @return void
		 * @static 
		 */
		 public static function logQuery($query, $bindings, $time = null){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::logQuery($query, $bindings, $time);
		 }

		/**
		 * Register a database query listener with the connection.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function listen($callback){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::listen($callback);
		 }

		/**
		 * Get a Doctrine Schema Column instance.
		 *
		 * @param string  $table
		 * @param string  $column
		 * @return \Doctrine\DBAL\Schema\Column
		 * @static 
		 */
		 public static function getDoctrineColumn($table, $column){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getDoctrineColumn($table, $column);
		 }

		/**
		 * Get the Doctrine DBAL schema manager for the connection.
		 *
		 * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
		 * @static 
		 */
		 public static function getDoctrineSchemaManager(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getDoctrineSchemaManager();
		 }

		/**
		 * Get the Doctrine DBAL database connection instance.
		 *
		 * @return \Doctrine\DBAL\Connection
		 * @static 
		 */
		 public static function getDoctrineConnection(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getDoctrineConnection();
		 }

		/**
		 * Get the current PDO connection.
		 *
		 * @return PDO
		 * @static 
		 */
		 public static function getPdo(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getPdo();
		 }

		/**
		 * Get the current PDO connection used for reading.
		 *
		 * @return PDO
		 * @static 
		 */
		 public static function getReadPdo(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getReadPdo();
		 }

		/**
		 * Set the PDO connection.
		 *
		 * @param \PDO  $pdo
		 * @return \Illuminate\Database\Connection
		 * @static 
		 */
		 public static function setPdo($pdo){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::setPdo($pdo);
		 }

		/**
		 * Set the PDO connection used for reading.
		 *
		 * @param \PDO  $pdo
		 * @return \Illuminate\Database\Connection
		 * @static 
		 */
		 public static function setReadPdo($pdo){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::setReadPdo($pdo);
		 }

		/**
		 * Get the database connection name.
		 *
		 * @return string|null
		 * @static 
		 */
		 public static function getName(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getName();
		 }

		/**
		 * Get an option from the configuration options.
		 *
		 * @param string  $option
		 * @return mixed
		 * @static 
		 */
		 public static function getConfig($option){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getConfig($option);
		 }

		/**
		 * Get the PDO driver name.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDriverName(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getDriverName();
		 }

		/**
		 * Get the query grammar used by the connection.
		 *
		 * @return \Illuminate\Database\Query\Grammars\Grammar
		 * @static 
		 */
		 public static function getQueryGrammar(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getQueryGrammar();
		 }

		/**
		 * Set the query grammar used by the connection.
		 *
		 * @param \Illuminate\Database\Query\Grammars\Grammar
		 * @return void
		 * @static 
		 */
		 public static function setQueryGrammar($grammar){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::setQueryGrammar($grammar);
		 }

		/**
		 * Get the schema grammar used by the connection.
		 *
		 * @return \Illuminate\Database\Query\Grammars\Grammar
		 * @static 
		 */
		 public static function getSchemaGrammar(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getSchemaGrammar();
		 }

		/**
		 * Set the schema grammar used by the connection.
		 *
		 * @param \Illuminate\Database\Schema\Grammars\Grammar
		 * @return void
		 * @static 
		 */
		 public static function setSchemaGrammar($grammar){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::setSchemaGrammar($grammar);
		 }

		/**
		 * Get the query post processor used by the connection.
		 *
		 * @return \Illuminate\Database\Query\Processors\Processor
		 * @static 
		 */
		 public static function getPostProcessor(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getPostProcessor();
		 }

		/**
		 * Set the query post processor used by the connection.
		 *
		 * @param \Illuminate\Database\Query\Processors\Processor
		 * @return void
		 * @static 
		 */
		 public static function setPostProcessor($processor){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::setPostProcessor($processor);
		 }

		/**
		 * Get the event dispatcher used by the connection.
		 *
		 * @return \Illuminate\Events\Dispatcher
		 * @static 
		 */
		 public static function getEventDispatcher(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getEventDispatcher();
		 }

		/**
		 * Set the event dispatcher instance on the connection.
		 *
		 * @param \Illuminate\Events\Dispatcher
		 * @return void
		 * @static 
		 */
		 public static function setEventDispatcher($events){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::setEventDispatcher($events);
		 }

		/**
		 * Get the paginator environment instance.
		 *
		 * @return \Illuminate\Pagination\Factory
		 * @static 
		 */
		 public static function getPaginator(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getPaginator();
		 }

		/**
		 * Set the pagination environment instance.
		 *
		 * @param \Illuminate\Pagination\Factory|\Closure  $paginator
		 * @return void
		 * @static 
		 */
		 public static function setPaginator($paginator){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::setPaginator($paginator);
		 }

		/**
		 * Get the cache manager instance.
		 *
		 * @return \Illuminate\Cache\CacheManager
		 * @static 
		 */
		 public static function getCacheManager(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getCacheManager();
		 }

		/**
		 * Set the cache manager instance on the connection.
		 *
		 * @param \Illuminate\Cache\CacheManager|\Closure  $cache
		 * @return void
		 * @static 
		 */
		 public static function setCacheManager($cache){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::setCacheManager($cache);
		 }

		/**
		 * Determine if the connection in a "dry run".
		 *
		 * @return bool
		 * @static 
		 */
		 public static function pretending(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::pretending();
		 }

		/**
		 * Get the default fetch mode for the connection.
		 *
		 * @return int
		 * @static 
		 */
		 public static function getFetchMode(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getFetchMode();
		 }

		/**
		 * Set the default fetch mode for the connection.
		 *
		 * @param int  $fetchMode
		 * @return int
		 * @static 
		 */
		 public static function setFetchMode($fetchMode){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::setFetchMode($fetchMode);
		 }

		/**
		 * Get the connection query log.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getQueryLog(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getQueryLog();
		 }

		/**
		 * Clear the query log.
		 *
		 * @return void
		 * @static 
		 */
		 public static function flushQueryLog(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::flushQueryLog();
		 }

		/**
		 * Enable the query log on the connection.
		 *
		 * @return void
		 * @static 
		 */
		 public static function enableQueryLog(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::enableQueryLog();
		 }

		/**
		 * Disable the query log on the connection.
		 *
		 * @return void
		 * @static 
		 */
		 public static function disableQueryLog(){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::disableQueryLog();
		 }

		/**
		 * Determine whether we're logging queries.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function logging(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::logging();
		 }

		/**
		 * Get the name of the connected database.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDatabaseName(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getDatabaseName();
		 }

		/**
		 * Set the name of the connected database.
		 *
		 * @param string  $database
		 * @return string
		 * @static 
		 */
		 public static function setDatabaseName($database){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::setDatabaseName($database);
		 }

		/**
		 * Get the table prefix for the connection.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getTablePrefix(){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::getTablePrefix();
		 }

		/**
		 * Set the table prefix in use by the connection.
		 *
		 * @param string  $prefix
		 * @return void
		 * @static 
		 */
		 public static function setTablePrefix($prefix){
			//Method inherited from \Illuminate\Database\Connection
			 \Illuminate\Database\MySqlConnection::setTablePrefix($prefix);
		 }

		/**
		 * Set the table prefix and return the grammar.
		 *
		 * @param \Illuminate\Database\Grammar  $grammar
		 * @return \Illuminate\Database\Grammar
		 * @static 
		 */
		 public static function withTablePrefix($grammar){
			//Method inherited from \Illuminate\Database\Connection
			return \Illuminate\Database\MySqlConnection::withTablePrefix($grammar);
		 }

	}
	class Eloquent extends \Illuminate\Database\Eloquent\Model{
		/**
		 * Find a model by its primary key.
		 *
		 * @param array  $id
		 * @param array  $columns
		 * @return \Illuminate\Database\Eloquent\Model|Collection|static
		 * @static 
		 */
		 public static function findMany($id, $columns = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::findMany($id, $columns);
		 }

		/**
		 * Execute the query and get the first result.
		 *
		 * @param array  $columns
		 * @return \Illuminate\Database\Eloquent\Model|static|null
		 * @static 
		 */
		 public static function first($columns = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::first($columns);
		 }

		/**
		 * Execute the query and get the first result or throw an exception.
		 *
		 * @param array  $columns
		 * @return \Illuminate\Database\Eloquent\Model|static
		 * @throws ModelNotFoundException
		 * @static 
		 */
		 public static function firstOrFail($columns = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::firstOrFail($columns);
		 }

		/**
		 * Execute the query as a "select" statement.
		 *
		 * @param array  $columns
		 * @return \Illuminate\Database\Eloquent\Collection|static[]
		 * @static 
		 */
		 public static function get($columns = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::get($columns);
		 }

		/**
		 * Pluck a single column from the database.
		 *
		 * @param string  $column
		 * @return mixed
		 * @static 
		 */
		 public static function pluck($column){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::pluck($column);
		 }

		/**
		 * Chunk the results of the query.
		 *
		 * @param int  $count
		 * @param callable  $callback
		 * @return void
		 * @static 
		 */
		 public static function chunk($count, $callback){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			 \Illuminate\Database\Eloquent\Builder::chunk($count, $callback);
		 }

		/**
		 * Get an array with the values of a given column.
		 *
		 * @param string  $column
		 * @param string  $key
		 * @return array
		 * @static 
		 */
		 public static function lists($column, $key = null){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::lists($column, $key);
		 }

		/**
		 * Get a paginator for the "select" statement.
		 *
		 * @param int    $perPage
		 * @param array  $columns
		 * @return \Illuminate\Pagination\Paginator
		 * @static 
		 */
		 public static function paginate($perPage = null, $columns = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::paginate($perPage, $columns);
		 }

		/**
		 * Get a paginator only supporting simple next and previous links.
		 * 
		 * This is more efficient on larger data-sets, etc.
		 *
		 * @param int    $perPage
		 * @param array  $columns
		 * @return \Illuminate\Pagination\Paginator
		 * @static 
		 */
		 public static function simplePaginate($perPage = null, $columns = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::simplePaginate($perPage, $columns);
		 }

		/**
		 * Increment a column's value by a given amount.
		 *
		 * @param string  $column
		 * @param int     $amount
		 * @param array   $extra
		 * @return int
		 * @static 
		 */
		 public static function increment($column, $amount = 1, $extra = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::increment($column, $amount, $extra);
		 }

		/**
		 * Decrement a column's value by a given amount.
		 *
		 * @param string  $column
		 * @param int     $amount
		 * @param array   $extra
		 * @return int
		 * @static 
		 */
		 public static function decrement($column, $amount = 1, $extra = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::decrement($column, $amount, $extra);
		 }

		/**
		 * Run the default delete function on the builder.
		 *
		 * @return mixed
		 * @static 
		 */
		 public static function forceDelete(){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::forceDelete();
		 }

		/**
		 * Register a replacement for the default delete function.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function onDelete($callback){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			 \Illuminate\Database\Eloquent\Builder::onDelete($callback);
		 }

		/**
		 * Get the hydrated models without eager loading.
		 *
		 * @param array  $columns
		 * @return array|static[]
		 * @static 
		 */
		 public static function getModels($columns = array()){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::getModels($columns);
		 }

		/**
		 * Eager load the relationships for the models.
		 *
		 * @param array  $models
		 * @return array
		 * @static 
		 */
		 public static function eagerLoadRelations($models){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::eagerLoadRelations($models);
		 }

		/**
		 * Add a basic where clause to the query.
		 *
		 * @param string  $column
		 * @param string  $operator
		 * @param mixed   $value
		 * @param string  $boolean
		 * @return \Illuminate\Database\Eloquent\Builder|static
		 * @static 
		 */
		 public static function where($column, $operator = null, $value = null, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::where($column, $operator, $value, $boolean);
		 }

		/**
		 * Add an "or where" clause to the query.
		 *
		 * @param string  $column
		 * @param string  $operator
		 * @param mixed   $value
		 * @return \Illuminate\Database\Eloquent\Builder|static
		 * @static 
		 */
		 public static function orWhere($column, $operator = null, $value = null){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::orWhere($column, $operator, $value);
		 }

		/**
		 * Add a relationship count condition to the query.
		 *
		 * @param string  $relation
		 * @param string  $operator
		 * @param int     $count
		 * @param string  $boolean
		 * @param \Closure  $callback
		 * @return \Illuminate\Database\Eloquent\Builder|static
		 * @static 
		 */
		 public static function has($relation, $operator = '>=', $count = 1, $boolean = 'and', $callback = null){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::has($relation, $operator, $count, $boolean, $callback);
		 }

		/**
		 * Add a relationship count condition to the query with where clauses.
		 *
		 * @param string  $relation
		 * @param \Closure  $callback
		 * @param string  $operator
		 * @param int     $count
		 * @return \Illuminate\Database\Eloquent\Builder|static
		 * @static 
		 */
		 public static function whereHas($relation, $callback, $operator = '>=', $count = 1){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::whereHas($relation, $callback, $operator, $count);
		 }

		/**
		 * Add a relationship count condition to the query with an "or".
		 *
		 * @param string  $relation
		 * @param string  $operator
		 * @param int     $count
		 * @return \Illuminate\Database\Eloquent\Builder|static
		 * @static 
		 */
		 public static function orHas($relation, $operator = '>=', $count = 1){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::orHas($relation, $operator, $count);
		 }

		/**
		 * Add a relationship count condition to the query with where clauses and an "or".
		 *
		 * @param string  $relation
		 * @param \Closure  $callback
		 * @param string  $operator
		 * @param int     $count
		 * @return \Illuminate\Database\Eloquent\Builder|static
		 * @static 
		 */
		 public static function orWhereHas($relation, $callback, $operator = '>=', $count = 1){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::orWhereHas($relation, $callback, $operator, $count);
		 }

		/**
		 * Get the underlying query builder instance.
		 *
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function getQuery(){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::getQuery();
		 }

		/**
		 * Set the underlying query builder instance.
		 *
		 * @param \Illuminate\Database\Query\Builder  $query
		 * @return void
		 * @static 
		 */
		 public static function setQuery($query){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			 \Illuminate\Database\Eloquent\Builder::setQuery($query);
		 }

		/**
		 * Get the relationships being eagerly loaded.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getEagerLoads(){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::getEagerLoads();
		 }

		/**
		 * Set the relationships being eagerly loaded.
		 *
		 * @param array  $eagerLoad
		 * @return void
		 * @static 
		 */
		 public static function setEagerLoads($eagerLoad){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			 \Illuminate\Database\Eloquent\Builder::setEagerLoads($eagerLoad);
		 }

		/**
		 * Get the model instance being queried.
		 *
		 * @return \Illuminate\Database\Eloquent\Model
		 * @static 
		 */
		 public static function getModel(){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::getModel();
		 }

		/**
		 * Set a model instance for the model being queried.
		 *
		 * @param \Illuminate\Database\Eloquent\Model  $model
		 * @return \Illuminate\Database\Eloquent\Builder
		 * @static 
		 */
		 public static function setModel($model){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::setModel($model);
		 }

		/**
		 * Extend the builder with a given callback.
		 *
		 * @param string  $name
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function macro($name, $callback){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			 \Illuminate\Database\Eloquent\Builder::macro($name, $callback);
		 }

		/**
		 * Get the given macro by name.
		 *
		 * @param string  $name
		 * @return \Closure
		 * @static 
		 */
		 public static function getMacro($name){
			//Method inherited from \Illuminate\Database\Eloquent\Builder
			return \Illuminate\Database\Eloquent\Builder::getMacro($name);
		 }

		/**
		 * Set the columns to be selected.
		 *
		 * @param array  $columns
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function select($columns = array()){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::select($columns);
		 }

		/**
		 * Add a new "raw" select expression to the query.
		 *
		 * @param string  $expression
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function selectRaw($expression){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::selectRaw($expression);
		 }

		/**
		 * Add a new select column to the query.
		 *
		 * @param mixed  $column
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function addSelect($column){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::addSelect($column);
		 }

		/**
		 * Force the query to only return distinct results.
		 *
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function distinct(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::distinct();
		 }

		/**
		 * Set the table which the query is targeting.
		 *
		 * @param string  $table
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function from($table){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::from($table);
		 }

		/**
		 * Add a join clause to the query.
		 *
		 * @param string  $table
		 * @param string  $one
		 * @param string  $operator
		 * @param string  $two
		 * @param string  $type
		 * @param bool    $where
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::join($table, $one, $operator, $two, $type, $where);
		 }

		/**
		 * Add a "join where" clause to the query.
		 *
		 * @param string  $table
		 * @param string  $one
		 * @param string  $operator
		 * @param string  $two
		 * @param string  $type
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function joinWhere($table, $one, $operator, $two, $type = 'inner'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::joinWhere($table, $one, $operator, $two, $type);
		 }

		/**
		 * Add a left join to the query.
		 *
		 * @param string  $table
		 * @param string  $first
		 * @param string  $operator
		 * @param string  $second
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function leftJoin($table, $first, $operator = null, $second = null){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::leftJoin($table, $first, $operator, $second);
		 }

		/**
		 * Add a "join where" clause to the query.
		 *
		 * @param string  $table
		 * @param string  $one
		 * @param string  $operator
		 * @param string  $two
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function leftJoinWhere($table, $one, $operator, $two){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::leftJoinWhere($table, $one, $operator, $two);
		 }

		/**
		 * Add a right join to the query.
		 *
		 * @param string  $table
		 * @param string  $first
		 * @param string  $operator
		 * @param string  $second
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function rightJoin($table, $first, $operator = null, $second = null){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::rightJoin($table, $first, $operator, $second);
		 }

		/**
		 * Add a "right join where" clause to the query.
		 *
		 * @param string  $table
		 * @param string  $one
		 * @param string  $operator
		 * @param string  $two
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function rightJoinWhere($table, $one, $operator, $two){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::rightJoinWhere($table, $one, $operator, $two);
		 }

		/**
		 * Add a raw where clause to the query.
		 *
		 * @param string  $sql
		 * @param array   $bindings
		 * @param string  $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereRaw($sql, $bindings = array(), $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereRaw($sql, $bindings, $boolean);
		 }

		/**
		 * Add a raw or where clause to the query.
		 *
		 * @param string  $sql
		 * @param array   $bindings
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereRaw($sql, $bindings = array()){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereRaw($sql, $bindings);
		 }

		/**
		 * Add a where between statement to the query.
		 *
		 * @param string  $column
		 * @param array   $values
		 * @param string  $boolean
		 * @param bool  $not
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereBetween($column, $values, $boolean = 'and', $not = false){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereBetween($column, $values, $boolean, $not);
		 }

		/**
		 * Add an or where between statement to the query.
		 *
		 * @param string  $column
		 * @param array   $values
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereBetween($column, $values){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereBetween($column, $values);
		 }

		/**
		 * Add a where not between statement to the query.
		 *
		 * @param string  $column
		 * @param array   $values
		 * @param string  $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereNotBetween($column, $values, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereNotBetween($column, $values, $boolean);
		 }

		/**
		 * Add an or where not between statement to the query.
		 *
		 * @param string  $column
		 * @param array   $values
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereNotBetween($column, $values){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereNotBetween($column, $values);
		 }

		/**
		 * Add a nested where statement to the query.
		 *
		 * @param \Closure $callback
		 * @param string   $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereNested($callback, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereNested($callback, $boolean);
		 }

		/**
		 * Add another query builder as a nested where to the query builder.
		 *
		 * @param \Illuminate\Database\Query\Builder|static $query
		 * @param string  $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function addNestedWhereQuery($query, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::addNestedWhereQuery($query, $boolean);
		 }

		/**
		 * Add an exists clause to the query.
		 *
		 * @param \Closure $callback
		 * @param string   $boolean
		 * @param bool     $not
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereExists($callback, $boolean = 'and', $not = false){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereExists($callback, $boolean, $not);
		 }

		/**
		 * Add an or exists clause to the query.
		 *
		 * @param \Closure $callback
		 * @param bool     $not
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereExists($callback, $not = false){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereExists($callback, $not);
		 }

		/**
		 * Add a where not exists clause to the query.
		 *
		 * @param \Closure $callback
		 * @param string   $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereNotExists($callback, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereNotExists($callback, $boolean);
		 }

		/**
		 * Add a where not exists clause to the query.
		 *
		 * @param \Closure  $callback
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereNotExists($callback){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereNotExists($callback);
		 }

		/**
		 * Add a "where in" clause to the query.
		 *
		 * @param string  $column
		 * @param mixed   $values
		 * @param string  $boolean
		 * @param bool    $not
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereIn($column, $values, $boolean = 'and', $not = false){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereIn($column, $values, $boolean, $not);
		 }

		/**
		 * Add an "or where in" clause to the query.
		 *
		 * @param string  $column
		 * @param mixed   $values
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereIn($column, $values){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereIn($column, $values);
		 }

		/**
		 * Add a "where not in" clause to the query.
		 *
		 * @param string  $column
		 * @param mixed   $values
		 * @param string  $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereNotIn($column, $values, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereNotIn($column, $values, $boolean);
		 }

		/**
		 * Add an "or where not in" clause to the query.
		 *
		 * @param string  $column
		 * @param mixed   $values
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereNotIn($column, $values){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereNotIn($column, $values);
		 }

		/**
		 * Add a "where null" clause to the query.
		 *
		 * @param string  $column
		 * @param string  $boolean
		 * @param bool    $not
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereNull($column, $boolean = 'and', $not = false){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereNull($column, $boolean, $not);
		 }

		/**
		 * Add an "or where null" clause to the query.
		 *
		 * @param string  $column
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereNull($column){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereNull($column);
		 }

		/**
		 * Add a "where not null" clause to the query.
		 *
		 * @param string  $column
		 * @param string  $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereNotNull($column, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereNotNull($column, $boolean);
		 }

		/**
		 * Add an "or where not null" clause to the query.
		 *
		 * @param string  $column
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orWhereNotNull($column){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orWhereNotNull($column);
		 }

		/**
		 * Add a "where day" statement to the query.
		 *
		 * @param string  $column
		 * @param string   $operator
		 * @param int   $value
		 * @param string   $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereDay($column, $operator, $value, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereDay($column, $operator, $value, $boolean);
		 }

		/**
		 * Add a "where month" statement to the query.
		 *
		 * @param string  $column
		 * @param string   $operator
		 * @param int   $value
		 * @param string   $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereMonth($column, $operator, $value, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereMonth($column, $operator, $value, $boolean);
		 }

		/**
		 * Add a "where year" statement to the query.
		 *
		 * @param string  $column
		 * @param string   $operator
		 * @param int   $value
		 * @param string   $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function whereYear($column, $operator, $value, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::whereYear($column, $operator, $value, $boolean);
		 }

		/**
		 * Handles dynamic "where" clauses to the query.
		 *
		 * @param string  $method
		 * @param string  $parameters
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function dynamicWhere($method, $parameters){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::dynamicWhere($method, $parameters);
		 }

		/**
		 * Add a "group by" clause to the query.
		 *
		 * @param dynamic  $columns
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function groupBy(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::groupBy();
		 }

		/**
		 * Add a "having" clause to the query.
		 *
		 * @param string  $column
		 * @param string  $operator
		 * @param string  $value
		 * @param string  $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function having($column, $operator = null, $value = null, $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::having($column, $operator, $value, $boolean);
		 }

		/**
		 * Add a "or having" clause to the query.
		 *
		 * @param string  $column
		 * @param string  $operator
		 * @param string  $value
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orHaving($column, $operator = null, $value = null){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orHaving($column, $operator, $value);
		 }

		/**
		 * Add a raw having clause to the query.
		 *
		 * @param string  $sql
		 * @param array   $bindings
		 * @param string  $boolean
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function havingRaw($sql, $bindings = array(), $boolean = 'and'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::havingRaw($sql, $bindings, $boolean);
		 }

		/**
		 * Add a raw or having clause to the query.
		 *
		 * @param string  $sql
		 * @param array   $bindings
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orHavingRaw($sql, $bindings = array()){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orHavingRaw($sql, $bindings);
		 }

		/**
		 * Add an "order by" clause to the query.
		 *
		 * @param string  $column
		 * @param string  $direction
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orderBy($column, $direction = 'asc'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orderBy($column, $direction);
		 }

		/**
		 * Add an "order by" clause for a timestamp to the query.
		 *
		 * @param string  $column
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function latest($column = 'created_at'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::latest($column);
		 }

		/**
		 * Add an "order by" clause for a timestamp to the query.
		 *
		 * @param string  $column
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function oldest($column = 'created_at'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::oldest($column);
		 }

		/**
		 * Add a raw "order by" clause to the query.
		 *
		 * @param string  $sql
		 * @param array  $bindings
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function orderByRaw($sql, $bindings = array()){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::orderByRaw($sql, $bindings);
		 }

		/**
		 * Set the "offset" value of the query.
		 *
		 * @param int  $value
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function offset($value){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::offset($value);
		 }

		/**
		 * Alias to set the "offset" value of the query.
		 *
		 * @param int  $value
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function skip($value){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::skip($value);
		 }

		/**
		 * Set the "limit" value of the query.
		 *
		 * @param int  $value
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function limit($value){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::limit($value);
		 }

		/**
		 * Alias to set the "limit" value of the query.
		 *
		 * @param int  $value
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function take($value){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::take($value);
		 }

		/**
		 * Set the limit and offset for a given page.
		 *
		 * @param int  $page
		 * @param int  $perPage
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function forPage($page, $perPage = 15){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::forPage($page, $perPage);
		 }

		/**
		 * Add a union statement to the query.
		 *
		 * @param \Illuminate\Database\Query\Builder|\Closure  $query
		 * @param bool $all
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function union($query, $all = false){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::union($query, $all);
		 }

		/**
		 * Add a union all statement to the query.
		 *
		 * @param \Illuminate\Database\Query\Builder|\Closure  $query
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function unionAll($query){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::unionAll($query);
		 }

		/**
		 * Lock the selected rows in the table.
		 *
		 * @param bool  $update
		 * @return \Illuminate\Database\Query\Builder
		 * @static 
		 */
		 public static function lock($value = true){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::lock($value);
		 }

		/**
		 * Lock the selected rows in the table for updating.
		 *
		 * @return \Illuminate\Database\Query\Builder
		 * @static 
		 */
		 public static function lockForUpdate(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::lockForUpdate();
		 }

		/**
		 * Share lock the selected rows in the table.
		 *
		 * @return \Illuminate\Database\Query\Builder
		 * @static 
		 */
		 public static function sharedLock(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::sharedLock();
		 }

		/**
		 * Get the SQL representation of the query.
		 *
		 * @return string
		 * @static 
		 */
		 public static function toSql(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::toSql();
		 }

		/**
		 * Indicate that the query results should be cached.
		 *
		 * @param \DateTime|int  $minutes
		 * @param string  $key
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function remember($minutes, $key = null){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::remember($minutes, $key);
		 }

		/**
		 * Indicate that the query results should be cached forever.
		 *
		 * @param string  $key
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function rememberForever($key = null){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::rememberForever($key);
		 }

		/**
		 * Indicate that the results, if cached, should use the given cache tags.
		 *
		 * @param array|dynamic  $cacheTags
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function cacheTags($cacheTags){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::cacheTags($cacheTags);
		 }

		/**
		 * Indicate that the results, if cached, should use the given cache driver.
		 *
		 * @param string  $cacheDriver
		 * @return \Illuminate\Database\Query\Builder|static
		 * @static 
		 */
		 public static function cacheDriver($cacheDriver){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::cacheDriver($cacheDriver);
		 }

		/**
		 * Execute the query as a fresh "select" statement.
		 *
		 * @param array  $columns
		 * @return array|static[]
		 * @static 
		 */
		 public static function getFresh($columns = array()){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getFresh($columns);
		 }

		/**
		 * Execute the query as a cached "select" statement.
		 *
		 * @param array  $columns
		 * @return array
		 * @static 
		 */
		 public static function getCached($columns = array()){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getCached($columns);
		 }

		/**
		 * Get a unique cache key for the complete query.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getCacheKey(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getCacheKey();
		 }

		/**
		 * Generate the unique cache key for the query.
		 *
		 * @return string
		 * @static 
		 */
		 public static function generateCacheKey(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::generateCacheKey();
		 }

		/**
		 * Concatenate values of a given column as a string.
		 *
		 * @param string  $column
		 * @param string  $glue
		 * @return string
		 * @static 
		 */
		 public static function implode($column, $glue = null){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::implode($column, $glue);
		 }

		/**
		 * Build a paginator instance from a raw result array.
		 *
		 * @param \Illuminate\Pagination\Factory  $paginator
		 * @param array  $results
		 * @param int    $perPage
		 * @return \Illuminate\Pagination\Paginator
		 * @static 
		 */
		 public static function buildRawPaginator($paginator, $results, $perPage){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::buildRawPaginator($paginator, $results, $perPage);
		 }

		/**
		 * Get the count of the total records for pagination.
		 *
		 * @return int
		 * @static 
		 */
		 public static function getPaginationCount(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getPaginationCount();
		 }

		/**
		 * Determine if any rows exist for the current query.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function exists(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::exists();
		 }

		/**
		 * Retrieve the "count" result of the query.
		 *
		 * @param string  $columns
		 * @return int
		 * @static 
		 */
		 public static function count($columns = '*'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::count($columns);
		 }

		/**
		 * Retrieve the minimum value of a given column.
		 *
		 * @param string  $column
		 * @return mixed
		 * @static 
		 */
		 public static function min($column){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::min($column);
		 }

		/**
		 * Retrieve the maximum value of a given column.
		 *
		 * @param string  $column
		 * @return mixed
		 * @static 
		 */
		 public static function max($column){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::max($column);
		 }

		/**
		 * Retrieve the sum of the values of a given column.
		 *
		 * @param string  $column
		 * @return mixed
		 * @static 
		 */
		 public static function sum($column){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::sum($column);
		 }

		/**
		 * Retrieve the average of the values of a given column.
		 *
		 * @param string  $column
		 * @return mixed
		 * @static 
		 */
		 public static function avg($column){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::avg($column);
		 }

		/**
		 * Execute an aggregate function on the database.
		 *
		 * @param string  $function
		 * @param array   $columns
		 * @return mixed
		 * @static 
		 */
		 public static function aggregate($function, $columns = array()){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::aggregate($function, $columns);
		 }

		/**
		 * Insert a new record into the database.
		 *
		 * @param array  $values
		 * @return bool
		 * @static 
		 */
		 public static function insert($values){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::insert($values);
		 }

		/**
		 * Insert a new record and get the value of the primary key.
		 *
		 * @param array   $values
		 * @param string  $sequence
		 * @return int
		 * @static 
		 */
		 public static function insertGetId($values, $sequence = null){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::insertGetId($values, $sequence);
		 }

		/**
		 * Run a truncate statement on the table.
		 *
		 * @return void
		 * @static 
		 */
		 public static function truncate(){
			//Method inherited from \Illuminate\Database\Query\Builder
			 \Illuminate\Database\Query\Builder::truncate();
		 }

		/**
		 * Merge an array of where clauses and bindings.
		 *
		 * @param array  $wheres
		 * @param array  $bindings
		 * @return void
		 * @static 
		 */
		 public static function mergeWheres($wheres, $bindings){
			//Method inherited from \Illuminate\Database\Query\Builder
			 \Illuminate\Database\Query\Builder::mergeWheres($wheres, $bindings);
		 }

		/**
		 * Create a raw database expression.
		 *
		 * @param mixed  $value
		 * @return \Illuminate\Database\Query\Expression
		 * @static 
		 */
		 public static function raw($value){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::raw($value);
		 }

		/**
		 * Get the current query value bindings in a flattened array.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getBindings(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getBindings();
		 }

		/**
		 * Get the raw array of bindings.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getRawBindings(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getRawBindings();
		 }

		/**
		 * Set the bindings on the query builder.
		 *
		 * @param array   $bindings
		 * @param string  $type
		 * @return \Illuminate\Database\Query\Builder
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function setBindings($bindings, $type = 'where'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::setBindings($bindings, $type);
		 }

		/**
		 * Add a binding to the query.
		 *
		 * @param mixed   $value
		 * @param string  $type
		 * @return \Illuminate\Database\Query\Builder
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function addBinding($value, $type = 'where'){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::addBinding($value, $type);
		 }

		/**
		 * Merge an array of bindings into our bindings.
		 *
		 * @param \Illuminate\Database\Query\Builder  $query
		 * @return \Illuminate\Database\Query\Builder
		 * @static 
		 */
		 public static function mergeBindings($query){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::mergeBindings($query);
		 }

		/**
		 * Get the database query processor instance.
		 *
		 * @return \Illuminate\Database\Query\Processors\Processor
		 * @static 
		 */
		 public static function getProcessor(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getProcessor();
		 }

		/**
		 * Get the query grammar instance.
		 *
		 * @return \Illuminate\Database\Grammar
		 * @static 
		 */
		 public static function getGrammar(){
			//Method inherited from \Illuminate\Database\Query\Builder
			return \Illuminate\Database\Query\Builder::getGrammar();
		 }

	}
	class Event extends \Illuminate\Support\Facades\Event{
		/**
		 * Create a new event dispatcher instance.
		 *
		 * @param \Illuminate\Container\Container  $container
		 * @return void
		 * @static 
		 */
		 public static function __construct($container = null){
			//Method inherited from \Illuminate\Events\Dispatcher
			 \Illuminate\Events\Dispatcher::__construct($container);
		 }

		/**
		 * Register an event listener with the dispatcher.
		 *
		 * @param string|array  $event
		 * @param mixed   $listener
		 * @param int     $priority
		 * @return void
		 * @static 
		 */
		 public static function listen($events, $listener, $priority = 0){
			//Method inherited from \Illuminate\Events\Dispatcher
			 \Illuminate\Events\Dispatcher::listen($events, $listener, $priority);
		 }

		/**
		 * Determine if a given event has listeners.
		 *
		 * @param string  $eventName
		 * @return bool
		 * @static 
		 */
		 public static function hasListeners($eventName){
			//Method inherited from \Illuminate\Events\Dispatcher
			return \Illuminate\Events\Dispatcher::hasListeners($eventName);
		 }

		/**
		 * Register a queued event and payload.
		 *
		 * @param string  $event
		 * @param array   $payload
		 * @return void
		 * @static 
		 */
		 public static function queue($event, $payload = array()){
			//Method inherited from \Illuminate\Events\Dispatcher
			 \Illuminate\Events\Dispatcher::queue($event, $payload);
		 }

		/**
		 * Register an event subscriber with the dispatcher.
		 *
		 * @param string  $subscriber
		 * @return void
		 * @static 
		 */
		 public static function subscribe($subscriber){
			//Method inherited from \Illuminate\Events\Dispatcher
			 \Illuminate\Events\Dispatcher::subscribe($subscriber);
		 }

		/**
		 * Fire an event until the first non-null response is returned.
		 *
		 * @param string  $event
		 * @param array   $payload
		 * @return mixed
		 * @static 
		 */
		 public static function until($event, $payload = array()){
			//Method inherited from \Illuminate\Events\Dispatcher
			return \Illuminate\Events\Dispatcher::until($event, $payload);
		 }

		/**
		 * Flush a set of queued events.
		 *
		 * @param string  $event
		 * @return void
		 * @static 
		 */
		 public static function flush($event){
			//Method inherited from \Illuminate\Events\Dispatcher
			 \Illuminate\Events\Dispatcher::flush($event);
		 }

		/**
		 * Get the event that is currently firing.
		 *
		 * @return string
		 * @static 
		 */
		 public static function firing(){
			//Method inherited from \Illuminate\Events\Dispatcher
			return \Illuminate\Events\Dispatcher::firing();
		 }

		/**
		 * Fire an event and call the listeners.
		 *
		 * @param string  $event
		 * @param mixed   $payload
		 * @param bool    $halt
		 * @return array|null
		 * @static 
		 */
		 public static function fire($event, $payload = array(), $halt = false){
			//Method inherited from \Illuminate\Events\Dispatcher
			return \Illuminate\Events\Dispatcher::fire($event, $payload, $halt);
		 }

		/**
		 * Get all of the listeners for a given event name.
		 *
		 * @param string  $eventName
		 * @return array
		 * @static 
		 */
		 public static function getListeners($eventName){
			//Method inherited from \Illuminate\Events\Dispatcher
			return \Illuminate\Events\Dispatcher::getListeners($eventName);
		 }

		/**
		 * Register an event listener with the dispatcher.
		 *
		 * @param mixed   $listener
		 * @return mixed
		 * @static 
		 */
		 public static function makeListener($listener){
			//Method inherited from \Illuminate\Events\Dispatcher
			return \Illuminate\Events\Dispatcher::makeListener($listener);
		 }

		/**
		 * Create a class based listener using the IoC container.
		 *
		 * @param mixed    $listener
		 * @return \Closure
		 * @static 
		 */
		 public static function createClassListener($listener){
			//Method inherited from \Illuminate\Events\Dispatcher
			return \Illuminate\Events\Dispatcher::createClassListener($listener);
		 }

		/**
		 * Remove a set of listeners from the dispatcher.
		 *
		 * @param string  $event
		 * @return void
		 * @static 
		 */
		 public static function forget($event){
			//Method inherited from \Illuminate\Events\Dispatcher
			 \Illuminate\Events\Dispatcher::forget($event);
		 }

	}
	class File extends \Illuminate\Support\Facades\File{
		/**
		 * Determine if a file exists.
		 *
		 * @param string  $path
		 * @return bool
		 * @static 
		 */
		 public static function exists($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::exists($path);
		 }

		/**
		 * Get the contents of a file.
		 *
		 * @param string  $path
		 * @return string
		 * @throws FileNotFoundException
		 * @static 
		 */
		 public static function get($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::get($path);
		 }

		/**
		 * Get the returned value of a file.
		 *
		 * @param string  $path
		 * @return mixed
		 * @throws FileNotFoundException
		 * @static 
		 */
		 public static function getRequire($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::getRequire($path);
		 }

		/**
		 * Require the given file once.
		 *
		 * @param string  $file
		 * @return mixed
		 * @static 
		 */
		 public static function requireOnce($file){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::requireOnce($file);
		 }

		/**
		 * Write the contents of a file.
		 *
		 * @param string  $path
		 * @param string  $contents
		 * @return int
		 * @static 
		 */
		 public static function put($path, $contents){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::put($path, $contents);
		 }

		/**
		 * Prepend to a file.
		 *
		 * @param string  $path
		 * @param string  $data
		 * @return int
		 * @static 
		 */
		 public static function prepend($path, $data){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::prepend($path, $data);
		 }

		/**
		 * Append to a file.
		 *
		 * @param string  $path
		 * @param string  $data
		 * @return int
		 * @static 
		 */
		 public static function append($path, $data){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::append($path, $data);
		 }

		/**
		 * Delete the file at a given path.
		 *
		 * @param string|array  $paths
		 * @return bool
		 * @static 
		 */
		 public static function delete($paths){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::delete($paths);
		 }

		/**
		 * Move a file to a new location.
		 *
		 * @param string  $path
		 * @param string  $target
		 * @return bool
		 * @static 
		 */
		 public static function move($path, $target){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::move($path, $target);
		 }

		/**
		 * Copy a file to a new location.
		 *
		 * @param string  $path
		 * @param string  $target
		 * @return bool
		 * @static 
		 */
		 public static function copy($path, $target){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::copy($path, $target);
		 }

		/**
		 * Extract the file extension from a file path.
		 *
		 * @param string  $path
		 * @return string
		 * @static 
		 */
		 public static function extension($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::extension($path);
		 }

		/**
		 * Get the file type of a given file.
		 *
		 * @param string  $path
		 * @return string
		 * @static 
		 */
		 public static function type($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::type($path);
		 }

		/**
		 * Get the file size of a given file.
		 *
		 * @param string  $path
		 * @return int
		 * @static 
		 */
		 public static function size($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::size($path);
		 }

		/**
		 * Get the file's last modification time.
		 *
		 * @param string  $path
		 * @return int
		 * @static 
		 */
		 public static function lastModified($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::lastModified($path);
		 }

		/**
		 * Determine if the given path is a directory.
		 *
		 * @param string  $directory
		 * @return bool
		 * @static 
		 */
		 public static function isDirectory($directory){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::isDirectory($directory);
		 }

		/**
		 * Determine if the given path is writable.
		 *
		 * @param string  $path
		 * @return bool
		 * @static 
		 */
		 public static function isWritable($path){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::isWritable($path);
		 }

		/**
		 * Determine if the given path is a file.
		 *
		 * @param string  $file
		 * @return bool
		 * @static 
		 */
		 public static function isFile($file){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::isFile($file);
		 }

		/**
		 * Find path names matching a given pattern.
		 *
		 * @param string  $pattern
		 * @param int     $flags
		 * @return array
		 * @static 
		 */
		 public static function glob($pattern, $flags = 0){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::glob($pattern, $flags);
		 }

		/**
		 * Get an array of all files in a directory.
		 *
		 * @param string  $directory
		 * @return array
		 * @static 
		 */
		 public static function files($directory){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::files($directory);
		 }

		/**
		 * Get all of the files from the given directory (recursive).
		 *
		 * @param string  $directory
		 * @return array
		 * @static 
		 */
		 public static function allFiles($directory){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::allFiles($directory);
		 }

		/**
		 * Get all of the directories within a given directory.
		 *
		 * @param string  $directory
		 * @return array
		 * @static 
		 */
		 public static function directories($directory){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::directories($directory);
		 }

		/**
		 * Create a directory.
		 *
		 * @param string  $path
		 * @param int     $mode
		 * @param bool    $recursive
		 * @param bool    $force
		 * @return bool
		 * @static 
		 */
		 public static function makeDirectory($path, $mode = 493, $recursive = false, $force = false){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::makeDirectory($path, $mode, $recursive, $force);
		 }

		/**
		 * Copy a directory from one location to another.
		 *
		 * @param string  $directory
		 * @param string  $destination
		 * @param int     $options
		 * @return bool
		 * @static 
		 */
		 public static function copyDirectory($directory, $destination, $options = null){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::copyDirectory($directory, $destination, $options);
		 }

		/**
		 * Recursively delete a directory.
		 * 
		 * The directory itself may be optionally preserved.
		 *
		 * @param string  $directory
		 * @param bool    $preserve
		 * @return bool
		 * @static 
		 */
		 public static function deleteDirectory($directory, $preserve = false){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::deleteDirectory($directory, $preserve);
		 }

		/**
		 * Empty the specified directory of all files and folders.
		 *
		 * @param string  $directory
		 * @return bool
		 * @static 
		 */
		 public static function cleanDirectory($directory){
			//Method inherited from \Illuminate\Filesystem\Filesystem
			return \Illuminate\Filesystem\Filesystem::cleanDirectory($directory);
		 }

	}
	class Form extends \Illuminate\Support\Facades\Form{
		/**
		 * Create a new form builder instance.
		 *
		 * @param \Illuminate\Routing\UrlGenerator  $url
		 * @param \Illuminate\Html\HtmlBuilder  $html
		 * @param string  $csrfToken
		 * @return void
		 * @static 
		 */
		 public static function __construct($html, $url, $csrfToken){
			//Method inherited from \Illuminate\Html\FormBuilder
			 \Illuminate\Html\FormBuilder::__construct($html, $url, $csrfToken);
		 }

		/**
		 * Open up a new HTML form.
		 *
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function open($options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::open($options);
		 }

		/**
		 * Create a new model based form builder.
		 *
		 * @param mixed  $model
		 * @param array  $options
		 * @return string
		 * @static 
		 */
		 public static function model($model, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::model($model, $options);
		 }

		/**
		 * Set the model instance on the form builder.
		 *
		 * @param mixed  $model
		 * @return void
		 * @static 
		 */
		 public static function setModel($model){
			//Method inherited from \Illuminate\Html\FormBuilder
			 \Illuminate\Html\FormBuilder::setModel($model);
		 }

		/**
		 * Close the current form.
		 *
		 * @return string
		 * @static 
		 */
		 public static function close(){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::close();
		 }

		/**
		 * Generate a hidden field with the current CSRF token.
		 *
		 * @return string
		 * @static 
		 */
		 public static function token(){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::token();
		 }

		/**
		 * Create a form label element.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function label($name, $value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::label($name, $value, $options);
		 }

		/**
		 * Create a form input field.
		 *
		 * @param string  $type
		 * @param string  $name
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function input($type, $name, $value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::input($type, $name, $value, $options);
		 }

		/**
		 * Create a text input field.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function text($name, $value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::text($name, $value, $options);
		 }

		/**
		 * Create a password input field.
		 *
		 * @param string  $name
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function password($name, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::password($name, $options);
		 }

		/**
		 * Create a hidden input field.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function hidden($name, $value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::hidden($name, $value, $options);
		 }

		/**
		 * Create an e-mail input field.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function email($name, $value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::email($name, $value, $options);
		 }

		/**
		 * Create a url input field.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function url($name, $value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::url($name, $value, $options);
		 }

		/**
		 * Create a file input field.
		 *
		 * @param string  $name
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function file($name, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::file($name, $options);
		 }

		/**
		 * Create a textarea input field.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function textarea($name, $value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::textarea($name, $value, $options);
		 }

		/**
		 * Create a select box field.
		 *
		 * @param string  $name
		 * @param array   $list
		 * @param string  $selected
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function select($name, $list = array(), $selected = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::select($name, $list, $selected, $options);
		 }

		/**
		 * Create a select range field.
		 *
		 * @param string  $name
		 * @param string  $begin
		 * @param string  $end
		 * @param string  $selected
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function selectRange($name, $begin, $end, $selected = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::selectRange($name, $begin, $end, $selected, $options);
		 }

		/**
		 * Create a select year field.
		 *
		 * @param string  $name
		 * @param string  $begin
		 * @param string  $end
		 * @param string  $selected
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function selectYear(){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::selectYear();
		 }

		/**
		 * Create a select month field.
		 *
		 * @param string  $name
		 * @param string  $selected
		 * @param array   $options
		 * @param string  $format
		 * @return string
		 * @static 
		 */
		 public static function selectMonth($name, $selected = null, $options = array(), $format = '%B'){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::selectMonth($name, $selected, $options, $format);
		 }

		/**
		 * Get the select option for the given value.
		 *
		 * @param string  $display
		 * @param string  $value
		 * @param string  $selected
		 * @return string
		 * @static 
		 */
		 public static function getSelectOption($display, $value, $selected){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::getSelectOption($display, $value, $selected);
		 }

		/**
		 * Create a checkbox input field.
		 *
		 * @param string  $name
		 * @param mixed   $value
		 * @param bool    $checked
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function checkbox($name, $value = 1, $checked = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::checkbox($name, $value, $checked, $options);
		 }

		/**
		 * Create a radio button input field.
		 *
		 * @param string  $name
		 * @param mixed   $value
		 * @param bool    $checked
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function radio($name, $value = null, $checked = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::radio($name, $value, $checked, $options);
		 }

		/**
		 * Create a HTML reset input element.
		 *
		 * @param string  $value
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function reset($value, $attributes = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::reset($value, $attributes);
		 }

		/**
		 * Create a HTML image input element.
		 *
		 * @param string  $url
		 * @param string  $name
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function image($url, $name = null, $attributes = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::image($url, $name, $attributes);
		 }

		/**
		 * Create a submit button element.
		 *
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function submit($value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::submit($value, $options);
		 }

		/**
		 * Create a button element.
		 *
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @static 
		 */
		 public static function button($value = null, $options = array()){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::button($value, $options);
		 }

		/**
		 * Get the ID attribute for a field name.
		 *
		 * @param string  $name
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function getIdAttribute($name, $attributes){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::getIdAttribute($name, $attributes);
		 }

		/**
		 * Get the value that should be assigned to the field.
		 *
		 * @param string  $name
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function getValueAttribute($name, $value = null){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::getValueAttribute($name, $value);
		 }

		/**
		 * Get a value from the session's old input.
		 *
		 * @param string  $name
		 * @return string
		 * @static 
		 */
		 public static function old($name){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::old($name);
		 }

		/**
		 * Determine if the old input is empty.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function oldInputIsEmpty(){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::oldInputIsEmpty();
		 }

		/**
		 * Get the session store implementation.
		 *
		 * @return \Illuminate\Session\Store  $session
		 * @static 
		 */
		 public static function getSessionStore(){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::getSessionStore();
		 }

		/**
		 * Set the session store implementation.
		 *
		 * @param \Illuminate\Session\Store  $session
		 * @return \Illuminate\Html\FormBuilder
		 * @static 
		 */
		 public static function setSessionStore($session){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::setSessionStore($session);
		 }

		/**
		 * Register a custom macro.
		 *
		 * @param string    $name
		 * @param callable  $macro
		 * @return void
		 * @static 
		 */
		 public static function macro($name, $macro){
			//Method inherited from \Illuminate\Html\FormBuilder
			 \Illuminate\Html\FormBuilder::macro($name, $macro);
		 }

		/**
		 * Checks if macro is registered
		 *
		 * @param string    $name
		 * @return boolean
		 * @static 
		 */
		 public static function hasMacro($name){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::hasMacro($name);
		 }

		/**
		 * Dynamically handle calls to the class.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @throws \BadMethodCallException
		 * @static 
		 */
		 public static function __callStatic($method, $parameters){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::__callStatic($method, $parameters);
		 }

		/**
		 * Dynamically handle calls to the form builder.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @throws \BadMethodCallException
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Html\FormBuilder
			return \Illuminate\Html\FormBuilder::__call($method, $parameters);
		 }

	}
	class Hash extends \Illuminate\Support\Facades\Hash{
		/**
		 * Hash the given value.
		 *
		 * @param string  $value
		 * @param array   $options
		 * @return string
		 * @throws \RuntimeException
		 * @static 
		 */
		 public static function make($value, $options = array()){
			//Method inherited from \Illuminate\Hashing\BcryptHasher
			return \Illuminate\Hashing\BcryptHasher::make($value, $options);
		 }

		/**
		 * Check the given plain value against a hash.
		 *
		 * @param string  $value
		 * @param string  $hashedValue
		 * @param array   $options
		 * @return bool
		 * @static 
		 */
		 public static function check($value, $hashedValue, $options = array()){
			//Method inherited from \Illuminate\Hashing\BcryptHasher
			return \Illuminate\Hashing\BcryptHasher::check($value, $hashedValue, $options);
		 }

		/**
		 * Check if the given hash has been hashed using the given options.
		 *
		 * @param string  $hashedValue
		 * @param array   $options
		 * @return bool
		 * @static 
		 */
		 public static function needsRehash($hashedValue, $options = array()){
			//Method inherited from \Illuminate\Hashing\BcryptHasher
			return \Illuminate\Hashing\BcryptHasher::needsRehash($hashedValue, $options);
		 }

	}
	class HTML extends \Illuminate\Support\Facades\HTML{
		/**
		 * Create a new HTML builder instance.
		 *
		 * @param \Illuminate\Routing\UrlGenerator  $url
		 * @return void
		 * @static 
		 */
		 public static function __construct($url = null){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			 \Illuminate\Html\HtmlBuilder::__construct($url);
		 }

		/**
		 * Convert an HTML string to entities.
		 *
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function entities($value){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::entities($value);
		 }

		/**
		 * Convert entities to HTML characters.
		 *
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function decode($value){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::decode($value);
		 }

		/**
		 * Generate a link to a JavaScript file.
		 *
		 * @param string  $url
		 * @param array   $attributes
		 * @param bool    $secure
		 * @return string
		 * @static 
		 */
		 public static function script($url, $attributes = array(), $secure = null){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::script($url, $attributes, $secure);
		 }

		/**
		 * Generate a link to a CSS file.
		 *
		 * @param string  $url
		 * @param array   $attributes
		 * @param bool    $secure
		 * @return string
		 * @static 
		 */
		 public static function style($url, $attributes = array(), $secure = null){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::style($url, $attributes, $secure);
		 }

		/**
		 * Generate an HTML image element.
		 *
		 * @param string  $url
		 * @param string  $alt
		 * @param array   $attributes
		 * @param bool    $secure
		 * @return string
		 * @static 
		 */
		 public static function image($url, $alt = null, $attributes = array(), $secure = null){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::image($url, $alt, $attributes, $secure);
		 }

		/**
		 * Generate a HTML link.
		 *
		 * @param string  $url
		 * @param string  $title
		 * @param array   $attributes
		 * @param bool    $secure
		 * @return string
		 * @static 
		 */
		 public static function link($url, $title = null, $attributes = array(), $secure = null){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::link($url, $title, $attributes, $secure);
		 }

		/**
		 * Generate a HTTPS HTML link.
		 *
		 * @param string  $url
		 * @param string  $title
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function secureLink($url, $title = null, $attributes = array()){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::secureLink($url, $title, $attributes);
		 }

		/**
		 * Generate a HTML link to an asset.
		 *
		 * @param string  $url
		 * @param string  $title
		 * @param array   $attributes
		 * @param bool    $secure
		 * @return string
		 * @static 
		 */
		 public static function linkAsset($url, $title = null, $attributes = array(), $secure = null){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::linkAsset($url, $title, $attributes, $secure);
		 }

		/**
		 * Generate a HTTPS HTML link to an asset.
		 *
		 * @param string  $url
		 * @param string  $title
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function linkSecureAsset($url, $title = null, $attributes = array()){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::linkSecureAsset($url, $title, $attributes);
		 }

		/**
		 * Generate a HTML link to a named route.
		 *
		 * @param string  $name
		 * @param string  $title
		 * @param array   $parameters
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function linkRoute($name, $title = null, $parameters = array(), $attributes = array()){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::linkRoute($name, $title, $parameters, $attributes);
		 }

		/**
		 * Generate a HTML link to a controller action.
		 *
		 * @param string  $action
		 * @param string  $title
		 * @param array   $parameters
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function linkAction($action, $title = null, $parameters = array(), $attributes = array()){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::linkAction($action, $title, $parameters, $attributes);
		 }

		/**
		 * Generate a HTML link to an email address.
		 *
		 * @param string  $email
		 * @param string  $title
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function mailto($email, $title = null, $attributes = array()){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::mailto($email, $title, $attributes);
		 }

		/**
		 * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
		 *
		 * @param string  $email
		 * @return string
		 * @static 
		 */
		 public static function email($email){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::email($email);
		 }

		/**
		 * Generate an ordered list of items.
		 *
		 * @param array   $list
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function ol($list, $attributes = array()){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::ol($list, $attributes);
		 }

		/**
		 * Generate an un-ordered list of items.
		 *
		 * @param array   $list
		 * @param array   $attributes
		 * @return string
		 * @static 
		 */
		 public static function ul($list, $attributes = array()){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::ul($list, $attributes);
		 }

		/**
		 * Build an HTML attribute string from an array.
		 *
		 * @param array  $attributes
		 * @return string
		 * @static 
		 */
		 public static function attributes($attributes){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::attributes($attributes);
		 }

		/**
		 * Obfuscate a string to prevent spam-bots from sniffing it.
		 *
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function obfuscate($value){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::obfuscate($value);
		 }

		/**
		 * Register a custom macro.
		 *
		 * @param string    $name
		 * @param callable  $macro
		 * @return void
		 * @static 
		 */
		 public static function macro($name, $macro){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			 \Illuminate\Html\HtmlBuilder::macro($name, $macro);
		 }

		/**
		 * Checks if macro is registered
		 *
		 * @param string    $name
		 * @return boolean
		 * @static 
		 */
		 public static function hasMacro($name){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::hasMacro($name);
		 }

		/**
		 * Dynamically handle calls to the class.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @throws \BadMethodCallException
		 * @static 
		 */
		 public static function __callStatic($method, $parameters){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::__callStatic($method, $parameters);
		 }

		/**
		 * Dynamically handle calls to the form builder.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @throws \BadMethodCallException
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Html\HtmlBuilder
			return \Illuminate\Html\HtmlBuilder::__call($method, $parameters);
		 }

	}
	class Input extends \Illuminate\Support\Facades\Input{
		/**
		 * Return the Request instance.
		 *
		 * @return \Illuminate\Http\Request
		 * @static 
		 */
		 public static function instance(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::instance();
		 }

		/**
		 * Get the request method.
		 *
		 * @return string
		 * @static 
		 */
		 public static function method(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::method();
		 }

		/**
		 * Get the root URL for the application.
		 *
		 * @return string
		 * @static 
		 */
		 public static function root(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::root();
		 }

		/**
		 * Get the URL (no query string) for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function url(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::url();
		 }

		/**
		 * Get the full URL for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function fullUrl(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::fullUrl();
		 }

		/**
		 * Get the current path info for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function path(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::path();
		 }

		/**
		 * Get the current encoded path info for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function decodedPath(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::decodedPath();
		 }

		/**
		 * Get a segment from the URI (1 based index).
		 *
		 * @param string  $index
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function segment($index, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::segment($index, $default);
		 }

		/**
		 * Get all of the segments for the request path.
		 *
		 * @return array
		 * @static 
		 */
		 public static function segments(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::segments();
		 }

		/**
		 * Determine if the current request URI matches a pattern.
		 *
		 * @param dynamic  string
		 * @return bool
		 * @static 
		 */
		 public static function is(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::is();
		 }

		/**
		 * Determine if the request is the result of an AJAX call.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function ajax(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::ajax();
		 }

		/**
		 * Determine if the request is over HTTPS.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function secure(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::secure();
		 }

		/**
		 * Determine if the request contains a given input item key.
		 *
		 * @param string|array  $key
		 * @return bool
		 * @static 
		 */
		 public static function exists($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::exists($key);
		 }

		/**
		 * Determine if the request contains a non-empty value for an input item.
		 *
		 * @param string|array  $key
		 * @return bool
		 * @static 
		 */
		 public static function has($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::has($key);
		 }

		/**
		 * Get all of the input and files for the request.
		 *
		 * @return array
		 * @static 
		 */
		 public static function all(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::all();
		 }

		/**
		 * Retrieve an input item from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function input($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::input($key, $default);
		 }

		/**
		 * Get a subset of the items from the input data.
		 *
		 * @param array  $keys
		 * @return array
		 * @static 
		 */
		 public static function only($keys){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::only($keys);
		 }

		/**
		 * Get all of the input except for a specified array of items.
		 *
		 * @param array  $keys
		 * @return array
		 * @static 
		 */
		 public static function except($keys){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::except($keys);
		 }

		/**
		 * Retrieve a query string item from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function query($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::query($key, $default);
		 }

		/**
		 * Determine if a cookie is set on the request.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function hasCookie($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::hasCookie($key);
		 }

		/**
		 * Retrieve a cookie from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function cookie($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::cookie($key, $default);
		 }

		/**
		 * Retrieve a file from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return \Symfony\Component\HttpFoundation\File\UploadedFile|array
		 * @static 
		 */
		 public static function file($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::file($key, $default);
		 }

		/**
		 * Determine if the uploaded data contains a file.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function hasFile($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::hasFile($key);
		 }

		/**
		 * Retrieve a header from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function header($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::header($key, $default);
		 }

		/**
		 * Retrieve a server variable from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function server($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::server($key, $default);
		 }

		/**
		 * Retrieve an old input item.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function old($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::old($key, $default);
		 }

		/**
		 * Flash the input for the current request to the session.
		 *
		 * @param string $filter
		 * @param array  $keys
		 * @return void
		 * @static 
		 */
		 public static function flash($filter = null, $keys = array()){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flash($filter, $keys);
		 }

		/**
		 * Flash only some of the input to the session.
		 *
		 * @param dynamic  string
		 * @return void
		 * @static 
		 */
		 public static function flashOnly($keys){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flashOnly($keys);
		 }

		/**
		 * Flash only some of the input to the session.
		 *
		 * @param dynamic  string
		 * @return void
		 * @static 
		 */
		 public static function flashExcept($keys){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flashExcept($keys);
		 }

		/**
		 * Flush all of the old input from the session.
		 *
		 * @return void
		 * @static 
		 */
		 public static function flush(){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flush();
		 }

		/**
		 * Merge new input into the current request's input array.
		 *
		 * @param array  $input
		 * @return void
		 * @static 
		 */
		 public static function merge($input){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::merge($input);
		 }

		/**
		 * Replace the input for the current request.
		 *
		 * @param array  $input
		 * @return void
		 * @static 
		 */
		 public static function replace($input){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::replace($input);
		 }

		/**
		 * Get the JSON payload for the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function json($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::json($key, $default);
		 }

		/**
		 * Determine if the request is sending JSON.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isJson(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::isJson();
		 }

		/**
		 * Determine if the current request is asking for JSON in return.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function wantsJson(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::wantsJson();
		 }

		/**
		 * Get the data format expected in the response.
		 *
		 * @param string  $default
		 * @return string
		 * @static 
		 */
		 public static function format($default = 'html'){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::format($default);
		 }

		/**
		 * Create an Illuminate request from a Symfony instance.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @return \Illuminate\Http\Request
		 * @static 
		 */
		 public static function createFromBase($request){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::createFromBase($request);
		 }

		/**
		 * Get the session associated with the request.
		 *
		 * @return \Illuminate\Session\Store
		 * @throws \RuntimeException
		 * @static 
		 */
		 public static function session(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::session();
		 }

		/**
		 * Constructor.
		 *
		 * @param array  $query      The GET parameters
		 * @param array  $request    The POST parameters
		 * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
		 * @param array  $cookies    The COOKIE parameters
		 * @param array  $files      The FILES parameters
		 * @param array  $server     The SERVER parameters
		 * @param string $content    The raw body data
		 * @api 
		 * @static 
		 */
		 public static function __construct($query = array(), $request = array(), $attributes = array(), $cookies = array(), $files = array(), $server = array(), $content = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
		 }

		/**
		 * Sets the parameters for this request.
		 * 
		 * This method also re-initializes all properties.
		 *
		 * @param array  $query      The GET parameters
		 * @param array  $request    The POST parameters
		 * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
		 * @param array  $cookies    The COOKIE parameters
		 * @param array  $files      The FILES parameters
		 * @param array  $server     The SERVER parameters
		 * @param string $content    The raw body data
		 * @api 
		 * @static 
		 */
		 public static function initialize($query = array(), $request = array(), $attributes = array(), $cookies = array(), $files = array(), $server = array(), $content = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::initialize($query, $request, $attributes, $cookies, $files, $server, $content);
		 }

		/**
		 * Creates a new request with values from PHP's super globals.
		 *
		 * @return Request A new request
		 * @api 
		 * @static 
		 */
		 public static function createFromGlobals(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::createFromGlobals();
		 }

		/**
		 * Creates a Request based on a given URI and configuration.
		 * 
		 * The information contained in the URI always take precedence
		 * over the other information (server and parameters).
		 *
		 * @param string $uri        The URI
		 * @param string $method     The HTTP method
		 * @param array  $parameters The query (GET) or request (POST) parameters
		 * @param array  $cookies    The request cookies ($_COOKIE)
		 * @param array  $files      The request files ($_FILES)
		 * @param array  $server     The server parameters ($_SERVER)
		 * @param string $content    The raw body data
		 * @return Request A Request instance
		 * @api 
		 * @static 
		 */
		 public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
		 }

		/**
		 * Sets a callable able to create a Request instance.
		 * 
		 * This is mainly useful when you need to override the Request class
		 * to keep BC with an existing system. It should not be used for any
		 * other purpose.
		 *
		 * @param callable|null $callable A PHP callable
		 * @static 
		 */
		 public static function setFactory($callable){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setFactory($callable);
		 }

		/**
		 * Clones a request and overrides some of its parameters.
		 *
		 * @param array $query      The GET parameters
		 * @param array $request    The POST parameters
		 * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
		 * @param array $cookies    The COOKIE parameters
		 * @param array $files      The FILES parameters
		 * @param array $server     The SERVER parameters
		 * @return Request The duplicated request
		 * @api 
		 * @static 
		 */
		 public static function duplicate($query = null, $request = null, $attributes = null, $cookies = null, $files = null, $server = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::duplicate($query, $request, $attributes, $cookies, $files, $server);
		 }

		/**
		 * Returns the request as a string.
		 *
		 * @return string The request
		 * @static 
		 */
		 public static function __toString(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::__toString();
		 }

		/**
		 * Overrides the PHP global variables according to this request instance.
		 * 
		 * It overrides $_GET, $_POST, $_REQUEST, $_SERVER, $_COOKIE.
		 * $_FILES is never override, see rfc1867
		 *
		 * @api 
		 * @static 
		 */
		 public static function overrideGlobals(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::overrideGlobals();
		 }

		/**
		 * Sets a list of trusted proxies.
		 * 
		 * You should only list the reverse proxies that you manage directly.
		 *
		 * @param array $proxies A list of trusted proxies
		 * @api 
		 * @static 
		 */
		 public static function setTrustedProxies($proxies){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setTrustedProxies($proxies);
		 }

		/**
		 * Gets the list of trusted proxies.
		 *
		 * @return array An array of trusted proxies.
		 * @static 
		 */
		 public static function getTrustedProxies(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getTrustedProxies();
		 }

		/**
		 * Sets a list of trusted host patterns.
		 * 
		 * You should only list the hosts you manage using regexs.
		 *
		 * @param array $hostPatterns A list of trusted host patterns
		 * @static 
		 */
		 public static function setTrustedHosts($hostPatterns){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setTrustedHosts($hostPatterns);
		 }

		/**
		 * Gets the list of trusted host patterns.
		 *
		 * @return array An array of trusted host patterns.
		 * @static 
		 */
		 public static function getTrustedHosts(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getTrustedHosts();
		 }

		/**
		 * Sets the name for trusted headers.
		 * 
		 * The following header keys are supported:
		 * 
		 *  * Request::HEADER_CLIENT_IP:    defaults to X-Forwarded-For   (see getClientIp())
		 *  * Request::HEADER_CLIENT_HOST:  defaults to X-Forwarded-Host  (see getClientHost())
		 *  * Request::HEADER_CLIENT_PORT:  defaults to X-Forwarded-Port  (see getClientPort())
		 *  * Request::HEADER_CLIENT_PROTO: defaults to X-Forwarded-Proto (see getScheme() and isSecure())
		 * 
		 * Setting an empty value allows to disable the trusted header for the given key.
		 *
		 * @param string $key   The header key
		 * @param string $value The header name
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function setTrustedHeaderName($key, $value){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setTrustedHeaderName($key, $value);
		 }

		/**
		 * Gets the trusted proxy header name.
		 *
		 * @param string $key The header key
		 * @return string The header name
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function getTrustedHeaderName($key){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getTrustedHeaderName($key);
		 }

		/**
		 * Normalizes a query string.
		 * 
		 * It builds a normalized query string, where keys/value pairs are alphabetized,
		 * have consistent escaping and unneeded delimiters are removed.
		 *
		 * @param string $qs Query string
		 * @return string A normalized query string for the Request
		 * @static 
		 */
		 public static function normalizeQueryString($qs){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::normalizeQueryString($qs);
		 }

		/**
		 * Enables support for the _method request parameter to determine the intended HTTP method.
		 * 
		 * Be warned that enabling this feature might lead to CSRF issues in your code.
		 * Check that you are using CSRF tokens when required.
		 * 
		 * The HTTP method can only be overridden when the real HTTP method is POST.
		 *
		 * @static 
		 */
		 public static function enableHttpMethodParameterOverride(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::enableHttpMethodParameterOverride();
		 }

		/**
		 * Checks whether support for the _method request parameter is enabled.
		 *
		 * @return bool    True when the _method request parameter is enabled, false otherwise
		 * @static 
		 */
		 public static function getHttpMethodParameterOverride(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getHttpMethodParameterOverride();
		 }

		/**
		 * Gets a "parameter" value.
		 * 
		 * This method is mainly useful for libraries that want to provide some flexibility.
		 * 
		 * Order of precedence: GET, PATH, POST
		 * 
		 * Avoid using this method in controllers:
		 * 
		 *  * slow
		 *  * prefer to get from a "named" source
		 * 
		 * It is better to explicitly get request parameters from the appropriate
		 * public property instead (query, attributes, request).
		 *
		 * @param string  $key     the key
		 * @param mixed   $default the default value
		 * @param bool    $deep    is parameter deep in multidimensional array
		 * @return mixed
		 * @static 
		 */
		 public static function get($key, $default = null, $deep = false){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::get($key, $default, $deep);
		 }

		/**
		 * Gets the Session.
		 *
		 * @return SessionInterface|null The session
		 * @api 
		 * @static 
		 */
		 public static function getSession(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getSession();
		 }

		/**
		 * Whether the request contains a Session which was started in one of the
		 * previous requests.
		 *
		 * @return bool
		 * @api 
		 * @static 
		 */
		 public static function hasPreviousSession(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::hasPreviousSession();
		 }

		/**
		 * Whether the request contains a Session object.
		 * 
		 * This method does not give any information about the state of the session object,
		 * like whether the session is started or not. It is just a way to check if this Request
		 * is associated with a Session instance.
		 *
		 * @return bool    true when the Request contains a Session object, false otherwise
		 * @api 
		 * @static 
		 */
		 public static function hasSession(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::hasSession();
		 }

		/**
		 * Sets the Session.
		 *
		 * @param SessionInterface $session The Session
		 * @api 
		 * @static 
		 */
		 public static function setSession($session){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setSession($session);
		 }

		/**
		 * Returns the client IP addresses.
		 * 
		 * In the returned array the most trusted IP address is first, and the
		 * least trusted one last. The "real" client IP address is the last one,
		 * but this is also the least trusted one. Trusted proxies are stripped.
		 * 
		 * Use this method carefully; you should use getClientIp() instead.
		 *
		 * @return array The client IP addresses
		 * @see getClientIp()
		 * @static 
		 */
		 public static function getClientIps(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getClientIps();
		 }

		/**
		 * Returns the client IP address.
		 * 
		 * This method can read the client IP address from the "X-Forwarded-For" header
		 * when trusted proxies were set via "setTrustedProxies()". The "X-Forwarded-For"
		 * header value is a comma+space separated list of IP addresses, the left-most
		 * being the original client, and each successive proxy that passed the request
		 * adding the IP address where it received the request from.
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-For",
		 * ("Client-Ip" for instance), configure it via "setTrustedHeaderName()" with
		 * the "client-ip" key.
		 *
		 * @return string The client IP address
		 * @see getClientIps()
		 * @see http://en.wikipedia.org/wiki/X-Forwarded-For
		 * @api 
		 * @static 
		 */
		 public static function getClientIp(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getClientIp();
		 }

		/**
		 * Returns current script name.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getScriptName(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getScriptName();
		 }

		/**
		 * Returns the path being requested relative to the executed script.
		 * 
		 * The path info always starts with a /.
		 * 
		 * Suppose this request is instantiated from /mysite on localhost:
		 * 
		 *  * http://localhost/mysite              returns an empty string
		 *  * http://localhost/mysite/about        returns '/about'
		 *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
		 *  * http://localhost/mysite/about?var=1  returns '/about'
		 *
		 * @return string The raw path (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getPathInfo(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPathInfo();
		 }

		/**
		 * Returns the root path from which this request is executed.
		 * 
		 * Suppose that an index.php file instantiates this request object:
		 * 
		 *  * http://localhost/index.php         returns an empty string
		 *  * http://localhost/index.php/page    returns an empty string
		 *  * http://localhost/web/index.php     returns '/web'
		 *  * http://localhost/we%20b/index.php  returns '/we%20b'
		 *
		 * @return string The raw path (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getBasePath(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getBasePath();
		 }

		/**
		 * Returns the root URL from which this request is executed.
		 * 
		 * The base URL never ends with a /.
		 * 
		 * This is similar to getBasePath(), except that it also includes the
		 * script filename (e.g. index.php) if one exists.
		 *
		 * @return string The raw URL (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getBaseUrl(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getBaseUrl();
		 }

		/**
		 * Gets the request's scheme.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getScheme(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getScheme();
		 }

		/**
		 * Returns the port on which the request is made.
		 * 
		 * This method can read the client port from the "X-Forwarded-Port" header
		 * when trusted proxies were set via "setTrustedProxies()".
		 * 
		 * The "X-Forwarded-Port" header must contain the client port.
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-Port",
		 * configure it via "setTrustedHeaderName()" with the "client-port" key.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getPort(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPort();
		 }

		/**
		 * Returns the user.
		 *
		 * @return string|null
		 * @static 
		 */
		 public static function getUser(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUser();
		 }

		/**
		 * Returns the password.
		 *
		 * @return string|null
		 * @static 
		 */
		 public static function getPassword(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPassword();
		 }

		/**
		 * Gets the user info.
		 *
		 * @return string A user name and, optionally, scheme-specific information about how to gain authorization to access the server
		 * @static 
		 */
		 public static function getUserInfo(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUserInfo();
		 }

		/**
		 * Returns the HTTP host being requested.
		 * 
		 * The port name will be appended to the host if it's non-standard.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getHttpHost(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getHttpHost();
		 }

		/**
		 * Returns the requested URI.
		 *
		 * @return string The raw URI (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getRequestUri(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getRequestUri();
		 }

		/**
		 * Gets the scheme and HTTP host.
		 * 
		 * If the URL was called with basic authentication, the user
		 * and the password are not added to the generated string.
		 *
		 * @return string The scheme and HTTP host
		 * @static 
		 */
		 public static function getSchemeAndHttpHost(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getSchemeAndHttpHost();
		 }

		/**
		 * Generates a normalized URI for the Request.
		 *
		 * @return string A normalized URI for the Request
		 * @see getQueryString()
		 * @api 
		 * @static 
		 */
		 public static function getUri(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUri();
		 }

		/**
		 * Generates a normalized URI for the given path.
		 *
		 * @param string $path A path to use instead of the current one
		 * @return string The normalized URI for the path
		 * @api 
		 * @static 
		 */
		 public static function getUriForPath($path){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUriForPath($path);
		 }

		/**
		 * Generates the normalized query string for the Request.
		 * 
		 * It builds a normalized query string, where keys/value pairs are alphabetized
		 * and have consistent escaping.
		 *
		 * @return string|null A normalized query string for the Request
		 * @api 
		 * @static 
		 */
		 public static function getQueryString(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getQueryString();
		 }

		/**
		 * Checks whether the request is secure or not.
		 * 
		 * This method can read the client port from the "X-Forwarded-Proto" header
		 * when trusted proxies were set via "setTrustedProxies()".
		 * 
		 * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-Proto"
		 * ("SSL_HTTPS" for instance), configure it via "setTrustedHeaderName()" with
		 * the "client-proto" key.
		 *
		 * @return bool
		 * @api 
		 * @static 
		 */
		 public static function isSecure(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isSecure();
		 }

		/**
		 * Returns the host name.
		 * 
		 * This method can read the client port from the "X-Forwarded-Host" header
		 * when trusted proxies were set via "setTrustedProxies()".
		 * 
		 * The "X-Forwarded-Host" header must contain the client host name.
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-Host",
		 * configure it via "setTrustedHeaderName()" with the "client-host" key.
		 *
		 * @return string
		 * @throws \UnexpectedValueException when the host name is invalid
		 * @api 
		 * @static 
		 */
		 public static function getHost(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getHost();
		 }

		/**
		 * Sets the request method.
		 *
		 * @param string $method
		 * @api 
		 * @static 
		 */
		 public static function setMethod($method){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setMethod($method);
		 }

		/**
		 * Gets the request "intended" method.
		 * 
		 * If the X-HTTP-Method-Override header is set, and if the method is a POST,
		 * then it is used to determine the "real" intended HTTP method.
		 * 
		 * The _method request parameter can also be used to determine the HTTP method,
		 * but only if enableHttpMethodParameterOverride() has been called.
		 * 
		 * The method is always an uppercased string.
		 *
		 * @return string The request method
		 * @api 
		 * @see getRealMethod
		 * @static 
		 */
		 public static function getMethod(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getMethod();
		 }

		/**
		 * Gets the "real" request method.
		 *
		 * @return string The request method
		 * @see getMethod
		 * @static 
		 */
		 public static function getRealMethod(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getRealMethod();
		 }

		/**
		 * Gets the mime type associated with the format.
		 *
		 * @param string $format The format
		 * @return string The associated mime type (null if not found)
		 * @api 
		 * @static 
		 */
		 public static function getMimeType($format){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getMimeType($format);
		 }

		/**
		 * Gets the format associated with the mime type.
		 *
		 * @param string $mimeType The associated mime type
		 * @return string|null The format (null if not found)
		 * @api 
		 * @static 
		 */
		 public static function getFormat($mimeType){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getFormat($mimeType);
		 }

		/**
		 * Associates a format with mime types.
		 *
		 * @param string       $format    The format
		 * @param string|array $mimeTypes The associated mime types (the preferred one must be the first as it will be used as the content type)
		 * @api 
		 * @static 
		 */
		 public static function setFormat($format, $mimeTypes){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setFormat($format, $mimeTypes);
		 }

		/**
		 * Gets the request format.
		 * 
		 * Here is the process to determine the format:
		 * 
		 *  * format defined by the user (with setRequestFormat())
		 *  * _format request parameter
		 *  * $default
		 *
		 * @param string $default The default format
		 * @return string The request format
		 * @api 
		 * @static 
		 */
		 public static function getRequestFormat($default = 'html'){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getRequestFormat($default);
		 }

		/**
		 * Sets the request format.
		 *
		 * @param string $format The request format.
		 * @api 
		 * @static 
		 */
		 public static function setRequestFormat($format){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setRequestFormat($format);
		 }

		/**
		 * Gets the format associated with the request.
		 *
		 * @return string|null The format (null if no content type is present)
		 * @api 
		 * @static 
		 */
		 public static function getContentType(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getContentType();
		 }

		/**
		 * Sets the default locale.
		 *
		 * @param string $locale
		 * @api 
		 * @static 
		 */
		 public static function setDefaultLocale($locale){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setDefaultLocale($locale);
		 }

		/**
		 * Sets the locale.
		 *
		 * @param string $locale
		 * @api 
		 * @static 
		 */
		 public static function setLocale($locale){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setLocale($locale);
		 }

		/**
		 * Get the locale.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getLocale(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getLocale();
		 }

		/**
		 * Checks if the request method is of specified type.
		 *
		 * @param string $method Uppercase request method (GET, POST etc).
		 * @return bool
		 * @static 
		 */
		 public static function isMethod($method){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isMethod($method);
		 }

		/**
		 * Checks whether the method is safe or not.
		 *
		 * @return bool
		 * @api 
		 * @static 
		 */
		 public static function isMethodSafe(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isMethodSafe();
		 }

		/**
		 * Returns the request body content.
		 *
		 * @param bool    $asResource If true, a resource will be returned
		 * @return string|resource The request body content or a resource to read the body stream.
		 * @throws \LogicException
		 * @static 
		 */
		 public static function getContent($asResource = false){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getContent($asResource);
		 }

		/**
		 * Gets the Etags.
		 *
		 * @return array The entity tags
		 * @static 
		 */
		 public static function getETags(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getETags();
		 }

		/**
		 * 
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isNoCache(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isNoCache();
		 }

		/**
		 * Returns the preferred language.
		 *
		 * @param array $locales An array of ordered available locales
		 * @return string|null The preferred locale
		 * @api 
		 * @static 
		 */
		 public static function getPreferredLanguage($locales = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPreferredLanguage($locales);
		 }

		/**
		 * Gets a list of languages acceptable by the client browser.
		 *
		 * @return array Languages ordered in the user browser preferences
		 * @api 
		 * @static 
		 */
		 public static function getLanguages(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getLanguages();
		 }

		/**
		 * Gets a list of charsets acceptable by the client browser.
		 *
		 * @return array List of charsets in preferable order
		 * @api 
		 * @static 
		 */
		 public static function getCharsets(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getCharsets();
		 }

		/**
		 * Gets a list of encodings acceptable by the client browser.
		 *
		 * @return array List of encodings in preferable order
		 * @static 
		 */
		 public static function getEncodings(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getEncodings();
		 }

		/**
		 * Gets a list of content types acceptable by the client browser
		 *
		 * @return array List of content types in preferable order
		 * @api 
		 * @static 
		 */
		 public static function getAcceptableContentTypes(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getAcceptableContentTypes();
		 }

		/**
		 * Returns true if the request is a XMLHttpRequest.
		 * 
		 * It works if your JavaScript library set an X-Requested-With HTTP header.
		 * It is known to work with common JavaScript frameworks:
		 *
		 * @link http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
		 * @return bool    true if the request is an XMLHttpRequest, false otherwise
		 * @api 
		 * @static 
		 */
		 public static function isXmlHttpRequest(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isXmlHttpRequest();
		 }

	}
	class Lang extends \Illuminate\Support\Facades\Lang{
		/**
		 * Create a new translator instance.
		 *
		 * @param \Illuminate\Translation\LoaderInterface  $loader
		 * @param string  $locale
		 * @return void
		 * @static 
		 */
		 public static function __construct($loader, $locale){
			//Method inherited from \Illuminate\Translation\Translator
			 \Illuminate\Translation\Translator::__construct($loader, $locale);
		 }

		/**
		 * Determine if a translation exists.
		 *
		 * @param string  $key
		 * @param string  $locale
		 * @return bool
		 * @static 
		 */
		 public static function has($key, $locale = null){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::has($key, $locale);
		 }

		/**
		 * Get the translation for the given key.
		 *
		 * @param string  $key
		 * @param array   $replace
		 * @param string  $locale
		 * @return string
		 * @static 
		 */
		 public static function get($key, $replace = array(), $locale = null){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::get($key, $replace, $locale);
		 }

		/**
		 * Get a translation according to an integer value.
		 *
		 * @param string  $key
		 * @param int     $number
		 * @param array   $replace
		 * @param string  $locale
		 * @return string
		 * @static 
		 */
		 public static function choice($key, $number, $replace = array(), $locale = null){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::choice($key, $number, $replace, $locale);
		 }

		/**
		 * Get the translation for a given key.
		 *
		 * @param string  $id
		 * @param array   $parameters
		 * @param string  $domain
		 * @param string  $locale
		 * @return string
		 * @static 
		 */
		 public static function trans($id, $parameters = array(), $domain = 'messages', $locale = null){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::trans($id, $parameters, $domain, $locale);
		 }

		/**
		 * Get a translation according to an integer value.
		 *
		 * @param string  $id
		 * @param int     $number
		 * @param array   $parameters
		 * @param string  $domain
		 * @param string  $locale
		 * @return string
		 * @static 
		 */
		 public static function transChoice($id, $number, $parameters = array(), $domain = 'messages', $locale = null){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::transChoice($id, $number, $parameters, $domain, $locale);
		 }

		/**
		 * Load the specified language group.
		 *
		 * @param string  $namespace
		 * @param string  $group
		 * @param string  $locale
		 * @return void
		 * @static 
		 */
		 public static function load($namespace, $group, $locale){
			//Method inherited from \Illuminate\Translation\Translator
			 \Illuminate\Translation\Translator::load($namespace, $group, $locale);
		 }

		/**
		 * Add a new namespace to the loader.
		 *
		 * @param string  $namespace
		 * @param string  $hint
		 * @return void
		 * @static 
		 */
		 public static function addNamespace($namespace, $hint){
			//Method inherited from \Illuminate\Translation\Translator
			 \Illuminate\Translation\Translator::addNamespace($namespace, $hint);
		 }

		/**
		 * Parse a key into namespace, group, and item.
		 *
		 * @param string  $key
		 * @return array
		 * @static 
		 */
		 public static function parseKey($key){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::parseKey($key);
		 }

		/**
		 * Get the message selector instance.
		 *
		 * @return \Symfony\Component\Translation\MessageSelector
		 * @static 
		 */
		 public static function getSelector(){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::getSelector();
		 }

		/**
		 * Set the message selector instance.
		 *
		 * @param \Symfony\Component\Translation\MessageSelector  $selector
		 * @return void
		 * @static 
		 */
		 public static function setSelector($selector){
			//Method inherited from \Illuminate\Translation\Translator
			 \Illuminate\Translation\Translator::setSelector($selector);
		 }

		/**
		 * Get the language line loader implementation.
		 *
		 * @return \Illuminate\Translation\LoaderInterface
		 * @static 
		 */
		 public static function getLoader(){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::getLoader();
		 }

		/**
		 * Get the default locale being used.
		 *
		 * @return string
		 * @static 
		 */
		 public static function locale(){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::locale();
		 }

		/**
		 * Get the default locale being used.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getLocale(){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::getLocale();
		 }

		/**
		 * Set the default locale.
		 *
		 * @param string  $locale
		 * @return void
		 * @static 
		 */
		 public static function setLocale($locale){
			//Method inherited from \Illuminate\Translation\Translator
			 \Illuminate\Translation\Translator::setLocale($locale);
		 }

		/**
		 * Get the fallback locale being used.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getFallback(){
			//Method inherited from \Illuminate\Translation\Translator
			return \Illuminate\Translation\Translator::getFallback();
		 }

		/**
		 * Set the fallback locale being used.
		 *
		 * @param string  $fallback
		 * @return void
		 * @static 
		 */
		 public static function setFallback($fallback){
			//Method inherited from \Illuminate\Translation\Translator
			 \Illuminate\Translation\Translator::setFallback($fallback);
		 }

		/**
		 * Set the parsed value of a key.
		 *
		 * @param string  $key
		 * @param array   $parsed
		 * @return void
		 * @static 
		 */
		 public static function setParsedKey($key, $parsed){
			//Method inherited from \Illuminate\Support\NamespacedItemResolver
			 \Illuminate\Translation\Translator::setParsedKey($key, $parsed);
		 }

	}
	class Log extends \Illuminate\Support\Facades\Log{
		/**
		 * Create a new log writer instance.
		 *
		 * @param \Monolog\Logger  $monolog
		 * @param \Illuminate\Events\Dispatcher  $dispatcher
		 * @return void
		 * @static 
		 */
		 public static function __construct($monolog, $dispatcher = null){
			//Method inherited from \Illuminate\Log\Writer
			 \Illuminate\Log\Writer::__construct($monolog, $dispatcher);
		 }

		/**
		 * Register a file log handler.
		 *
		 * @param string  $path
		 * @param string  $level
		 * @return void
		 * @static 
		 */
		 public static function useFiles($path, $level = 'debug'){
			//Method inherited from \Illuminate\Log\Writer
			 \Illuminate\Log\Writer::useFiles($path, $level);
		 }

		/**
		 * Register a daily file log handler.
		 *
		 * @param string  $path
		 * @param int     $days
		 * @param string  $level
		 * @return void
		 * @static 
		 */
		 public static function useDailyFiles($path, $days = 0, $level = 'debug'){
			//Method inherited from \Illuminate\Log\Writer
			 \Illuminate\Log\Writer::useDailyFiles($path, $days, $level);
		 }

		/**
		 * Register an error_log handler.
		 *
		 * @param integer $messageType
		 * @param string  $level
		 * @return void
		 * @static 
		 */
		 public static function useErrorLog($level = 'debug', $messageType = 0){
			//Method inherited from \Illuminate\Log\Writer
			 \Illuminate\Log\Writer::useErrorLog($level, $messageType);
		 }

		/**
		 * Register a new callback handler for when
		 * a log event is triggered.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @throws \RuntimeException
		 * @static 
		 */
		 public static function listen($callback){
			//Method inherited from \Illuminate\Log\Writer
			 \Illuminate\Log\Writer::listen($callback);
		 }

		/**
		 * Get the underlying Monolog instance.
		 *
		 * @return \Monolog\Logger
		 * @static 
		 */
		 public static function getMonolog(){
			//Method inherited from \Illuminate\Log\Writer
			return \Illuminate\Log\Writer::getMonolog();
		 }

		/**
		 * Get the event dispatcher instance.
		 *
		 * @return \Illuminate\Events\Dispatcher
		 * @static 
		 */
		 public static function getEventDispatcher(){
			//Method inherited from \Illuminate\Log\Writer
			return \Illuminate\Log\Writer::getEventDispatcher();
		 }

		/**
		 * Set the event dispatcher instance.
		 *
		 * @param \Illuminate\Events\Dispatcher
		 * @return void
		 * @static 
		 */
		 public static function setEventDispatcher($dispatcher){
			//Method inherited from \Illuminate\Log\Writer
			 \Illuminate\Log\Writer::setEventDispatcher($dispatcher);
		 }

		/**
		 * Dynamically pass log calls into the writer.
		 *
		 * @param dynamic (level, param, param)
		 * @return mixed
		 * @static 
		 */
		 public static function write(){
			//Method inherited from \Illuminate\Log\Writer
			return \Illuminate\Log\Writer::write();
		 }

		/**
		 * Dynamically handle error additions.
		 *
		 * @param string  $method
		 * @param mixed   $parameters
		 * @return mixed
		 * @throws \BadMethodCallException
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Log\Writer
			return \Illuminate\Log\Writer::__call($method, $parameters);
		 }

		/**
		 * Adds a log record at the DEBUG level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function debug($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addDebug($message, $context);
		 }

		/**
		 * Adds a log record at the INFO level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function info($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addInfo($message, $context);
		 }

		/**
		 * Adds a log record at the NOTICE level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function notice($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addNotice($message, $context);
		 }

		/**
		 * Adds a log record at the WARNING level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function warning($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addWarning($message, $context);
		 }

		/**
		 * Adds a log record at the ERROR level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function error($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addError($message, $context);
		 }

		/**
		 * Adds a log record at the CRITICAL level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function critical($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addCritical($message, $context);
		 }

		/**
		 * Adds a log record at the ALERT level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function alert($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addAlert($message, $context);
		 }

		/**
		 * Adds a log record at the EMERGENCY level.
		 *
		 * @param string  $message The log message
		 * @param array   $context The log context
		 * @return Boolean Whether the record has been processed
		 * @static 
		 */
		 public static function emergency($message, $context = array()){
			//Method inherited from \Monolog\Logger
			return \Monolog\Logger::addEmergency($message, $context);
		 }

	}
	class Mail extends \Illuminate\Support\Facades\Mail{
		/**
		 * Create a new Mailer instance.
		 *
		 * @param \Illuminate\View\Factory  $views
		 * @param \Swift_Mailer  $swift
		 * @param \Illuminate\Events\Dispatcher  $events
		 * @return void
		 * @static 
		 */
		 public static function __construct($views, $swift, $events = null){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::__construct($views, $swift, $events);
		 }

		/**
		 * Set the global from address and name.
		 *
		 * @param string  $address
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function alwaysFrom($address, $name = null){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::alwaysFrom($address, $name);
		 }

		/**
		 * Send a new message when only a plain part.
		 *
		 * @param string  $view
		 * @param array   $data
		 * @param mixed   $callback
		 * @return int
		 * @static 
		 */
		 public static function plain($view, $data, $callback){
			//Method inherited from \Illuminate\Mail\Mailer
			return \Illuminate\Mail\Mailer::plain($view, $data, $callback);
		 }

		/**
		 * Send a new message using a view.
		 *
		 * @param string|array  $view
		 * @param array  $data
		 * @param \Closure|string  $callback
		 * @return void
		 * @static 
		 */
		 public static function send($view, $data, $callback){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::send($view, $data, $callback);
		 }

		/**
		 * Queue a new e-mail message for sending.
		 *
		 * @param string|array  $view
		 * @param array   $data
		 * @param \Closure|string  $callback
		 * @param string  $queue
		 * @return void
		 * @static 
		 */
		 public static function queue($view, $data, $callback, $queue = null){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::queue($view, $data, $callback, $queue);
		 }

		/**
		 * Queue a new e-mail message for sending on the given queue.
		 *
		 * @param string  $queue
		 * @param string|array  $view
		 * @param array   $data
		 * @param \Closure|string  $callback
		 * @return void
		 * @static 
		 */
		 public static function queueOn($queue, $view, $data, $callback){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::queueOn($queue, $view, $data, $callback);
		 }

		/**
		 * Queue a new e-mail message for sending after (n) seconds.
		 *
		 * @param int  $delay
		 * @param string|array  $view
		 * @param array  $data
		 * @param \Closure|string  $callback
		 * @param string  $queue
		 * @return void
		 * @static 
		 */
		 public static function later($delay, $view, $data, $callback, $queue = null){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::later($delay, $view, $data, $callback, $queue);
		 }

		/**
		 * Queue a new e-mail message for sending after (n) seconds on the given queue.
		 *
		 * @param string  $queue
		 * @param int  $delay
		 * @param string|array  $view
		 * @param array  $data
		 * @param \Closure|string  $callback
		 * @return void
		 * @static 
		 */
		 public static function laterOn($queue, $delay, $view, $data, $callback){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::laterOn($queue, $delay, $view, $data, $callback);
		 }

		/**
		 * Handle a queued e-mail message job.
		 *
		 * @param \Illuminate\Queue\Jobs\Job  $job
		 * @param array  $data
		 * @return void
		 * @static 
		 */
		 public static function handleQueuedMessage($job, $data){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::handleQueuedMessage($job, $data);
		 }

		/**
		 * Tell the mailer to not really send messages.
		 *
		 * @param bool  $value
		 * @return void
		 * @static 
		 */
		 public static function pretend($value = true){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::pretend($value);
		 }

		/**
		 * Check if the mailer is pretending to send messages.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isPretending(){
			//Method inherited from \Illuminate\Mail\Mailer
			return \Illuminate\Mail\Mailer::isPretending();
		 }

		/**
		 * Get the view factory instance.
		 *
		 * @return \Illuminate\View\Factory
		 * @static 
		 */
		 public static function getViewFactory(){
			//Method inherited from \Illuminate\Mail\Mailer
			return \Illuminate\Mail\Mailer::getViewFactory();
		 }

		/**
		 * Get the Swift Mailer instance.
		 *
		 * @return \Swift_Mailer
		 * @static 
		 */
		 public static function getSwiftMailer(){
			//Method inherited from \Illuminate\Mail\Mailer
			return \Illuminate\Mail\Mailer::getSwiftMailer();
		 }

		/**
		 * Get the array of failed recipients.
		 *
		 * @return array
		 * @static 
		 */
		 public static function failures(){
			//Method inherited from \Illuminate\Mail\Mailer
			return \Illuminate\Mail\Mailer::failures();
		 }

		/**
		 * Set the Swift Mailer instance.
		 *
		 * @param \Swift_Mailer  $swift
		 * @return void
		 * @static 
		 */
		 public static function setSwiftMailer($swift){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::setSwiftMailer($swift);
		 }

		/**
		 * Set the log writer instance.
		 *
		 * @param \Illuminate\Log\Writer  $logger
		 * @return \Illuminate\Mail\Mailer
		 * @static 
		 */
		 public static function setLogger($logger){
			//Method inherited from \Illuminate\Mail\Mailer
			return \Illuminate\Mail\Mailer::setLogger($logger);
		 }

		/**
		 * Set the queue manager instance.
		 *
		 * @param \Illuminate\Queue\QueueManager  $queue
		 * @return \Illuminate\Mail\Mailer
		 * @static 
		 */
		 public static function setQueue($queue){
			//Method inherited from \Illuminate\Mail\Mailer
			return \Illuminate\Mail\Mailer::setQueue($queue);
		 }

		/**
		 * Set the IoC container instance.
		 *
		 * @param \Illuminate\Container\Container  $container
		 * @return void
		 * @static 
		 */
		 public static function setContainer($container){
			//Method inherited from \Illuminate\Mail\Mailer
			 \Illuminate\Mail\Mailer::setContainer($container);
		 }

	}
	class Paginator extends \Illuminate\Support\Facades\Paginator{
		/**
		 * Create a new pagination factory.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @param \Illuminate\View\Factory  $view
		 * @param \Symfony\Component\Translation\TranslatorInterface  $trans
		 * @param string  $pageName
		 * @return void
		 * @static 
		 */
		 public static function __construct($request, $view, $trans, $pageName = 'page'){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::__construct($request, $view, $trans, $pageName);
		 }

		/**
		 * Get a new paginator instance.
		 *
		 * @param array  $items
		 * @param int    $total
		 * @param int|null  $perPage
		 * @return \Illuminate\Pagination\Paginator
		 * @static 
		 */
		 public static function make($items, $total, $perPage = null){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::make($items, $total, $perPage);
		 }

		/**
		 * Get the pagination view.
		 *
		 * @param \Illuminate\Pagination\Paginator  $paginator
		 * @param string  $view
		 * @return \Illuminate\View\View
		 * @static 
		 */
		 public static function getPaginationView($paginator, $view = null){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getPaginationView($paginator, $view);
		 }

		/**
		 * Get the number of the current page.
		 *
		 * @return int
		 * @static 
		 */
		 public static function getCurrentPage(){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getCurrentPage();
		 }

		/**
		 * Set the number of the current page.
		 *
		 * @param int  $number
		 * @return void
		 * @static 
		 */
		 public static function setCurrentPage($number){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::setCurrentPage($number);
		 }

		/**
		 * Get the root URL for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getCurrentUrl(){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getCurrentUrl();
		 }

		/**
		 * Set the base URL in use by the paginator.
		 *
		 * @param string  $baseUrl
		 * @return void
		 * @static 
		 */
		 public static function setBaseUrl($baseUrl){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::setBaseUrl($baseUrl);
		 }

		/**
		 * Set the input page parameter name used by the paginator.
		 *
		 * @param string  $pageName
		 * @return void
		 * @static 
		 */
		 public static function setPageName($pageName){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::setPageName($pageName);
		 }

		/**
		 * Get the input page parameter name used by the paginator.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getPageName(){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getPageName();
		 }

		/**
		 * Get the name of the pagination view.
		 *
		 * @param string  $view
		 * @return string
		 * @static 
		 */
		 public static function getViewName($view = null){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getViewName($view);
		 }

		/**
		 * Set the name of the pagination view.
		 *
		 * @param string  $viewName
		 * @return void
		 * @static 
		 */
		 public static function setViewName($viewName){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::setViewName($viewName);
		 }

		/**
		 * Get the locale of the paginator.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getLocale(){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getLocale();
		 }

		/**
		 * Set the locale of the paginator.
		 *
		 * @param string  $locale
		 * @return void
		 * @static 
		 */
		 public static function setLocale($locale){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::setLocale($locale);
		 }

		/**
		 * Get the active request instance.
		 *
		 * @return \Symfony\Component\HttpFoundation\Request
		 * @static 
		 */
		 public static function getRequest(){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getRequest();
		 }

		/**
		 * Set the active request instance.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @return void
		 * @static 
		 */
		 public static function setRequest($request){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::setRequest($request);
		 }

		/**
		 * Get the current view factory.
		 *
		 * @return \Illuminate\View\Factory
		 * @static 
		 */
		 public static function getViewFactory(){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getViewFactory();
		 }

		/**
		 * Set the current view factory.
		 *
		 * @param \Illuminate\View\Factory  $view
		 * @return void
		 * @static 
		 */
		 public static function setViewFactory($view){
			//Method inherited from \Illuminate\Pagination\Factory
			 \Illuminate\Pagination\Factory::setViewFactory($view);
		 }

		/**
		 * Get the translator instance.
		 *
		 * @return \Symfony\Component\Translation\TranslatorInterface
		 * @static 
		 */
		 public static function getTranslator(){
			//Method inherited from \Illuminate\Pagination\Factory
			return \Illuminate\Pagination\Factory::getTranslator();
		 }

	}
	class Password extends \Illuminate\Support\Facades\Password{
		/**
		 * Create a new password broker instance.
		 *
		 * @param \Illuminate\Auth\Reminders\ReminderRepositoryInterface  $reminders
		 * @param \Illuminate\Auth\UserProviderInterface  $users
		 * @param \Illuminate\Mail\Mailer  $mailer
		 * @param string  $reminderView
		 * @return void
		 * @static 
		 */
		 public static function __construct($reminders, $users, $mailer, $reminderView){
			//Method inherited from \Illuminate\Auth\Reminders\PasswordBroker
			 \Illuminate\Auth\Reminders\PasswordBroker::__construct($reminders, $users, $mailer, $reminderView);
		 }

		/**
		 * Send a password reminder to a user.
		 *
		 * @param array     $credentials
		 * @param \Closure  $callback
		 * @return string
		 * @static 
		 */
		 public static function remind($credentials, $callback = null){
			//Method inherited from \Illuminate\Auth\Reminders\PasswordBroker
			return \Illuminate\Auth\Reminders\PasswordBroker::remind($credentials, $callback);
		 }

		/**
		 * Send the password reminder e-mail.
		 *
		 * @param \Illuminate\Auth\Reminders\RemindableInterface  $user
		 * @param string    $token
		 * @param \Closure  $callback
		 * @return int
		 * @static 
		 */
		 public static function sendReminder($user, $token, $callback = null){
			//Method inherited from \Illuminate\Auth\Reminders\PasswordBroker
			return \Illuminate\Auth\Reminders\PasswordBroker::sendReminder($user, $token, $callback);
		 }

		/**
		 * Reset the password for the given token.
		 *
		 * @param array     $credentials
		 * @param \Closure  $callback
		 * @return mixed
		 * @static 
		 */
		 public static function reset($credentials, $callback){
			//Method inherited from \Illuminate\Auth\Reminders\PasswordBroker
			return \Illuminate\Auth\Reminders\PasswordBroker::reset($credentials, $callback);
		 }

		/**
		 * Set a custom password validator.
		 *
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function validator($callback){
			//Method inherited from \Illuminate\Auth\Reminders\PasswordBroker
			 \Illuminate\Auth\Reminders\PasswordBroker::validator($callback);
		 }

		/**
		 * Get the user for the given credentials.
		 *
		 * @param array  $credentials
		 * @return \Illuminate\Auth\Reminders\RemindableInterface
		 * @throws \UnexpectedValueException
		 * @static 
		 */
		 public static function getUser($credentials){
			//Method inherited from \Illuminate\Auth\Reminders\PasswordBroker
			return \Illuminate\Auth\Reminders\PasswordBroker::getUser($credentials);
		 }

	}
	class Queue extends \Illuminate\Support\Facades\Queue{
		/**
		 * Create a new queue manager instance.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @return void
		 * @static 
		 */
		 public static function __construct($app){
			//Method inherited from \Illuminate\Queue\QueueManager
			 \Illuminate\Queue\QueueManager::__construct($app);
		 }

		/**
		 * Register an event listener for the daemon queue loop.
		 *
		 * @param mixed  $callback
		 * @return void
		 * @static 
		 */
		 public static function looping($callback){
			//Method inherited from \Illuminate\Queue\QueueManager
			 \Illuminate\Queue\QueueManager::looping($callback);
		 }

		/**
		 * Register an event listener for the failed job event.
		 *
		 * @param mixed  $callback
		 * @return void
		 * @static 
		 */
		 public static function failing($callback){
			//Method inherited from \Illuminate\Queue\QueueManager
			 \Illuminate\Queue\QueueManager::failing($callback);
		 }

		/**
		 * Register an event listener for the daemon queue stopping.
		 *
		 * @param mixed  $callback
		 * @return void
		 * @static 
		 */
		 public static function stopping($callback){
			//Method inherited from \Illuminate\Queue\QueueManager
			 \Illuminate\Queue\QueueManager::stopping($callback);
		 }

		/**
		 * Determine if the driver is connected.
		 *
		 * @param string  $name
		 * @return bool
		 * @static 
		 */
		 public static function connected($name = null){
			//Method inherited from \Illuminate\Queue\QueueManager
			return \Illuminate\Queue\QueueManager::connected($name);
		 }

		/**
		 * Resolve a queue connection instance.
		 *
		 * @param string  $name
		 * @return \Illuminate\Queue\QueueInterface
		 * @static 
		 */
		 public static function connection($name = null){
			//Method inherited from \Illuminate\Queue\QueueManager
			return \Illuminate\Queue\QueueManager::connection($name);
		 }

		/**
		 * Add a queue connection resolver.
		 *
		 * @param string    $driver
		 * @param \Closure  $resolver
		 * @return void
		 * @static 
		 */
		 public static function extend($driver, $resolver){
			//Method inherited from \Illuminate\Queue\QueueManager
			 \Illuminate\Queue\QueueManager::extend($driver, $resolver);
		 }

		/**
		 * Add a queue connection resolver.
		 *
		 * @param string    $driver
		 * @param \Closure  $resolver
		 * @return void
		 * @static 
		 */
		 public static function addConnector($driver, $resolver){
			//Method inherited from \Illuminate\Queue\QueueManager
			 \Illuminate\Queue\QueueManager::addConnector($driver, $resolver);
		 }

		/**
		 * Get the name of the default queue connection.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDefaultDriver(){
			//Method inherited from \Illuminate\Queue\QueueManager
			return \Illuminate\Queue\QueueManager::getDefaultDriver();
		 }

		/**
		 * Set the name of the default queue connection.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function setDefaultDriver($name){
			//Method inherited from \Illuminate\Queue\QueueManager
			 \Illuminate\Queue\QueueManager::setDefaultDriver($name);
		 }

		/**
		 * Get the full name for the given connection.
		 *
		 * @param string  $connection
		 * @return string
		 * @static 
		 */
		 public static function getName($connection = null){
			//Method inherited from \Illuminate\Queue\QueueManager
			return \Illuminate\Queue\QueueManager::getName($connection);
		 }

		/**
		 * Dynamically pass calls to the default connection.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Queue\QueueManager
			return \Illuminate\Queue\QueueManager::__call($method, $parameters);
		 }

		/**
		 * Push a new job onto the queue.
		 *
		 * @param string  $job
		 * @param mixed   $data
		 * @param string  $queue
		 * @return mixed
		 * @static 
		 */
		 public static function push($job, $data = '', $queue = null){
			//Method inherited from \Illuminate\Queue\SyncQueue
			return \Illuminate\Queue\SyncQueue::push($job, $data, $queue);
		 }

		/**
		 * Push a raw payload onto the queue.
		 *
		 * @param string  $payload
		 * @param string  $queue
		 * @param array   $options
		 * @return mixed
		 * @static 
		 */
		 public static function pushRaw($payload, $queue = null, $options = array()){
			//Method inherited from \Illuminate\Queue\SyncQueue
			return \Illuminate\Queue\SyncQueue::pushRaw($payload, $queue, $options);
		 }

		/**
		 * Push a new job onto the queue after a delay.
		 *
		 * @param \DateTime|int  $delay
		 * @param string  $job
		 * @param mixed   $data
		 * @param string  $queue
		 * @return mixed
		 * @static 
		 */
		 public static function later($delay, $job, $data = '', $queue = null){
			//Method inherited from \Illuminate\Queue\SyncQueue
			return \Illuminate\Queue\SyncQueue::later($delay, $job, $data, $queue);
		 }

		/**
		 * Pop the next job off of the queue.
		 *
		 * @param string  $queue
		 * @return \Illuminate\Queue\Jobs\Job|null
		 * @static 
		 */
		 public static function pop($queue = null){
			//Method inherited from \Illuminate\Queue\SyncQueue
			return \Illuminate\Queue\SyncQueue::pop($queue);
		 }

		/**
		 * Marshal a push queue request and fire the job.
		 *
		 * @throws \RuntimeException
		 * @static 
		 */
		 public static function marshal(){
			//Method inherited from \Illuminate\Queue\Queue
			 \Illuminate\Queue\SyncQueue::marshal();
		 }

		/**
		 * Push a new an array of jobs onto the queue.
		 *
		 * @param array   $jobs
		 * @param mixed   $data
		 * @param string  $queue
		 * @return mixed
		 * @static 
		 */
		 public static function bulk($jobs, $data = '', $queue = null){
			//Method inherited from \Illuminate\Queue\Queue
			return \Illuminate\Queue\SyncQueue::bulk($jobs, $data, $queue);
		 }

		/**
		 * Get the current UNIX timestamp.
		 *
		 * @return int
		 * @static 
		 */
		 public static function getTime(){
			//Method inherited from \Illuminate\Queue\Queue
			return \Illuminate\Queue\SyncQueue::getTime();
		 }

		/**
		 * Set the IoC container instance.
		 *
		 * @param \Illuminate\Container\Container  $container
		 * @return void
		 * @static 
		 */
		 public static function setContainer($container){
			//Method inherited from \Illuminate\Queue\Queue
			 \Illuminate\Queue\SyncQueue::setContainer($container);
		 }

		/**
		 * Set the encrypter instance.
		 *
		 * @param \Illuminate\Encryption\Encrypter  $crypt
		 * @return void
		 * @static 
		 */
		 public static function setEncrypter($crypt){
			//Method inherited from \Illuminate\Queue\Queue
			 \Illuminate\Queue\SyncQueue::setEncrypter($crypt);
		 }

	}
	class Redirect extends \Illuminate\Support\Facades\Redirect{
		/**
		 * Create a new Redirector instance.
		 *
		 * @param \Illuminate\Routing\UrlGenerator  $generator
		 * @return void
		 * @static 
		 */
		 public static function __construct($generator){
			//Method inherited from \Illuminate\Routing\Redirector
			 \Illuminate\Routing\Redirector::__construct($generator);
		 }

		/**
		 * Create a new redirect response to the "home" route.
		 *
		 * @param int  $status
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function home($status = 302){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::home($status);
		 }

		/**
		 * Create a new redirect response to the previous location.
		 *
		 * @param int    $status
		 * @param array  $headers
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function back($status = 302, $headers = array()){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::back($status, $headers);
		 }

		/**
		 * Create a new redirect response to the current URI.
		 *
		 * @param int    $status
		 * @param array  $headers
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function refresh($status = 302, $headers = array()){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::refresh($status, $headers);
		 }

		/**
		 * Create a new redirect response, while putting the current URL in the session.
		 *
		 * @param string  $path
		 * @param int     $status
		 * @param array   $headers
		 * @param bool    $secure
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function guest($path, $status = 302, $headers = array(), $secure = null){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::guest($path, $status, $headers, $secure);
		 }

		/**
		 * Create a new redirect response to the previously intended location.
		 *
		 * @param string  $default
		 * @param int     $status
		 * @param array   $headers
		 * @param bool    $secure
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function intended($default = '/', $status = 302, $headers = array(), $secure = null){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::intended($default, $status, $headers, $secure);
		 }

		/**
		 * Create a new redirect response to the given path.
		 *
		 * @param string  $path
		 * @param int     $status
		 * @param array   $headers
		 * @param bool    $secure
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function to($path, $status = 302, $headers = array(), $secure = null){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::to($path, $status, $headers, $secure);
		 }

		/**
		 * Create a new redirect response to an external URL (no validation).
		 *
		 * @param string  $path
		 * @param int     $status
		 * @param array   $headers
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function away($path, $status = 302, $headers = array()){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::away($path, $status, $headers);
		 }

		/**
		 * Create a new redirect response to the given HTTPS path.
		 *
		 * @param string  $path
		 * @param int     $status
		 * @param array   $headers
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function secure($path, $status = 302, $headers = array()){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::secure($path, $status, $headers);
		 }

		/**
		 * Create a new redirect response to a named route.
		 *
		 * @param string  $route
		 * @param array   $parameters
		 * @param int     $status
		 * @param array   $headers
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function route($route, $parameters = array(), $status = 302, $headers = array()){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::route($route, $parameters, $status, $headers);
		 }

		/**
		 * Create a new redirect response to a controller action.
		 *
		 * @param string  $action
		 * @param array   $parameters
		 * @param int     $status
		 * @param array   $headers
		 * @return \Illuminate\Http\RedirectResponse
		 * @static 
		 */
		 public static function action($action, $parameters = array(), $status = 302, $headers = array()){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::action($action, $parameters, $status, $headers);
		 }

		/**
		 * Get the URL generator instance.
		 *
		 * @return \Illuminate\Routing\UrlGenerator
		 * @static 
		 */
		 public static function getUrlGenerator(){
			//Method inherited from \Illuminate\Routing\Redirector
			return \Illuminate\Routing\Redirector::getUrlGenerator();
		 }

		/**
		 * Set the active session store.
		 *
		 * @param \Illuminate\Session\Store  $session
		 * @return void
		 * @static 
		 */
		 public static function setSession($session){
			//Method inherited from \Illuminate\Routing\Redirector
			 \Illuminate\Routing\Redirector::setSession($session);
		 }

	}
	class Redis extends \Illuminate\Support\Facades\Redis{
		/**
		 * Create a new Redis connection instance.
		 *
		 * @param array  $servers
		 * @return void
		 * @static 
		 */
		 public static function __construct($servers = array()){
			//Method inherited from \Illuminate\Redis\Database
			 \Illuminate\Redis\Database::__construct($servers);
		 }

		/**
		 * Get a specific Redis connection instance.
		 *
		 * @param string  $name
		 * @return \Predis\Connection\SingleConnectionInterface
		 * @static 
		 */
		 public static function connection($name = 'default'){
			//Method inherited from \Illuminate\Redis\Database
			return \Illuminate\Redis\Database::connection($name);
		 }

		/**
		 * Run a command against the Redis database.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function command($method, $parameters = array()){
			//Method inherited from \Illuminate\Redis\Database
			return \Illuminate\Redis\Database::command($method, $parameters);
		 }

		/**
		 * Dynamically make a Redis command.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Redis\Database
			return \Illuminate\Redis\Database::__call($method, $parameters);
		 }

	}
	class Request extends \Illuminate\Support\Facades\Request{
		/**
		 * Return the Request instance.
		 *
		 * @return \Illuminate\Http\Request
		 * @static 
		 */
		 public static function instance(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::instance();
		 }

		/**
		 * Get the request method.
		 *
		 * @return string
		 * @static 
		 */
		 public static function method(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::method();
		 }

		/**
		 * Get the root URL for the application.
		 *
		 * @return string
		 * @static 
		 */
		 public static function root(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::root();
		 }

		/**
		 * Get the URL (no query string) for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function url(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::url();
		 }

		/**
		 * Get the full URL for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function fullUrl(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::fullUrl();
		 }

		/**
		 * Get the current path info for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function path(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::path();
		 }

		/**
		 * Get the current encoded path info for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function decodedPath(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::decodedPath();
		 }

		/**
		 * Get a segment from the URI (1 based index).
		 *
		 * @param string  $index
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function segment($index, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::segment($index, $default);
		 }

		/**
		 * Get all of the segments for the request path.
		 *
		 * @return array
		 * @static 
		 */
		 public static function segments(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::segments();
		 }

		/**
		 * Determine if the current request URI matches a pattern.
		 *
		 * @param dynamic  string
		 * @return bool
		 * @static 
		 */
		 public static function is(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::is();
		 }

		/**
		 * Determine if the request is the result of an AJAX call.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function ajax(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::ajax();
		 }

		/**
		 * Determine if the request is over HTTPS.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function secure(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::secure();
		 }

		/**
		 * Determine if the request contains a given input item key.
		 *
		 * @param string|array  $key
		 * @return bool
		 * @static 
		 */
		 public static function exists($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::exists($key);
		 }

		/**
		 * Determine if the request contains a non-empty value for an input item.
		 *
		 * @param string|array  $key
		 * @return bool
		 * @static 
		 */
		 public static function has($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::has($key);
		 }

		/**
		 * Get all of the input and files for the request.
		 *
		 * @return array
		 * @static 
		 */
		 public static function all(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::all();
		 }

		/**
		 * Retrieve an input item from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function input($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::input($key, $default);
		 }

		/**
		 * Get a subset of the items from the input data.
		 *
		 * @param array  $keys
		 * @return array
		 * @static 
		 */
		 public static function only($keys){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::only($keys);
		 }

		/**
		 * Get all of the input except for a specified array of items.
		 *
		 * @param array  $keys
		 * @return array
		 * @static 
		 */
		 public static function except($keys){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::except($keys);
		 }

		/**
		 * Retrieve a query string item from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function query($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::query($key, $default);
		 }

		/**
		 * Determine if a cookie is set on the request.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function hasCookie($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::hasCookie($key);
		 }

		/**
		 * Retrieve a cookie from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function cookie($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::cookie($key, $default);
		 }

		/**
		 * Retrieve a file from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return \Symfony\Component\HttpFoundation\File\UploadedFile|array
		 * @static 
		 */
		 public static function file($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::file($key, $default);
		 }

		/**
		 * Determine if the uploaded data contains a file.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function hasFile($key){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::hasFile($key);
		 }

		/**
		 * Retrieve a header from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function header($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::header($key, $default);
		 }

		/**
		 * Retrieve a server variable from the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return string
		 * @static 
		 */
		 public static function server($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::server($key, $default);
		 }

		/**
		 * Retrieve an old input item.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function old($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::old($key, $default);
		 }

		/**
		 * Flash the input for the current request to the session.
		 *
		 * @param string $filter
		 * @param array  $keys
		 * @return void
		 * @static 
		 */
		 public static function flash($filter = null, $keys = array()){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flash($filter, $keys);
		 }

		/**
		 * Flash only some of the input to the session.
		 *
		 * @param dynamic  string
		 * @return void
		 * @static 
		 */
		 public static function flashOnly($keys){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flashOnly($keys);
		 }

		/**
		 * Flash only some of the input to the session.
		 *
		 * @param dynamic  string
		 * @return void
		 * @static 
		 */
		 public static function flashExcept($keys){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flashExcept($keys);
		 }

		/**
		 * Flush all of the old input from the session.
		 *
		 * @return void
		 * @static 
		 */
		 public static function flush(){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::flush();
		 }

		/**
		 * Merge new input into the current request's input array.
		 *
		 * @param array  $input
		 * @return void
		 * @static 
		 */
		 public static function merge($input){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::merge($input);
		 }

		/**
		 * Replace the input for the current request.
		 *
		 * @param array  $input
		 * @return void
		 * @static 
		 */
		 public static function replace($input){
			//Method inherited from \Illuminate\Http\Request
			 \Illuminate\Http\Request::replace($input);
		 }

		/**
		 * Get the JSON payload for the request.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function json($key = null, $default = null){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::json($key, $default);
		 }

		/**
		 * Determine if the request is sending JSON.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isJson(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::isJson();
		 }

		/**
		 * Determine if the current request is asking for JSON in return.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function wantsJson(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::wantsJson();
		 }

		/**
		 * Get the data format expected in the response.
		 *
		 * @param string  $default
		 * @return string
		 * @static 
		 */
		 public static function format($default = 'html'){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::format($default);
		 }

		/**
		 * Create an Illuminate request from a Symfony instance.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @return \Illuminate\Http\Request
		 * @static 
		 */
		 public static function createFromBase($request){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::createFromBase($request);
		 }

		/**
		 * Get the session associated with the request.
		 *
		 * @return \Illuminate\Session\Store
		 * @throws \RuntimeException
		 * @static 
		 */
		 public static function session(){
			//Method inherited from \Illuminate\Http\Request
			return \Illuminate\Http\Request::session();
		 }

		/**
		 * Constructor.
		 *
		 * @param array  $query      The GET parameters
		 * @param array  $request    The POST parameters
		 * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
		 * @param array  $cookies    The COOKIE parameters
		 * @param array  $files      The FILES parameters
		 * @param array  $server     The SERVER parameters
		 * @param string $content    The raw body data
		 * @api 
		 * @static 
		 */
		 public static function __construct($query = array(), $request = array(), $attributes = array(), $cookies = array(), $files = array(), $server = array(), $content = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
		 }

		/**
		 * Sets the parameters for this request.
		 * 
		 * This method also re-initializes all properties.
		 *
		 * @param array  $query      The GET parameters
		 * @param array  $request    The POST parameters
		 * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
		 * @param array  $cookies    The COOKIE parameters
		 * @param array  $files      The FILES parameters
		 * @param array  $server     The SERVER parameters
		 * @param string $content    The raw body data
		 * @api 
		 * @static 
		 */
		 public static function initialize($query = array(), $request = array(), $attributes = array(), $cookies = array(), $files = array(), $server = array(), $content = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::initialize($query, $request, $attributes, $cookies, $files, $server, $content);
		 }

		/**
		 * Creates a new request with values from PHP's super globals.
		 *
		 * @return Request A new request
		 * @api 
		 * @static 
		 */
		 public static function createFromGlobals(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::createFromGlobals();
		 }

		/**
		 * Creates a Request based on a given URI and configuration.
		 * 
		 * The information contained in the URI always take precedence
		 * over the other information (server and parameters).
		 *
		 * @param string $uri        The URI
		 * @param string $method     The HTTP method
		 * @param array  $parameters The query (GET) or request (POST) parameters
		 * @param array  $cookies    The request cookies ($_COOKIE)
		 * @param array  $files      The request files ($_FILES)
		 * @param array  $server     The server parameters ($_SERVER)
		 * @param string $content    The raw body data
		 * @return Request A Request instance
		 * @api 
		 * @static 
		 */
		 public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
		 }

		/**
		 * Sets a callable able to create a Request instance.
		 * 
		 * This is mainly useful when you need to override the Request class
		 * to keep BC with an existing system. It should not be used for any
		 * other purpose.
		 *
		 * @param callable|null $callable A PHP callable
		 * @static 
		 */
		 public static function setFactory($callable){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setFactory($callable);
		 }

		/**
		 * Clones a request and overrides some of its parameters.
		 *
		 * @param array $query      The GET parameters
		 * @param array $request    The POST parameters
		 * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
		 * @param array $cookies    The COOKIE parameters
		 * @param array $files      The FILES parameters
		 * @param array $server     The SERVER parameters
		 * @return Request The duplicated request
		 * @api 
		 * @static 
		 */
		 public static function duplicate($query = null, $request = null, $attributes = null, $cookies = null, $files = null, $server = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::duplicate($query, $request, $attributes, $cookies, $files, $server);
		 }

		/**
		 * Returns the request as a string.
		 *
		 * @return string The request
		 * @static 
		 */
		 public static function __toString(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::__toString();
		 }

		/**
		 * Overrides the PHP global variables according to this request instance.
		 * 
		 * It overrides $_GET, $_POST, $_REQUEST, $_SERVER, $_COOKIE.
		 * $_FILES is never override, see rfc1867
		 *
		 * @api 
		 * @static 
		 */
		 public static function overrideGlobals(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::overrideGlobals();
		 }

		/**
		 * Sets a list of trusted proxies.
		 * 
		 * You should only list the reverse proxies that you manage directly.
		 *
		 * @param array $proxies A list of trusted proxies
		 * @api 
		 * @static 
		 */
		 public static function setTrustedProxies($proxies){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setTrustedProxies($proxies);
		 }

		/**
		 * Gets the list of trusted proxies.
		 *
		 * @return array An array of trusted proxies.
		 * @static 
		 */
		 public static function getTrustedProxies(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getTrustedProxies();
		 }

		/**
		 * Sets a list of trusted host patterns.
		 * 
		 * You should only list the hosts you manage using regexs.
		 *
		 * @param array $hostPatterns A list of trusted host patterns
		 * @static 
		 */
		 public static function setTrustedHosts($hostPatterns){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setTrustedHosts($hostPatterns);
		 }

		/**
		 * Gets the list of trusted host patterns.
		 *
		 * @return array An array of trusted host patterns.
		 * @static 
		 */
		 public static function getTrustedHosts(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getTrustedHosts();
		 }

		/**
		 * Sets the name for trusted headers.
		 * 
		 * The following header keys are supported:
		 * 
		 *  * Request::HEADER_CLIENT_IP:    defaults to X-Forwarded-For   (see getClientIp())
		 *  * Request::HEADER_CLIENT_HOST:  defaults to X-Forwarded-Host  (see getClientHost())
		 *  * Request::HEADER_CLIENT_PORT:  defaults to X-Forwarded-Port  (see getClientPort())
		 *  * Request::HEADER_CLIENT_PROTO: defaults to X-Forwarded-Proto (see getScheme() and isSecure())
		 * 
		 * Setting an empty value allows to disable the trusted header for the given key.
		 *
		 * @param string $key   The header key
		 * @param string $value The header name
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function setTrustedHeaderName($key, $value){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setTrustedHeaderName($key, $value);
		 }

		/**
		 * Gets the trusted proxy header name.
		 *
		 * @param string $key The header key
		 * @return string The header name
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function getTrustedHeaderName($key){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getTrustedHeaderName($key);
		 }

		/**
		 * Normalizes a query string.
		 * 
		 * It builds a normalized query string, where keys/value pairs are alphabetized,
		 * have consistent escaping and unneeded delimiters are removed.
		 *
		 * @param string $qs Query string
		 * @return string A normalized query string for the Request
		 * @static 
		 */
		 public static function normalizeQueryString($qs){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::normalizeQueryString($qs);
		 }

		/**
		 * Enables support for the _method request parameter to determine the intended HTTP method.
		 * 
		 * Be warned that enabling this feature might lead to CSRF issues in your code.
		 * Check that you are using CSRF tokens when required.
		 * 
		 * The HTTP method can only be overridden when the real HTTP method is POST.
		 *
		 * @static 
		 */
		 public static function enableHttpMethodParameterOverride(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::enableHttpMethodParameterOverride();
		 }

		/**
		 * Checks whether support for the _method request parameter is enabled.
		 *
		 * @return bool    True when the _method request parameter is enabled, false otherwise
		 * @static 
		 */
		 public static function getHttpMethodParameterOverride(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getHttpMethodParameterOverride();
		 }

		/**
		 * Gets a "parameter" value.
		 * 
		 * This method is mainly useful for libraries that want to provide some flexibility.
		 * 
		 * Order of precedence: GET, PATH, POST
		 * 
		 * Avoid using this method in controllers:
		 * 
		 *  * slow
		 *  * prefer to get from a "named" source
		 * 
		 * It is better to explicitly get request parameters from the appropriate
		 * public property instead (query, attributes, request).
		 *
		 * @param string  $key     the key
		 * @param mixed   $default the default value
		 * @param bool    $deep    is parameter deep in multidimensional array
		 * @return mixed
		 * @static 
		 */
		 public static function get($key, $default = null, $deep = false){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::get($key, $default, $deep);
		 }

		/**
		 * Gets the Session.
		 *
		 * @return SessionInterface|null The session
		 * @api 
		 * @static 
		 */
		 public static function getSession(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getSession();
		 }

		/**
		 * Whether the request contains a Session which was started in one of the
		 * previous requests.
		 *
		 * @return bool
		 * @api 
		 * @static 
		 */
		 public static function hasPreviousSession(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::hasPreviousSession();
		 }

		/**
		 * Whether the request contains a Session object.
		 * 
		 * This method does not give any information about the state of the session object,
		 * like whether the session is started or not. It is just a way to check if this Request
		 * is associated with a Session instance.
		 *
		 * @return bool    true when the Request contains a Session object, false otherwise
		 * @api 
		 * @static 
		 */
		 public static function hasSession(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::hasSession();
		 }

		/**
		 * Sets the Session.
		 *
		 * @param SessionInterface $session The Session
		 * @api 
		 * @static 
		 */
		 public static function setSession($session){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setSession($session);
		 }

		/**
		 * Returns the client IP addresses.
		 * 
		 * In the returned array the most trusted IP address is first, and the
		 * least trusted one last. The "real" client IP address is the last one,
		 * but this is also the least trusted one. Trusted proxies are stripped.
		 * 
		 * Use this method carefully; you should use getClientIp() instead.
		 *
		 * @return array The client IP addresses
		 * @see getClientIp()
		 * @static 
		 */
		 public static function getClientIps(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getClientIps();
		 }

		/**
		 * Returns the client IP address.
		 * 
		 * This method can read the client IP address from the "X-Forwarded-For" header
		 * when trusted proxies were set via "setTrustedProxies()". The "X-Forwarded-For"
		 * header value is a comma+space separated list of IP addresses, the left-most
		 * being the original client, and each successive proxy that passed the request
		 * adding the IP address where it received the request from.
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-For",
		 * ("Client-Ip" for instance), configure it via "setTrustedHeaderName()" with
		 * the "client-ip" key.
		 *
		 * @return string The client IP address
		 * @see getClientIps()
		 * @see http://en.wikipedia.org/wiki/X-Forwarded-For
		 * @api 
		 * @static 
		 */
		 public static function getClientIp(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getClientIp();
		 }

		/**
		 * Returns current script name.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getScriptName(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getScriptName();
		 }

		/**
		 * Returns the path being requested relative to the executed script.
		 * 
		 * The path info always starts with a /.
		 * 
		 * Suppose this request is instantiated from /mysite on localhost:
		 * 
		 *  * http://localhost/mysite              returns an empty string
		 *  * http://localhost/mysite/about        returns '/about'
		 *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
		 *  * http://localhost/mysite/about?var=1  returns '/about'
		 *
		 * @return string The raw path (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getPathInfo(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPathInfo();
		 }

		/**
		 * Returns the root path from which this request is executed.
		 * 
		 * Suppose that an index.php file instantiates this request object:
		 * 
		 *  * http://localhost/index.php         returns an empty string
		 *  * http://localhost/index.php/page    returns an empty string
		 *  * http://localhost/web/index.php     returns '/web'
		 *  * http://localhost/we%20b/index.php  returns '/we%20b'
		 *
		 * @return string The raw path (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getBasePath(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getBasePath();
		 }

		/**
		 * Returns the root URL from which this request is executed.
		 * 
		 * The base URL never ends with a /.
		 * 
		 * This is similar to getBasePath(), except that it also includes the
		 * script filename (e.g. index.php) if one exists.
		 *
		 * @return string The raw URL (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getBaseUrl(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getBaseUrl();
		 }

		/**
		 * Gets the request's scheme.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getScheme(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getScheme();
		 }

		/**
		 * Returns the port on which the request is made.
		 * 
		 * This method can read the client port from the "X-Forwarded-Port" header
		 * when trusted proxies were set via "setTrustedProxies()".
		 * 
		 * The "X-Forwarded-Port" header must contain the client port.
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-Port",
		 * configure it via "setTrustedHeaderName()" with the "client-port" key.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getPort(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPort();
		 }

		/**
		 * Returns the user.
		 *
		 * @return string|null
		 * @static 
		 */
		 public static function getUser(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUser();
		 }

		/**
		 * Returns the password.
		 *
		 * @return string|null
		 * @static 
		 */
		 public static function getPassword(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPassword();
		 }

		/**
		 * Gets the user info.
		 *
		 * @return string A user name and, optionally, scheme-specific information about how to gain authorization to access the server
		 * @static 
		 */
		 public static function getUserInfo(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUserInfo();
		 }

		/**
		 * Returns the HTTP host being requested.
		 * 
		 * The port name will be appended to the host if it's non-standard.
		 *
		 * @return string
		 * @api 
		 * @static 
		 */
		 public static function getHttpHost(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getHttpHost();
		 }

		/**
		 * Returns the requested URI.
		 *
		 * @return string The raw URI (i.e. not urldecoded)
		 * @api 
		 * @static 
		 */
		 public static function getRequestUri(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getRequestUri();
		 }

		/**
		 * Gets the scheme and HTTP host.
		 * 
		 * If the URL was called with basic authentication, the user
		 * and the password are not added to the generated string.
		 *
		 * @return string The scheme and HTTP host
		 * @static 
		 */
		 public static function getSchemeAndHttpHost(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getSchemeAndHttpHost();
		 }

		/**
		 * Generates a normalized URI for the Request.
		 *
		 * @return string A normalized URI for the Request
		 * @see getQueryString()
		 * @api 
		 * @static 
		 */
		 public static function getUri(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUri();
		 }

		/**
		 * Generates a normalized URI for the given path.
		 *
		 * @param string $path A path to use instead of the current one
		 * @return string The normalized URI for the path
		 * @api 
		 * @static 
		 */
		 public static function getUriForPath($path){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getUriForPath($path);
		 }

		/**
		 * Generates the normalized query string for the Request.
		 * 
		 * It builds a normalized query string, where keys/value pairs are alphabetized
		 * and have consistent escaping.
		 *
		 * @return string|null A normalized query string for the Request
		 * @api 
		 * @static 
		 */
		 public static function getQueryString(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getQueryString();
		 }

		/**
		 * Checks whether the request is secure or not.
		 * 
		 * This method can read the client port from the "X-Forwarded-Proto" header
		 * when trusted proxies were set via "setTrustedProxies()".
		 * 
		 * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-Proto"
		 * ("SSL_HTTPS" for instance), configure it via "setTrustedHeaderName()" with
		 * the "client-proto" key.
		 *
		 * @return bool
		 * @api 
		 * @static 
		 */
		 public static function isSecure(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isSecure();
		 }

		/**
		 * Returns the host name.
		 * 
		 * This method can read the client port from the "X-Forwarded-Host" header
		 * when trusted proxies were set via "setTrustedProxies()".
		 * 
		 * The "X-Forwarded-Host" header must contain the client host name.
		 * 
		 * If your reverse proxy uses a different header name than "X-Forwarded-Host",
		 * configure it via "setTrustedHeaderName()" with the "client-host" key.
		 *
		 * @return string
		 * @throws \UnexpectedValueException when the host name is invalid
		 * @api 
		 * @static 
		 */
		 public static function getHost(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getHost();
		 }

		/**
		 * Sets the request method.
		 *
		 * @param string $method
		 * @api 
		 * @static 
		 */
		 public static function setMethod($method){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setMethod($method);
		 }

		/**
		 * Gets the request "intended" method.
		 * 
		 * If the X-HTTP-Method-Override header is set, and if the method is a POST,
		 * then it is used to determine the "real" intended HTTP method.
		 * 
		 * The _method request parameter can also be used to determine the HTTP method,
		 * but only if enableHttpMethodParameterOverride() has been called.
		 * 
		 * The method is always an uppercased string.
		 *
		 * @return string The request method
		 * @api 
		 * @see getRealMethod
		 * @static 
		 */
		 public static function getMethod(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getMethod();
		 }

		/**
		 * Gets the "real" request method.
		 *
		 * @return string The request method
		 * @see getMethod
		 * @static 
		 */
		 public static function getRealMethod(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getRealMethod();
		 }

		/**
		 * Gets the mime type associated with the format.
		 *
		 * @param string $format The format
		 * @return string The associated mime type (null if not found)
		 * @api 
		 * @static 
		 */
		 public static function getMimeType($format){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getMimeType($format);
		 }

		/**
		 * Gets the format associated with the mime type.
		 *
		 * @param string $mimeType The associated mime type
		 * @return string|null The format (null if not found)
		 * @api 
		 * @static 
		 */
		 public static function getFormat($mimeType){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getFormat($mimeType);
		 }

		/**
		 * Associates a format with mime types.
		 *
		 * @param string       $format    The format
		 * @param string|array $mimeTypes The associated mime types (the preferred one must be the first as it will be used as the content type)
		 * @api 
		 * @static 
		 */
		 public static function setFormat($format, $mimeTypes){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setFormat($format, $mimeTypes);
		 }

		/**
		 * Gets the request format.
		 * 
		 * Here is the process to determine the format:
		 * 
		 *  * format defined by the user (with setRequestFormat())
		 *  * _format request parameter
		 *  * $default
		 *
		 * @param string $default The default format
		 * @return string The request format
		 * @api 
		 * @static 
		 */
		 public static function getRequestFormat($default = 'html'){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getRequestFormat($default);
		 }

		/**
		 * Sets the request format.
		 *
		 * @param string $format The request format.
		 * @api 
		 * @static 
		 */
		 public static function setRequestFormat($format){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setRequestFormat($format);
		 }

		/**
		 * Gets the format associated with the request.
		 *
		 * @return string|null The format (null if no content type is present)
		 * @api 
		 * @static 
		 */
		 public static function getContentType(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getContentType();
		 }

		/**
		 * Sets the default locale.
		 *
		 * @param string $locale
		 * @api 
		 * @static 
		 */
		 public static function setDefaultLocale($locale){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setDefaultLocale($locale);
		 }

		/**
		 * Sets the locale.
		 *
		 * @param string $locale
		 * @api 
		 * @static 
		 */
		 public static function setLocale($locale){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			 \Illuminate\Http\Request::setLocale($locale);
		 }

		/**
		 * Get the locale.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getLocale(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getLocale();
		 }

		/**
		 * Checks if the request method is of specified type.
		 *
		 * @param string $method Uppercase request method (GET, POST etc).
		 * @return bool
		 * @static 
		 */
		 public static function isMethod($method){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isMethod($method);
		 }

		/**
		 * Checks whether the method is safe or not.
		 *
		 * @return bool
		 * @api 
		 * @static 
		 */
		 public static function isMethodSafe(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isMethodSafe();
		 }

		/**
		 * Returns the request body content.
		 *
		 * @param bool    $asResource If true, a resource will be returned
		 * @return string|resource The request body content or a resource to read the body stream.
		 * @throws \LogicException
		 * @static 
		 */
		 public static function getContent($asResource = false){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getContent($asResource);
		 }

		/**
		 * Gets the Etags.
		 *
		 * @return array The entity tags
		 * @static 
		 */
		 public static function getETags(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getETags();
		 }

		/**
		 * 
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isNoCache(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isNoCache();
		 }

		/**
		 * Returns the preferred language.
		 *
		 * @param array $locales An array of ordered available locales
		 * @return string|null The preferred locale
		 * @api 
		 * @static 
		 */
		 public static function getPreferredLanguage($locales = null){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getPreferredLanguage($locales);
		 }

		/**
		 * Gets a list of languages acceptable by the client browser.
		 *
		 * @return array Languages ordered in the user browser preferences
		 * @api 
		 * @static 
		 */
		 public static function getLanguages(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getLanguages();
		 }

		/**
		 * Gets a list of charsets acceptable by the client browser.
		 *
		 * @return array List of charsets in preferable order
		 * @api 
		 * @static 
		 */
		 public static function getCharsets(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getCharsets();
		 }

		/**
		 * Gets a list of encodings acceptable by the client browser.
		 *
		 * @return array List of encodings in preferable order
		 * @static 
		 */
		 public static function getEncodings(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getEncodings();
		 }

		/**
		 * Gets a list of content types acceptable by the client browser
		 *
		 * @return array List of content types in preferable order
		 * @api 
		 * @static 
		 */
		 public static function getAcceptableContentTypes(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::getAcceptableContentTypes();
		 }

		/**
		 * Returns true if the request is a XMLHttpRequest.
		 * 
		 * It works if your JavaScript library set an X-Requested-With HTTP header.
		 * It is known to work with common JavaScript frameworks:
		 *
		 * @link http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
		 * @return bool    true if the request is an XMLHttpRequest, false otherwise
		 * @api 
		 * @static 
		 */
		 public static function isXmlHttpRequest(){
			//Method inherited from \Symfony\Component\HttpFoundation\Request
			return \Illuminate\Http\Request::isXmlHttpRequest();
		 }

	}
	class Response extends \Illuminate\Support\Facades\Response{
	}
	class Route extends \Illuminate\Support\Facades\Route{
		/**
		 * Create a new Router instance.
		 *
		 * @param \Illuminate\Events\Dispatcher  $events
		 * @param \Illuminate\Container\Container  $container
		 * @return void
		 * @static 
		 */
		 public static function __construct($events, $container = null){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::__construct($events, $container);
		 }

		/**
		 * Register a new GET route with the router.
		 *
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function get($uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::get($uri, $action);
		 }

		/**
		 * Register a new POST route with the router.
		 *
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function post($uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::post($uri, $action);
		 }

		/**
		 * Register a new PUT route with the router.
		 *
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function put($uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::put($uri, $action);
		 }

		/**
		 * Register a new PATCH route with the router.
		 *
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function patch($uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::patch($uri, $action);
		 }

		/**
		 * Register a new DELETE route with the router.
		 *
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function delete($uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::delete($uri, $action);
		 }

		/**
		 * Register a new OPTIONS route with the router.
		 *
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function options($uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::options($uri, $action);
		 }

		/**
		 * Register a new route responding to all verbs.
		 *
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function any($uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::any($uri, $action);
		 }

		/**
		 * Register a new route with the given verbs.
		 *
		 * @param array|string  $methods
		 * @param string  $uri
		 * @param \Closure|array|string  $action
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function match($methods, $uri, $action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::match($methods, $uri, $action);
		 }

		/**
		 * Register an array of controllers with wildcard routing.
		 *
		 * @param array  $controllers
		 * @return void
		 * @static 
		 */
		 public static function controllers($controllers){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::controllers($controllers);
		 }

		/**
		 * Route a controller to a URI with wildcard routing.
		 *
		 * @param string  $uri
		 * @param string  $controller
		 * @param array   $names
		 * @return void
		 * @static 
		 */
		 public static function controller($uri, $controller, $names = array()){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::controller($uri, $controller, $names);
		 }

		/**
		 * Route a resource to a controller.
		 *
		 * @param string  $name
		 * @param string  $controller
		 * @param array   $options
		 * @return void
		 * @static 
		 */
		 public static function resource($name, $controller, $options = array()){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::resource($name, $controller, $options);
		 }

		/**
		 * Get the base resource URI for a given resource.
		 *
		 * @param string  $resource
		 * @return string
		 * @static 
		 */
		 public static function getResourceUri($resource){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getResourceUri($resource);
		 }

		/**
		 * Format a resource wildcard for usage.
		 *
		 * @param string  $value
		 * @return string
		 * @static 
		 */
		 public static function getResourceWildcard($value){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getResourceWildcard($value);
		 }

		/**
		 * Create a route group with shared attributes.
		 *
		 * @param array     $attributes
		 * @param \Closure  $callback
		 * @return void
		 * @static 
		 */
		 public static function group($attributes, $callback){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::group($attributes, $callback);
		 }

		/**
		 * Merge the given array with the last group stack.
		 *
		 * @param array  $new
		 * @return array
		 * @static 
		 */
		 public static function mergeWithLastGroup($new){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::mergeWithLastGroup($new);
		 }

		/**
		 * Merge the given group attributes.
		 *
		 * @param array  $new
		 * @param array  $old
		 * @return array
		 * @static 
		 */
		 public static function mergeGroup($new, $old){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::mergeGroup($new, $old);
		 }

		/**
		 * Dispatch the request to the application.
		 *
		 * @param \Illuminate\Http\Request  $request
		 * @return \Illuminate\Http\Response
		 * @static 
		 */
		 public static function dispatch($request){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::dispatch($request);
		 }

		/**
		 * Dispatch the request to a route and return the response.
		 *
		 * @param \Illuminate\Http\Request  $request
		 * @return mixed
		 * @static 
		 */
		 public static function dispatchToRoute($request){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::dispatchToRoute($request);
		 }

		/**
		 * Register a route matched event listener.
		 *
		 * @param string|callable  $callback
		 * @return void
		 * @static 
		 */
		 public static function matched($callback){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::matched($callback);
		 }

		/**
		 * Register a new "before" filter with the router.
		 *
		 * @param string|callable  $callback
		 * @return void
		 * @static 
		 */
		 public static function before($callback){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::before($callback);
		 }

		/**
		 * Register a new "after" filter with the router.
		 *
		 * @param string|callable  $callback
		 * @return void
		 * @static 
		 */
		 public static function after($callback){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::after($callback);
		 }

		/**
		 * Register a new filter with the router.
		 *
		 * @param string  $name
		 * @param string|callable  $callback
		 * @return void
		 * @static 
		 */
		 public static function filter($name, $callback){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::filter($name, $callback);
		 }

		/**
		 * Register a pattern-based filter with the router.
		 *
		 * @param string  $pattern
		 * @param string  $name
		 * @param array|null  $methods
		 * @static 
		 */
		 public static function when($pattern, $name, $methods = null){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::when($pattern, $name, $methods);
		 }

		/**
		 * Register a regular expression based filter with the router.
		 *
		 * @param string     $pattern
		 * @param string     $name
		 * @param array|null $methods
		 * @return void
		 * @static 
		 */
		 public static function whenRegex($pattern, $name, $methods = null){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::whenRegex($pattern, $name, $methods);
		 }

		/**
		 * Register a model binder for a wildcard.
		 *
		 * @param string  $key
		 * @param string  $class
		 * @param \Closure  $callback
		 * @return void
		 * @throws NotFoundHttpException
		 * @static 
		 */
		 public static function model($key, $class, $callback = null){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::model($key, $class, $callback);
		 }

		/**
		 * Add a new route parameter binder.
		 *
		 * @param string  $key
		 * @param string|callable  $binder
		 * @return void
		 * @static 
		 */
		 public static function bind($key, $binder){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::bind($key, $binder);
		 }

		/**
		 * Create a class based binding using the IoC container.
		 *
		 * @param string    $binding
		 * @return \Closure
		 * @static 
		 */
		 public static function createClassBinding($binding){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::createClassBinding($binding);
		 }

		/**
		 * Set a global where pattern on all routes
		 *
		 * @param string  $key
		 * @param string  $pattern
		 * @return void
		 * @static 
		 */
		 public static function pattern($key, $pattern){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::pattern($key, $pattern);
		 }

		/**
		 * Set a group of global where patterns on all routes
		 *
		 * @param array  $patterns
		 * @return void
		 * @static 
		 */
		 public static function patterns($patterns){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::patterns($patterns);
		 }

		/**
		 * Call the given route's before filters.
		 *
		 * @param \Illuminate\Routing\Route  $route
		 * @param \Illuminate\Http\Request  $request
		 * @return mixed
		 * @static 
		 */
		 public static function callRouteBefore($route, $request){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::callRouteBefore($route, $request);
		 }

		/**
		 * Find the patterned filters matching a request.
		 *
		 * @param \Illuminate\Http\Request  $request
		 * @return array
		 * @static 
		 */
		 public static function findPatternFilters($request){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::findPatternFilters($request);
		 }

		/**
		 * Call the given route's before filters.
		 *
		 * @param \Illuminate\Routing\Route  $route
		 * @param \Illuminate\Http\Request  $request
		 * @param \Illuminate\Http\Response  $response
		 * @return mixed
		 * @static 
		 */
		 public static function callRouteAfter($route, $request, $response){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::callRouteAfter($route, $request, $response);
		 }

		/**
		 * Call the given route filter.
		 *
		 * @param string  $filter
		 * @param array  $parameters
		 * @param \Illuminate\Routing\Route  $route
		 * @param \Illuminate\Http\Request  $request
		 * @param \Illuminate\Http\Response|null $response
		 * @return mixed
		 * @static 
		 */
		 public static function callRouteFilter($filter, $parameters, $route, $request, $response = null){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::callRouteFilter($filter, $parameters, $route, $request, $response);
		 }

		/**
		 * Run a callback with filters disable on the router.
		 *
		 * @param callable  $callback
		 * @return void
		 * @static 
		 */
		 public static function withoutFilters($callback){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::withoutFilters($callback);
		 }

		/**
		 * Enable route filtering on the router.
		 *
		 * @return void
		 * @static 
		 */
		 public static function enableFilters(){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::enableFilters();
		 }

		/**
		 * Disable route filtering on the router.
		 *
		 * @return void
		 * @static 
		 */
		 public static function disableFilters(){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::disableFilters();
		 }

		/**
		 * Get a route parameter for the current route.
		 *
		 * @param string  $key
		 * @param string  $default
		 * @return mixed
		 * @static 
		 */
		 public static function input($key, $default = null){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::input($key, $default);
		 }

		/**
		 * Get the currently dispatched route instance.
		 *
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function getCurrentRoute(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getCurrentRoute();
		 }

		/**
		 * Get the currently dispatched route instance.
		 *
		 * @return \Illuminate\Routing\Route
		 * @static 
		 */
		 public static function current(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::current();
		 }

		/**
		 * Check if a route with the given name exists.
		 *
		 * @param string  $name
		 * @return bool
		 * @static 
		 */
		 public static function has($name){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::has($name);
		 }

		/**
		 * Get the current route name.
		 *
		 * @return string|null
		 * @static 
		 */
		 public static function currentRouteName(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::currentRouteName();
		 }

		/**
		 * Alias for the "currentRouteNamed" method.
		 *
		 * @param dynamic  string
		 * @return bool
		 * @static 
		 */
		 public static function is(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::is();
		 }

		/**
		 * Determine if the current route matches a given name.
		 *
		 * @param string  $name
		 * @return bool
		 * @static 
		 */
		 public static function currentRouteNamed($name){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::currentRouteNamed($name);
		 }

		/**
		 * Get the current route action.
		 *
		 * @return string|null
		 * @static 
		 */
		 public static function currentRouteAction(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::currentRouteAction();
		 }

		/**
		 * Alias for the "currentRouteUses" method.
		 *
		 * @param dynamic  string
		 * @return bool
		 * @static 
		 */
		 public static function uses(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::uses();
		 }

		/**
		 * Determine if the current route action matches a given action.
		 *
		 * @param string  $action
		 * @return bool
		 * @static 
		 */
		 public static function currentRouteUses($action){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::currentRouteUses($action);
		 }

		/**
		 * Get the request currently being dispatched.
		 *
		 * @return \Illuminate\Http\Request
		 * @static 
		 */
		 public static function getCurrentRequest(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getCurrentRequest();
		 }

		/**
		 * Get the underlying route collection.
		 *
		 * @return \Illuminate\Routing\RouteCollection
		 * @static 
		 */
		 public static function getRoutes(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getRoutes();
		 }

		/**
		 * Get the controller dispatcher instance.
		 *
		 * @return \Illuminate\Routing\ControllerDispatcher
		 * @static 
		 */
		 public static function getControllerDispatcher(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getControllerDispatcher();
		 }

		/**
		 * Set the controller dispatcher instance.
		 *
		 * @param \Illuminate\Routing\ControllerDispatcher  $dispatcher
		 * @return void
		 * @static 
		 */
		 public static function setControllerDispatcher($dispatcher){
			//Method inherited from \Illuminate\Routing\Router
			 \Illuminate\Routing\Router::setControllerDispatcher($dispatcher);
		 }

		/**
		 * Get a controller inspector instance.
		 *
		 * @return \Illuminate\Routing\ControllerInspector
		 * @static 
		 */
		 public static function getInspector(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getInspector();
		 }

		/**
		 * Get the global "where" patterns.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getPatterns(){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::getPatterns();
		 }

		/**
		 * Get the response for a given request.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @param int   $type
		 * @param bool  $catch
		 * @return \Symfony\Component\HttpFoundation\Response
		 * @static 
		 */
		 public static function handle($request, $type = 1, $catch = true){
			//Method inherited from \Illuminate\Routing\Router
			return \Illuminate\Routing\Router::handle($request, $type, $catch);
		 }

	}
	class Schema extends \Illuminate\Support\Facades\Schema{
		/**
		 * Determine if the given table exists.
		 *
		 * @param string  $table
		 * @return bool
		 * @static 
		 */
		 public static function hasTable($table){
			//Method inherited from \Illuminate\Database\Schema\MySqlBuilder
			return \Illuminate\Database\Schema\MySqlBuilder::hasTable($table);
		 }

		/**
		 * Get the column listing for a given table.
		 *
		 * @param string  $table
		 * @return array
		 * @static 
		 */
		 public static function getColumnListing($table){
			//Method inherited from \Illuminate\Database\Schema\MySqlBuilder
			return \Illuminate\Database\Schema\MySqlBuilder::getColumnListing($table);
		 }

		/**
		 * Create a new database Schema manager.
		 *
		 * @param \Illuminate\Database\Connection  $connection
		 * @return void
		 * @static 
		 */
		 public static function __construct($connection){
			//Method inherited from \Illuminate\Database\Schema\Builder
			 \Illuminate\Database\Schema\MySqlBuilder::__construct($connection);
		 }

		/**
		 * Determine if the given table has a given column.
		 *
		 * @param string  $table
		 * @param string  $column
		 * @return bool
		 * @static 
		 */
		 public static function hasColumn($table, $column){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::hasColumn($table, $column);
		 }

		/**
		 * Modify a table on the schema.
		 *
		 * @param string    $table
		 * @param \Closure  $callback
		 * @return \Illuminate\Database\Schema\Blueprint
		 * @static 
		 */
		 public static function table($table, $callback){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::table($table, $callback);
		 }

		/**
		 * Create a new table on the schema.
		 *
		 * @param string    $table
		 * @param \Closure  $callback
		 * @return \Illuminate\Database\Schema\Blueprint
		 * @static 
		 */
		 public static function create($table, $callback){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::create($table, $callback);
		 }

		/**
		 * Drop a table from the schema.
		 *
		 * @param string  $table
		 * @return \Illuminate\Database\Schema\Blueprint
		 * @static 
		 */
		 public static function drop($table){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::drop($table);
		 }

		/**
		 * Drop a table from the schema if it exists.
		 *
		 * @param string  $table
		 * @return \Illuminate\Database\Schema\Blueprint
		 * @static 
		 */
		 public static function dropIfExists($table){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::dropIfExists($table);
		 }

		/**
		 * Rename a table on the schema.
		 *
		 * @param string  $from
		 * @param string  $to
		 * @return \Illuminate\Database\Schema\Blueprint
		 * @static 
		 */
		 public static function rename($from, $to){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::rename($from, $to);
		 }

		/**
		 * Get the database connection instance.
		 *
		 * @return \Illuminate\Database\Connection
		 * @static 
		 */
		 public static function getConnection(){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::getConnection();
		 }

		/**
		 * Set the database connection instance.
		 *
		 * @param \Illuminate\Database\Connection
		 * @return \Illuminate\Database\Schema\Builder
		 * @static 
		 */
		 public static function setConnection($connection){
			//Method inherited from \Illuminate\Database\Schema\Builder
			return \Illuminate\Database\Schema\MySqlBuilder::setConnection($connection);
		 }

		/**
		 * Set the Schema Blueprint resolver callback.
		 *
		 * @param \Closure  $resolver
		 * @return void
		 * @static 
		 */
		 public static function blueprintResolver($resolver){
			//Method inherited from \Illuminate\Database\Schema\Builder
			 \Illuminate\Database\Schema\MySqlBuilder::blueprintResolver($resolver);
		 }

	}
	class Seeder extends \Illuminate\Database\Seeder{
	}
	class Session extends \Illuminate\Support\Facades\Session{
		/**
		 * Get the session configuration.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getSessionConfig(){
			//Method inherited from \Illuminate\Session\SessionManager
			return \Illuminate\Session\SessionManager::getSessionConfig();
		 }

		/**
		 * Get the default session driver name.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDefaultDriver(){
			//Method inherited from \Illuminate\Session\SessionManager
			return \Illuminate\Session\SessionManager::getDefaultDriver();
		 }

		/**
		 * Set the default session driver name.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function setDefaultDriver($name){
			//Method inherited from \Illuminate\Session\SessionManager
			 \Illuminate\Session\SessionManager::setDefaultDriver($name);
		 }

		/**
		 * Create a new manager instance.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @return void
		 * @static 
		 */
		 public static function __construct($app){
			//Method inherited from \Illuminate\Support\Manager
			 \Illuminate\Session\SessionManager::__construct($app);
		 }

		/**
		 * Get a driver instance.
		 *
		 * @param string  $driver
		 * @return mixed
		 * @static 
		 */
		 public static function driver($driver = null){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Session\SessionManager::driver($driver);
		 }

		/**
		 * Register a custom driver creator Closure.
		 *
		 * @param string    $driver
		 * @param \Closure  $callback
		 * @return \Illuminate\Support\Manager|static
		 * @static 
		 */
		 public static function extend($driver, $callback){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Session\SessionManager::extend($driver, $callback);
		 }

		/**
		 * Get all of the created "drivers".
		 *
		 * @return array
		 * @static 
		 */
		 public static function getDrivers(){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Session\SessionManager::getDrivers();
		 }

		/**
		 * Dynamically call the default driver instance.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Support\Manager
			return \Illuminate\Session\SessionManager::__call($method, $parameters);
		 }

		/**
		 * Starts the session storage.
		 *
		 * @return bool    True if session started.
		 * @throws \RuntimeException If session fails to start.
		 * @api 
		 * @static 
		 */
		 public static function start(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::start();
		 }

		/**
		 * Returns the session ID.
		 *
		 * @return string The session ID.
		 * @api 
		 * @static 
		 */
		 public static function getId(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getId();
		 }

		/**
		 * Sets the session ID
		 *
		 * @param string $id
		 * @api 
		 * @static 
		 */
		 public static function setId($id){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::setId($id);
		 }

		/**
		 * Returns the session name.
		 *
		 * @return mixed The session name.
		 * @api 
		 * @static 
		 */
		 public static function getName(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getName();
		 }

		/**
		 * Sets the session name.
		 *
		 * @param string $name
		 * @api 
		 * @static 
		 */
		 public static function setName($name){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::setName($name);
		 }

		/**
		 * Invalidates the current session.
		 * 
		 * Clears all session attributes and flashes and regenerates the
		 * session and deletes the old session from persistence.
		 *
		 * @param int     $lifetime Sets the cookie lifetime for the session cookie. A null value
		 *                          will leave the system settings unchanged, 0 sets the cookie
		 *                          to expire with browser session. Time is in seconds, and is
		 *                          not a Unix timestamp.
		 * @return bool    True if session invalidated, false if error.
		 * @api 
		 * @static 
		 */
		 public static function invalidate($lifetime = null){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::invalidate($lifetime);
		 }

		/**
		 * Migrates the current session to a new session id while maintaining all
		 * session attributes.
		 *
		 * @param bool    $destroy  Whether to delete the old session or leave it to garbage collection.
		 * @param int     $lifetime Sets the cookie lifetime for the session cookie. A null value
		 *                          will leave the system settings unchanged, 0 sets the cookie
		 *                          to expire with browser session. Time is in seconds, and is
		 *                          not a Unix timestamp.
		 * @return bool    True if session migrated, false if error.
		 * @api 
		 * @static 
		 */
		 public static function migrate($destroy = false, $lifetime = null){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::migrate($destroy, $lifetime);
		 }

		/**
		 * Generate a new session identifier.
		 *
		 * @param bool  $destroy
		 * @return bool
		 * @static 
		 */
		 public static function regenerate($destroy = false){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::regenerate($destroy);
		 }

		/**
		 * Force the session to be saved and closed.
		 * 
		 * This method is generally not required for real sessions as
		 * the session will be automatically saved at the end of
		 * code execution.
		 *
		 * @static 
		 */
		 public static function save(){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::save();
		 }

		/**
		 * Age the flash data for the session.
		 *
		 * @return void
		 * @static 
		 */
		 public static function ageFlashData(){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::ageFlashData();
		 }

		/**
		 * Checks if an attribute is defined.
		 *
		 * @param string $name The attribute name
		 * @return bool    true if the attribute is defined, false otherwise
		 * @api 
		 * @static 
		 */
		 public static function has($name){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::has($name);
		 }

		/**
		 * Returns an attribute.
		 *
		 * @param string $name    The attribute name
		 * @param mixed  $default The default value if not found.
		 * @return mixed
		 * @api 
		 * @static 
		 */
		 public static function get($name, $default = null){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::get($name, $default);
		 }

		/**
		 * Get the value of a given key and then forget it.
		 *
		 * @param string  $key
		 * @param string  $default
		 * @return mixed
		 * @static 
		 */
		 public static function pull($key, $default = null){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::pull($key, $default);
		 }

		/**
		 * Determine if the session contains old input.
		 *
		 * @param string  $key
		 * @return bool
		 * @static 
		 */
		 public static function hasOldInput($key = null){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::hasOldInput($key);
		 }

		/**
		 * Get the requested item from the flashed input array.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function getOldInput($key = null, $default = null){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getOldInput($key, $default);
		 }

		/**
		 * Sets an attribute.
		 *
		 * @param string $name
		 * @param mixed  $value
		 * @api 
		 * @static 
		 */
		 public static function set($name, $value){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::set($name, $value);
		 }

		/**
		 * Put a key / value pair or array of key / value pairs in the session.
		 *
		 * @param string|array  $key
		 * @param mixed|null  	 $value
		 * @return void
		 * @static 
		 */
		 public static function put($key, $value = null){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::put($key, $value);
		 }

		/**
		 * Push a value onto a session array.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function push($key, $value){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::push($key, $value);
		 }

		/**
		 * Flash a key / value pair to the session.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function flash($key, $value){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::flash($key, $value);
		 }

		/**
		 * Flash an input array to the session.
		 *
		 * @param array  $value
		 * @return void
		 * @static 
		 */
		 public static function flashInput($value){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::flashInput($value);
		 }

		/**
		 * Reflash all of the session flash data.
		 *
		 * @return void
		 * @static 
		 */
		 public static function reflash(){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::reflash();
		 }

		/**
		 * Reflash a subset of the current flash data.
		 *
		 * @param array|dynamic  $keys
		 * @return void
		 * @static 
		 */
		 public static function keep($keys = null){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::keep($keys);
		 }

		/**
		 * Returns attributes.
		 *
		 * @return array Attributes
		 * @api 
		 * @static 
		 */
		 public static function all(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::all();
		 }

		/**
		 * Sets attributes.
		 *
		 * @param array $attributes Attributes
		 * @static 
		 */
		 public static function replace($attributes){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::replace($attributes);
		 }

		/**
		 * Removes an attribute.
		 *
		 * @param string $name
		 * @return mixed The removed value or null when it does not exist
		 * @api 
		 * @static 
		 */
		 public static function remove($name){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::remove($name);
		 }

		/**
		 * Remove an item from the session.
		 *
		 * @param string  $key
		 * @return void
		 * @static 
		 */
		 public static function forget($key){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::forget($key);
		 }

		/**
		 * Clears all attributes.
		 *
		 * @api 
		 * @static 
		 */
		 public static function clear(){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::clear();
		 }

		/**
		 * Remove all of the items from the session.
		 *
		 * @return void
		 * @static 
		 */
		 public static function flush(){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::flush();
		 }

		/**
		 * Checks if the session was started.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function isStarted(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::isStarted();
		 }

		/**
		 * Registers a SessionBagInterface with the session.
		 *
		 * @param SessionBagInterface $bag
		 * @static 
		 */
		 public static function registerBag($bag){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::registerBag($bag);
		 }

		/**
		 * Gets a bag instance by name.
		 *
		 * @param string $name
		 * @return SessionBagInterface
		 * @static 
		 */
		 public static function getBag($name){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getBag($name);
		 }

		/**
		 * Gets session meta.
		 *
		 * @return MetadataBag
		 * @static 
		 */
		 public static function getMetadataBag(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getMetadataBag();
		 }

		/**
		 * Get the raw bag data array for a given bag.
		 *
		 * @param string  $name
		 * @return array
		 * @static 
		 */
		 public static function getBagData($name){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getBagData($name);
		 }

		/**
		 * Get the CSRF token value.
		 *
		 * @return string
		 * @static 
		 */
		 public static function token(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::token();
		 }

		/**
		 * Get the CSRF token value.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getToken(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getToken();
		 }

		/**
		 * Regenerate the CSRF token value.
		 *
		 * @return void
		 * @static 
		 */
		 public static function regenerateToken(){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::regenerateToken();
		 }

		/**
		 * Set the existence of the session on the handler if applicable.
		 *
		 * @param bool  $value
		 * @return void
		 * @static 
		 */
		 public static function setExists($value){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::setExists($value);
		 }

		/**
		 * Get the underlying session handler implementation.
		 *
		 * @return \SessionHandlerInterface
		 * @static 
		 */
		 public static function getHandler(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::getHandler();
		 }

		/**
		 * Determine if the session handler needs a request.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function handlerNeedsRequest(){
			//Method inherited from \Illuminate\Session\Store
			return \Illuminate\Session\Store::handlerNeedsRequest();
		 }

		/**
		 * Set the request on the handler instance.
		 *
		 * @param \Symfony\Component\HttpFoundation\Request  $request
		 * @return void
		 * @static 
		 */
		 public static function setRequestOnHandler($request){
			//Method inherited from \Illuminate\Session\Store
			 \Illuminate\Session\Store::setRequestOnHandler($request);
		 }

	}
	class SSH extends \Illuminate\Support\Facades\SSH{
		/**
		 * Create a new remote manager instance.
		 *
		 * @param \Illuminate\Foundation\Application  $app
		 * @return void
		 * @static 
		 */
		 public static function __construct($app){
			//Method inherited from \Illuminate\Remote\RemoteManager
			 \Illuminate\Remote\RemoteManager::__construct($app);
		 }

		/**
		 * Get a remote connection instance.
		 *
		 * @param string|array|dynamic  $name
		 * @return \Illuminate\Remote\ConnectionInterface
		 * @static 
		 */
		 public static function into($name){
			//Method inherited from \Illuminate\Remote\RemoteManager
			return \Illuminate\Remote\RemoteManager::into($name);
		 }

		/**
		 * Get a remote connection instance.
		 *
		 * @param string|array  $name
		 * @return \Illuminate\Remote\ConnectionInterface
		 * @static 
		 */
		 public static function connection($name = null){
			//Method inherited from \Illuminate\Remote\RemoteManager
			return \Illuminate\Remote\RemoteManager::connection($name);
		 }

		/**
		 * Get a connection group instance by name.
		 *
		 * @param string  $name
		 * @return \Illuminate\Remote\ConnectionInterface
		 * @static 
		 */
		 public static function group($name){
			//Method inherited from \Illuminate\Remote\RemoteManager
			return \Illuminate\Remote\RemoteManager::group($name);
		 }

		/**
		 * Resolve a multiple connection instance.
		 *
		 * @param array  $names
		 * @return \Illuminate\Remote\MultiConnection
		 * @static 
		 */
		 public static function multiple($names){
			//Method inherited from \Illuminate\Remote\RemoteManager
			return \Illuminate\Remote\RemoteManager::multiple($names);
		 }

		/**
		 * Resolve a remote connection instance.
		 *
		 * @param string  $name
		 * @return \Illuminate\Remote\Connection
		 * @static 
		 */
		 public static function resolve($name){
			//Method inherited from \Illuminate\Remote\RemoteManager
			return \Illuminate\Remote\RemoteManager::resolve($name);
		 }

		/**
		 * Get the default connection name.
		 *
		 * @return string
		 * @static 
		 */
		 public static function getDefaultConnection(){
			//Method inherited from \Illuminate\Remote\RemoteManager
			return \Illuminate\Remote\RemoteManager::getDefaultConnection();
		 }

		/**
		 * Set the default connection name.
		 *
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function setDefaultConnection($name){
			//Method inherited from \Illuminate\Remote\RemoteManager
			 \Illuminate\Remote\RemoteManager::setDefaultConnection($name);
		 }

		/**
		 * Dynamically pass methods to the default connection.
		 *
		 * @param string  $method
		 * @param array   $parameters
		 * @return mixed
		 * @static 
		 */
		 public static function __call($method, $parameters){
			//Method inherited from \Illuminate\Remote\RemoteManager
			return \Illuminate\Remote\RemoteManager::__call($method, $parameters);
		 }

	}
	class Str extends \Illuminate\Support\Str{
	}
	class URL extends \Illuminate\Support\Facades\URL{
		/**
		 * Create a new URL Generator instance.
		 *
		 * @param \Illuminate\Routing\RouteCollection  $routes
		 * @param \Symfony\Component\HttpFoundation\Request   $request
		 * @return void
		 * @static 
		 */
		 public static function __construct($routes, $request){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			 \Illuminate\Routing\UrlGenerator::__construct($routes, $request);
		 }

		/**
		 * Get the full URL for the current request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function full(){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::full();
		 }

		/**
		 * Get the current URL for the request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function current(){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::current();
		 }

		/**
		 * Get the URL for the previous request.
		 *
		 * @return string
		 * @static 
		 */
		 public static function previous(){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::previous();
		 }

		/**
		 * Generate a absolute URL to the given path.
		 *
		 * @param string  $path
		 * @param mixed  $extra
		 * @param bool  $secure
		 * @return string
		 * @static 
		 */
		 public static function to($path, $extra = array(), $secure = null){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::to($path, $extra, $secure);
		 }

		/**
		 * Generate a secure, absolute URL to the given path.
		 *
		 * @param string  $path
		 * @param array   $parameters
		 * @return string
		 * @static 
		 */
		 public static function secure($path, $parameters = array()){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::secure($path, $parameters);
		 }

		/**
		 * Generate a URL to an application asset.
		 *
		 * @param string  $path
		 * @param bool    $secure
		 * @return string
		 * @static 
		 */
		 public static function asset($path, $secure = null){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::asset($path, $secure);
		 }

		/**
		 * Generate a URL to a secure asset.
		 *
		 * @param string  $path
		 * @return string
		 * @static 
		 */
		 public static function secureAsset($path){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::secureAsset($path);
		 }

		/**
		 * Force the schema for URLs.
		 *
		 * @param string  $schema
		 * @return void
		 * @static 
		 */
		 public static function forceSchema($schema){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			 \Illuminate\Routing\UrlGenerator::forceSchema($schema);
		 }

		/**
		 * Get the URL to a named route.
		 *
		 * @param string  $name
		 * @param mixed   $parameters
		 * @param bool  $absolute
		 * @param \Illuminate\Routing\Route  $route
		 * @return string
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function route($name, $parameters = array(), $absolute = true, $route = null){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::route($name, $parameters, $absolute, $route);
		 }

		/**
		 * Get the URL to a controller action.
		 *
		 * @param string  $action
		 * @param mixed   $parameters
		 * @param bool    $absolute
		 * @return string
		 * @static 
		 */
		 public static function action($action, $parameters = array(), $absolute = true){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::action($action, $parameters, $absolute);
		 }

		/**
		 * Set the forced root URL.
		 *
		 * @param string  $root
		 * @return void
		 * @static 
		 */
		 public static function forceRootUrl($root){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			 \Illuminate\Routing\UrlGenerator::forceRootUrl($root);
		 }

		/**
		 * Determine if the given path is a valid URL.
		 *
		 * @param string  $path
		 * @return bool
		 * @static 
		 */
		 public static function isValidUrl($path){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::isValidUrl($path);
		 }

		/**
		 * Get the request instance.
		 *
		 * @return \Symfony\Component\HttpFoundation\Request
		 * @static 
		 */
		 public static function getRequest(){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			return \Illuminate\Routing\UrlGenerator::getRequest();
		 }

		/**
		 * Set the current request instance.
		 *
		 * @param \Illuminate\Http\Request  $request
		 * @return void
		 * @static 
		 */
		 public static function setRequest($request){
			//Method inherited from \Illuminate\Routing\UrlGenerator
			 \Illuminate\Routing\UrlGenerator::setRequest($request);
		 }

	}
	class Validator extends \Illuminate\Support\Facades\Validator{
		/**
		 * Create a new Validator factory instance.
		 *
		 * @param \Symfony\Component\Translation\TranslatorInterface  $translator
		 * @param \Illuminate\Container\Container  $container
		 * @return void
		 * @static 
		 */
		 public static function __construct($translator, $container = null){
			//Method inherited from \Illuminate\Validation\Factory
			 \Illuminate\Validation\Factory::__construct($translator, $container);
		 }

		/**
		 * Create a new Validator instance.
		 *
		 * @param array  $data
		 * @param array  $rules
		 * @param array  $messages
		 * @param array  $customAttributes
		 * @return \Illuminate\Validation\Validator
		 * @static 
		 */
		 public static function make($data, $rules, $messages = array(), $customAttributes = array()){
			//Method inherited from \Illuminate\Validation\Factory
			return \Illuminate\Validation\Factory::make($data, $rules, $messages, $customAttributes);
		 }

		/**
		 * Register a custom validator extension.
		 *
		 * @param string  $rule
		 * @param \Closure|string  $extension
		 * @param string  $message
		 * @return void
		 * @static 
		 */
		 public static function extend($rule, $extension, $message = null){
			//Method inherited from \Illuminate\Validation\Factory
			 \Illuminate\Validation\Factory::extend($rule, $extension, $message);
		 }

		/**
		 * Register a custom implicit validator extension.
		 *
		 * @param string   $rule
		 * @param \Closure|string  $extension
		 * @param string  $message
		 * @return void
		 * @static 
		 */
		 public static function extendImplicit($rule, $extension, $message = null){
			//Method inherited from \Illuminate\Validation\Factory
			 \Illuminate\Validation\Factory::extendImplicit($rule, $extension, $message);
		 }

		/**
		 * Register a custom implicit validator message replacer.
		 *
		 * @param string   $rule
		 * @param \Closure|string  $replacer
		 * @return void
		 * @static 
		 */
		 public static function replacer($rule, $replacer){
			//Method inherited from \Illuminate\Validation\Factory
			 \Illuminate\Validation\Factory::replacer($rule, $replacer);
		 }

		/**
		 * Set the Validator instance resolver.
		 *
		 * @param \Closure  $resolver
		 * @return void
		 * @static 
		 */
		 public static function resolver($resolver){
			//Method inherited from \Illuminate\Validation\Factory
			 \Illuminate\Validation\Factory::resolver($resolver);
		 }

		/**
		 * Get the Translator implementation.
		 *
		 * @return \Symfony\Component\Translation\TranslatorInterface
		 * @static 
		 */
		 public static function getTranslator(){
			//Method inherited from \Illuminate\Validation\Factory
			return \Illuminate\Validation\Factory::getTranslator();
		 }

		/**
		 * Get the Presence Verifier implementation.
		 *
		 * @return \Illuminate\Validation\PresenceVerifierInterface
		 * @static 
		 */
		 public static function getPresenceVerifier(){
			//Method inherited from \Illuminate\Validation\Factory
			return \Illuminate\Validation\Factory::getPresenceVerifier();
		 }

		/**
		 * Set the Presence Verifier implementation.
		 *
		 * @param \Illuminate\Validation\PresenceVerifierInterface  $presenceVerifier
		 * @return void
		 * @static 
		 */
		 public static function setPresenceVerifier($presenceVerifier){
			//Method inherited from \Illuminate\Validation\Factory
			 \Illuminate\Validation\Factory::setPresenceVerifier($presenceVerifier);
		 }

	}
	class View extends \Illuminate\Support\Facades\View{
		/**
		 * Create a new view factory instance.
		 *
		 * @param \Illuminate\View\Engines\EngineResolver  $engines
		 * @param \Illuminate\View\ViewFinderInterface  $finder
		 * @param \Illuminate\Events\Dispatcher  $events
		 * @return void
		 * @static 
		 */
		 public static function __construct($engines, $finder, $events){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::__construct($engines, $finder, $events);
		 }

		/**
		 * Get the evaluated view contents for the given view.
		 *
		 * @param string  $view
		 * @param array   $data
		 * @param array   $mergeData
		 * @return \Illuminate\View\View
		 * @static 
		 */
		 public static function make($view, $data = array(), $mergeData = array()){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::make($view, $data, $mergeData);
		 }

		/**
		 * Get the evaluated view contents for a named view.
		 *
		 * @param string  $view
		 * @param mixed   $data
		 * @return \Illuminate\View\View
		 * @static 
		 */
		 public static function of($view, $data = array()){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::of($view, $data);
		 }

		/**
		 * Register a named view.
		 *
		 * @param string  $view
		 * @param string  $name
		 * @return void
		 * @static 
		 */
		 public static function name($view, $name){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::name($view, $name);
		 }

		/**
		 * Add an alias for a view.
		 *
		 * @param string  $view
		 * @param string  $alias
		 * @return void
		 * @static 
		 */
		 public static function alias($view, $alias){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::alias($view, $alias);
		 }

		/**
		 * Determine if a given view exists.
		 *
		 * @param string  $view
		 * @return bool
		 * @static 
		 */
		 public static function exists($view){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::exists($view);
		 }

		/**
		 * Get the rendered contents of a partial from a loop.
		 *
		 * @param string  $view
		 * @param array   $data
		 * @param string  $iterator
		 * @param string  $empty
		 * @return string
		 * @static 
		 */
		 public static function renderEach($view, $data, $iterator, $empty = 'raw|'){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::renderEach($view, $data, $iterator, $empty);
		 }

		/**
		 * Get the appropriate view engine for the given path.
		 *
		 * @param string  $path
		 * @return \Illuminate\View\Engines\EngineInterface
		 * @throws \InvalidArgumentException
		 * @static 
		 */
		 public static function getEngineFromPath($path){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getEngineFromPath($path);
		 }

		/**
		 * Add a piece of shared data to the environment.
		 *
		 * @param string  $key
		 * @param mixed   $value
		 * @return void
		 * @static 
		 */
		 public static function share($key, $value = null){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::share($key, $value);
		 }

		/**
		 * Register a view creator event.
		 *
		 * @param array|string     $views
		 * @param \Closure|string  $callback
		 * @return array
		 * @static 
		 */
		 public static function creator($views, $callback){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::creator($views, $callback);
		 }

		/**
		 * Register multiple view composers via an array.
		 *
		 * @param array  $composers
		 * @return array
		 * @static 
		 */
		 public static function composers($composers){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::composers($composers);
		 }

		/**
		 * Register a view composer event.
		 *
		 * @param array|string  $views
		 * @param \Closure|string  $callback
		 * @param int|null  $priority
		 * @return array
		 * @static 
		 */
		 public static function composer($views, $callback, $priority = null){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::composer($views, $callback, $priority);
		 }

		/**
		 * Call the composer for a given view.
		 *
		 * @param \Illuminate\View\View  $view
		 * @return void
		 * @static 
		 */
		 public static function callComposer($view){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::callComposer($view);
		 }

		/**
		 * Call the creator for a given view.
		 *
		 * @param \Illuminate\View\View  $view
		 * @return void
		 * @static 
		 */
		 public static function callCreator($view){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::callCreator($view);
		 }

		/**
		 * Start injecting content into a section.
		 *
		 * @param string  $section
		 * @param string  $content
		 * @return void
		 * @static 
		 */
		 public static function startSection($section, $content = ''){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::startSection($section, $content);
		 }

		/**
		 * Inject inline content into a section.
		 *
		 * @param string  $section
		 * @param string  $content
		 * @return void
		 * @static 
		 */
		 public static function inject($section, $content){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::inject($section, $content);
		 }

		/**
		 * Stop injecting content into a section and return its contents.
		 *
		 * @return string
		 * @static 
		 */
		 public static function yieldSection(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::yieldSection();
		 }

		/**
		 * Stop injecting content into a section.
		 *
		 * @param bool  $overwrite
		 * @return string
		 * @static 
		 */
		 public static function stopSection($overwrite = false){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::stopSection($overwrite);
		 }

		/**
		 * Stop injecting content into a section and append it.
		 *
		 * @return string
		 * @static 
		 */
		 public static function appendSection(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::appendSection();
		 }

		/**
		 * Get the string contents of a section.
		 *
		 * @param string  $section
		 * @param string  $default
		 * @return string
		 * @static 
		 */
		 public static function yieldContent($section, $default = ''){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::yieldContent($section, $default);
		 }

		/**
		 * Flush all of the section contents.
		 *
		 * @return void
		 * @static 
		 */
		 public static function flushSections(){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::flushSections();
		 }

		/**
		 * Flush all of the section contents if done rendering.
		 *
		 * @return void
		 * @static 
		 */
		 public static function flushSectionsIfDoneRendering(){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::flushSectionsIfDoneRendering();
		 }

		/**
		 * Increment the rendering counter.
		 *
		 * @return void
		 * @static 
		 */
		 public static function incrementRender(){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::incrementRender();
		 }

		/**
		 * Decrement the rendering counter.
		 *
		 * @return void
		 * @static 
		 */
		 public static function decrementRender(){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::decrementRender();
		 }

		/**
		 * Check if there are no active render operations.
		 *
		 * @return bool
		 * @static 
		 */
		 public static function doneRendering(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::doneRendering();
		 }

		/**
		 * Add a location to the array of view locations.
		 *
		 * @param string  $location
		 * @return void
		 * @static 
		 */
		 public static function addLocation($location){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::addLocation($location);
		 }

		/**
		 * Add a new namespace to the loader.
		 *
		 * @param string  $namespace
		 * @param string|array  $hints
		 * @return void
		 * @static 
		 */
		 public static function addNamespace($namespace, $hints){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::addNamespace($namespace, $hints);
		 }

		/**
		 * Prepend a new namespace to the loader.
		 *
		 * @param string  $namespace
		 * @param string|array  $hints
		 * @return void
		 * @static 
		 */
		 public static function prependNamespace($namespace, $hints){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::prependNamespace($namespace, $hints);
		 }

		/**
		 * Register a valid view extension and its engine.
		 *
		 * @param string    $extension
		 * @param string    $engine
		 * @param \Closure  $resolver
		 * @return void
		 * @static 
		 */
		 public static function addExtension($extension, $engine, $resolver = null){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::addExtension($extension, $engine, $resolver);
		 }

		/**
		 * Get the extension to engine bindings.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getExtensions(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getExtensions();
		 }

		/**
		 * Get the engine resolver instance.
		 *
		 * @return \Illuminate\View\Engines\EngineResolver
		 * @static 
		 */
		 public static function getEngineResolver(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getEngineResolver();
		 }

		/**
		 * Get the view finder instance.
		 *
		 * @return \Illuminate\View\ViewFinderInterface
		 * @static 
		 */
		 public static function getFinder(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getFinder();
		 }

		/**
		 * Set the view finder instance.
		 *
		 * @param \Illuminate\View\ViewFinderInterface  $finder
		 * @return void
		 * @static 
		 */
		 public static function setFinder($finder){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::setFinder($finder);
		 }

		/**
		 * Get the event dispatcher instance.
		 *
		 * @return \Illuminate\Events\Dispatcher
		 * @static 
		 */
		 public static function getDispatcher(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getDispatcher();
		 }

		/**
		 * Set the event dispatcher instance.
		 *
		 * @param \Illuminate\Events\Dispatcher
		 * @return void
		 * @static 
		 */
		 public static function setDispatcher($events){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::setDispatcher($events);
		 }

		/**
		 * Get the IoC container instance.
		 *
		 * @return \Illuminate\Container\Container
		 * @static 
		 */
		 public static function getContainer(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getContainer();
		 }

		/**
		 * Set the IoC container instance.
		 *
		 * @param \Illuminate\Container\Container  $container
		 * @return void
		 * @static 
		 */
		 public static function setContainer($container){
			//Method inherited from \Illuminate\View\Factory
			 \Illuminate\View\Factory::setContainer($container);
		 }

		/**
		 * Get an item from the shared data.
		 *
		 * @param string  $key
		 * @param mixed   $default
		 * @return mixed
		 * @static 
		 */
		 public static function shared($key, $default = null){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::shared($key, $default);
		 }

		/**
		 * Get all of the shared data for the environment.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getShared(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getShared();
		 }

		/**
		 * Get the entire array of sections.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getSections(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getSections();
		 }

		/**
		 * Get all of the registered named views in environment.
		 *
		 * @return array
		 * @static 
		 */
		 public static function getNames(){
			//Method inherited from \Illuminate\View\Factory
			return \Illuminate\View\Factory::getNames();
		 }

	}
	class Captcha extends \Sayakb\Captcha\Facades\Captcha{
		/**
		 * 
		 *
		 * @static 
		 */
		 public static function instance(){
			//Method inherited from \Sayakb\Captcha\Captcha
			 \Sayakb\Captcha\Captcha::instance();
		 }

		/**
		 * Generates a captcha image, writing it to the output
		 * It is used internally by this bundle when pointing to "/captcha" (see [vendor]\routes.php)
		 * Typically, you won't use this function, but use the above img() function instead
		 *
		 * @access public
		 * @return img
		 * @static 
		 */
		 public static function create($id = null){
			//Method inherited from \Sayakb\Captcha\Captcha
			return \Sayakb\Captcha\Captcha::create($id);
		 }

		/**
		 * Fonts
		 *
		 * @access public
		 * @param string
		 * @return array
		 * @static 
		 */
		 public static function assets($type = null){
			//Method inherited from \Sayakb\Captcha\Captcha
			return \Sayakb\Captcha\Captcha::assets($type);
		 }

		/**
		 * Select asset
		 *
		 * @access public
		 * @param string
		 * @return string
		 * @static 
		 */
		 public static function asset($type = null){
			//Method inherited from \Sayakb\Captcha\Captcha
			return \Sayakb\Captcha\Captcha::asset($type);
		 }

		/**
		 * Checks if the supplied captcha test value matches the stored one
		 *
		 * @param string	$value
		 * @access public
		 * @return bool
		 * @static 
		 */
		 public static function check($value){
			//Method inherited from \Sayakb\Captcha\Captcha
			return \Sayakb\Captcha\Captcha::check($value);
		 }

		/**
		 * Returns an URL to the captcha image
		 * For example, you can use in your view something like
		 * <img src="<?php echo Captcha::img(); ?>" alt="" />
		 *
		 * @access public
		 * @return string
		 * @static 
		 */
		 public static function img(){
			//Method inherited from \Sayakb\Captcha\Captcha
			return \Sayakb\Captcha\Captcha::img();
		 }

	}
}

