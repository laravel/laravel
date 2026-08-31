.. index::
    single: Mocking; Partial Mocks

Creating Partial Mocks
======================

Partial mocks are useful when we only need to mock several methods of an
object leaving the remainder free to respond to calls normally (i.e.  as
implemented). Mockery implements three distinct strategies for creating
partials. Each has specific advantages and disadvantages so which strategy we
use will depend on our own preferences and the source code in need of
mocking.

We have previously talked a bit about :ref:`creating-test-doubles-partial-test-doubles`,
but we'd like to expand on the subject a bit here.

#. Runtime partial test doubles
#. Generated partial test doubles
#. Proxied Partial Mock

Runtime partial test doubles
----------------------------

A runtime partial test double, also known as a passive partial mock, is a kind
of a default state of being for a mocked object.

.. code-block:: php

    $mock = \Mockery::mock('MyClass')->makePartial();

With a runtime partial, we assume that all methods will simply defer to the
parent class (``MyClass``) original methods unless a method call matches a
known expectation. If we have no matching expectation for a specific method
call, that call is deferred to the class being mocked. Since the division
between mocked and unmocked calls depends entirely on the expectations we
define, there is no need to define which methods to mock in advance.

See the cookbook entry on :doc:`../cookbook/big_parent_class` for an example
usage of runtime partial test doubles.

Generated Partial Test Doubles
------------------------------

A generated partial test double, also known as a traditional partial mock,
defines ahead of time which methods of a class are to be mocked and which are
to be left unmocked (i.e. callable as normal). The syntax for creating
traditional mocks is:

.. code-block:: php

    $mock = \Mockery::mock('MyClass[foo,bar]');

In the above example, the ``foo()`` and ``bar()`` methods of MyClass will be
mocked but no other MyClass methods are touched. We will need to define
expectations for the ``foo()`` and ``bar()`` methods to dictate their mocked
behaviour.

Don't forget that we can pass in constructor arguments since unmocked methods
may rely on those!

.. code-block:: php

    $mock = \Mockery::mock('MyNamespace\MyClass[foo]', array($arg1, $arg2));

See the :ref:`creating-test-doubles-constructor-arguments` section to read up
on them.

.. note::

    Even though we support generated partial test doubles, we do not recommend
    using them.

Proxied Partial Mock
--------------------

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

Special Internal Cases
----------------------

All mock objects, with the exception of Proxied Partials, allows us to make
any expectation call to the underlying real class method using the ``passthru()``
expectation call. This will return values from the real call and bypass any
mocked return queue (which can simply be omitted obviously).

There is a fourth kind of partial mock reserved for internal use. This is
automatically generated when we attempt to mock a class containing methods
marked final. Since we cannot override such methods, they are simply left
unmocked. Typically, we don't need to worry about this but if we really
really must mock a final method, the only possible means is through a Proxied
Partial Mock. SplFileInfo, for example, is a common class subject to this form
of automatic internal partial since it contains public final methods used
internally.
