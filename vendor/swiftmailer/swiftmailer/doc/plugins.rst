Plugins
=======

Plugins are provided with Swift Mailer and can be used to extend the behavior
of the library in situations where using simple class inheritance would be more complex.

AntiFlood Plugin
----------------

Many SMTP servers have limits on the number of messages that may be sent
during any single SMTP connection. The AntiFlood plugin provides a way to stay
within this limit while still managing a large number of emails.

A typical limit for a single connection is 100 emails. If the server you
connect to imposes such a limit, it expects you to disconnect after that
number of emails has been sent. You could manage this manually within a loop,
but the AntiFlood plugin provides the necessary wrapper code so that you don't
need to worry about this logic.

Regardless of limits imposed by the server, it's usually a good idea to be
conservative with the resources of the SMTP server. Sending will become
sluggish if the server is being over-used so using the AntiFlood plugin will
not be a bad idea even if no limits exist.

The AntiFlood plugin's logic is basically to disconnect and the immediately
re-connect with the SMTP server every X number of emails sent, where X is a
number you specify to the plugin.

You can also specify a time period in seconds that Swift Mailer should pause
for between the disconnect/re-connect process. It's a good idea to pause for a
short time (say 30 seconds every 100 emails) simply to give the SMTP server a
chance to process its queue and recover some resources.

Using the AntiFlood Plugin
~~~~~~~~~~~~~~~~~~~~~~~~~~

The AntiFlood Plugin -- like all plugins -- is added with the Mailer class's
``registerPlugin()`` method. It takes two constructor parameters: the number of
emails to pause after, and optionally the number of seconds to pause for.

To use the AntiFlood plugin:

* Create an instance of the Mailer using any Transport you choose.

* Create an instance of the ``Swift_Plugins_AntiFloodPlugin`` class, passing
  in one or two constructor parameters.

* Register the plugin using the Mailer's ``registerPlugin()`` method.

* Continue using Swift Mailer to send messages as normal.

When Swift Mailer sends messages it will count the number of messages that
have been sent since the last re-connect. Once the number hits your specified
threshold it will disconnect and re-connect, optionally pausing for a
specified amount of time.

.. code-block:: php

    require_once 'lib/swift_required.php';

    // Create the Mailer using any Transport
    $mailer = Swift_Mailer::newInstance(
      Swift_SmtpTransport::newInstance('smtp.example.org', 25)
    );

    // Use AntiFlood to re-connect after 100 emails
    $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100));

    // And specify a time in seconds to pause for (30 secs)
    $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100, 30));

    // Continue sending as normal
    for ($lotsOfRecipients as $recipient) {
      ...

      $mailer->send( ... );
    }

Throttler Plugin
----------------

If your SMTP server has restrictions in place to limit the rate at which you
send emails, then your code will need to be aware of this rate-limiting. The
Throttler plugin makes Swift Mailer run at a rate-limited speed.

Many shared hosts don't open their SMTP servers as a free-for-all. Usually
they have policies in place (probably to discourage spammers) that only allow
you to send a fixed number of emails per-hour/day.

The Throttler plugin supports two modes of rate-limiting and with each, you
will need to do that math to figure out the values you want. The plugin can
limit based on the number of emails per minute, or the number of
bytes-transferred per-minute.

Using the Throttler Plugin
~~~~~~~~~~~~~~~~~~~~~~~~~~

The Throttler Plugin -- like all plugins -- is added with the Mailer class'
``registerPlugin()`` method. It has two required constructor parameters that
tell it how to do its rate-limiting.

To use the Throttler plugin:

* Create an instance of the Mailer using any Transport you choose.

* Create an instance of the ``Swift_Plugins_ThrottlerPlugin`` class, passing
  the number of emails, or bytes you wish to limit by, along with the mode
  you're using.

* Register the plugin using the Mailer's ``registerPlugin()`` method.

* Continue using Swift Mailer to send messages as normal.

When Swift Mailer sends messages it will keep track of the rate at which sending
messages is occurring. If it realises that sending is happening too fast, it
will cause your program to ``sleep()`` for enough time to average out the rate.

