This is the PHP port of Hamcrest Matchers
=========================================

[![tests](https://github.com/hamcrest/hamcrest-php/actions/workflows/tests.yml/badge.svg)](https://github.com/hamcrest/hamcrest-php/actions/workflows/tests.yml)

Hamcrest is a matching library originally written for Java, but
subsequently ported to many other languages.  hamcrest-php is the
official PHP port of Hamcrest and essentially follows a literal
translation of the original Java API for Hamcrest, with a few
Exceptions, mostly down to PHP language barriers:

  1. `instanceOf($theClass)` is actually `anInstanceOf($theClass)`

  2. `both(containsString('a'))->and(containsString('b'))`
     is actually `both(containsString('a'))->andAlso(containsString('b'))`

  3. `either(containsString('a'))->or(containsString('b'))`
     is actually `either(containsString('a'))->orElse(containsString('b'))`

  4. Unless it would be non-semantic for a matcher to do so, hamcrest-php
     allows dynamic typing for it's input, in "the PHP way". Exception are
     where semantics surrounding the type itself would suggest otherwise,
     such as stringContains() and greaterThan().

  5. Several official matchers have not been ported because they don't
     make sense or don't apply in PHP:

       - `typeCompatibleWith($theClass)`
       - `eventFrom($source)`
       - `hasProperty($name)` **
       - `samePropertyValuesAs($obj)` **

  6. When most of the collections matchers are finally ported, PHP-specific
     aliases will probably be created due to a difference in naming
     conventions between Java's Arrays, Collections, Sets and Maps compared
     with PHP's Arrays.

---
** [Unless we consider POPO's (Plain Old PHP Objects) akin to JavaBeans]
     - The POPO thing is a joke.  Java devs coin the term POJO's (Plain Old
       Java Objects).


Usage
-----

Hamcrest matchers are easy to use as:

```php
Hamcrest_MatcherAssert::assertThat('a', Hamcrest_Matchers::equalToIgnoringCase('A'));
```

Alternatively, you can use the global proxy-functions:

```php
$result = true;
// with an identifier
assertThat("result should be true", $result, equalTo(true));

// without an identifier
assertThat($result, equalTo(true));

// evaluate a boolean expression
assertThat($result === true);

// with syntactic sugar is()
assertThat(true, is(true));
```

:warning: **NOTE:** the global proxy-functions aren't autoloaded by default, so you will need to load them first:

```php
\Hamcrest\Util::registerGlobalFunctions();
```

For brevity, all of the examples below use the proxy-functions.


Documentation
-------------
A tutorial can be found on the [Hamcrest site](https://code.google.com/archive/p/hamcrest/wikis/TutorialPHP.wiki).


Available Matchers
------------------
* [Array](../master/README.md#array)
* [Collection](../master/README.md#collection)
* [Object](../master/README.md#object)
* [Numbers](../master/README.md#numbers)
* [Type checking](../master/README.md#type-checking)
* [XML](../master/README.md#xml)


### Array

* `anArray` - evaluates an array
```php
assertThat([], anArray());
```

* `hasItemInArray` - check if item exists in array
```php
$list = range(2, 7, 2);
$item = 4;
assertThat($list, hasItemInArray($item));
```

* `hasValue` - alias of hasItemInArray

* `arrayContainingInAnyOrder` - check if array contains elements in any order
```php
assertThat([2, 4, 6], arrayContainingInAnyOrder([6, 4, 2]));
assertThat([2, 4, 6], arrayContainingInAnyOrder([4, 2, 6]));
```

* `containsInAnyOrder` - alias of arrayContainingInAnyOrder

* `arrayContaining` - An array with elements that match the given matchers in the same order.
```php
assertThat([2, 4, 6], arrayContaining([2, 4, 6]));
assertthat([2, 4, 6], not(arrayContaining([6, 4, 2])));
```

* `contains` - check array in same order
```php
assertThat([2, 4, 6], contains([2, 4, 6]));
```

* `hasKeyInArray` - check if array has given key
```php
assertThat(['name'=> 'foobar'], hasKeyInArray('name'));
```

* `hasKey` - alias of hasKeyInArray

* `hasKeyValuePair` - check if array has given key, value pair
```php
assertThat(['name'=> 'foobar'], hasKeyValuePair('name', 'foobar'));
```
* `hasEntry` - same as hasKeyValuePair

* `arrayWithSize` - check array has given size
```php
assertthat([2, 4, 6], arrayWithSize(3));
```
* `emptyArray` - check if array is empty
```php
assertThat([], emptyArray());
```

* `nonEmptyArray`
```php
assertThat([1], nonEmptyArray());
```

### Collection

* `emptyTraversable` - check if traversable is empty
```php
$empty_it = new EmptyIterator;
assertThat($empty_it, emptyTraversable());
```

* `nonEmptyTraversable` - check if traversable isn't empty
```php
$non_empty_it = new ArrayIterator(range(1, 10));
assertThat($non_empty_it, nonEmptyTraversable());
a
```

* `traversableWithSize`
```php
$non_empty_it = new ArrayIterator(range(1, 10));
assertThat($non_empty_it, traversableWithSize(count(range(1, 10))));
`
```

### Core

* `allOf` - Evaluates to true only if ALL of the passed in matchers evaluate to true.
```php
assertThat([2,4,6], allOf(hasValue(2), arrayWithSize(3)));
```

* `anyOf` - Evaluates to true if ANY of the passed in matchers evaluate to true.
```php
assertThat([2, 4, 6], anyOf(hasValue(8), hasValue(2)));
```

* `noneOf` - Evaluates to false if ANY of the passed in matchers evaluate to true.
```php
assertThat([2, 4, 6], noneOf(hasValue(1), hasValue(3)));
```

* `both` + `andAlso` - This is useful for fluently combining matchers that must both pass.
```php
assertThat([2, 4, 6], both(hasValue(2))->andAlso(hasValue(4)));
```

* `either` + `orElse` - This is useful for fluently combining matchers where either may pass,
```php
assertThat([2, 4, 6], either(hasValue(2))->orElse(hasValue(4)));
```

* `describedAs` - Wraps an existing matcher and overrides the description when it fails.
```php 
$expected = "Dog";
$found = null;
// this assertion would result error message as Expected: is not null but: was null
//assertThat("Expected {$expected}, got {$found}", $found, is(notNullValue()));
// and this assertion would result error message as Expected: Dog but: was null
//assertThat($found, describedAs($expected, notNullValue()));
```

* `everyItem` - A matcher to apply to every element in an array.
```php
assertThat([2, 4, 6], everyItem(notNullValue()));
```

* `hasItem` - check array has given item, it can take a matcher argument
```php
assertThat([2, 4, 6], hasItem(equalTo(2)));
```

* `hasItems` - check array has given items, it can take multiple matcher as arguments
```php
assertThat([1, 3, 5], hasItems(equalTo(1), equalTo(3)));
```

### Object

* `hasToString` - check `__toString` or `toString` method
```php
class Foo {
    public $name = null;

    public function __toString() {
        return "[Foo]Instance";
    }
}
$foo = new Foo;
assertThat($foo, hasToString(equalTo("[Foo]Instance")));
```

* `equalTo` - compares two instances using comparison operator '=='
```php
$foo = new Foo;
$foo2 = new Foo;
assertThat($foo, equalTo($foo2));
```

* `identicalTo` - compares two instances using identity operator '==='
```php
assertThat($foo, is(not(identicalTo($foo2))));
```

* `anInstanceOf` - check instance is an instance|sub-class of given class
```php
assertThat($foo, anInstanceOf(Foo::class));
```

* `any` - alias of `anInstanceOf`

* `nullValue` check null
```php
assertThat(null, is(nullValue()));
```

* `notNullValue` check not null
```php
assertThat("", notNullValue());
```

* `sameInstance` - check for same instance
```php
assertThat($foo, is(not(sameInstance($foo2))));
assertThat($foo, is(sameInstance($foo)));
```

* `typeOf`- check type
```php 
assertThat(1, typeOf("integer"));
```

* `notSet` - check if instance property is not set
```php
assertThat($foo, notSet("name"));
```

* `set` - check if instance property is set
```php
$foo->name = "bar";
assertThat($foo, set("name"));
```

### Numbers

* `closeTo` - check value close to a range
```php
assertThat(3, closeTo(3, 0.5));
```

* `comparesEqualTo` - check with '=='
```php
assertThat(2, comparesEqualTo(2));
```

* `greaterThan` - check '>'
```
assertThat(2, greaterThan(1));
```

* `greaterThanOrEqualTo`
```php
assertThat(2, greaterThanOrEqualTo(2));
```

* `atLeast` - The value is >= given value
```php
assertThat(3, atLeast(2));
```
* `lessThan`
```php
assertThat(2, lessThan(3));
```

* `lessThanOrEqualTo`
```php
assertThat(2, lessThanOrEqualTo(3));
```

* `atMost` - The value is <= given value
```php
assertThat(2, atMost(3));
```

### String

* `emptyString` - check for empty string
```php
assertThat("", emptyString());
```

* `isEmptyOrNullString`
```php
assertThat(null, isEmptyOrNullString());
```

* `nullOrEmptyString`
```php
assertThat("", nullOrEmptyString());
```

* `isNonEmptyString`
```php
assertThat("foo", isNonEmptyString());
```

* `nonEmptyString`
```php
assertThat("foo", nonEmptyString());
```

* `equalToIgnoringCase`
```php
assertThat("Foo", equalToIgnoringCase("foo"));
```
* `equalToIgnoringWhiteSpace`
```php
assertThat(" Foo ", equalToIgnoringWhiteSpace("Foo"));
```

* `matchesPattern` - matches with regex pattern
```php
assertThat("foobarbaz", matchesPattern('/(foo)(bar)(baz)/'));
```

* `containsString` - check for substring
```php
assertThat("foobar", containsString("foo"));
```

* `containsStringIgnoringCase`
```php
assertThat("fooBar", containsStringIgnoringCase("bar"));
```

* `stringContainsInOrder`
```php
assertThat("foo", stringContainsInOrder("foo"));
```

* `endsWith` - check string that ends with given value
```php
assertThat("foo", endsWith("oo"));
```

* `startsWith` - check string that starts with given value
```php
assertThat("bar", startsWith("ba"));
```

### Type-checking

* `arrayValue` - check array type
```php
assertThat([], arrayValue());
```

* `booleanValue`
```php
assertThat(true, booleanValue());
```
* `boolValue` - alias of booleanValue

* `callableValue` - check if value is callable
```php
$func = function () {};
assertThat($func, callableValue());
```
* `doubleValue`
```php
assertThat(3.14, doubleValue());
```

* `floatValue`
```php
assertThat(3.14, floatValue());
```

* `integerValue`
```php
assertThat(1, integerValue());
```

* `intValue` - alias of `integerValue`

* `numericValue` - check if value is numeric
```php
assertThat("123", numericValue());
```

* `objectValue` - check for object
```php
$obj = new stdClass;
assertThat($obj, objectValue());
```
* `anObject`
```php
assertThat($obj, anObject());
```

* `resourceValue` - check resource type
```php
$fp = fopen("/tmp/foo", "w+");
assertThat($fp, resourceValue());
```

* `scalarValue` - check for scalar value
```php
assertThat(1, scalarValue());
```

* `stringValue`
```php
assertThat("", stringValue());
```

### XML

* `hasXPath` - check xml with a xpath
```php
$xml = <<<XML
<books>
  <book>
    <isbn>1</isbn>   
  </book>
  <book>
    <isbn>2</isbn>   
  </book>
</books>
XML;

$doc = new DOMDocument;
$doc->loadXML($xml);
assertThat($doc, hasXPath("book", 2));
```

