CHANGELOG
=========

2.4.0
-----

 * added event listeners for the session
 * added the KernelEvents::FINISH_REQUEST event

2.3.0
-----

 * [BC BREAK] renamed `Symfony\Component\HttpKernel\EventListener\DeprecationLoggerListener` to `Symfony\Component\HttpKernel\EventListener\ErrorsLoggerListener` and changed its constructor
 * deprecated `Symfony\Component\HttpKernel\Debug\ErrorHandler`, `Symfony\Component\HttpKernel\Debug\ExceptionHandler`,
   `Symfony\Component\HttpKernel\Exception\FatalErrorException`, and `Symfony\Component\HttpKernel\Exception\FlattenException`
 * deprecated `Symfony\Component\HttpKernel\Kernel::init()``
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
