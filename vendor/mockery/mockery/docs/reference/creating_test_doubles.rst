.. index::
    single: Reference; Creating Test Doubles

Creating Test Doubles
=====================

Mockery's main goal is to help us create test doubles. It can create stubs,
mocks, and spies.

Stubs and mocks are created the same. The difference between the two is that a
stub only returns a preset result when called, while a mock needs to have
expectations set on the method calls it expects to receive.

Spies are a type of test doubles that keep track of the calls they received, and
allow us to inspect these calls after the fact.

When creating a test double object, we can pass in an identifier as a name for
our test double. If we pass it no identifier, the test double name will be
unknown. Furthermore, the identifier does not have to be a class name. It is a
good practice, and our recommendation, to always name the test doubles with the
same name as the underlying class we are creating test doubles for.

If the identifier we use for our test double is a name of an existing class,
the test double will inherit the type of the class (via inheritance), i.e. the
mock object will pass type hints or ``instanceof`` evaluations for the existing
class. This is useful when a test double must be of a specific type, to satisfy
the expectations our code has.

Stubs and mocks
---------------

Stubs and mocks are created by calling the ``\Mockery::mock()`` method. The
following example shows how to create a stub, or a mock, object named "foo":

.. code-block:: php

    $mock = \Mockery::mock('foo');

The mock object created like this is the loosest form of mocks possible, and is
an instance of ``\Mockery\MockInterface``.

.. note::

    All test doubles created with Mockery are an instance of
    ``\Mockery\MockInterface``, regardless are they a stub, mock or a spy.

To create a stub or a mock object with no name, we can call the ``mock()``
method with no parameters:

.. code-block:: php

    $mock = \Mockery::mock();

As we stated earlier, we don't recommend creating stub or mock objects without
a name.

Classes, abstracts, interfaces
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The recommended way to create a stub or a mock object is by using a name of
an existing class we want to create a test double of:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');

This stub or mock object will have the type of ``MyClass``, through inheritance.

Stub or mock objects can be based on any concrete class, abstract class or even
an interface. The primary purpose is to ensure the mock object inherits a
specific type for type hinting.

.. code-block:: php

    $mock = \Mockery::mock('MyInterface');

This stub or mock object will implement the ``MyInterface`` interface.

.. note::

    Classes marked final, or classes that have methods marked final cannot be
    mocked fully. Mockery supports creating partial mocks for these cases.
    Partial mocks will be explained later in the documentation.

Mockery also supports creating stub or mock objects based on a single existing
class, which must implement one or more interfaces. We can do this by providing
a comma-separated list of the class and interfaces as the first argument to the
``\Mockery::mock()`` method:

.. code-block:: php

    $mock = \Mockery::mock('MyClass, MyInterface, OtherInterface');

This stub or mock object will now be of type ``MyClass`` and implement the
``MyInterface`` and ``OtherInterface`` interfaces.

.. note::

    The class name doesn't need to be the first member of the list but it's a
    friendly convention to use for readability.

We can tell a mock to implement the desired interfaces by passing the list of
interfaces as the second argument:

.. code-block:: php

    $mock = \Mockery::mock('MyClass', 'MyInterface, OtherInterface');

For all intents and purposes, this is the same as the previous example.

Spies
-----

The third type of test doubles Mockery supports are spies. The main difference
between spies and mock objects is that with spies we verify the calls made
against our test double after the calls were made. We would use a spy when we
don't necessarily care about all of the calls that are going to be made to an
object.

A spy will return ``null`` for all method calls it receives. It is not possible
to tell a spy what will be the return value of a method call. If we do that, then
we would deal with a mock object, and not with a spy.

We create a spy by calling the ``\Mockery::spy()`` method:

.. code-block:: php

    $spy = \Mockery::spy('MyClass');

Just as with stubs or mocks, we can tell Mockery to base a spy on any concrete 
or abstract class, or to implement any number of interfaces:

.. code-block:: php

    $spy = \Mockery::spy('MyClass, MyInterface, OtherInterface');

This spy will now be of type ``MyClass`` and implement the ``MyInterface`` and
``OtherInterface`` interfaces.

