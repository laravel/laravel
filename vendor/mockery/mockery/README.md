Mockery
=======

[![Build Status](https://github.com/mockery/mockery/actions/workflows/tests.yml/badge.svg)](https://github.com/mockery/mockery/actions)
[![Supported PHP Version](https://badgen.net/packagist/php/mockery/mockery?color=8892bf)](https://www.php.net/supported-versions)
[![Code Coverage](https://codecov.io/gh/mockery/mockery/branch/1.6.x/graph/badge.svg?token=oxHwVM56bT)](https://codecov.io/gh/mockery/mockery)
[![Type Coverage](https://shepherd.dev/github/mockery/mockery/coverage.svg)](https://shepherd.dev/github/mockery/mockery)
[![Latest Stable Version](https://poser.pugx.org/mockery/mockery/v/stable.svg)](https://packagist.org/packages/mockery/mockery)
[![Total Downloads](https://poser.pugx.org/mockery/mockery/downloads.svg)](https://packagist.org/packages/mockery/mockery)

Mockery is a simple yet flexible PHP mock object framework for use in unit testing
with PHPUnit, PHPSpec or any other testing framework. Its core goal is to offer a
test double framework with a succinct API capable of clearly defining all possible
object operations and interactions using a human readable Domain Specific Language
(DSL). Designed as a drop in alternative to PHPUnit's phpunit-mock-objects library,
Mockery is easy to integrate with PHPUnit and can operate alongside
phpunit-mock-objects without the World ending.

Mockery is released under a New BSD License.

## Installation

To install Mockery, run the command below and you will get the latest
version

```sh
composer require --dev mockery/mockery
```

## Documentation

In older versions, this README file was the documentation for Mockery. Over time
we have improved this, and have created an extensive documentation for you. Please
use this README file as a starting point for Mockery, but do read the documentation
to learn how to use Mockery.

The current version can be seen at [docs.mockery.io](http://docs.mockery.io).

## PHPUnit Integration

Mockery ships with some helpers if you are using PHPUnit. You can extend the
[`Mockery\Adapter\Phpunit\MockeryTestCase`](library/Mockery/Adapter/Phpunit/MockeryTestCase.php)
class instead of `PHPUnit\Framework\TestCase`, or if you are already using a
custom base class for your tests, take a look at the traits available in the
[`Mockery\Adapter\Phpunit`](library/Mockery/Adapter/Phpunit) namespace.

## Test Doubles

Test doubles (often called mocks) simulate the behaviour of real objects. They are
commonly utilised to offer test isolation, to stand in for objects which do not
yet exist, or to allow for the exploratory design of class APIs without
requiring actual implementation up front.

The benefits of a test double framework are to allow for the flexible generation
and configuration of test doubles. They allow the setting of expected method calls
and/or return values using a flexible API which is capable of capturing every
possible real object behaviour in way that is stated as close as possible to a
natural language description. Use the `Mockery::mock` method to create a test
double.

``` php
$double = Mockery::mock();
```

If you need Mockery to create a test double to satisfy a particular type hint,
you can pass the type to the `mock` method.

``` php
class Book {}

interface BookRepository {
    function find($id): Book;
    function findAll(): array;
    function add(Book $book): void;
}

$double = Mockery::mock(BookRepository::class);
```

A detailed explanation of creating and working with test doubles is given in the
documentation, [Creating test doubles](http://docs.mockery.io/en/latest/reference/creating_test_doubles.html)
section.

## Method Stubs 🎫

A method stub is a mechanism for having your test double return canned responses
to certain method calls. With stubs, you don't care how many times, if at all,
the method is called. Stubs are used to provide indirect input to the system
under test.

``` php
$double->allows()->find(123)->andReturns(new Book());

$book = $double->find(123);
```

If you have used Mockery before, you might see something new in the example
above &mdash; we created a method stub using `allows`, instead of the "old"
`shouldReceive` syntax. This is a new feature of Mockery v1, but fear not,
the trusty ol' `shouldReceive` is still here.

For new users of Mockery, the above example can also be written as:

``` php
$double->shouldReceive('find')->with(123)->andReturn(new Book());
$book = $double->find(123);
```

If your stub doesn't require specific arguments, you can also use this shortcut
for setting up multiple calls at once:

``` php
$double->allows([
    "findAll" => [new Book(), new Book()],
]);
```

or

``` php
$double->shouldReceive('findAll')
    ->andReturn([new Book(), new Book()]);
```

You can also use this shortcut, which creates a double and sets up some stubs in
one call:

``` php
$double = Mockery::mock(BookRepository::class, [
    "findAll" => [new Book(), new Book()],
]);
```

## Method Call Expectations 📲

A Method call expectation is a mechanism to allow you to verify that a
particular method has been called. You can specify the parameters and you can
also specify how many times you expect it to be called. Method call expectations
are used to verify indirect output of the system under test.

``` php
$book = new Book();

$double = Mockery::mock(BookRepository::class);
$double->expects()->add($book);
```

During the test, Mockery accept calls to the `add` method as prescribed.
After you have finished exercising the system under test, you need to
tell Mockery to check that the method was called as expected, using the
`Mockery::close` method. One way to do that is to add it to your `tearDown`
method in PHPUnit.

``` php

public function tearDown()
{
    Mockery::close();
}
```

The `expects()` method automatically sets up an expectation that the method call
(and matching parameters) is called **once and once only**. You can choose to change
this if you are expecting more calls.

``` php
$double->expects()->add($book)->twice();
```

If you have used Mockery before, you might see something new in the example
above &mdash; we created a method expectation using `expects`, instead of the "old"
`shouldReceive` syntax. This is a new feature of Mockery v1, but same as with
`allows` in the previous section, it can be written in the "old" style.

For new users of Mockery, the above example can also be written as:

``` php
$double->shouldReceive('find')
    ->with(123)
    ->once()
    ->andReturn(new Book());
$book = $double->find(123);
```

A detailed explanation of declaring expectations on method calls, please
read the documentation, the [Expectation declarations](http://docs.mockery.io/en/latest/reference/expectations.html)
section. After that, you can also learn about the new `allows` and `expects` methods
in the [Alternative shouldReceive syntax](http://docs.mockery.io/en/latest/reference/alternative_should_receive_syntax.html)
section.

It is worth mentioning that one way of setting up expectations is no better or worse
than the other. Under the hood, `allows` and `expects` are doing the same thing as
`shouldReceive`, at times in "less words", and as such it comes to a personal preference
of the programmer which way to use.

## Test Spies 🕵️

By default, all test doubles created with the `Mockery::mock` method will only
accept calls that they have been configured to `allow` or `expect` (or in other words,
calls that they `shouldReceive`). Sometimes we don't necessarily care about all of the
calls that are going to be made to an object. To facilitate this, we can tell Mockery
to ignore any calls it has not been told to expect or allow. To do so, we can tell a
test double `shouldIgnoreMissing`, or we can create the double using the `Mocker::spy`
shortcut.

``` php
// $double = Mockery::mock()->shouldIgnoreMissing();
$double = Mockery::spy();

$double->foo(); // null
$double->bar(); // null
```

Further to this, sometimes we want to have the object accept any call during the test execution
and then verify the calls afterwards. For these purposes, we need our test
double to act as a Spy. All mockery test doubles record the calls that are made
to them for verification afterwards by default:

``` php
$double->baz(123);

$double->shouldHaveReceived()->baz(123); // null
$double->shouldHaveReceived()->baz(12345); // Uncaught Exception Mockery\Exception\InvalidCountException...
```

Please refer to the [Spies](http://docs.mockery.io/en/latest/reference/spies.html) section
of the documentation to learn more about the spies.

## Utilities 🔌

### Global Helpers

Mockery ships with a handful of global helper methods, you just need to ask
Mockery to declare them.

``` php
Mockery::globalHelpers();

$mock = mock(Some::class);
$spy = spy(Some::class);

$spy->shouldHaveReceived()
    ->foo(anyArgs());
```

All of the global helpers are wrapped in a `!function_exists` call to avoid
conflicts. So if you already have a global function called `spy`, Mockery will
silently skip the declaring its own `spy` function.

### Testing Traits

As Mockery ships with code generation capabilities, it was trivial to add
functionality allowing users to create objects on the fly that use particular
traits. Any abstract methods defined by the trait will be created and can have
expectations or stubs configured like normal Test Doubles.

``` php
trait Foo {
    function foo() {
        return $this->doFoo();
    }

    abstract function doFoo();
}

$double = Mockery::mock(Foo::class);
$double->allows()->doFoo()->andReturns(123);
$double->foo(); // int(123)
```

## Versioning

The Mockery team attempts to adhere to [Semantic Versioning](http://semver.org),
however, some of Mockery's internals are considered private and will be open to
change at any time. Just because a class isn't final, or a method isn't marked
private, does not mean it constitutes part of the API we guarantee under the
versioning scheme.

### Alternative Runtimes

Mockery 1.3 was the last version to support HHVM 3 and PHP 5. There is no support for HHVM 4+.

## A new home for Mockery

⚠️️ Update your remotes! Mockery has transferred to a new location. While it was once
at `padraic/mockery`, it is now at `mockery/mockery`. While your
existing repositories will redirect transparently for any operations, take some
time to transition to the new URL.
```sh
$ git remote set-url upstream https://github.com/mockery/mockery.git
```
Replace `upstream` with the name of the remote you use locally; `upstream` is commonly
used but you may be using something else. Run `git remote -v` to see what you're actually
using.
