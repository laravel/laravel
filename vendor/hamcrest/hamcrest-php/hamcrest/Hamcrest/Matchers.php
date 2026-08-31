<?php

/*
 Copyright (c) 2009-2010 hamcrest.org
 */

// This file is generated from the static method @factory doctags.

namespace Hamcrest;

/**
 * A series of static factories for all hamcrest matchers.
 */
class Matchers
{

    /**
     * Evaluates to true only if each $matcher[$i] is satisfied by $array[$i].
     */
    public static function anArray(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Arrays\IsArray', 'anArray'), $args);
    }

    /**
     * Evaluates to true if any item in an array satisfies the given matcher.
     *
     * @param mixed $item as a {@link Hamcrest\Matcher} or a value.
     *
     * @return \Hamcrest\Arrays\IsArrayContaining
     */
    public static function hasItemInArray($item)
    {
        return \Hamcrest\Arrays\IsArrayContaining::hasItemInArray($item);
    }

    /**
     * Evaluates to true if any item in an array satisfies the given matcher.
     *
     * @param mixed $item as a {@link Hamcrest\Matcher} or a value.
     *
     * @return \Hamcrest\Arrays\IsArrayContaining
     */
    public static function hasValue($item)
    {
        return \Hamcrest\Arrays\IsArrayContaining::hasItemInArray($item);
    }

    /**
     * An array with elements that match the given matchers.
     */
    public static function arrayContainingInAnyOrder(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInAnyOrder', 'arrayContainingInAnyOrder'), $args);
    }

    /**
     * An array with elements that match the given matchers.
     */
    public static function containsInAnyOrder(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInAnyOrder', 'arrayContainingInAnyOrder'), $args);
    }

    /**
     * An array with elements that match the given matchers in the same order.
     */
    public static function arrayContaining(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInOrder', 'arrayContaining'), $args);
    }

    /**
     * An array with elements that match the given matchers in the same order.
     */
    public static function contains(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInOrder', 'arrayContaining'), $args);
    }

    /**
     * Evaluates to true if any key in an array matches the given matcher.
     *
     * @param mixed $key as a {@link Hamcrest\Matcher} or a value.
     *
     * @return \Hamcrest\Arrays\IsArrayContainingKey
     */
    public static function hasKeyInArray($key)
    {
        return \Hamcrest\Arrays\IsArrayContainingKey::hasKeyInArray($key);
    }

    /**
     * Evaluates to true if any key in an array matches the given matcher.
     *
     * @param mixed $key as a {@link Hamcrest\Matcher} or a value.
     *
     * @return \Hamcrest\Arrays\IsArrayContainingKey
     */
    public static function hasKey($key)
    {
        return \Hamcrest\Arrays\IsArrayContainingKey::hasKeyInArray($key);
    }

    /**
     * Test if an array has both an key and value in parity with each other.
     */
    public static function hasKeyValuePair($key, $value)
    {
        return \Hamcrest\Arrays\IsArrayContainingKeyValuePair::hasKeyValuePair($key, $value);
    }

    /**
     * Test if an array has both an key and value in parity with each other.
     */
    public static function hasEntry($key, $value)
    {
        return \Hamcrest\Arrays\IsArrayContainingKeyValuePair::hasKeyValuePair($key, $value);
    }

    /**
     * Does array size satisfy a given matcher?
     *
     * @param \Hamcrest\Matcher|int $size as a {@link Hamcrest\Matcher} or a value.
     *
     * @return \Hamcrest\Arrays\IsArrayWithSize
     */
    public static function arrayWithSize($size)
    {
        return \Hamcrest\Arrays\IsArrayWithSize::arrayWithSize($size);
    }

    /**
     * Matches an empty array.
     */
    public static function emptyArray()
    {
        return \Hamcrest\Arrays\IsArrayWithSize::emptyArray();
    }

    /**
     * Matches an empty array.
     */
    public static function nonEmptyArray()
    {
        return \Hamcrest\Arrays\IsArrayWithSize::nonEmptyArray();
    }

    /**
     * Returns true if traversable is empty.
     */
    public static function emptyTraversable()
    {
        return \Hamcrest\Collection\IsEmptyTraversable::emptyTraversable();
    }

    /**
     * Returns true if traversable is not empty.
     */
    public static function nonEmptyTraversable()
    {
        return \Hamcrest\Collection\IsEmptyTraversable::nonEmptyTraversable();
    }

