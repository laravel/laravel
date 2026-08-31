CHANGELOG
=========

7.4
---

 * Add support for the `QUERY` HTTP method
 * Deprecate implementing `__sleep/wakeup()` on kernels; use `__(un)serialize()` instead
 * Deprecate implementing `__sleep/wakeup()` on data collectors; use `__(un)serialize()` instead
 * Add `#[IsSignatureValid]` attribute to validate URI signatures
 * Make `Profile` final and `Profiler::__sleep()` internal
 * Collect the application runner class
 * Allow configuring `DumpListener` to use a different dumper when CLI profiling is enabled

7.3
---

 * Record a `waiting` trace in the `HttpCache` when the cache had to wait for another request to finish
 * Add `$key` argument to `#[MapQueryString]` that allows using a specific key for argument resolving
 * Support `Uid` in `#[MapQueryParameter]`
 * Add `ServicesResetterInterface`, implemented by `ServicesResetter`
 * Allow configuring the logging channel per type of exceptions in ErrorListener

7.2
---

 * Remove `@internal` flag and add `@final` to `ServicesResetter`
 * Add support for `SYMFONY_DISABLE_RESOURCE_TRACKING` env var
 * Add support for configuring trusted proxies/headers/hosts via env vars

7.1
---

 * Add method `isKernelTerminating()` to `ExceptionEvent` that allows to check if an exception was thrown while the kernel is being terminated
 * Add `HttpException::fromStatusCode()`
 * Add `$validationFailedStatusCode` argument to `#[MapQueryParameter]` that allows setting a custom HTTP status code when validation fails
 * Add `NearMissValueResolverException` to let value resolvers report when an argument could be under their watch but failed to be resolved
 * Add `$type` argument to `#[MapRequestPayload]` that allows mapping a list of items
 * The `Extension` class is marked as internal, extend the `Extension` class from the DependencyInjection component instead
 * Deprecate `Extension::addAnnotatedClassesToCompile()`
 * Deprecate `AddAnnotatedClassesToCachePass`
 * Deprecate the `setAnnotatedClassCache()` and `getAnnotatedClassesToCompile()` methods of the `Kernel` class
 * Add `#[MapUploadedFile]` attribute to fetch, validate, and inject uploaded files into controller arguments

7.0
---

 * Add argument `$reflector` to `ArgumentResolverInterface::getArguments()` and `ArgumentMetadataFactoryInterface::createArgumentMetadata()`
 * Remove `ArgumentValueResolverInterface`, use `ValueResolverInterface` instead
 * Remove `StreamedResponseListener`
 * Remove `AbstractSurrogate::$phpEscapeMap`
 * Remove `HttpKernelInterface::MASTER_REQUEST`
 * Remove `terminate_on_cache_hit` option from `HttpCache`
 * Require explicit argument when calling `ConfigDataCollector::setKernel()`, `RouterListener::setCurrentRequest()`
 * Remove `Kernel::stripComments()`
 * Remove `FileLinkFormatter`, use `FileLinkFormatter` from the ErrorHandler component instead
 * Remove `UriSigner`, use `UriSigner` from the HttpFoundation component instead
 * Add argument `$buildDir` to `WarmableInterface`
 * Add argument `$filter` to `Profiler::find()` and `FileProfilerStorage::find()`

6.4
---

 * Support backed enums in #[MapQueryParameter]
 * `BundleInterface` no longer extends `ContainerAwareInterface`
 * Add optional `$className` parameter to `ControllerEvent::getAttributes()`
 * Add native return types to `TraceableEventDispatcher` and to `MergeExtensionConfigurationPass`
 * Add argument `$validationFailedStatusCode` to `#[MapQueryString]` and `#[MapRequestPayload]`
 * Add argument `$debug` to `Logger`
 * Add class `DebugLoggerConfigurator`
 * Add parameters `kernel.runtime_mode` and `kernel.runtime_mode.*`, all set from env var `APP_RUNTIME_MODE`
 * Deprecate `Kernel::stripComments()`
 * Support the `!` character at the beginning of a string as a negation operator in the url filter of the profiler
 * Deprecate `UriSigner`, use `UriSigner` from the HttpFoundation component instead
 * Deprecate `FileLinkFormatter`, use `FileLinkFormatter` from the ErrorHandler component instead
 * Add argument `$buildDir` to `WarmableInterface`
 * Add argument `$filter` to `Profiler::find()` and `FileProfilerStorage::find()`
 * Add `ControllerResolver::allowControllers()` to define which callables are legit controllers when the `_check_controller_is_allowed` request attribute is set

