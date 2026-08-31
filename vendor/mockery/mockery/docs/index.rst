Mockery
=======

Mockery is a simple yet flexible PHP mock object framework for use in unit
testing with PHPUnit, PHPSpec or any other testing framework. Its core goal is
to offer a test double framework with a succinct API capable of clearly
defining all possible object operations and interactions using a human
readable Domain Specific Language (DSL). Designed as a drop in alternative to
PHPUnit's phpunit-mock-objects library, Mockery is easy to integrate with
PHPUnit and can operate alongside phpunit-mock-objects without the World
ending.

Mock Objects
------------

In unit tests, mock objects simulate the behaviour of real objects. They are
commonly utilised to offer test isolation, to stand in for objects which do
not yet exist, or to allow for the exploratory design of class APIs without
requiring actual implementation up front.

The benefits of a mock object framework are to allow for the flexible
generation of such mock objects (and stubs). They allow the setting of
expected method calls and return values using a flexible API which is capable
of capturing every possible real object behaviour in way that is stated as
close as possible to a natural language description.

Getting Started
---------------

Ready to dive into the Mockery framework? Then you can get started by reading
the "Getting Started" section!

.. toctree::
    :hidden:

    getting_started/index

.. include:: getting_started/map.rst.inc

Reference
---------

The reference contains a complete overview of all features of the Mockery
framework.

.. toctree::
    :hidden:

    reference/index

.. include:: reference/map.rst.inc

Mockery
-------

Learn about Mockery's configuration, reserved method names, exceptions...

.. toctree::
    :hidden:

    mockery/index

.. include:: mockery/map.rst.inc

Cookbook
--------

Want to learn some easy tips and tricks? Take a look at the cookbook articles!

.. toctree::
    :hidden:

    cookbook/index

.. include:: cookbook/map.rst.inc

