.. index::
    single: Getting Started; Simple Example

Simple Example
==============

Imagine we have a ``Temperature`` class which samples the temperature of a
locale before reporting an average temperature. The data could come from a web
service or any other data source, but we do not have such a class at present.
We can, however, assume some basic interactions with such a class based on its
interaction with the ``Temperature`` class:

.. code-block:: php

    class Temperature
    {
        private $service;

        public function __construct($service)
        {
            $this->service = $service;
        }

        public function average()
        {
            $total = 0;
            for ($i=0; $i<3; $i++) {
                $total += $this->service->readTemp();
            }
            return $total/3;
        }
    }

Even without an actual service class, we can see how we expect it to operate.
When writing a test for the ``Temperature`` class, we can now substitute a
mock object for the real service which allows us to test the behaviour of the
``Temperature`` class without actually needing a concrete service instance.

.. code-block:: php

    use \Mockery;

    class TemperatureTest extends \PHPUnit\Framework\TestCase
    {
        public function tearDown()
        {
            Mockery::close();
        }

        public function testGetsAverageTemperatureFromThreeServiceReadings()
        {
            $service = Mockery::mock('service');
            $service->shouldReceive('readTemp')
                ->times(3)
                ->andReturn(10, 12, 14);

            $temperature = new Temperature($service);

            $this->assertEquals(12, $temperature->average());
        }
    }

We create a mock object which our ``Temperature`` class will use and set some
expectations for that mock — that it should receive three calls to the ``readTemp``
method, and these calls will return 10, 12, and 14 as results.

.. note::

    PHPUnit integration can remove the need for a ``tearDown()`` method. See
    ":doc:`/reference/phpunit_integration`" for more information.
