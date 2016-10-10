<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: unit_tester.php 1794 2008-06-28 12:55:41Z pp11 $
 */

/**#@+
 *  include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/test_case.php');
require_once(dirname(__FILE__) . '/dumper.php');
/**#@-*/

/**
 *    Standard unit test class for day to day testing
 *    of PHP code XP style. Adds some useful standard
 *    assertions.
 *    @package  SimpleTest
 *    @subpackage   UnitTester
 */
class UnitTestCase extends SimpleTestCase {

    /**
     *    Creates an empty test case. Should be subclassed
     *    with test methods for a functional test case.
     *    @param string $label     Name of test case. Will use
     *                             the class name if none specified.
     *    @access public
     */
    function __construct($label = false) {
        if (! $label) {
            $label = get_class($this);
        }
        parent::__construct($label);
    }

    /**
     *    Called from within the test methods to register
     *    passes and failures.
     *    @param boolean $result    Pass on true.
     *    @param string $message    Message to display describing
     *                              the test state.
     *    @return boolean           True on pass
     *    @access public
     */
    function assertTrue($result, $message = '%s') {
        return $this->assert(new TrueExpectation(), $result, $message);
    }

    /**
     *    Will be true on false and vice versa. False
     *    is the PHP definition of false, so that null,
     *    empty strings, zero and an empty array all count
     *    as false.
     *    @param boolean $result    Pass on false.
     *    @param string $message    Message to display.
     *    @return boolean           True on pass
     *    @access public
     */
    function assertFalse($result, $message = '%s') {
        return $this->assert(new FalseExpectation(), $result, $message);
    }

    /**
     *    Will be true if the value is null.
     *    @param null $value       Supposedly null value.
     *    @param string $message   Message to display.
     *    @return boolean                        True on pass
     *    @access public
     */
    function assertNull($value, $message = '%s') {
        $dumper = new SimpleDumper();
        $message = sprintf(
                $message,
                '[' . $dumper->describeValue($value) . '] should be null');
        return $this->assertTrue(! isset($value), $message);
    }

    /**
     *    Will be true if the value is set.
     *    @param mixed $value           Supposedly set value.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass.
     *    @access public
     */
    function assertNotNull($value, $message = '%s') {
        $dumper = new SimpleDumper();
        $message = sprintf(
                $message,
                '[' . $dumper->describeValue($value) . '] should not be null');
        return $this->assertTrue(isset($value), $message);
    }

    /**
     *    Type and class test. Will pass if class
     *    matches the type name or is a subclass or
     *    if not an object, but the type is correct.
     *    @param mixed $object         Object to test.
     *    @param string $type          Type name as string.
     *    @param string $message       Message to display.
     *    @return boolean              True on pass.
     *    @access public
     */
    function assertIsA($object, $type, $message = '%s') {
        return $this->assert(
                new IsAExpectation($type),
                $object,
                $message);
    }

    /**
     *    Type and class mismatch test. Will pass if class
     *    name or underling type does not match the one
     *    specified.
     *    @param mixed $object         Object to test.
     *    @param string $type          Type name as string.
     *    @param string $message       Message to display.
     *    @return boolean              True on pass.
     *    @access public
     */
    function assertNotA($object, $type, $message = '%s') {
        return $this->assert(
                new NotAExpectation($type),
                $object,
                $message);
    }

    /**
     *    Will trigger a pass if the two parameters have
     *    the same value only. Otherwise a fail.
     *    @param mixed $first          Value to compare.
     *    @param mixed $second         Value to compare.
     *    @param string $message       Message to display.
     *    @return boolean              True on pass
     *    @access public
     */
    function assertEqual($first, $second, $message = '%s') {
        return $this->assert(
                new EqualExpectation($first),
                $second,
                $message);
    }

    /**
     *    Will trigger a pass if the two parameters have
     *    a different value. Otherwise a fail.
     *    @param mixed $first           Value to compare.
     *    @param mixed $second          Value to compare.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass
     *    @access public
     */
    function assertNotEqual($first, $second, $message = '%s') {
        return $this->assert(
                new NotEqualExpectation($first),
                $second,
                $message);
    }

    /**
     *    Will trigger a pass if the if the first parameter
     *    is near enough to the second by the margin.
     *    @param mixed $first          Value to compare.
     *    @param mixed $second         Value to compare.
     *    @param mixed $margin         Fuzziness of match.
     *    @param string $message       Message to display.
     *    @return boolean              True on pass
     *    @access public
     */
    function assertWithinMargin($first, $second, $margin, $message = '%s') {
        return $this->assert(
                new WithinMarginExpectation($first, $margin),
                $second,
                $message);
    }

    /**
     *    Will trigger a pass if the two parameters differ
     *    by more than the margin.
     *    @param mixed $first          Value to compare.
     *    @param mixed $second         Value to compare.
     *    @param mixed $margin         Fuzziness of match.
     *    @param string $message       Message to display.
     *    @return boolean              True on pass
     *    @access public
     */
    function assertOutsideMargin($first, $second, $margin, $message = '%s') {
        return $this->assert(
                new OutsideMarginExpectation($first, $margin),
                $second,
                $message);
    }

    /**
     *    Will trigger a pass if the two parameters have
     *    the same value and same type. Otherwise a fail.
     *    @param mixed $first           Value to compare.
     *    @param mixed $second          Value to compare.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass
     *    @access public
     */
    function assertIdentical($first, $second, $message = '%s') {
        return $this->assert(
                new IdenticalExpectation($first),
                $second,
                $message);
    }

