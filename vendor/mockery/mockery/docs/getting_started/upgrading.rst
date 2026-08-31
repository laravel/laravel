.. index::
    single: Upgrading

Upgrading
=========

Upgrading to 1.0.0
------------------

Minimum PHP version
+++++++++++++++++++

As of Mockery 1.0.0 the minimum PHP version required is 5.6.

Using Mockery with PHPUnit
++++++++++++++++++++++++++

In the "old days", 0.9.x and older, the way Mockery was integrated with PHPUnit was
through a PHPUnit listener. That listener would in turn call the ``\Mockery::close()``
method for us.

As of 1.0.0, PHPUnit test cases where we want to use Mockery, should either use the
``\Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration`` trait, or extend the
``\Mockery\Adapter\Phpunit\MockeryTestCase`` test case. This will in turn call the
``\Mockery::close()`` method for us.

Read the documentation for a detailed overview of ":doc:`/reference/phpunit_integration`".

``\Mockery\Matcher\MustBe`` is deprecated
+++++++++++++++++++++++++++++++++++++++++

As of 1.0.0 the ``\Mockery\Matcher\MustBe`` matcher is deprecated and will be removed in
Mockery 2.0.0. We recommend instead to use the PHPUnit equivalents of the
MustBe matcher.

``allows`` and ``expects``
++++++++++++++++++++++++++

As of 1.0.0, Mockery has two new methods to set up expectations: ``allows`` and ``expects``.
This means that these methods names are now "reserved" for Mockery, or in other words
classes you want to mock with Mockery, can't have methods called ``allows`` or ``expects``.

Read more in the documentation about this ":doc:`/reference/alternative_should_receive_syntax`".

No more implicit regex matching for string arguments
++++++++++++++++++++++++++++++++++++++++++++++++++++

When setting up string arguments in method expectations, Mockery 0.9.x and older, would try
to match arguments using a regular expression in a "last attempt" scenario.

As of 1.0.0, Mockery will no longer attempt to do this regex matching, but will only try
first the identical operator ``===``, and failing that, the equals operator ``==``.

If you want to match an argument using regular expressions, please use the new
``\Mockery\Matcher\Pattern`` matcher. Read more in the documentation about this
pattern matcher in the ":doc:`/reference/argument_validation`" section.

``andThrow`` ``\Throwable``
+++++++++++++++++++++++++++

As of 1.0.0, the ``andThrow`` can now throw any ``\Throwable``.

Upgrading to 0.9
----------------

The generator was completely rewritten, so any code with a deep integration to
mockery will need evaluating.

Upgrading to 0.8
----------------

Since the release of 0.8.0 the following behaviours were altered:

1. The ``shouldIgnoreMissing()`` behaviour optionally applied to mock objects
   returned an instance of ``\Mockery\Undefined`` when methods called did not
   match a known expectation. Since 0.8.0, this behaviour was switched to
   returning ``null`` instead. You can restore the 0.7.2 behaviour by using the
   following:

   .. code-block:: php

       $mock = \Mockery::mock('stdClass')->shouldIgnoreMissing()->asUndefined();