.. code-block:: php

    require_once 'lib/swift_required.php';

    // Create the Mailer using any Transport
    $mailer = Swift_Mailer::newInstance(
      Swift_SmtpTransport::newInstance('smtp.example.org', 25)
    );

    // Rate limit to 100 emails per-minute
    $mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
      100, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
    ));

    // Rate limit to 10MB per-minute
    $mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
      1024 * 1024 * 10, Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
    ));

    // Continue sending as normal
    for ($lotsOfRecipients as $recipient) {
      ...

      $mailer->send( ... );
    }

Logger Plugin
-------------

The Logger plugins helps with debugging during the process of sending. It can
help to identify why an SMTP server is rejecting addresses, or any other
hard-to-find problems that may arise.

The Logger plugin comes in two parts. There's the plugin itself, along with
one of a number of possible Loggers that you may choose to use. For example,
the logger may output messages directly in realtime, or it may capture
messages in an array.

One other notable feature is the way in which the Logger plugin changes
Exception messages. If Exceptions are being thrown but the error message does
not provide conclusive information as to the source of the problem (such as an
ambiguous SMTP error) the Logger plugin includes the entire SMTP transcript in
the error message so that debugging becomes a simpler task.

There are a few available Loggers included with Swift Mailer, but writing your
own implementation is incredibly simple and is achieved by creating a short
class that implements the ``Swift_Plugins_Logger`` interface.

* ``Swift_Plugins_Loggers_ArrayLogger``: Keeps a collection of log messages
  inside an array. The array content can be cleared or dumped out to the
  screen.

* ``Swift_Plugins_Loggers_EchoLogger``: Prints output to the screen in
  realtime. Handy for very rudimentary debug output.

Using the Logger Plugin
~~~~~~~~~~~~~~~~~~~~~~~

The Logger Plugin -- like all plugins -- is added with the Mailer class'
``registerPlugin()`` method. It accepts an instance of ``Swift_Plugins_Logger``
in its constructor.

To use the Logger plugin:

* Create an instance of the Mailer using any Transport you choose.

* Create an instance of the a Logger implementation of
  ``Swift_Plugins_Logger``.

* Create an instance of the ``Swift_Plugins_LoggerPlugin`` class, passing the
  created Logger instance to its constructor.

* Register the plugin using the Mailer's ``registerPlugin()`` method.

* Continue using Swift Mailer to send messages as normal.

* Dump the contents of the log with the logger's ``dump()`` method.

When Swift Mailer sends messages it will keep a log of all the interactions
with the underlying Transport being used. Depending upon the Logger that has
been used the behaviour will differ, but all implementations offer a way to
get the contents of the log.

.. code-block:: php

    require_once 'lib/swift_required.php';

    // Create the Mailer using any Transport
    $mailer = Swift_Mailer::newInstance(
     Swift_SmtpTransport::newInstance('smtp.example.org', 25)
    );

    // To use the ArrayLogger
    $logger = new Swift_Plugins_Loggers_ArrayLogger();
    $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

    // Or to use the Echo Logger
    $logger = new Swift_Plugins_Loggers_EchoLogger();
    $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

    // Continue sending as normal
    for ($lotsOfRecipients as $recipient) {
     ...

     $mailer->send( ... );
    }

    // Dump the log contents
    // NOTE: The EchoLogger dumps in realtime so dump() does nothing for it
    echo $logger->dump();

Decorator Plugin
----------------

Often there's a need to send the same message to multiple recipients, but with
tiny variations such as the recipient's name being used inside the message
body. The Decorator plugin aims to provide a solution for allowing these small
differences.

The decorator plugin works by intercepting the sending process of Swift
Mailer, reading the email address in the To: field and then looking up a set
of replacements for a template.

While the use of this plugin is simple, it is probably the most commonly
misunderstood plugin due to the way in which it works. The typical mistake
users make is to try registering the plugin multiple times (once for each
recipient) -- inside a loop for example. This is incorrect.

