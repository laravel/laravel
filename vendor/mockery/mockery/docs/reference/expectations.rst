.. index::
    single: Expectations

Expectation Declarations
========================

.. note::

    In order for our expectations to work we MUST call ``Mockery::close()``,
    preferably in a callback method such as ``tearDown`` or ``_after``
    (depending on whether or not we're integrating Mockery with another
    framework). This static call cleans up the Mockery container used by the
    current test, and run any verification tasks needed for our expectations.

Once we have created a mock object, we'll often want to start defining how
exactly it should behave (and how it should be called). This is where the
Mockery expectation declarations take over.

Declaring Method Call Expectations
----------------------------------

To tell our test double to expect a call for a method with a given name, we use
the ``shouldReceive`` method:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method');

This is the starting expectation upon which all other expectations and
constraints are appended.

We can declare more than one method call to be expected:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method_1', 'name_of_method_2');

All of these will adopt any chained expectations or constraints.

It is possible to declare the expectations for the method calls, along with
their return values:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive([
        'name_of_method_1' => 'return value 1',
        'name_of_method_2' => 'return value 2',
    ]);

There's also a shorthand way of setting up method call expectations and their
return values:

.. code-block:: php

    $mock = \Mockery::mock('MyClass', ['name_of_method_1' => 'return value 1', 'name_of_method_2' => 'return value 2']);

All of these will adopt any additional chained expectations or constraints.

We can declare that a test double should not expect a call to the given method
name:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldNotReceive('name_of_method');

This method is a convenience method for calling ``shouldReceive()->never()``.

Declaring Method Argument Expectations
--------------------------------------

For every method we declare expectation for, we can add constraints that the
defined expectations apply only to the method calls that match the expected
argument list:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->with($arg1, $arg2, ...);
    // or
    $mock->shouldReceive('name_of_method')
        ->withArgs([$arg1, $arg2, ...]);

We can add a lot more flexibility to argument matching using the built in
matcher classes (see later). For example, ``\Mockery::any()`` matches any
argument passed to that position in the ``with()`` parameter list. Mockery also
allows Hamcrest library matchers - for example, the Hamcrest function
``anything()`` is equivalent to ``\Mockery::any()``.

It's important to note that this means all expectations attached only apply to
the given method when it is called with these exact arguments:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');

    $mock->shouldReceive('foo')->with('Hello');

    $mock->foo('Goodbye'); // throws a NoMatchingExpectationException

This allows for setting up differing expectations based on the arguments
provided to expected calls.

Argument matching with closures
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Instead of providing a built-in matcher for each argument, we can provide a
closure that matches all passed arguments at once:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->withArgs(closure);

The given closure receives all the arguments passed in the call to the expected
method. In this way, this expectation only applies to method calls where passed
arguments make the closure evaluate to true:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');

    $mock->shouldReceive('foo')->withArgs(function ($arg) {
        if ($arg % 2 == 0) {
            return true;
        }
        return false;
    });

    $mock->foo(4); // matches the expectation
    $mock->foo(3); // throws a NoMatchingExpectationException

Argument matching with some of given values
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

We can provide expected arguments that match passed arguments when mocked method
is called.

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->withSomeOfArgs(arg1, arg2, arg3, ...);

The given expected arguments order doesn't matter.
Check if expected values are included or not, but type should be matched:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('foo')
        ->withSomeOfArgs(1, 2);

    $mock->foo(1, 2, 3);  // matches the expectation
    $mock->foo(3, 2, 1);  // matches the expectation (passed order doesn't matter)
    $mock->foo('1', '2'); // throws a NoMatchingExpectationException (type should be matched) 
    $mock->foo(3);        // throws a NoMatchingExpectationException 

Any, or no arguments
^^^^^^^^^^^^^^^^^^^^

We can declare that the expectation matches a method call regardless of what
arguments are passed:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->withAnyArgs();

This is set by default unless otherwise specified.

We can declare that the expectation matches method calls with zero arguments:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->withNoArgs();

Declaring Return Value Expectations
-----------------------------------

For mock objects, we can tell Mockery what return values to return from the
expected method calls.

For that we can use the ``andReturn()`` method:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andReturn($value);

This sets a value to be returned from the expected method call.

It is possible to set up expectation for multiple return values. By providing
a sequence of return values, we tell Mockery what value to return on every
subsequent call to the method:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andReturn($value1, $value2, ...)

The first call will return ``$value1`` and the second call will return ``$value2``.

If we call the method more times than the number of return values we declared,
Mockery will return the final value for any subsequent method call:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');

    $mock->shouldReceive('foo')->andReturn(1, 2, 3);

    $mock->foo(); // int(1)
    $mock->foo(); // int(2)
    $mock->foo(); // int(3)
    $mock->foo(); // int(3)

The same can be achieved using the alternative syntax:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andReturnValues([$value1, $value2, ...])

It accepts a simple array instead of a list of parameters. The order of return
is determined by the numerical index of the given array with the last array
member being returned on all calls once previous return values are exhausted.

The following two options are primarily for communication with test readers:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andReturnNull();
    // or
    $mock->shouldReceive('name_of_method')
        ->andReturn([null]);

They mark the mock object method call as returning ``null`` or nothing.

Sometimes we want to calculate the return results of the method calls, based on
the arguments passed to the method. We can do that with the ``andReturnUsing()``
method which accepts one or more closure:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andReturnUsing(closure, ...);

