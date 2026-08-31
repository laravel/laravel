.. index::
    single: Cookbook; Not Calling the Original Constructor

Not Calling the Original Constructor
====================================

When creating generated partial test doubles, Mockery mocks out only the method
which we specifically told it to. This means that the original constructor of
the class we are mocking will be called.

In some cases this is not a desired behavior, as the constructor might issue
calls to other methods, or other object collaborators, and as such, can create
undesired side-effects in the application's environment when running the tests.

If this happens, we need to use runtime partial test doubles, as they don't
call the original constructor.

.. code-block:: php

    class MyClass
    {
        public function __construct()
        {
            echo "Original constructor called." . PHP_EOL;
            // Other side-effects can happen...
        }
    }

    // This will print "Original constructor called."
    $mock = \Mockery::mock('MyClass[foo]');

A better approach is to use runtime partial doubles:

.. code-block:: php

    class MyClass
    {
        public function __construct()
        {
            echo "Original constructor called." . PHP_EOL;
            // Other side-effects can happen...
        }
    }

    // This will not print anything
    $mock = \Mockery::mock('MyClass')->makePartial();
    $mock->shouldReceive('foo');

This is one of the reason why we don't recommend using generated partial test
doubles, but if possible, always use the runtime partials.

Read more about :ref:`creating-test-doubles-partial-test-doubles`.

.. note::

    The way generated partial test doubles work, is a BC break. If you use a
    really old version of Mockery, it might behave in a way that the constructor
    is not being called for these generated partials. In the case if you upgrade
    to a more recent version of Mockery, you'll probably have to change your
    tests to use runtime partials, instead of generated ones.

    This change was introduced in early 2013, so it is highly unlikely that you
    are using a Mockery from before that, so this should not be an issue.