    /**
     *    Will trigger a pass if the two parameters have
     *    the different value or different type.
     *    @param mixed $first           Value to compare.
     *    @param mixed $second          Value to compare.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass
     *    @access public
     */
    function assertNotIdentical($first, $second, $message = '%s') {
        return $this->assert(
                new NotIdenticalExpectation($first),
                $second,
                $message);
    }

    /**
     *    Will trigger a pass if both parameters refer
     *    to the same object or value. Fail otherwise.
     *    This will cause problems testing objects under
     *    E_STRICT.
     *    TODO: Replace with expectation.
     *    @param mixed $first           Reference to check.
     *    @param mixed $second          Hopefully the same variable.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass
     *    @access public
     */
    function assertReference(&$first, &$second, $message = '%s') {
        $dumper = new SimpleDumper();
        $message = sprintf(
                $message,
                '[' . $dumper->describeValue($first) .
                        '] and [' . $dumper->describeValue($second) .
                        '] should reference the same object');
        return $this->assertTrue(
                SimpleTestCompatibility::isReference($first, $second),
                $message);
    }

    /**
     *    Will trigger a pass if both parameters refer
     *    to the same object. Fail otherwise. This has
     *    the same semantics at the PHPUnit assertSame.
     *    That is, if values are passed in it has roughly
     *    the same affect as assertIdentical.
     *    TODO: Replace with expectation.
     *    @param mixed $first           Object reference to check.
     *    @param mixed $second          Hopefully the same object.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass
     *    @access public
     */
    function assertSame($first, $second, $message = '%s') {
        $dumper = new SimpleDumper();
        $message = sprintf(
                $message,
                '[' . $dumper->describeValue($first) .
                        '] and [' . $dumper->describeValue($second) .
                        '] should reference the same object');
        return $this->assertTrue($first === $second, $message);
    }

    /**
     *    Will trigger a pass if both parameters refer
     *    to different objects. Fail otherwise. The objects
     *    have to be identical though.
     *    @param mixed $first           Object reference to check.
     *    @param mixed $second          Hopefully not the same object.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass
     *    @access public
     */
    function assertClone($first, $second, $message = '%s') {
        $dumper = new SimpleDumper();
        $message = sprintf(
                $message,
                '[' . $dumper->describeValue($first) .
                        '] and [' . $dumper->describeValue($second) .
                        '] should not be the same object');
        $identical = new IdenticalExpectation($first);
        return $this->assertTrue(
                $identical->test($second) && ! ($first === $second),
                $message);
    }

    /**
     *    Will trigger a pass if both parameters refer
     *    to different variables. Fail otherwise. The objects
     *    have to be identical references though.
     *    This will fail under E_STRICT with objects. Use
     *    assertClone() for this.
     *    @param mixed $first           Object reference to check.
     *    @param mixed $second          Hopefully not the same object.
     *    @param string $message        Message to display.
     *    @return boolean               True on pass
     *    @access public
     */
    function assertCopy(&$first, &$second, $message = "%s") {
        $dumper = new SimpleDumper();
        $message = sprintf(
                $message,
                "[" . $dumper->describeValue($first) .
                        "] and [" . $dumper->describeValue($second) .
                        "] should not be the same object");
        return $this->assertFalse(
                SimpleTestCompatibility::isReference($first, $second),
                $message);
    }

    /**
     *    Will trigger a pass if the Perl regex pattern
     *    is found in the subject. Fail otherwise.
     *    @param string $pattern    Perl regex to look for including
     *                              the regex delimiters.
     *    @param string $subject    String to search in.
     *    @param string $message    Message to display.
     *    @return boolean           True on pass
     *    @access public
     */
    function assertPattern($pattern, $subject, $message = '%s') {
        return $this->assert(
                new PatternExpectation($pattern),
                $subject,
                $message);
    }

    /**
     *    Will trigger a pass if the perl regex pattern
     *    is not present in subject. Fail if found.
     *    @param string $pattern    Perl regex to look for including
     *                              the regex delimiters.
     *    @param string $subject    String to search in.
     *    @param string $message    Message to display.
     *    @return boolean           True on pass
     *    @access public
     */
    function assertNoPattern($pattern, $subject, $message = '%s') {
        return $this->assert(
                new NoPatternExpectation($pattern),
                $subject,
                $message);
    }

    /**
     *    Prepares for an error. If the error mismatches it
     *    passes through, otherwise it is swallowed. Any
     *    left over errors trigger failures.
     *    @param SimpleExpectation/string $expected   The error to match.
     *    @param string $message                      Message on failure.
     *    @access public
     */
    function expectError($expected = false, $message = '%s') {
        $queue = SimpleTest::getContext()->get('SimpleErrorQueue');
        $queue->expectError($this->coerceExpectation($expected), $message);
    }

    /**
     *    Prepares for an exception. If the error mismatches it
     *    passes through, otherwise it is swallowed. Any
     *    left over errors trigger failures.
     *    @param SimpleExpectation/Exception $expected  The error to match.
     *    @param string $message                        Message on failure.
     *    @access public
     */
    function expectException($expected = false, $message = '%s') {
        $queue = SimpleTest::getContext()->get('SimpleExceptionTrap');
        $line = $this->getAssertionLine();
        $queue->expectException($expected, $message . $line);
    }

    /**
     *    Creates an equality expectation if the
     *    object/value is not already some type
     *    of expectation.
     *    @param mixed $expected      Expected value.
     *    @return SimpleExpectation   Expectation object.
     *    @access private
     */
    protected function coerceExpectation($expected) {
        if ($expected == false) {
            return new TrueExpectation();
        }
        if (SimpleTestCompatibility::isA($expected, 'SimpleExpectation')) {
            return $expected;
        }
        return new EqualExpectation(
                is_string($expected) ? str_replace('%', '%%', $expected) : $expected);
    }
}
?>