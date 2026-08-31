.. index::
    single: Reference; Spies

Spies
=====

Spies are a type of test doubles, but they differ from stubs or mocks in that,
that the spies record any interaction between the spy and the System Under Test
(SUT), and allow us to make assertions against those interactions after the fact.

Creating a spy means we don't have to set up expectations for every method call
the double might receive during the test, some of which may not be relevant to
the current test. A spy allows us to make assertions about the calls we care
about for this test only, reducing the chances of over-specification and making
our tests more clear.

Spies also allow us to follow the more familiar Arrange-Act-Assert or
Given-When-Then style within our tests. With mocks, we have to follow a less
familiar style, something along the lines of Arrange-Expect-Act-Assert, where
we have to tell our mocks what to expect before we act on the SUT, then assert
that those expectations were met:

.. code-block:: php

    // arrange
    $mock = \Mockery::mock('MyDependency');
    $sut = new MyClass($mock);

    // expect
    $mock->shouldReceive('foo')
        ->once()
        ->with('bar');

    // act
    $sut->callFoo();

    // assert
    \Mockery::close();

Spies allow us to skip the expect part and move the assertion to after we have
acted on the SUT, usually making our tests more readable:

.. code-block:: php

    // arrange
    $spy = \Mockery::spy('MyDependency');
    $sut = new MyClass($spy);

    // act
    $sut->callFoo();

    // assert
    $spy->shouldHaveReceived()
        ->foo()
        ->with('bar');

On the other hand, spies are far less restrictive than mocks, meaning tests are
usually less precise, as they let us get away with more. This is usually a
good thing, they should only be as precise as they need to be, but while spies
make our tests more intent-revealing, they do tend to reveal less about the
design of the SUT. If we're having to setup lots of expectations for a mock,
in lots of different tests, our tests are trying to tell us something - the SUT
is doing too much and probably should be refactored. We don't get this with
spies, they simply ignore the calls that aren't relevant to them.

Another downside to using spies is debugging. When a mock receives a call that
it wasn't expecting, it immediately throws an exception (failing fast), giving
us a nice stack trace or possibly even invoking our debugger.  With spies, we're
simply asserting calls were made after the fact, so if the wrong calls were made,
we don't have quite the same just in time context we have with the mocks.

Finally, if we need to define a return value for our test double, we can't do
that with a spy, only with a mock object.

.. note::

    This documentation page is an adaption of the blog post titled
    `"Mockery Spies" <https://davedevelopment.co.uk/2014/10/09/mockery-spies.html>`_,
    published by Dave Marshall on his blog. Dave is the original author of spies
    in Mockery.

Spies Reference
---------------

To verify that a method was called on a spy, we use the ``shouldHaveReceived()``
method:

.. code-block:: php

    $spy->shouldHaveReceived('foo');

To verify that a method was **not** called on a spy, we use the
``shouldNotHaveReceived()`` method:

.. code-block:: php

    $spy->shouldNotHaveReceived('foo');

We can also do argument matching with spies:

.. code-block:: php

    $spy->shouldHaveReceived('foo')
        ->with('bar');

Argument matching is also possible by passing in an array of arguments to
match:

.. code-block:: php

    $spy->shouldHaveReceived('foo', ['bar']);

Although when verifying a method was not called, the argument matching can only
be done by supplying the array of arguments as the 2nd argument to the
``shouldNotHaveReceived()`` method:

.. code-block:: php

    $spy->shouldNotHaveReceived('foo', ['bar']);

This is due to Mockery's internals.

Finally, when expecting calls that should have been received, we can also verify
the number of calls:

.. code-block:: php

    $spy->shouldHaveReceived('foo')
        ->with('bar')
        ->twice();

Alternative shouldReceive syntax
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

As of Mockery 1.0.0, we support calling methods as we would call any PHP method,
and not as string arguments to Mockery ``should*`` methods.

In cases of spies, this only applies to the ``shouldHaveReceived()`` method:

.. code-block:: php

    $spy->shouldHaveReceived()
        ->foo('bar');

We can set expectation on number of calls as well:

.. code-block:: php

    $spy->shouldHaveReceived()
        ->foo('bar')
        ->twice();

Unfortunately, due to limitations we can't support the same syntax for the
``shouldNotHaveReceived()`` method.