The Decorator plugin should be registered just once, but containing the list
of all recipients prior to sending. It will use this list of recipients to
find the required replacements during sending.

Using the Decorator Plugin
~~~~~~~~~~~~~~~~~~~~~~~~~~

To use the Decorator plugin, simply create an associative array of replacements
based on email addresses and then use the mailer's ``registerPlugin()`` method
to add the plugin.

First create an associative array of replacements based on the email addresses
you'll be sending the message to.

.. note::

    The replacements array becomes a 2-dimensional array whose keys are the
    email addresses and whose values are an associative array of replacements
    for that email address. The curly braces used in this example can be any
    type of syntax you choose, provided they match the placeholders in your
    email template.

    .. code-block:: php

        $replacements = array();
        foreach ($users as $user) {
          $replacements[$user['email']] = array(
            '{username}'=>$user['username'],
            '{password}'=>$user['password']
          );
        }

Now create an instance of the Decorator plugin using this array of replacements
and then register it with the Mailer. Do this only once!

.. code-block:: php

    $decorator = new Swift_Plugins_DecoratorPlugin($replacements);

    $mailer->registerPlugin($decorator);

When you create your message, replace elements in the body (and/or the subject
line) with your placeholders.

.. code-block:: php

    $message = Swift_Message::newInstance()
      ->setSubject('Important notice for {username}')
      ->setBody(
        "Hello {username}, we have reset your password to {password}\n" .
        "Please log in and change it at your earliest convenience."
      )
      ;

    foreach ($users as $user) {
      $message->addTo($user['email']);
    }

When you send this message to each of your recipients listed in your
``$replacements`` array they will receive a message customized for just
themselves. For example, the message used above when received may appear like
this to one user:

.. code-block:: text

    Subject: Important notice for smilingsunshine2009

    Hello smilingsunshine2009, we have reset your password to rainyDays
    Please log in and change it at your earliest convenience.

While another use may receive the message as:

.. code-block:: text

    Subject: Important notice for billy-bo-bob

    Hello billy-bo-bob, we have reset your password to dancingOctopus
    Please log in and change it at your earliest convenience.

While the decorator plugin provides a means to solve this problem, there are
various ways you could tackle this problem without the need for a plugin.
We're trying to come up with a better way ourselves and while we have several
(obvious) ideas we don't quite have the perfect solution to go ahead and
implement it. Watch this space.

Providing Your Own Replacements Lookup for the Decorator
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Filling an array with replacements may not be the best solution for providing
replacement information to the decorator. If you have a more elegant algorithm
that performs replacement lookups on-the-fly you may provide your own
implementation.

Providing your own replacements lookup implementation for the Decorator is
simply a matter of passing an instance of ``Swift_Plugins_Decorator_Replacements`` to the decorator plugin's constructor,
rather than passing in an array.

The Replacements interface is very simple to implement since it has just one
method: ``getReplacementsFor($address)``.

Imagine you want to look up replacements from a database on-the-fly, you might
provide an implementation that does this. You need to create a small class.

.. code-block:: php

    class DbReplacements implements Swift_Plugins_Decorator_Replacements {
      public function getReplacementsFor($address) {
        $sql = sprintf(
          "SELECT * FROM user WHERE email = '%s'",
          mysql_real_escape_string($address)
        );

        $result = mysql_query($sql);

        if ($row = mysql_fetch_assoc($result)) {
          return array(
            '{username}'=>$row['username'],
            '{password}'=>$row['password']
          );
        }
      }
    }

Now all you need to do is pass an instance of your class into the Decorator
plugin's constructor instead of passing an array.

.. code-block:: php

    $decorator = new Swift_Plugins_DecoratorPlugin(new DbReplacements());

    $mailer->registerPlugin($decorator);

For each message sent, the plugin will call your class' ``getReplacementsFor()``
method to find the array of replacements it needs.

.. note::

    If your lookup algorithm is case sensitive, you should transform the
    ``$address`` argument as appropriate -- for example by passing it
    through ``strtolower()``.