6.3
---

 * Deprecate parameters `container.dumper.inline_factories` and `container.dumper.inline_class_loader`, use `.container.dumper.inline_factories` and `.container.dumper.inline_class_loader` instead
 * `FileProfilerStorage` removes profiles automatically after two days
 * Add `#[WithHttpStatus]` for defining status codes for exceptions
 * Use an instance of `Psr\Clock\ClockInterface` to generate the current date time in `DateTimeValueResolver`
 * Add `#[WithLogLevel]` for defining log levels for exceptions
 * Add `skip_response_headers` to the `HttpCache` options
 * Introduce targeted value resolvers with `#[ValueResolver]` and `#[AsTargetedValueResolver]`
 * Add `#[MapRequestPayload]` to map and validate request payload from `Request::getContent()` or `Request::$request->all()` to typed objects
 * Add `#[MapQueryString]` to map and validate request query string from `Request::$query->all()` to typed objects
 * Add `#[MapQueryParameter]` to map and validate individual query parameters to controller arguments
 * Collect data from every event dispatcher

6.2
---

 * Add constructor argument `bool $handleAllThrowable` to `HttpKernel`
 * Add `ControllerEvent::getAttributes()` to handle attributes on controllers
 * Add `#[Cache]` to describe the default HTTP cache headers on controllers
 * Add `absolute_uri` option to surrogate fragment renderers
 * Add `ValueResolverInterface` and deprecate `ArgumentValueResolverInterface`
 * Add argument `$reflector` to `ArgumentResolverInterface` and `ArgumentMetadataFactoryInterface`
 * Deprecate calling `ConfigDataCollector::setKernel()`, `RouterListener::setCurrentRequest()` without arguments

6.1
---

 * Add `BackedEnumValueResolver` to resolve backed enum cases from request attributes in controller arguments
 * Add `DateTimeValueResolver` to resolve request attributes into DateTime objects in controller arguments
 * Deprecate StreamedResponseListener, it's not needed anymore
 * Add `Profiler::isEnabled()` so collaborating collector services may elect to omit themselves
 * Add the `UidValueResolver` argument value resolver
 * Add `AbstractBundle` class for DI configuration/definition on a single file
 * Update the path of a bundle placed in the `src/` directory to the parent directory when `AbstractBundle` is used

6.0
---

 * Remove `ArgumentInterface`
 * Remove `ArgumentMetadata::getAttribute()`, use `getAttributes()` instead
 * Remove support for returning a `ContainerBuilder` from `KernelInterface::registerContainerConfiguration()`
 * Remove `KernelEvent::isMasterRequest()`, use `isMainRequest()` instead
 * Remove support for `service:action` syntax to reference controllers, use `serviceOrFqcn::method` instead

5.4
---

 * Add the ability to enable the profiler using a request query parameter, body parameter or attribute
 * Deprecate `AbstractTestSessionListener` and `TestSessionListener`, use `AbstractSessionListener` and `SessionListener` instead
 * Deprecate the `fileLinkFormat` parameter of `DebugHandlersListener`
 * Add support for configuring log level, and status code by exception class
 * Allow ignoring "kernel.reset" methods that don't exist with "on_invalid" attribute

5.3
---

 * Deprecate `ArgumentInterface`
 * Add `ArgumentMetadata::getAttributes()`
 * Deprecate `ArgumentMetadata::getAttribute()`, use `getAttributes()` instead
 * Mark the class `Symfony\Component\HttpKernel\EventListener\DebugHandlersListener` as internal
 * Deprecate returning a `ContainerBuilder` from `KernelInterface::registerContainerConfiguration()`
 * Deprecate `HttpKernelInterface::MASTER_REQUEST` and add `HttpKernelInterface::MAIN_REQUEST` as replacement
 * Deprecate `KernelEvent::isMasterRequest()` and add `isMainRequest()` as replacement
 * Add `#[AsController]` attribute for declaring standalone controllers on PHP 8
 * Add `FragmentUriGeneratorInterface` and `FragmentUriGenerator` to generate the URI of a fragment

