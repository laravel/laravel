.. index::
    single: Pass-By-Reference Method Parameter Behaviour

Preserving Pass-By-Reference Method Parameter Behaviour
=======================================================

PHP Class method may accept parameters by reference. In this case, changes
made to the parameter (a reference to the original variable passed to the
method) are reflected in the original variable. An example:

.. code-block:: php

    class Foo
    {

        public function bar(&$a)
        {
            $a++;
        }

    }

    $baz = 1;
    $foo = new Foo;
    $foo->bar($baz);

    echo $baz; // will echo the integer 2

In the example above, the variable ``$baz`` is passed by reference to
``Foo::bar()`` (notice the ``&`` symbol in front of the parameter?).  Any
change ``bar()`` makes to the parameter reference is reflected in the original
variable, ``$baz``.

Mockery handles references correctly for all methods where it can analyse
the parameter (using ``Reflection``) to see if it is passed by reference. To
mock how a reference is manipulated by the class method, we can use a closure
argument matcher to manipulate it, i.e. ``\Mockery::on()`` - see the
:ref:`argument-validation-complex-argument-validation` chapter.

There is an exception for internal PHP classes where Mockery cannot analyse
method parameters using ``Reflection`` (a limitation in PHP). To work around
this, we can explicitly declare method parameters for an internal class using
``\Mockery\Configuration::setInternalClassMethodParamMap()``.

Here's an example using ``MongoCollection::insert()``. ``MongoCollection`` is
an internal class offered by the mongo extension from PECL. Its ``insert()``
method accepts an array of data as the first parameter, and an optional
options array as the second parameter. The original data array is updated
(i.e. when a ``insert()`` pass-by-reference parameter) to include a new
``_id`` field. We can mock this behaviour using a configured parameter map (to
tell Mockery to expect a pass by reference parameter) and a ``Closure``
attached to the expected method parameter to be updated.

Here's a PHPUnit unit test verifying that this pass-by-reference behaviour is
preserved:

.. code-block:: php

    public function testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs()
    {
        \Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'MongoCollection',
            'insert',
            array('&$data', '$options = array()')
        );
        $m = \Mockery::mock('MongoCollection');
        $m->shouldReceive('insert')->with(
            \Mockery::on(function(&$data) {
                if (!is_array($data)) return false;
                $data['_id'] = 123;
                return true;
            }),
            \Mockery::any()
        );

        $data = array('a'=>1,'b'=>2);
        $m->insert($data);

        $this->assertTrue(isset($data['_id']));
        $this->assertEquals(123, $data['_id']);

        \Mockery::resetContainer();
    }

Protected Methods
-----------------

When dealing with protected methods, and trying to preserve pass by reference
behavior for them, a different approach is required.

.. code-block:: php

    class Model
    {
        public function test(&$data)
        {
            return $this->doTest($data);
        }

        protected function doTest(&$data)
        {
            $data['something'] = 'wrong';
            return $this;
        }
    }

    class Test extends \PHPUnit\Framework\TestCase
    {
        public function testModel()
        {
            $mock = \Mockery::mock('Model[test]')->shouldAllowMockingProtectedMethods();

            $mock->shouldReceive('test')
                ->with(\Mockery::on(function(&$data) {
                    $data['something'] = 'wrong';
                    return true;
                }));

            $data = array('foo' => 'bar');

            $mock->test($data);
            $this->assertTrue(isset($data['something']));
            $this->assertEquals('wrong', $data['something']);
        }
    }

This is quite an edge case, so we need to change the original code a little bit,
by creating a public method that will call our protected method, and then mock
that, instead of the protected method. This new public method will act as a
proxy to our protected method.
