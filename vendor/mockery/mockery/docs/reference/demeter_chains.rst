.. index::
    single: Mocking; Demeter Chains

Mocking Demeter Chains And Fluent Interfaces
============================================

Both of these terms refer to the growing practice of invoking statements
similar to:

.. code-block:: php

    $object->foo()->bar()->zebra()->alpha()->selfDestruct();

The long chain of method calls isn't necessarily a bad thing, assuming they
each link back to a local object the calling class knows. As a fun example,
Mockery's long chains (after the first ``shouldReceive()`` method) all call to
the same instance of ``\Mockery\Expectation``. However, sometimes this is not
the case and the chain is constantly crossing object boundaries.

In either case, mocking such a chain can be a horrible task. To make it easier
Mockery supports demeter chain mocking. Essentially, we shortcut through the
chain and return a defined value from the final call. For example, let's
assume ``selfDestruct()`` returns the string "Ten!" to $object (an instance of
``CaptainsConsole``). Here's how we could mock it.

.. code-block:: php

    $mock = \Mockery::mock('CaptainsConsole');
    $mock->shouldReceive('foo->bar->zebra->alpha->selfDestruct')->andReturn('Ten!');

The above expectation can follow any previously seen format or expectation,
except that the method name is simply the string of all expected chain calls
separated by ``->``. Mockery will automatically setup the chain of expected
calls with its final return values, regardless of whatever intermediary object
might be used in the real implementation.

Arguments to all members of the chain (except the final call) are ignored in
this process.
