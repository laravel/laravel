# PHP Super Closure

[![Latest Stable Version](https://poser.pugx.org/jeremeamia/superclosure/v/stable.png)](https://packagist.org/packages/jeremeamia/superclosure)
[![Total Downloads](https://poser.pugx.org/jeremeamia/superclosure/downloads.png)](https://packagist.org/packages/jeremeamia/superclosure)
[![Build Status](https://travis-ci.org/jeremeamia/super_closure.svg?branch=multiple-parsers)][2]
[![GitTip](http://img.shields.io/gittip/jeremeamia.svg)](https://www.gittip.com/jeremeamia)

Have you ever seen this?

> Uncaught exception 'Exception' with message 'Serialization of 'Closure' is not allowed'

It's true! If you try to serialize a `Closure`, PHP will throw an exception and tell you that it is not allowed. But
even though it is not "allowed" by PHP, the Super Closure library ([jeremeamia/superclosure][3] on Packagist) makes it
**possible**.

I'm not joking, *you really can serialize a PHP closure*!

```php
require 'vendor/autoload.php';

use Jeremeamia\SuperClosure\SerializableClosure;

$greeting = 'Hello';
$helloWorld = new SerializableClosure(function ($name = 'World') use ($greeting) {
    echo "{$greeting}, {$name}!\n";
});

$helloWorld();
//> Hello, World!
$helloWorld('Jeremy');
//> Hello, Jeremy!

$serialized = serialize($helloWorld);
$unserialized = unserialize($serialized);

$unserialized();
//> Hello, World!
$unserialized('Jeremy');
//> Hello, Jeremy!
```
Yep, pretty cool huh?

## Tell Me More!

It all started way back in the beginning of 2010 when PHP 5.3 was starting to gain traction. I wrote a blog post called
[Extending PHP 5.3 Closures with Serialization and Reflection][4] on my former employers' blog, [HTMList][5], showing
how it can be done. Since then I've made a few iterations on the code, and this most recent iteration brings with it a
generally more robust solution that takes advantage of the fabulous [nikic/php-parser][6] library.

### Features

* Grants the ability to serialize closures
* Handles closures with used/inherited/imported variables
* Handles closures that use other closures
* Handles closures that reference class names in the parameters or body
* Handles recursive closures (PHP 5.4+ only)
* Allows you to get the code of a closure
* Allows you to get the names and values of variables used by a closure
* Allows you to get an Abstract Syntax Tree (AST) representing the code of a closure
* Replaces magic constants with their expected values so that the closure behaves as expected after unserialization
* Uses an accurate parsing method of a context-free grammar via the [nikic/php-parser][6] library
* PSR-0 compliant and installable via Composer

### Caveats

1. For any variables used by reference (e.g., `function () use (&$vars, &$like, &$these) {…}`), the references are not
   maintained after serialization/unserialization. The only exception is when (in PHP 5.4+ only) the used variable is a
   reference to the `SerializableClosure` object being serialized, which is the case with a recursive function. For some
   reason — *that I actually don't quite understand* — this works.
2. If you have two closures defined on a single line (you shouldn't do this anyway), you will not be able to serialize
   either one since it is ambiguous which closure's code should be parsed.
3. Because the technique to acquire the code and context of the closure requires reflection and full AST-style parsing,
   the performance of serializing a closure is likely not good.
4. **Warning**: Both `eval()` and `extract()` are required to unserialize the closure. These functions are considered
   dangerous by many, so you will have to evaluate whether or not you actual want to be using this library if these
   functions concern you. These functions *must* be used to make this technique work.

## Installation

To install the Super Closure library in your project using Composer, first add the following to your `composer.json`
config file.
```javascript
{
    "require": {
        "jeremeamia/superclosure": "~1.0"
    }
}
```
Then run Composer's install or update commands to complete installation. Please visit the [Composer homepage][7] for
more information about how to use Composer.

## Why Would I Need To Serialize Closures?

Well, since you are here looking at this README, you may already have a use case in mind. Even though this concept began
as an experiment, there have been some use cases that have come up in the wild.

For example, in a [video about Laravel 4 and IronMQ][8] by [UserScape][9], at about the 7:50 mark they show how you can
push a closure onto a queue as a job so that it can be executed by a worker. This is nice because you do not have to
create a whole class for a job that might be really simple. The closure serialization is done by a [class in the Laravel
4 framework][10] that is based on one of my older versions of SuperClosure.

Essentially this library let's you create closures in one process and use them in another. It would even be possible to
provide closures (or algorithms) as a service through an API.

## Who Is Using Super Closure?

- [Laravel 4](https://github.com/laravel/framework) - Serializes a closure to potentially push onto a job queue.
- [HTTP Mock for PHP](https://github.com/InterNations/http-mock) - Serialize a closure to send to remote server within
  a test workflow.
- [Jumper](https://github.com/kakawait/Jumper) - Serialize a closure to run on remote host via SSH.
- [nicmart/Benchmark](https://github.com/nicmart/Benchmark) - Uses the `ClosureParser` to display a benchmarked
  Closure's code.
- Please let me know if and how your project uses Super Closure.

[1]:  https://secure.travis-ci.org/jeremeamia/super_closure.png?branch=master
[2]:  http://travis-ci.org/#!/jeremeamia/super_closure
[3]:  http://packagist.org/packages/jeremeamia/SuperClosure
[4]:  http://www.htmlist.com/development/extending-php-5-3-closures-with-serialization-and-reflection/
[5]:  http://www.htmlist.com
[6]:  https://github.com/nikic/PHP-Parser
[7]:  http://getcomposer.org
[8]:  http://vimeo.com/64703617
[9]:  http://www.userscape.com
[10]: https://github.com/illuminate/support/blob/master/SerializableClosure.php
