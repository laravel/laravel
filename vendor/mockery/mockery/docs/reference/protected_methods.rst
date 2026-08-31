.. index::
    single: Mocking; Protected Methods

Mocking Protected Methods
=========================

By default, Mockery does not allow mocking protected methods. We do not recommend
mocking protected methods, but there are cases when there is no other solution.

For those cases we have the ``shouldAllowMockingProtectedMethods()`` method. It
instructs Mockery to specifically allow mocking of protected methods, for that
one class only:

.. code-block:: php

    class MyClass
    {
        protected function foo()
        {
        }
    }

    $mock = \Mockery::mock('MyClass')
        ->shouldAllowMockingProtectedMethods();
    $mock->shouldReceive('foo');