5.2.0
-----

 * added session usage
 * made the public `http_cache` service handle requests when available
 * allowed enabling trusted hosts and proxies using new `kernel.trusted_hosts`,
   `kernel.trusted_proxies` and `kernel.trusted_headers` parameters
 * content of request parameter `_password` is now also hidden
   in the request profiler raw content section
 * Allowed adding attributes on controller arguments that will be passed to argument resolvers.
 * kernels implementing the `ExtensionInterface` will now be auto-registered to the container
 * added parameter `kernel.runtime_environment`, defined as `%env(default:kernel.environment:APP_RUNTIME_ENV)%`
 * do not set a default `Accept` HTTP header when using `HttpKernelBrowser`

5.1.0
-----

 * allowed to use a specific logger channel for deprecations
 * made `WarmableInterface::warmUp()` return a list of classes or files to preload on PHP 7.4+;
   not returning an array is deprecated
 * made kernels implementing `WarmableInterface` be part of the cache warmup stage
 * deprecated support for `service:action` syntax to reference controllers, use `serviceOrFqcn::method` instead
 * allowed using public aliases to reference controllers
 * added session usage reporting when the `_stateless` attribute of the request is set to `true`
 * added `AbstractSessionListener::onSessionUsage()` to report when the session is used while a request is stateless

5.0.0
-----

 * removed support for getting the container from a non-booted kernel
 * removed the first and second constructor argument of `ConfigDataCollector`
 * removed `ConfigDataCollector::getApplicationName()`
 * removed `ConfigDataCollector::getApplicationVersion()`
 * removed support for `Symfony\Component\Templating\EngineInterface` in `HIncludeFragmentRenderer`, use a `Twig\Environment` only
 * removed `TranslatorListener` in favor of `LocaleAwareListener`
 * removed `getRootDir()` and `getName()` from `Kernel` and `KernelInterface`
 * removed `FilterControllerArgumentsEvent`, use `ControllerArgumentsEvent` instead
 * removed `FilterControllerEvent`, use `ControllerEvent` instead
 * removed `FilterResponseEvent`, use `ResponseEvent` instead
 * removed `GetResponseEvent`, use `RequestEvent` instead
 * removed `GetResponseForControllerResultEvent`, use `ViewEvent` instead
 * removed `GetResponseForExceptionEvent`, use `ExceptionEvent` instead
 * removed `PostResponseEvent`, use `TerminateEvent` instead
 * removed `SaveSessionListener` in favor of `AbstractSessionListener`
 * removed `Client`, use `HttpKernelBrowser` instead
 * added method `getProjectDir()` to `KernelInterface`
 * removed methods `serialize` and `unserialize` from `DataCollector`, store the serialized state in the data property instead
 * made `ProfilerStorageInterface` internal
 * removed the second and third argument of `KernelInterface::locateResource`
 * removed the second and third argument of `FileLocator::__construct`
 * removed loading resources from `%kernel.root_dir%/Resources` and `%kernel.root_dir%` as
   fallback directories.
 * removed class `ExceptionListener`, use `ErrorListener` instead

