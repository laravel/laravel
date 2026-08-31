.. index::
    single: Mocking; Final Classes/Methods

Dealing with Final Classes/Methods
==================================

One of the primary restrictions of mock objects in PHP, is that mocking
classes or methods marked final is hard. The final keyword prevents methods so
marked from being replaced in subclasses (subclassing is how mock objects can
inherit the type of the class or object being mocked).

The simplest solution is to implement an interface in your final class and 
typehint against / mock this.

However this may not be possible in some third party libraries.
Mockery does allow creating "proxy mocks" from classes marked final, or from
classes with methods marked final. This offers all the usual mock object
goodness but the resulting mock will not inherit the class type of the object
being mocked, i.e. it will not pass any instanceof comparison. Methods marked
as final will be proxied to the original method, i.e., final methods can't be
mocked.

We can create a proxy mock by passing the instantiated object we wish to
mock into ``\Mockery::mock()``, i.e. Mockery will then generate a Proxy to the
real object and selectively intercept method calls for the purposes of setting
and meeting expectations.

See the :ref:`creating-test-doubles-partial-test-doubles` chapter, the subsection
about proxied partial test doubles.
