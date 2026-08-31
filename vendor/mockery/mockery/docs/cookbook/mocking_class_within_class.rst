.. index::
    single: Cookbook; Mocking class within class

.. _mocking-class-within-class:

Mocking class within class
==========================

Imagine a case where you need to create an instance of a class and use it
within the same method:

.. code-block:: php

    // Point.php
    <?php
    namespace App;

    class Point {
        public function setPoint($x, $y) {
            echo "Point (" . $x . ", " . $y . ")" . PHP_EOL;
        }
    }

    // Rectangle.php
    <?php
    namespace App;
    use App\Point;

    class Rectangle {
        public function create($x1, $y1, $x2, $y2) {
            $a = new Point();
            $a->setPoint($x1, $y1);

            $b = new Point();
            $b->setPoint($x2, $y1);

            $c = new Point();
            $c->setPoint($x2, $y2);

            $d = new Point();
            $d->setPoint($x1, $y2);

            $this->draw([$a, $b, $c, $d]);
        }

        public function draw($points) {
            echo "Do something with the points";
        }
    }

And that you want to test that a logic in ``Rectangle->create()`` calls
properly each used thing - in this case calls ``Point->setPoint()``, but
``Rectangle->draw()`` does some graphical stuff that you want to avoid calling.

You set the mocks for ``App\Point`` and ``App\Rectangle``:

.. code-block:: php

    <?php
    class MyTest extends PHPUnit\Framework\TestCase {
        public function testCreate() {
            $point = Mockery::mock("App\Point");
            // check if our mock is called
            $point->shouldReceive("setPoint")->andThrow(Exception::class);

            $rect = Mockery::mock("App\Rectangle")->makePartial();
            $rect->shouldReceive("draw");

            $rect->create(0, 0, 100, 100);  // does not throw exception
            Mockery::close();
        }
    }

and the test does not work. Why? The mocking relies on the class not being
present yet, but the class is autoloaded therefore the mock alone for
``App\Point`` is useless which you can see with ``echo`` being executed.

Mocks however work for the first class in the order of loading i.e.
``App\Rectangle``, which loads the ``App\Point`` class. In more complex example
that would be a single point that initiates the whole loading (``use Class``)
such as::

    A        // main loading initiator
    |- B     // another loading initiator
    |  |-E
    |  +-G
    |
    |- C     // another loading initiator
    |  +-F
    |
    +- D

That basically means that the loading prevents mocking and for each such
a loading initiator there needs to be implemented a workaround.
Overloading is one approach, however it pollutes the global state. In this case
we try to completely avoid the global state pollution with custom
``new Class()`` behavior per loading initiator and that can be mocked easily
in few critical places.

That being said, although we can't stop loading, we can return mocks. Let's
look at ``Rectangle->create()`` method:

.. code-block:: php

    class Rectangle {
        public function newPoint() {
            return new Point();
        }

        public function create($x1, $y1, $x2, $y2) {
            $a = $this->newPoint();
            $a->setPoint($x1, $y1);
            ...
        }
        ...
    }

We create a custom function to encapsulate ``new`` keyword that would otherwise
just use the autoloaded class ``App\Point`` and in our test we mock that function
so that it returns our mock:

.. code-block:: php

    <?php
    class MyTest extends PHPUnit\Framework\TestCase {
        public function testCreate() {
            $point = Mockery::mock("App\Point");
            // check if our mock is called
            $point->shouldReceive("setPoint")->andThrow(Exception::class);

            $rect = Mockery::mock("App\Rectangle")->makePartial();
            $rect->shouldReceive("draw");

            // pass the App\Point mock into App\Rectangle as an alternative
            // to using new App\Point() in-place.
            $rect->shouldReceive("newPoint")->andReturn($point);

            $this->expectException(Exception::class);
            $rect->create(0, 0, 100, 100);
            Mockery::close();
        }
    }

If we run this test now, it should pass. For more complex cases we'd find
the next loader in the program flow and proceed with wrapping and passing
mock instances with predefined behavior into already existing classes.
