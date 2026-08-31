.. index::
    single: Quick Reference

Quick Reference
===============

The purpose of this page is to give a quick and short overview of some of the
most common Mockery features.

Do read the :doc:`../reference/index` to learn about all the Mockery features.

Integrate Mockery with PHPUnit, either by extending the ``MockeryTestCase``:

.. code-block:: php

    use \Mockery\Adapter\Phpunit\MockeryTestCase;

    class MyTest extends MockeryTestCase
    {
    }

or by using the ``MockeryPHPUnitIntegration`` trait:

.. code-block:: php

    use \PHPUnit\Framework\TestCase;
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    class MyTest extends TestCase
    {
        use MockeryPHPUnitIntegration;
    }

Creating a test double:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass');

Creating a test double that implements a certain interface:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass, MyInterface');

Expecting a method to be called on a test double:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass');
    $testDouble->shouldReceive('foo');

Expecting a method to **not** be called on a test double:

.. code-block:: php

    $testDouble = \Mockery::mock('MyClass');
    $testDouble->shouldNotReceive('foo');

Expecting a method to be called on a test double, once, with a certain argument,
and to return a value:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('foo')
        ->once()
        ->with($arg)
        ->andReturn($returnValue);

Expecting a method to be called on a test double and to return a different value
for each successive call:

.. code-block:: php

    $mock = \Mockery::mock('MyClass');
    $mock->shouldReceive('foo')
        ->andReturn(1, 2, 3);

    $mock->foo(); // int(1);
    $mock->foo(); // int(2);
    $mock->foo(); // int(3);
    $mock->foo(); // int(3);

Creating a runtime partial test double:

.. code-block:: php

    $mock = \Mockery::mock('MyClass')->makePartial();

Creating a spy:

.. code-block:: php

    $spy = \Mockery::spy('MyClass');

Expecting that a spy should have received a method call:

.. code-block:: php

    $spy = \Mockery::spy('MyClass');

    $spy->foo();

    $spy->shouldHaveReceived()->foo();

Not so simple examples
^^^^^^^^^^^^^^^^^^^^^^

Creating a mock object to return a sequence of values from a set of method
calls:

.. code-block:: php

    use \Mockery\Adapter\Phpunit\MockeryTestCase;

    class SimpleTest extends MockeryTestCase
    {
        public function testSimpleMock()
        {
            $mock = \Mockery::mock(array('pi' => 3.1416, 'e' => 2.71));
            $this->assertEquals(3.1416, $mock->pi());
            $this->assertEquals(2.71, $mock->e());
        }
    }

Creating a mock object which returns a self-chaining Undefined object for a
method call:

.. code-block:: php

    use \Mockery\Adapter\Phpunit\MockeryTestCase;

    class UndefinedTest extends MockeryTestCase
    {
        public function testUndefinedValues()
        {
            $mock = \Mockery::mock('mymock');
            $mock->shouldReceive('divideBy')->with(0)->andReturnUndefined();
            $this->assertTrue($mock->divideBy(0) instanceof \Mockery\Undefined);
        }
    }

Creating a mock object with multiple query calls and a single update call:

.. code-block:: php

    use \Mockery\Adapter\Phpunit\MockeryTestCase;

    class DbTest extends MockeryTestCase
    {
        public function testDbAdapter()
        {
            $mock = \Mockery::mock('db');
            $mock->shouldReceive('query')->andReturn(1, 2, 3);
            $mock->shouldReceive('update')->with(5)->andReturn(NULL)->once();

            // ... test code here using the mock
        }
    }

Expecting all queries to be executed before any updates:

.. code-block:: php

    use \Mockery\Adapter\Phpunit\MockeryTestCase;

    class DbTest extends MockeryTestCase
    {
        public function testQueryAndUpdateOrder()
        {
            $mock = \Mockery::mock('db');
            $mock->shouldReceive('query')->andReturn(1, 2, 3)->ordered();
            $mock->shouldReceive('update')->andReturn(NULL)->once()->ordered();

            // ... test code here using the mock
        }
    }

Creating a mock object where all queries occur after startup, but before finish,
and where queries are expected with several different params:

.. code-block:: php

    use \Mockery\Adapter\Phpunit\MockeryTestCase;

    class DbTest extends MockeryTestCase
    {
        public function testOrderedQueries()
        {
            $db = \Mockery::mock('db');
            $db->shouldReceive('startup')->once()->ordered();
            $db->shouldReceive('query')->with('CPWR')->andReturn(12.3)->once()->ordered('queries');
            $db->shouldReceive('query')->with('MSFT')->andReturn(10.0)->once()->ordered('queries');
            $db->shouldReceive('query')->with(\Mockery::pattern("/^....$/"))->andReturn(3.3)->atLeast()->once()->ordered('queries');
            $db->shouldReceive('finish')->once()->ordered();

            // ... test code here using the mock
        }
    }