    /**
     * Does traversable size satisfy a given matcher?
     */
    public static function traversableWithSize($size)
    {
        return \Hamcrest\Collection\IsTraversableWithSize::traversableWithSize($size);
    }

    /**
     * Evaluates to true only if ALL of the passed in matchers evaluate to true.
     */
    public static function allOf(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Core\AllOf', 'allOf'), $args);
    }

    /**
     * Evaluates to true if ANY of the passed in matchers evaluate to true.
     */
    public static function anyOf(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Core\AnyOf', 'anyOf'), $args);
    }

    /**
     * Evaluates to false if ANY of the passed in matchers evaluate to true.
     */
    public static function noneOf(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Core\AnyOf', 'noneOf'), $args);
    }

    /**
     * This is useful for fluently combining matchers that must both pass.
     * For example:
     * <pre>
     *   assertThat($string, both(containsString("a"))->andAlso(containsString("b")));
     * </pre>
     */
    public static function both(\Hamcrest\Matcher $matcher)
    {
        return \Hamcrest\Core\CombinableMatcher::both($matcher);
    }

    /**
     * This is useful for fluently combining matchers where either may pass,
     * for example:
     * <pre>
     *   assertThat($string, either(containsString("a"))->orElse(containsString("b")));
     * </pre>
     */
    public static function either(\Hamcrest\Matcher $matcher)
    {
        return \Hamcrest\Core\CombinableMatcher::either($matcher);
    }

    /**
     * Wraps an existing matcher and overrides the description when it fails.
     */
    public static function describedAs(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Core\DescribedAs', 'describedAs'), $args);
    }

    /**
     * @param Matcher $itemMatcher
     *   A matcher to apply to every element in an array.
     *
     * @return \Hamcrest\Core\Every
     *   Evaluates to TRUE for a collection in which every item matches $itemMatcher
     */
    public static function everyItem(\Hamcrest\Matcher $itemMatcher)
    {
        return \Hamcrest\Core\Every::everyItem($itemMatcher);
    }

    /**
     * Does array size satisfy a given matcher?
     */
    public static function hasToString($matcher)
    {
        return \Hamcrest\Core\HasToString::hasToString($matcher);
    }

    /**
     * Decorates another Matcher, retaining the behavior but allowing tests
     * to be slightly more expressive.
     *
     * For example:  assertThat($cheese, equalTo($smelly))
     *          vs.  assertThat($cheese, is(equalTo($smelly)))
     */
    public static function is($value)
    {
        return \Hamcrest\Core\Is::is($value);
    }

    /**
     * This matcher always evaluates to true.
     *
     * @param string $description A meaningful string used when describing itself.
     *
     * @return \Hamcrest\Core\IsAnything
     */
    public static function anything($description = 'ANYTHING')
    {
        return \Hamcrest\Core\IsAnything::anything($description);
    }

    /**
     * Test if the value is an array containing this matcher.
     *
     * Example:
     * <pre>
     * assertThat(array('a', 'b'), hasItem(equalTo('b')));
     * //Convenience defaults to equalTo()
     * assertThat(array('a', 'b'), hasItem('b'));
     * </pre>
     */
    public static function hasItem(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Core\IsCollectionContaining', 'hasItem'), $args);
    }

    /**
     * Test if the value is an array containing elements that match all of these
     * matchers.
     *
     * Example:
     * <pre>
     * assertThat(array('a', 'b', 'c'), hasItems(equalTo('a'), equalTo('b')));
     * </pre>
     */
    public static function hasItems(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Core\IsCollectionContaining', 'hasItems'), $args);
    }

    /**
     * Is the value equal to another value, as tested by the use of the "=="
     * comparison operator?
     */
    public static function equalTo($item)
    {
        return \Hamcrest\Core\IsEqual::equalTo($item);
    }

    /**
     * Tests of the value is identical to $value as tested by the "===" operator.
     */
    public static function identicalTo($value)
    {
        return \Hamcrest\Core\IsIdentical::identicalTo($value);
    }

    /**
     * Is the value an instance of a particular type?
     * This version assumes no relationship between the required type and
     * the signature of the method that sets it up, for example in
     * <code>assertThat($anObject, anInstanceOf('Thing'));</code>
     */
    public static function anInstanceOf($theClass)
    {
        return \Hamcrest\Core\IsInstanceOf::anInstanceOf($theClass);
    }