Closures can be queued by passing them as extra parameters as for ``andReturn()``.

Occasionally, it can be useful to echo back one of the arguments that a method
is called with. In this case we can use the ``andReturnArg()`` method; the
argument to be returned is specified by its index in the arguments list:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andReturnArg(1);

This returns the second argument (index #1) from the list of arguments when the
method is called.

.. note::

    We cannot currently mix ``andReturnUsing()`` or ``andReturnArg`` with
    ``andReturn()``.

If we are mocking fluid interfaces, the following method will be helpful:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andReturnSelf();

It sets the return value to the mocked class name.

Throwing Exceptions
-------------------

We can tell the method of mock objects to throw exceptions:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andThrow(new Exception);

It will throw the given ``Exception`` object when called.

Rather than an object, we can pass in the ``Exception`` class, message and/or code to
use when throwing an ``Exception`` from the mocked method:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andThrow('exception_name', 'message', 123456789);

.. _expectations-setting-public-properties:

Setting Public Properties
-------------------------

Used with an expectation so that when a matching method is called, we can cause
a mock object's public property to be set to a specified value, by using
``andSet()`` or ``set()``:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->andSet($property, $value);
    // or
    $mock->shouldReceive('name_of_method')
        ->set($property, $value);

In cases where we want to call the real method of the class that was mocked and
return its result, the ``passthru()`` method tells the expectation to bypass
a return queue:

.. code-block:: php

    passthru()

It allows expectation matching and call count validation to be applied against
real methods while still calling the real class method with the expected
arguments.

Declaring Call Count Expectations
---------------------------------

Besides setting expectations on the arguments of the method calls, and the
return values of those same calls, we can set expectations on how many times
should any method be called.

When a call count expectation is not met, a
``\Mockery\Expectation\InvalidCountException`` will be thrown.

.. note::

    It is absolutely required to call ``\Mockery::close()`` at the end of our
    tests, for example in the ``tearDown()`` method of PHPUnit. Otherwise
    Mockery will not verify the calls made against our mock objects.

We can declare that the expected method may be called zero or more times:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->zeroOrMoreTimes();

This is the default for all methods unless otherwise set.

To tell Mockery to expect an exact number of calls to a method, we can use the
following:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->times($n);

where ``$n`` is the number of times the method should be called.

A couple of most common cases got their shorthand methods.

To declare that the expected method must be called one time only:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->once();

To declare that the expected method must be called two times:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->twice();

To declare that the expected method must never be called:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->never();

Call count modifiers
^^^^^^^^^^^^^^^^^^^^

The call count expectations can have modifiers set.

If we want to tell Mockery the minimum number of times a method should be called,
we use ``atLeast()``:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->atLeast()
        ->times(3);

``atLeast()->times(3)`` means the call must be called at least three times
(given matching method args) but never less than three times.

Similarly, we can tell Mockery the maximum number of times a method should be
called, using ``atMost()``:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->atMost()
        ->times(3);

``atMost()->times(3)`` means the call must be called no more than three times.
If the method gets no calls at all, the expectation will still be met.

We can also set a range of call counts, using ``between()``:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('name_of_method')
        ->between($min, $max);

This is actually identical to using ``atLeast()->times($min)->atMost()->times($max)``
but is provided as a shorthand. It may be followed by a ``times()`` call with no
parameter to preserve the APIs natural language readability.

Multiple Calls with Different Expectations
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If a method is expected to get called multiple times with different arguments
and/or return values we can simply repeat the expectations. The same of course
also works if we expect multiple calls to different methods.

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    // Expectations for the 1st call
    $mock->shouldReceive('name_of_method')
        ->once()
        ->with('arg1')
        ->andReturn($value1)

        // 2nd call to same method
        ->shouldReceive('name_of_method')
        ->once()
        ->with('arg2')
        ->andReturn($value2)

        // final call to another method
        ->shouldReceive('other_method')
        ->once()
        ->with('other')
        ->andReturn($value_other);

Expectation Declaration Utilities
---------------------------------

Declares that this method is expected to be called in a specific order in
relation to similarly marked methods.

.. code-block:: php

    ordered()

The order is dictated by the order in which this modifier is actually used when
setting up mocks.

Declares the method as belonging to an order group (which can be named or
numbered). Methods within a group can be called in any order, but the ordered
calls from outside the group are ordered in relation to the group:

.. code-block:: php

    ordered(group)

We can set up so that method1 is called before group1 which is in turn called
before method2.

When called prior to ``ordered()`` or ``ordered(group)``, it declares this
ordering to apply across all mock objects (not just the current mock):

.. code-block:: php

    globally()

This allows for dictating order expectations across multiple mocks.

The ``byDefault()`` marks an expectation as a default. Default expectations are
applied unless a non-default expectation is created:

.. code-block:: php

    byDefault()

These later expectations immediately replace the previously defined default.
This is useful so we can setup default mocks in our unit test ``setup()`` and
later tweak them in specific tests as needed.

Returns the current mock object from an expectation chain:

.. code-block:: php

    getMock()

Useful where we prefer to keep mock setups as a single statement, e.g.:

.. code-block:: php

    $mock = \Mockery::mock('foo')->shouldReceive('foo')->andReturn(1)->getMock();
