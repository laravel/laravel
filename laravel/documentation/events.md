# Events

## Contents

- [The Basics](#the-basics)
- [Firing Events](#firing-events)
- [Listening To Events](#listening-to-events)
- [Queued Events](#queued-events)
- [Laravel Events](#laravel-events)

<a name="the-basics"></a>
## The Basics

Events can provide a great away to build de-coupled applications, and allow plug-ins to tap into the core of your application without modifying its code.

<a name="firing-events"></a>
## Firing Events

To fire an event, just tell the **Event** class the name of the event you want to fire:

#### Firing an event:

	$responses = Event::fire('loaded');

Notice that we assigned the result of the **fire** method to a variable. This method will return an array containing the responses of all the event's listeners.

Sometimes you may want to fire an event, but just get the first response. Here's how:

#### Firing an event and retrieving the first response:

	$response = Event::first('loaded');

> **Note:** The **first** method will still fire all of the handlers listening to the event, but will only return the first response.

The **Event::until** method will execute the event handlers until the first non-null response is returned.

#### Firing an event until the first non-null response:

	$response = Event::until('loaded');

<a name="listening-to-events"></a>
## Listening To Events

So, what good are events if nobody is listening? Register an event handler that will be called when an event fires:

#### Registering an event handler:

	Event::listen('loaded', function()
	{
		// I'm executed on the "loaded" event!
	});

The Closure we provided to the method will be executed each time the "loaded" event is fired.

<a name="queued-events"></a>
## Queued Events

Sometimes you may wish to "queue" an event for firing, but not fire it immediately. This is possible using the `queue` and `flush` methods. First, throw an event on a given queue with a unique identifier:

#### Registering a queued event:

	Event::queue('foo', $user->id, array($user));

This method accepts three parameters. The first is the name of the queue, the second is a unique identifier for this item on the queue, and the third is an array of data to pass to the queue flusher.

Next, we'll register a flusher for the `foo` queue:

#### Registering an event flusher:

	Event::flusher('foo', function($key, $user)
	{
		//
	});

Note that the event flusher receives two arguments. The first, is the unique identifier for the queued event, which in this case would be the user's ID. The second (and any remaining) parameters would be the payload items for the queued event.

Finally, we can run our flusher and flush all queued events using the `flush` method:

	Event::flush('foo');

<a name="laravel-events"></a>
## Laravel Events

There are several events that are fired by the Laravel core. Here they are:

#### Event fired when a bundle is started:

	Event::listen('laravel.started: bundle', function() {});

#### Event fired when a database query is executed:

	Event::listen('laravel.query', function($sql, $bindings, $time) {});

#### Event fired right before response is sent to browser:

	Event::listen('laravel.done', function($response) {});

#### Event fired when a messaged is logged using the Log class:

	Event::listen('laravel.log', function($type, $message) {});