4.4.0
-----

 * The `DebugHandlersListener` class has been marked as `final`
 * Added new Bundle directory convention consistent with standard skeletons
 * Deprecated the second and third argument of `KernelInterface::locateResource`
 * Deprecated the second and third argument of `FileLocator::__construct`
 * Deprecated loading resources from `%kernel.root_dir%/Resources` and `%kernel.root_dir%` as
   fallback directories. Resources like service definitions are usually loaded relative to the
   current directory or with a glob pattern. The fallback directories have never been advocated
   so you likely do not use those in any app based on the SF Standard or Flex edition.
 * Marked all dispatched event classes as `@final`
 * Added `ErrorController` to enable the preview and error rendering mechanism
 * Getting the container from a non-booted kernel is deprecated.
 * Marked the `AjaxDataCollector`, `ConfigDataCollector`, `EventDataCollector`,
   `ExceptionDataCollector`, `LoggerDataCollector`, `MemoryDataCollector`,
   `RequestDataCollector` and `TimeDataCollector` classes as `@final`.
 * Marked the `RouterDataCollector::collect()` method as `@final`.
 * The `DataCollectorInterface::collect()` and `Profiler::collect()` methods third parameter signature
   will be `\Throwable $exception = null` instead of `\Exception $exception = null` in Symfony 5.0.
 * Deprecated methods `ExceptionEvent::get/setException()`, use `get/setThrowable()` instead
 * Deprecated class `ExceptionListener`, use `ErrorListener` instead

4.3.0
-----

 * renamed `Client` to `HttpKernelBrowser`
 * `KernelInterface` doesn't extend `Serializable` anymore
 * deprecated the `Kernel::serialize()` and `unserialize()` methods
 * increased the priority of `Symfony\Component\HttpKernel\EventListener\AddRequestFormatsListener`
 * made `Symfony\Component\HttpKernel\EventListener\LocaleListener` set the default locale early
 * deprecated `TranslatorListener` in favor of `LocaleAwareListener`
 * added the registration of all `LocaleAwareInterface` implementations into the `LocaleAwareListener`
 * made `FileLinkFormatter` final and not implement `Serializable` anymore
 * the base `DataCollector` doesn't implement `Serializable` anymore, you should
   store all the serialized state in the data property instead
 * `DumpDataCollector` has been marked as `final`
 * added an event listener to prevent search engines from indexing applications in debug mode.
 * renamed `FilterControllerArgumentsEvent` to `ControllerArgumentsEvent`
 * renamed `FilterControllerEvent` to `ControllerEvent`
 * renamed `FilterResponseEvent` to `ResponseEvent`
 * renamed `GetResponseEvent` to `RequestEvent`
 * renamed `GetResponseForControllerResultEvent` to `ViewEvent`
 * renamed `GetResponseForExceptionEvent` to `ExceptionEvent`
 * renamed `PostResponseEvent` to `TerminateEvent`
 * added `HttpClientKernel` for handling requests with an `HttpClientInterface` instance
 * added `trace_header` and `trace_level` configuration options to `HttpCache`

4.2.0
-----

 * deprecated `KernelInterface::getRootDir()` and the `kernel.root_dir` parameter
 * deprecated `KernelInterface::getName()` and the `kernel.name` parameter
 * deprecated the first and second constructor argument of `ConfigDataCollector`
 * deprecated `ConfigDataCollector::getApplicationName()`
 * deprecated `ConfigDataCollector::getApplicationVersion()`

4.1.0
-----

 * added orphaned events support to `EventDataCollector`
 * `ExceptionListener` now logs exceptions at priority `0` (previously logged at `-128`)
 * Added support for using `service::method` to reference controllers, making it consistent with other cases. It is recommended over the `service:action` syntax with a single colon, which will be deprecated in the future.
 * Added the ability to profile individual argument value resolvers via the
   `Symfony\Component\HttpKernel\Controller\ArgumentResolver\TraceableValueResolver`

4.0.0
-----

 * removed the `DataCollector::varToString()` method, use `DataCollector::cloneVar()`
   instead
 * using the `DataCollector::cloneVar()` method requires the VarDumper component
 * removed the `ValueExporter` class
 * removed `ControllerResolverInterface::getArguments()`
 * removed `TraceableControllerResolver::getArguments()`
 * removed `ControllerResolver::getArguments()` and the ability to resolve arguments
 * removed the `argument_resolver` service dependency from the `debug.controller_resolver`
 * removed `LazyLoadingFragmentHandler::addRendererService()`
 * removed `Psr6CacheClearer::addPool()`
 * removed `Extension::addClassesToCompile()` and `Extension::getClassesToCompile()`
 * removed `Kernel::loadClassCache()`, `Kernel::doLoadClassCache()`, `Kernel::setClassCache()`,
   and `Kernel::getEnvParameters()`
 * support for the `X-Status-Code` when handling exceptions in the `HttpKernel`
   has been dropped, use the `HttpKernel::allowCustomResponseCode()` method
   instead
 * removed convention-based commands registration
 * removed the `ChainCacheClearer::add()` method
 * removed the `CacheaWarmerAggregate::add()` and `setWarmers()` methods
 * made `CacheWarmerAggregate` and `ChainCacheClearer` classes final