.. note::

    The ``\Mockery::spy()`` method call is actually a shorthand for calling
    ``\Mockery::mock()->shouldIgnoreMissing()``. The ``shouldIgnoreMissing``
    method is a "behaviour modifier". We'll discuss them a bit later.

Mocks vs. Spies
---------------

Let's try and illustrate the difference between mocks and spies with the
following example:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $spy = \Mockery::spy('MyClass');

    $mock->shouldReceive('foo')->andReturn(42);

    $mockResult = $mock->foo();
    $spyResult = $spy->foo();

    $spy->shouldHaveReceived()->foo();

    var_dump($mockResult); // int(42)
    var_dump($spyResult); // null

As we can see from this example, with a mock object we set the call expectations
before the call itself, and we get the return result we expect it to return.
With a spy object on the other hand, we verify the call has happened after the
fact. The return result of a method call against a spy is always ``null``.

We also have a dedicated chapter to :doc:`spies` only.

.. _creating-test-doubles-partial-test-doubles:

Partial Test Doubles
--------------------

Partial doubles are useful when we want to stub out, set expectations for, or
spy on *some* methods of a class, but run the actual code for other methods.

We differentiate between three types of partial test doubles:

 * runtime partial test doubles,
 * generated partial test doubles, and
 * proxied partial test doubles.

Runtime partial test doubles
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

What we call a runtime partial, involves creating a test double and then telling
it to make itself partial. Any method calls that the double hasn't been told to
allow or expect, will act as they would on a normal instance of the object.

.. code-block:: php

    class Foo {
        function foo() { return 123; }
        function bar() { return $this->foo(); }
    }

    $foo = mock(Foo::class)->makePartial();
    $foo->foo(); // int(123);

We can then tell the test double to allow or expect calls as with any other
Mockery double.

.. code-block:: php

    $foo->shouldReceive('foo')->andReturn(456);
    $foo->bar(); // int(456)

See the cookbook entry on :doc:`../cookbook/big_parent_class` for an example
usage of runtime partial test doubles.

Generated partial test doubles
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The second type of partial double we can create is what we call a generated
partial. With generated partials, we specifically tell Mockery which methods
we want to be able to allow or expect calls to. All other methods will run the
actual code *directly*, so stubs and expectations on these methods will not
work.

.. code-block:: php

    class Foo {
        function foo() { return 123; }
        function bar() { return $this->foo(); }
    }

    $foo = mock("Foo[foo]");

    $foo->foo(); // error, no expectation set

    $foo->shouldReceive('foo')->andReturn(456);
    $foo->foo(); // int(456)

    // setting an expectation for this has no effect
    $foo->shouldReceive('bar')->andReturn(999);
    $foo->bar(); // int(456)

It's also possible to specify explicitly which methods to run directly using
the `!method` syntax:

.. code-block:: php

    class Foo {
        function foo() { return 123; }
        function bar() { return $this->foo(); }
    }

    $foo = mock("Foo[!foo]");

    $foo->foo(); // int(123)

    $foo->bar(); // error, no expectation set

.. note::

    Even though we support generated partial test doubles, we do not recommend
    using them.

    One of the reasons why is because a generated partial will call the original
    constructor of the mocked class. This can have unwanted side-effects during
    testing application code.

    See :doc:`../cookbook/not_calling_the_constructor` for more details.

Proxied partial test doubles
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

A proxied partial mock is a partial of last resort. We may encounter a class
which is simply not capable of being mocked because it has been marked as
final. Similarly, we may find a class with methods marked as final. In such a
scenario, we cannot simply extend the class and override methods to mock - we
need to get creative.

.. code-block:: php

    $mock = \Mockery::mock(new MyClass);

Yes, the new mock is a Proxy. It intercepts calls and reroutes them to the
proxied object (which we construct and pass in) for methods which are not
subject to any expectations. Indirectly, this allows us to mock methods
marked final since the Proxy is not subject to those limitations. The tradeoff
should be obvious - a proxied partial will fail any typehint checks for the
class being mocked since it cannot extend that class.

.. _creating-test-doubles-aliasing:

Aliasing
--------

Prefixing the valid name of a class (which is NOT currently loaded) with
"alias:" will generate an "alias mock". Alias mocks create a class alias with
the given classname to stdClass and are generally used to enable the mocking
of public static methods. Expectations set on the new mock object which refer
to static methods will be used by all static calls to this class.

