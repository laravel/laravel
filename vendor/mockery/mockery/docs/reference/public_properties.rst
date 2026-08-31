.. index::
    single: Mocking; Public Properties

Mocking Public Properties
=========================

Mockery allows us to mock properties in several ways. One way is that we can set
a public property and its value on any mock object. The second is that we can
use the expectation methods ``set()`` and ``andSet()`` to set property values if
that expectation is ever met.

You can read more about :ref:`expectations-setting-public-properties`.

.. note::

    In general, Mockery does not support mocking any magic methods since these
    are generally not considered a public API (and besides it is a bit difficult
    to differentiate them when you badly need them for mocking!). So please mock
    virtual properties (those relying on ``__get()`` and ``__set()``) as if they
    were actually declared on the class.
