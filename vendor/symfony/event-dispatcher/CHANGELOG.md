CHANGELOG
=========

2.5.0
-----

 * added Debug\TraceableEventDispatcher (originally in HttpKernel)
 * changed Debug\TraceableEventDispatcherInterface to extend EventDispatcherInterface
 * added RegisterListenersPass (originally in HttpKernel)

2.1.0
-----

 * added TraceableEventDispatcherInterface
 * added ContainerAwareEventDispatcher
 * added a reference to the EventDispatcher on the Event
 * added a reference to the Event name on the event
 * added fluid interface to the dispatch() method which now returns the Event
   object
 * added GenericEvent event class
 * added the possibility for subscribers to subscribe several times for the
   same event
 * added ImmutableEventDispatcher
