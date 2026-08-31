.. index::
    single: Reserved Method Names

Reserved Method Names
=====================

As you may have noticed, Mockery uses a number of methods called directly on
all mock objects, for example ``shouldReceive()``. Such methods are necessary
in order to setup expectations on the given mock, and so they cannot be
implemented on the classes or objects being mocked without creating a method
name collision (reported as a PHP fatal error). The methods reserved by
Mockery are:

* ``shouldReceive()``
* ``shouldNotReceive()``
* ``allows()``
* ``expects()``
* ``shouldAllowMockingMethod()``
* ``shouldIgnoreMissing()``
* ``asUndefined()``
* ``shouldAllowMockingProtectedMethods()``
* ``makePartial()``
* ``byDefault()``
* ``shouldHaveReceived()``
* ``shouldHaveBeenCalled()``
* ``shouldNotHaveReceived()``
* ``shouldNotHaveBeenCalled()``


In addition, all mocks utilise a set of added methods and protected properties
which cannot exist on the class or object being mocked. These are far less
likely to cause collisions. All properties are prefixed with ``_mockery`` and
all method names with ``mockery_``.
