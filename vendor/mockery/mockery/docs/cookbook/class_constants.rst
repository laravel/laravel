.. index::
    single: Cookbook; Class Constants

Class Constants
===============

When creating a test double for a class, Mockery does not create stubs out of
any class constants defined in the class we are mocking. Sometimes though, the
non-existence of these class constants, setup of the test, and the application
code itself, it can lead to undesired behavior, and even a PHP error:
``PHP Fatal error:  Uncaught Error: Undefined class constant 'FOO' in ...``

While supporting class constants in Mockery would be possible, it does require
an awful lot of work, for a small number of use cases.

Named Mocks
-----------

We can, however, deal with these constants in a way supported by Mockery - by
using :ref:`creating-test-doubles-named-mocks`.

A named mock is a test double that has a name of the class we want to mock, but
under it is a stubbed out class that mimics the real class with canned responses.

Lets look at the following made up, but not impossible scenario:

.. code-block:: php

    class Fetcher
    {
        const SUCCESS = 0;
        const FAILURE = 1;

        public static function fetch()
        {
            // Fetcher gets something for us from somewhere...
            return self::SUCCESS;
        }
    }

    class MyClass
    {
        public function doFetching()
        {
            $response = Fetcher::fetch();

            if ($response == Fetcher::SUCCESS) {
                echo "Thanks!" . PHP_EOL;
            } else {
                echo "Try again!" . PHP_EOL;
            }
        }
    }

Our ``MyClass`` calls a ``Fetcher`` that fetches some resource from somewhere -
maybe it downloads a file from a remote web service. Our ``MyClass`` prints out
a response message depending on the response from the ``Fetcher::fetch()`` call.

When testing ``MyClass`` we don't really want ``Fetcher`` to go and download
random stuff from the internet every time we run our test suite. So we mock it
out:

.. code-block:: php

    // Using alias: because fetch is called statically!
    \Mockery::mock('alias:Fetcher')
        ->shouldReceive('fetch')
        ->andReturn(0);

    $myClass = new MyClass();
    $myClass->doFetching();

If we run this, our test will error out with a nasty
``PHP Fatal error:  Uncaught Error: Undefined class constant 'SUCCESS' in ..``.

Here's how a ``namedMock()`` can help us in a situation like this.

We create a stub for the ``Fetcher`` class, stubbing out the class constants,
and then use ``namedMock()`` to create a mock named ``Fetcher`` based on our
stub:

.. code-block:: php

    class FetcherStub
    {
        const SUCCESS = 0;
        const FAILURE = 1;
    }

    \Mockery::namedMock('Fetcher', 'FetcherStub')
        ->shouldReceive('fetch')
        ->andReturn(0);

    $myClass = new MyClass();
    $myClass->doFetching();

This works because under the hood, Mockery creates a class called ``Fetcher``
that extends ``FetcherStub``.

The same approach will work even if ``Fetcher::fetch()`` is not a static
dependency:

.. code-block:: php

    class Fetcher
    {
        const SUCCESS = 0;
        const FAILURE = 1;

        public function fetch()
        {
            // Fetcher gets something for us from somewhere...
            return self::SUCCESS;
        }
    }

    class MyClass
    {
        public function doFetching($fetcher)
        {
            $response = $fetcher->fetch();

            if ($response == Fetcher::SUCCESS) {
                echo "Thanks!" . PHP_EOL;
            } else {
                echo "Try again!" . PHP_EOL;
            }
        }
    }

And the test will have something like this:

.. code-block:: php

    class FetcherStub
    {
        const SUCCESS = 0;
        const FAILURE = 1;
    }

    $mock = \Mockery::mock('Fetcher', 'FetcherStub')
    $mock->shouldReceive('fetch')
        ->andReturn(0);

    $myClass = new MyClass();
    $myClass->doFetching($mock);


Constants Map
-------------

Another way of mocking class constants can be with the use of the constants map configuration.

Given a class with constants:

.. code-block:: php

    class Fetcher
    {
        const SUCCESS = 0;
        const FAILURE = 1;

        public function fetch()
        {
            // Fetcher gets something for us from somewhere...
            return self::SUCCESS;
        }
    }

It can be mocked with:

.. code-block:: php

    \Mockery::getConfiguration()->setConstantsMap([
        'Fetcher' => [
            'SUCCESS' => 'success',
            'FAILURE' => 'fail',
        ]
    ]);

    $mock = \Mockery::mock('Fetcher');
    var_dump($mock::SUCCESS); // (string) 'success'
    var_dump($mock::FAILURE); // (string) 'fail'