.. code-block:: php

    $mock = \Mockery::mock('alias:MyClass');


.. note::

    Even though aliasing classes is supported, we do not recommend it.

Overloading
-----------

Prefixing the valid name of a class (which is NOT currently loaded) with
"overload:" will generate an alias mock (as with "alias:") except that created
new instances of that class will import any expectations set on the origin
mock (``$mock``). The origin mock is never verified since it's used an
expectation store for new instances. For this purpose we use the term "instance
mock" to differentiate it from the simpler "alias mock".

In other words, an instance mock will "intercept" when a new instance of the
mocked class is created, then the mock will be used instead. This is useful
especially when mocking hard dependencies which will be discussed later.

.. code-block:: php

    $mock = \Mockery::mock('overload:MyClass');

.. note::

    Using alias/instance mocks across more than one test will generate a fatal
    error since we can't have two classes of the same name. To avoid this,
    run each test of this kind in a separate PHP process (which is supported
    out of the box by both PHPUnit and PHPT).


.. _creating-test-doubles-named-mocks:

Named Mocks
-----------

The ``namedMock()`` method will generate a class called by the first argument,
so in this example ``MyClassName``. The rest of the arguments are treated in the
same way as the ``mock`` method:

.. code-block:: php

    $mock = \Mockery::namedMock('MyClassName', 'DateTime');

This example would create a class called ``MyClassName`` that extends
``DateTime``.

Named mocks are quite an edge case, but they can be useful when code depends
on the ``__CLASS__`` magic constant, or when we need two derivatives of an
abstract type, that are actually different classes.

See the cookbook entry on :doc:`../cookbook/class_constants` for an example
usage of named mocks.

.. note::

    We can only create a named mock once, any subsequent calls to
    ``namedMock``, with different arguments are likely to cause exceptions.

.. _creating-test-doubles-constructor-arguments:

Constructor Arguments
---------------------

Sometimes the mocked class has required constructor arguments. We can pass these
to Mockery as an indexed array, as the 2nd argument:

.. code-block:: php

    $mock = \Mockery::mock('MyClass', [$constructorArg1, $constructorArg2]);

or if we need the ``MyClass`` to implement an interface as well, as the 3rd
argument:

.. code-block:: php

    $mock = \Mockery::mock('MyClass', 'MyInterface', [$constructorArg1, $constructorArg2]);

Mockery now knows to pass in ``$constructorArg1`` and ``$constructorArg2`` as
arguments to the constructor.

.. _creating-test-doubles-behavior-modifiers:

Behavior Modifiers
------------------

When creating a mock object, we may wish to use some commonly preferred
behaviours that are not the default in Mockery.

The use of the ``shouldIgnoreMissing()`` behaviour modifier will label this
mock object as a Passive Mock:

.. code-block:: php

    \Mockery::mock('MyClass')->shouldIgnoreMissing();

In such a mock object, calls to methods which are not covered by expectations
will return ``null`` instead of the usual error about there being no expectation
matching the call.

On PHP >= 7.0.0, methods with missing expectations that have a return type
will return either a mock of the object (if return type is a class) or a
"falsy" primitive value, e.g. empty string, empty array, zero for ints and
floats, false for bools, or empty closures.

On PHP >= 7.1.0, methods with missing expectations and nullable return type
will return null.

We can optionally prefer to return an object of type ``\Mockery\Undefined``
(i.e.  a ``null`` object) (which was the 0.7.2 behaviour) by using an
additional modifier:

.. code-block:: php

    \Mockery::mock('MyClass')->shouldIgnoreMissing()->asUndefined();

The returned object is nothing more than a placeholder so if, by some act of
fate, it's erroneously used somewhere it shouldn't, it will likely not pass a
logic check.

We have encountered the ``makePartial()`` method before, as it is the method we
use to create runtime partial test doubles:

.. code-block:: php

    \Mockery::mock('MyClass')->makePartial();

This form of mock object will defer all methods not subject to an expectation to
the parent class of the mock, i.e. ``MyClass``. Whereas the previous
``shouldIgnoreMissing()`` returned ``null``, this behaviour simply calls the
parent's matching method.