3.4.0
-----

 * added a minimalist PSR-3 `Logger` class that writes in `stderr`
 * made kernels implementing `CompilerPassInterface` able to process the container
 * deprecated bundle inheritance
 * added `RebootableInterface` and implemented it in `Kernel`
 * deprecated commands auto registration
 * deprecated `EnvParametersResource`
 * added `Symfony\Component\HttpKernel\Client::catchExceptions()`
 * deprecated the `ChainCacheClearer::add()` method
 * deprecated the `CacheaWarmerAggregate::add()` and `setWarmers()` methods
 * made `CacheWarmerAggregate` and `ChainCacheClearer` classes final
 * added the possibility to reset the profiler to its initial state
 * deprecated data collectors without a `reset()` method
 * deprecated implementing `DebugLoggerInterface` without a `clear()` method

3.3.0
-----

 * added `kernel.project_dir` and `Kernel::getProjectDir()`
 * deprecated `kernel.root_dir` and `Kernel::getRootDir()`
 * deprecated `Kernel::getEnvParameters()`
 * deprecated the special `SYMFONY__` environment variables
 * added the possibility to change the query string parameter used by `UriSigner`
 * deprecated `LazyLoadingFragmentHandler::addRendererService()`
 * deprecated `Extension::addClassesToCompile()` and `Extension::getClassesToCompile()`
 * deprecated `Psr6CacheClearer::addPool()`

3.2.0
-----

 * deprecated `DataCollector::varToString()`, use `cloneVar()` instead
 * changed surrogate capability name in `AbstractSurrogate::addSurrogateCapability` to 'symfony'
 * Added `ControllerArgumentValueResolverPass`

3.1.0
-----
 * deprecated passing objects as URI attributes to the ESI and SSI renderers
 * deprecated `ControllerResolver::getArguments()`
 * added `Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface`
 * added `Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface` as argument to `HttpKernel`
 * added `Symfony\Component\HttpKernel\Controller\ArgumentResolver`
 * added `Symfony\Component\HttpKernel\DataCollector\RequestDataCollector::getMethod()`
 * added `Symfony\Component\HttpKernel\DataCollector\RequestDataCollector::getRedirect()`
 * added the `kernel.controller_arguments` event, triggered after controller arguments have been resolved

3.0.0
-----

 * removed `Symfony\Component\HttpKernel\Kernel::init()`
 * removed `Symfony\Component\HttpKernel\Kernel::isClassInActiveBundle()` and `Symfony\Component\HttpKernel\KernelInterface::isClassInActiveBundle()`
 * removed `Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher::setProfiler()`
 * removed `Symfony\Component\HttpKernel\EventListener\FragmentListener::getLocalIpAddresses()`
 * removed `Symfony\Component\HttpKernel\EventListener\LocaleListener::setRequest()`
 * removed `Symfony\Component\HttpKernel\EventListener\RouterListener::setRequest()`
 * removed `Symfony\Component\HttpKernel\EventListener\ProfilerListener::onKernelRequest()`
 * removed `Symfony\Component\HttpKernel\Fragment\FragmentHandler::setRequest()`
 * removed `Symfony\Component\HttpKernel\HttpCache\Esi::hasSurrogateEsiCapability()`
 * removed `Symfony\Component\HttpKernel\HttpCache\Esi::addSurrogateEsiCapability()`
 * removed `Symfony\Component\HttpKernel\HttpCache\Esi::needsEsiParsing()`
 * removed `Symfony\Component\HttpKernel\HttpCache\HttpCache::getEsi()`
 * removed `Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel`
 * removed `Symfony\Component\HttpKernel\DependencyInjection\RegisterListenersPass`
 * removed `Symfony\Component\HttpKernel\EventListener\ErrorsLoggerListener`
 * removed `Symfony\Component\HttpKernel\EventListener\EsiListener`
 * removed `Symfony\Component\HttpKernel\HttpCache\EsiResponseCacheStrategy`
 * removed `Symfony\Component\HttpKernel\HttpCache\EsiResponseCacheStrategyInterface`
 * removed `Symfony\Component\HttpKernel\Log\LoggerInterface`
 * removed `Symfony\Component\HttpKernel\Log\NullLogger`
 * removed `Symfony\Component\HttpKernel\Profiler::import()`
 * removed `Symfony\Component\HttpKernel\Profiler::export()`

