.. index::
    single: Cookbook; Mocking Hard Dependencies

Mocking Hard Dependencies (new Keyword)
=======================================

One prerequisite to mock hard dependencies is that the code we are trying to test uses autoloading.

Let's take the following code for an example:

.. code-block:: php

    <?php
    namespace App;
    class Service
    {
        function callExternalService($param)
        {
            $externalService = new Service\External($version = 5);
            $externalService->sendSomething($param);
            return $externalService->getSomething();
        }
    }

The way we can test this without doing any changes to the code itself is by creating :doc:`instance mocks </reference/instance_mocking>` by using the ``overload`` prefix.

.. code-block:: php

    <?php
    namespace AppTest;
    use Mockery as m;
    class ServiceTest extends \PHPUnit_Framework_TestCase
    {
        public function testCallingExternalService()
        {
            $param = 'Testing';

            $externalMock = m::mock('overload:App\Service\External');
            $externalMock->shouldReceive('sendSomething')
                ->once()
                ->with($param);
            $externalMock->shouldReceive('getSomething')
                ->once()
                ->andReturn('Tested!');

            $service = new \App\Service();

            $result = $service->callExternalService($param);

            $this->assertSame('Tested!', $result);
        }
    }

If we run this test now, it should pass. Mockery does its job and our ``App\Service`` will use the mocked external service instead of the real one.

The problem with this is when we want to, for example, test the ``App\Service\External`` itself, or if we use that class somewhere else in our tests.

When Mockery overloads a class, because of how PHP works with files, that overloaded class file must not be included otherwise Mockery will throw a "class already exists" exception. This is where autoloading kicks in and makes our job a lot easier.

To make this possible, we'll tell PHPUnit to run the tests that have overloaded classes in separate processes and to not preserve global state. That way we'll avoid having the overloaded class included more than once. Of course this has its downsides as these tests will run slower.

Our test example from above now becomes:

.. code-block:: php

    <?php
    namespace AppTest;
    use Mockery as m;
    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class ServiceTest extends \PHPUnit_Framework_TestCase
    {
        public function testCallingExternalService()
        {
            $param = 'Testing';

            $externalMock = m::mock('overload:App\Service\External');
            $externalMock->shouldReceive('sendSomething')
                ->once()
                ->with($param);
            $externalMock->shouldReceive('getSomething')
                ->once()
                ->andReturn('Tested!');

            $service = new \App\Service();

            $result = $service->callExternalService($param);

            $this->assertSame('Tested!', $result);
        }
    }



Testing the constructor arguments of hard Dependencies
------------------------------------------------------

Sometimes we might want to ensure that the hard dependency is instantiated with
particular arguments. With overloaded mocks, we can set up expectations on the
constructor.

.. code-block:: php

    <?php
    namespace AppTest;
    use Mockery as m;
    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class ServiceTest extends \PHPUnit_Framework_TestCase
    {
        public function testCallingExternalService()
        {
            $externalMock = m::mock('overload:App\Service\External');
            $externalMock->allows('sendSomething');
            $externalMock->shouldReceive('__construct')
                ->once()
                ->with(5);

            $service = new \App\Service();
            $result = $service->callExternalService($param);
        }
    }


.. note::
    For more straightforward and single-process tests oriented way check
    :ref:`mocking-class-within-class`.

.. note::

    This cookbook entry is an adaption of the blog post titled
    `"Mocking hard dependencies with Mockery" <https://robertbasic.com/blog/mocking-hard-dependencies-with-mockery/>`_,
    published by Robert Basic on his blog.
