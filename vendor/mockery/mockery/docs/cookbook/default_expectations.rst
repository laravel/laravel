.. index::
    single: Cookbook; Default Mock Expectations

Default Mock Expectations
=========================

Often in unit testing, we end up with sets of tests which use the same object
dependency over and over again. Rather than mocking this class/object within
every single unit test (requiring a mountain of duplicate code), we can
instead define reusable default mocks within the test case's ``setup()``
method. This even works where unit tests use varying expectations on the same
or similar mock object.

How this works, is that you can define mocks with default expectations. Then,
in a later unit test, you can add or fine-tune expectations for that specific
test. Any expectation can be set as a default using the ``byDefault()``
declaration.
