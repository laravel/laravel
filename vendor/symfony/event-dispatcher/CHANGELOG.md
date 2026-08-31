CHANGELOG
=========

6.0
---

 * Remove `LegacyEventDispatcherProxy`

5.4
---

 * Allow `#[AsEventListener]` attribute on methods

5.3
---

 * Add `#[AsEventListener]` attribute for declaring listeners on PHP 8

5.1.0
-----

 * The `LegacyEventDispatcherProxy` class has been deprecated.
 * Added an optional `dispatcher` attribute to the listener and subscriber tags in `RegisterListenerPass`.

5.0.0
-----

 * The signature of the `EventDispatcherInterface::dispatch()` method has been changed to `dispatch($event, string $eventName = null): object`.
 * The `Event` class has been removed in favor of `Symfony\Contracts\EventDispatcher\Event`.
 * The `TraceableEventDispatcherInterface` has been removed.
 * The `WrappedListener` class is now final.

4.4.0
-----

 * `AddEventAliasesPass` has been added, allowing applications and bundles to extend the event alias mapping used by `RegisterListenersPass`.
 * Made the `event` attribute of the `kernel.event_listener` tag optional for FQCN events.

4.3.0
-----

 * The signature of the `EventDispatcherInterface::dispatch()` method should be updated to `dispatch($event, string $eventName = null)`, not doing so is deprecated
 * deprecated the `Event` class, use `Symfony\Contracts\EventDispatcher\Event` instead

4.1.0
-----

 * added support for invokable event listeners tagged with `kernel.event_listener` by default
 * The `TraceableEventDispatcher::getOrphanedEvents()` method has been added.
 * The `TraceableEventDispatcherInterface` has been deprecated.

4.0.0
-----

 * removed the `ContainerAwareEventDispatcher` class
 * added the `reset()` method to the `TraceableEventDispatcherInterface`

3.4.0
-----

 * Implementing `TraceableEventDispatcherInterface` without the `reset()` method has been deprecated.

3.3.0
-----

 * The ContainerAwareEventDispatcher class has been deprecated. Use EventDispatcher with closure factories instead.

3.0.0
-----

 * The method `getListenerPriority($eventName, $listener)` has been added to the
   `EventDispatcherInterface`.
 * The methods `Event::setDispatcher()`, `Event::getDispatcher()`, `Event::setName()`
   and `Event::getName()` have been removed.
   The event dispatcher and the event name are passed to the listener call.

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