2.8.0
-----

 * deprecated `Profiler::import` and `Profiler::export`

2.7.0
-----

 * added the HTTP status code to profiles

2.6.0
-----

 * deprecated `Symfony\Component\HttpKernel\EventListener\ErrorsLoggerListener`, use `Symfony\Component\HttpKernel\EventListener\DebugHandlersListener` instead
 * deprecated unused method `Symfony\Component\HttpKernel\Kernel::isClassInActiveBundle` and `Symfony\Component\HttpKernel\KernelInterface::isClassInActiveBundle`

2.5.0
-----

 * deprecated `Symfony\Component\HttpKernel\DependencyInjection\RegisterListenersPass`, use `Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass` instead

2.4.0
-----

 * added event listeners for the session
 * added the KernelEvents::FINISH_REQUEST event

2.3.0
-----

 * [BC BREAK] renamed `Symfony\Component\HttpKernel\EventListener\DeprecationLoggerListener` to `Symfony\Component\HttpKernel\EventListener\ErrorsLoggerListener` and changed its constructor
 * deprecated `Symfony\Component\HttpKernel\Debug\ErrorHandler`, `Symfony\Component\HttpKernel\Debug\ExceptionHandler`,
   `Symfony\Component\HttpKernel\Exception\FatalErrorException` and `Symfony\Component\HttpKernel\Exception\FlattenException`
 * deprecated `Symfony\Component\HttpKernel\Kernel::init()`
 * added the possibility to specify an id an extra attributes to hinclude tags
 * added the collect of data if a controller is a Closure in the Request collector
 * pass exceptions from the ExceptionListener to the logger using the logging context to allow for more
   detailed messages

2.2.0
-----

 * [BC BREAK] the path info for sub-request is now always _fragment (or whatever you configured instead of the default)
 * added Symfony\Component\HttpKernel\EventListener\FragmentListener
 * added Symfony\Component\HttpKernel\UriSigner
 * added Symfony\Component\HttpKernel\FragmentRenderer and rendering strategies (in Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface)
 * added Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel
 * added ControllerReference to create reference of Controllers (used in the FragmentRenderer class)
 * [BC BREAK] renamed TimeDataCollector::getTotalTime() to
   TimeDataCollector::getDuration()
 * updated the MemoryDataCollector to include the memory used in the
   kernel.terminate event listeners
 * moved the Stopwatch classes to a new component
 * added TraceableControllerResolver
 * added TraceableEventDispatcher (removed ContainerAwareTraceableEventDispatcher)
 * added support for WinCache opcode cache in ConfigDataCollector

2.1.0
-----

 * [BC BREAK] the charset is now configured via the Kernel::getCharset() method
 * [BC BREAK] the current locale for the user is not stored anymore in the session
 * added the HTTP method to the profiler storage
 * updated all listeners to implement EventSubscriberInterface
 * added TimeDataCollector
 * added ContainerAwareTraceableEventDispatcher
 * moved TraceableEventDispatcherInterface to the EventDispatcher component
 * added RouterListener, LocaleListener, and StreamedResponseListener
 * added CacheClearerInterface (and ChainCacheClearer)
 * added a kernel.terminate event (via TerminableInterface and PostResponseEvent)
 * added a Stopwatch class
 * added WarmableInterface
 * improved extensibility between bundles
 * added profiler storages for Memcache(d), File-based, MongoDB, Redis
 * moved Filesystem class to its own component