    /**
     * Is the value an instance of a particular type?
     * This version assumes no relationship between the required type and
     * the signature of the method that sets it up, for example in
     * <code>assertThat($anObject, anInstanceOf('Thing'));</code>
     */
    public static function any($theClass)
    {
        return \Hamcrest\Core\IsInstanceOf::anInstanceOf($theClass);
    }

    /**
     * Matches if value does not match $value.
     */
    public static function not($value)
    {
        return \Hamcrest\Core\IsNot::not($value);
    }

    /**
     * Matches if value is null.
     */
    public static function nullValue()
    {
        return \Hamcrest\Core\IsNull::nullValue();
    }

    /**
     * Matches if value is not null.
     */
    public static function notNullValue()
    {
        return \Hamcrest\Core\IsNull::notNullValue();
    }

    /**
     * Creates a new instance of IsSame.
     *
     * @param mixed $object
     *   The predicate evaluates to true only when the argument is
     *   this object.
     *
     * @return \Hamcrest\Core\IsSame
     */
    public static function sameInstance($object)
    {
        return \Hamcrest\Core\IsSame::sameInstance($object);
    }

    /**
     * Is the value a particular built-in type?
     */
    public static function typeOf($theType)
    {
        return \Hamcrest\Core\IsTypeOf::typeOf($theType);
    }

    /**
     * Matches if value (class, object, or array) has named $property.
     */
    public static function set($property)
    {
        return \Hamcrest\Core\Set::set($property);
    }

    /**
     * Matches if value (class, object, or array) does not have named $property.
     */
    public static function notSet($property)
    {
        return \Hamcrest\Core\Set::notSet($property);
    }

    /**
     * Matches if value is a number equal to $value within some range of
     * acceptable error $delta.
     */
    public static function closeTo($value, $delta)
    {
        return \Hamcrest\Number\IsCloseTo::closeTo($value, $delta);
    }

    /**
     * The value is not > $value, nor < $value.
     */
    public static function comparesEqualTo($value)
    {
        return \Hamcrest\Number\OrderingComparison::comparesEqualTo($value);
    }

    /**
     * The value is > $value.
     */
    public static function greaterThan($value)
    {
        return \Hamcrest\Number\OrderingComparison::greaterThan($value);
    }

    /**
     * The value is >= $value.
     */
    public static function greaterThanOrEqualTo($value)
    {
        return \Hamcrest\Number\OrderingComparison::greaterThanOrEqualTo($value);
    }

    /**
     * The value is >= $value.
     */
    public static function atLeast($value)
    {
        return \Hamcrest\Number\OrderingComparison::greaterThanOrEqualTo($value);
    }

    /**
     * The value is < $value.
     */
    public static function lessThan($value)
    {
        return \Hamcrest\Number\OrderingComparison::lessThan($value);
    }

    /**
     * The value is <= $value.
     */
    public static function lessThanOrEqualTo($value)
    {
        return \Hamcrest\Number\OrderingComparison::lessThanOrEqualTo($value);
    }

    /**
     * The value is <= $value.
     */
    public static function atMost($value)
    {
        return \Hamcrest\Number\OrderingComparison::lessThanOrEqualTo($value);
    }

    /**
     * Matches if value is a zero-length string.
     */
    public static function isEmptyString()
    {
        return \Hamcrest\Text\IsEmptyString::isEmptyString();
    }

    /**
     * Matches if value is a zero-length string.
     */
    public static function emptyString()
    {
        return \Hamcrest\Text\IsEmptyString::isEmptyString();
    }

    /**
     * Matches if value is null or a zero-length string.
     */
    public static function isEmptyOrNullString()
    {
        return \Hamcrest\Text\IsEmptyString::isEmptyOrNullString();
    }

    /**
     * Matches if value is null or a zero-length string.
     */
    public static function nullOrEmptyString()
    {
        return \Hamcrest\Text\IsEmptyString::isEmptyOrNullString();
    }

    /**
     * Matches if value is a non-zero-length string.
     */
    public static function isNonEmptyString()
    {
        return \Hamcrest\Text\IsEmptyString::isNonEmptyString();
    }

    /**
     * Matches if value is a non-zero-length string.
     */
    public static function nonEmptyString()
    {
        return \Hamcrest\Text\IsEmptyString::isNonEmptyString();
    }

