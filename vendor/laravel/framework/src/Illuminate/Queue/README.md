## Illuminate Queue

The Laravel Queue component provides a unified API across a variety of different queue services. Queues allow you to defer the processing of a time consuming task, such as sending an e-mail, until a later time, thus drastically speeding up the web requests to your application.

### Usage Instructions

First, create a new Queue `Capsule` manager instance. Similar to the "Capsule" provided for the Eloquent ORM, the queue Capsule aims to make configuring the library for usage outside of the Laravel framework as easy as possible.

```PHP
use Illuminate\Queue\Capsule\Manager as Queue;

$queue = new Queue;

$queue->addConnection([
    'driver' => 'beanstalkd',
    'host' => 'localhost',
    'queue' => 'default',
]);

// Make this Capsule instance available globally via static methods... (optional)
$queue->setAsGlobal();
```

Once the Capsule instance has been registered. You may use it like so:

```PHP
// As an instance...
$queue->push('SendEmail', array('message' => $message));

// If setAsGlobal has been called...
Queue::push('SendEmail', array('message' => $message));
```

For further documentation on using the queue, consult the [Laravel framework documentation](http://laravel.com/docs).