    /**
     * Matches if value is a string equal to $string, regardless of the case.
     */
    public static function equalToIgnoringCase($string)
    {
        return \Hamcrest\Text\IsEqualIgnoringCase::equalToIgnoringCase($string);
    }

    /**
     * Matches if value is a string equal to $string, regardless of whitespace.
     */
    public static function equalToIgnoringWhiteSpace($string)
    {
        return \Hamcrest\Text\IsEqualIgnoringWhiteSpace::equalToIgnoringWhiteSpace($string);
    }

    /**
     * Matches if value is a string that matches regular expression $pattern.
     */
    public static function matchesPattern($pattern)
    {
        return \Hamcrest\Text\MatchesPattern::matchesPattern($pattern);
    }

    /**
     * Matches if value is a string that contains $substring.
     */
    public static function containsString($substring)
    {
        return \Hamcrest\Text\StringContains::containsString($substring);
    }

    /**
     * Matches if value is a string that contains $substring regardless of the case.
     */
    public static function containsStringIgnoringCase($substring)
    {
        return \Hamcrest\Text\StringContainsIgnoringCase::containsStringIgnoringCase($substring);
    }

    /**
     * Matches if value contains $substrings in a constrained order.
     */
    public static function stringContainsInOrder(/* args... */)
    {
        $args = func_get_args();
        return call_user_func_array(array('\Hamcrest\Text\StringContainsInOrder', 'stringContainsInOrder'), $args);
    }

    /**
     * Matches if value is a string that ends with $substring.
     */
    public static function endsWith($substring)
    {
        return \Hamcrest\Text\StringEndsWith::endsWith($substring);
    }

    /**
     * Matches if value is a string that starts with $substring.
     */
    public static function startsWith($substring)
    {
        return \Hamcrest\Text\StringStartsWith::startsWith($substring);
    }

    /**
     * Is the value an array?
     */
    public static function arrayValue()
    {
        return \Hamcrest\Type\IsArray::arrayValue();
    }

    /**
     * Is the value a boolean?
     */
    public static function booleanValue()
    {
        return \Hamcrest\Type\IsBoolean::booleanValue();
    }

    /**
     * Is the value a boolean?
     */
    public static function boolValue()
    {
        return \Hamcrest\Type\IsBoolean::booleanValue();
    }

    /**
     * Is the value callable?
     */
    public static function callableValue()
    {
        return \Hamcrest\Type\IsCallable::callableValue();
    }

    /**
     * Is the value a float/double?
     */
    public static function doubleValue()
    {
        return \Hamcrest\Type\IsDouble::doubleValue();
    }

    /**
     * Is the value a float/double?
     */
    public static function floatValue()
    {
        return \Hamcrest\Type\IsDouble::doubleValue();
    }

    /**
     * Is the value an integer?
     */
    public static function integerValue()
    {
        return \Hamcrest\Type\IsInteger::integerValue();
    }

    /**
     * Is the value an integer?
     */
    public static function intValue()
    {
        return \Hamcrest\Type\IsInteger::integerValue();
    }

    /**
     * Is the value a numeric?
     */
    public static function numericValue()
    {
        return \Hamcrest\Type\IsNumeric::numericValue();
    }

    /**
     * Is the value an object?
     */
    public static function objectValue()
    {
        return \Hamcrest\Type\IsObject::objectValue();
    }

    /**
     * Is the value an object?
     */
    public static function anObject()
    {
        return \Hamcrest\Type\IsObject::objectValue();
    }

    /**
     * Is the value a resource?
     */
    public static function resourceValue()
    {
        return \Hamcrest\Type\IsResource::resourceValue();
    }

    /**
     * Is the value a scalar (boolean, integer, double, or string)?
     */
    public static function scalarValue()
    {
        return \Hamcrest\Type\IsScalar::scalarValue();
    }

    /**
     * Is the value a string?
     */
    public static function stringValue()
    {
        return \Hamcrest\Type\IsString::stringValue();
    }

    /**
     * Wraps <code>$matcher</code> with {@link Hamcrest\Core\IsEqual)
     * if it's not a matcher and the XPath in <code>count()</code>
     * if it's an integer.
     */
    public static function hasXPath($xpath, $matcher = null)
    {
        return \Hamcrest\Xml\HasXPath::hasXPath($xpath, $matcher);
    }
}
