<?php
/**
 *    base include file for SimpleTest
 *    @package    SimpleTest
 *    @subpackage    UnitTester
 *    @version    $Id: expectation.php 1788 2008-04-27 11:01:59Z pp11 $
 */

/**#@+
 *    include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/dumper.php');
require_once(dirname(__FILE__) . '/compatibility.php');
/**#@-*/

/**
 *    Assertion that can display failure information.
 *    Also includes various helper methods.
 *    @package SimpleTest
 *    @subpackage UnitTester
 *    @abstract
 */
class SimpleExpectation {
    protected $dumper = false;
    private $message;

    /**
     *    Creates a dumper for displaying values and sets
     *    the test message.
     *    @param string $message    Customised message on failure.
     */
    function __construct($message = '%s') {
        $this->message = $message;
    }

    /**
     *    Tests the expectation. True if correct.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     *    @abstract
     */
    function test($compare) {
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     *    @abstract
     */
    function testMessage($compare) {
    }

    /**
     *    Overlays the generated message onto the stored user
     *    message. An additional message can be interjected.
     *    @param mixed $compare        Comparison value.
     *    @param SimpleDumper $dumper  For formatting the results.
     *    @return string               Description of success
     *                                 or failure.
     *    @access public
     */
    function overlayMessage($compare, $dumper) {
        $this->dumper = $dumper;
        return sprintf($this->message, $this->testMessage($compare));
    }

    /**
     *    Accessor for the dumper.
     *    @return SimpleDumper    Current value dumper.
     *    @access protected
     */
    protected function getDumper() {
        if (! $this->dumper) {
            $dumper = new SimpleDumper();
            return $dumper;
        }
        return $this->dumper;
    }

    /**
     *    Test to see if a value is an expectation object.
     *    A useful utility method.
     *    @param mixed $expectation    Hopefully an Expectation
     *                                 class.
     *    @return boolean              True if descended from
     *                                 this class.
     *    @access public
     */
    static function isExpectation($expectation) {
        return is_object($expectation) &&
                SimpleTestCompatibility::isA($expectation, 'SimpleExpectation');
    }
}

/**
 *    A wildcard expectation always matches.
 *    @package SimpleTest
 *    @subpackage MockObjects
 */
class AnythingExpectation extends SimpleExpectation {

    /**
     *    Tests the expectation. Always true.
     *    @param mixed $compare  Ignored.
     *    @return boolean        True.
     *    @access public
     */
    function test($compare) {
        return true;
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        return 'Anything always matches [' . $dumper->describeValue($compare) . ']';
    }
}

/**
 *    An expectation that never matches.
 *    @package SimpleTest
 *    @subpackage MockObjects
 */
class FailedExpectation extends SimpleExpectation {

    /**
     *    Tests the expectation. Always false.
     *    @param mixed $compare  Ignored.
     *    @return boolean        True.
     *    @access public
     */
    function test($compare) {
        return false;
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        return 'Failed expectation never matches [' . $dumper->describeValue($compare) . ']';
    }
}

/**
 *    An expectation that passes on boolean true.
 *    @package SimpleTest
 *    @subpackage MockObjects
 */
class TrueExpectation extends SimpleExpectation {

    /**
     *    Tests the expectation.
     *    @param mixed $compare  Should be true.
     *    @return boolean        True on match.
     *    @access public
     */
    function test($compare) {
        return (boolean)$compare;
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        return 'Expected true, got [' . $dumper->describeValue($compare) . ']';
    }
}

/**
 *    An expectation that passes on boolean false.
 *    @package SimpleTest
 *    @subpackage MockObjects
 */
class FalseExpectation extends SimpleExpectation {

    /**
     *    Tests the expectation.
     *    @param mixed $compare  Should be false.
     *    @return boolean        True on match.
     *    @access public
     */
    function test($compare) {
        return ! (boolean)$compare;
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        return 'Expected false, got [' . $dumper->describeValue($compare) . ']';
    }
}

/**
 *    Test for equality.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class EqualExpectation extends SimpleExpectation {
    private $value;

    /**
     *    Sets the value to compare against.
     *    @param mixed $value        Test value to match.
     *    @param string $message     Customised message on failure.
     *    @access public
     */
    function __construct($value, $message = '%s') {
        parent::__construct($message);
        $this->value = $value;
    }

    /**
     *    Tests the expectation. True if it matches the
     *    held value.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test($compare) {
        return (($this->value == $compare) && ($compare == $this->value));
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        if ($this->test($compare)) {
            return "Equal expectation [" . $this->dumper->describeValue($this->value) . "]";
        } else {
            return "Equal expectation fails " .
                    $this->dumper->describeDifference($this->value, $compare);
        }
    }

    /**
     *    Accessor for comparison value.
     *    @return mixed       Held value to compare with.
     *    @access protected
     */
    protected function getValue() {
        return $this->value;
    }
}

/**
 *    Test for inequality.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NotEqualExpectation extends EqualExpectation {

    /**
     *    Sets the value to compare against.
     *    @param mixed $value       Test value to match.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function __construct($value, $message = '%s') {
        parent::__construct($value, $message);
    }

    /**
     *    Tests the expectation. True if it differs from the
     *    held value.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test($compare) {
        return ! parent::test($compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        if ($this->test($compare)) {
            return "Not equal expectation passes " .
                    $dumper->describeDifference($this->getValue(), $compare);
        } else {
            return "Not equal expectation fails [" .
                    $dumper->describeValue($this->getValue()) .
                    "] matches";
        }
    }
}

/**
 *    Test for being within a range.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class WithinMarginExpectation extends SimpleExpectation {
    private $upper;
    private $lower;

    /**
     *    Sets the value to compare against and the fuzziness of
     *    the match. Used for comparing floating point values.
     *    @param mixed $value        Test value to match.
     *    @param mixed $margin       Fuzziness of match.
     *    @param string $message     Customised message on failure.
     *    @access public
     */
    function __construct($value, $margin, $message = '%s') {
        parent::__construct($message);
        $this->upper = $value + $margin;
        $this->lower = $value - $margin;
    }

    /**
     *    Tests the expectation. True if it matches the
     *    held value.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test($compare) {
        return (($compare <= $this->upper) && ($compare >= $this->lower));
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        if ($this->test($compare)) {
            return $this->withinMessage($compare);
        } else {
            return $this->outsideMessage($compare);
        }
    }

    /**
     *    Creates a the message for being within the range.
     *    @param mixed $compare        Value being tested.
     *    @access private
     */
    protected function withinMessage($compare) {
        return "Within expectation [" . $this->dumper->describeValue($this->lower) . "] and [" .
                $this->dumper->describeValue($this->upper) . "]";
    }

    /**
     *    Creates a the message for being within the range.
     *    @param mixed $compare        Value being tested.
     *    @access private
     */
    protected function outsideMessage($compare) {
        if ($compare > $this->upper) {
            return "Outside expectation " .
                    $this->dumper->describeDifference($compare, $this->upper);
        } else {
            return "Outside expectation " .
                    $this->dumper->describeDifference($compare, $this->lower);
        }
    }
}

/**
 *    Test for being outside of a range.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class OutsideMarginExpectation extends WithinMarginExpectation {

    /**
     *    Sets the value to compare against and the fuzziness of
     *    the match. Used for comparing floating point values.
     *    @param mixed $value        Test value to not match.
     *    @param mixed $margin       Fuzziness of match.
     *    @param string $message     Customised message on failure.
     *    @access public
     */
    function __construct($value, $margin, $message = '%s') {
        parent::__construct($value, $margin, $message);
    }

    /**
     *    Tests the expectation. True if it matches the
     *    held value.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test($compare) {
        return ! parent::test($compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        if (! $this->test($compare)) {
            return $this->withinMessage($compare);
        } else {
            return $this->outsideMessage($compare);
        }
    }
}

/**
 *    Test for reference.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class ReferenceExpectation {
    private $value;

    /**
     *    Sets the reference value to compare against.
     *    @param mixed $value       Test reference to match.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function __construct(&$value, $message = '%s') {
        $this->message = $message;
        $this->value = &$value;
    }

    /**
     *    Tests the expectation. True if it exactly
     *    references the held value.
     *    @param mixed $compare        Comparison reference.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test(&$compare) {
        return SimpleTestCompatibility::isReference($this->value, $compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        if ($this->test($compare)) {
            return "Reference expectation [" . $this->dumper->describeValue($this->value) . "]";
        } else {
            return "Reference expectation fails " .
                    $this->dumper->describeDifference($this->value, $compare);
        }
    }

    /**
     *    Overlays the generated message onto the stored user
     *    message. An additional message can be interjected.
     *    @param mixed $compare        Comparison value.
     *    @param SimpleDumper $dumper  For formatting the results.
     *    @return string               Description of success
     *                                 or failure.
     *    @access public
     */
    function overlayMessage($compare, $dumper) {
        $this->dumper = $dumper;
        return sprintf($this->message, $this->testMessage($compare));
    }

    /**
     *    Accessor for the dumper.
     *    @return SimpleDumper    Current value dumper.
     *    @access protected
     */
    protected function getDumper() {
        if (! $this->dumper) {
            $dumper = new SimpleDumper();
            return $dumper;
        }
        return $this->dumper;
    }
}

/**
 *    Test for identity.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class IdenticalExpectation extends EqualExpectation {

    /**
     *    Sets the value to compare against.
     *    @param mixed $value       Test value to match.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function __construct($value, $message = '%s') {
        parent::__construct($value, $message);
    }

    /**
     *    Tests the expectation. True if it exactly
     *    matches the held value.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test($compare) {
        return SimpleTestCompatibility::isIdentical($this->getValue(), $compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        if ($this->test($compare)) {
            return "Identical expectation [" . $dumper->describeValue($this->getValue()) . "]";
        } else {
            return "Identical expectation [" . $dumper->describeValue($this->getValue()) .
                    "] fails with [" .
                    $dumper->describeValue($compare) . "] " .
                    $dumper->describeDifference($this->getValue(), $compare, TYPE_MATTERS);
        }
    }
}

/**
 *    Test for non-identity.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NotIdenticalExpectation extends IdenticalExpectation {

    /**
     *    Sets the value to compare against.
     *    @param mixed $value        Test value to match.
     *    @param string $message     Customised message on failure.
     *    @access public
     */
    function __construct($value, $message = '%s') {
        parent::__construct($value, $message);
    }

    /**
     *    Tests the expectation. True if it differs from the
     *    held value.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test($compare) {
        return ! parent::test($compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        if ($this->test($compare)) {
            return "Not identical expectation passes " .
                    $dumper->describeDifference($this->getValue(), $compare, TYPE_MATTERS);
        } else {
            return "Not identical expectation [" . $dumper->describeValue($this->getValue()) . "] matches";
        }
    }
}

/**
 *    Test for a pattern using Perl regex rules.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class PatternExpectation extends SimpleExpectation {
    private $pattern;

    /**
     *    Sets the value to compare against.
     *    @param string $pattern    Pattern to search for.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function __construct($pattern, $message = '%s') {
        parent::__construct($message);
        $this->pattern = $pattern;
    }

    /**
     *    Accessor for the pattern.
     *    @return string       Perl regex as string.
     *    @access protected
     */
    protected function getPattern() {
        return $this->pattern;
    }

    /**
     *    Tests the expectation. True if the Perl regex
     *    matches the comparison value.
     *    @param string $compare        Comparison value.
     *    @return boolean               True if correct.
     *    @access public
     */
    function test($compare) {
        return (boolean)preg_match($this->getPattern(), $compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        if ($this->test($compare)) {
            return $this->describePatternMatch($this->getPattern(), $compare);
        } else {
            $dumper = $this->getDumper();
            return "Pattern [" . $this->getPattern() .
                    "] not detected in [" .
                    $dumper->describeValue($compare) . "]";
        }
    }

    /**
     *    Describes a pattern match including the string
     *    found and it's position.
     *    @param string $pattern        Regex to match against.
     *    @param string $subject        Subject to search.
     *    @access protected
     */
    protected function describePatternMatch($pattern, $subject) {
        preg_match($pattern, $subject, $matches);
        $position = strpos($subject, $matches[0]);
        $dumper = $this->getDumper();
        return "Pattern [$pattern] detected at character [$position] in [" .
                $dumper->describeValue($subject) . "] as [" .
                $matches[0] . "] in region [" .
                $dumper->clipString($subject, 100, $position) . "]";
    }
}

/**
 *    Fail if a pattern is detected within the
 *    comparison.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NoPatternExpectation extends PatternExpectation {

    /**
     *    Sets the reject pattern
     *    @param string $pattern    Pattern to search for.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function __construct($pattern, $message = '%s') {
        parent::__construct($pattern, $message);
    }

    /**
     *    Tests the expectation. False if the Perl regex
     *    matches the comparison value.
     *    @param string $compare        Comparison value.
     *    @return boolean               True if correct.
     *    @access public
     */
    function test($compare) {
        return ! parent::test($compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param string $compare      Comparison value.
     *    @return string              Description of success
     *                                or failure.
     *    @access public
     */
    function testMessage($compare) {
        if ($this->test($compare)) {
            $dumper = $this->getDumper();
            return "Pattern [" . $this->getPattern() .
                    "] not detected in [" .
                    $dumper->describeValue($compare) . "]";
        } else {
            return $this->describePatternMatch($this->getPattern(), $compare);
        }
    }
}

/**
 *    Tests either type or class name if it's an object.
 *      @package SimpleTest
 *      @subpackage UnitTester
 */
class IsAExpectation extends SimpleExpectation {
    private $type;

    /**
     *    Sets the type to compare with.
     *    @param string $type       Type or class name.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function __construct($type, $message = '%s') {
        parent::__construct($message);
        $this->type = $type;
    }

    /**
     *    Accessor for type to check against.
     *    @return string    Type or class name.
     *    @access protected
     */
    protected function getType() {
        return $this->type;
    }

    /**
     *    Tests the expectation. True if the type or
     *    class matches the string value.
     *    @param string $compare        Comparison value.
     *    @return boolean               True if correct.
     *    @access public
     */
    function test($compare) {
        if (is_object($compare)) {
            return SimpleTestCompatibility::isA($compare, $this->type);
        } else {
            return (strtolower(gettype($compare)) == $this->canonicalType($this->type));
        }
    }

    /**
     *    Coerces type name into a gettype() match.
     *    @param string $type        User type.
     *    @return string             Simpler type.
     *    @access private
     */
    protected function canonicalType($type) {
        $type = strtolower($type);
        $map = array(
                'bool' => 'boolean',
                'float' => 'double',
                'real' => 'double',
                'int' => 'integer');
        if (isset($map[$type])) {
            $type = $map[$type];
        }
        return $type;
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        return "Value [" . $dumper->describeValue($compare) .
                "] should be type [" . $this->type . "]";
    }
}

/**
 *    Tests either type or class name if it's an object.
 *    Will succeed if the type does not match.
 *      @package SimpleTest
 *      @subpackage UnitTester
 */
class NotAExpectation extends IsAExpectation {
    private $type;

    /**
     *    Sets the type to compare with.
     *    @param string $type       Type or class name.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function __construct($type, $message = '%s') {
        parent::__construct($type, $message);
    }

    /**
     *    Tests the expectation. False if the type or
     *    class matches the string value.
     *    @param string $compare        Comparison value.
     *    @return boolean               True if different.
     *    @access public
     */
    function test($compare) {
        return ! parent::test($compare);
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        return "Value [" . $dumper->describeValue($compare) .
                "] should not be type [" . $this->getType() . "]";
    }
}

/**
 *    Tests for existance of a method in an object
 *      @package SimpleTest
 *      @subpackage UnitTester
 */
class MethodExistsExpectation extends SimpleExpectation {
    private $method;

    /**
     *    Sets the value to compare against.
     *    @param string $method     Method to check.
     *    @param string $message    Customised message on failure.
     *    @access public
     *    @return void
     */
    function __construct($method, $message = '%s') {
        parent::__construct($message);
        $this->method = &$method;
    }

    /**
     *    Tests the expectation. True if the method exists in the test object.
     *    @param string $compare        Comparison method name.
     *    @return boolean               True if correct.
     *    @access public
     */
    function test($compare) {
        return (boolean)(is_object($compare) && method_exists($compare, $this->method));
    }

    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = $this->getDumper();
        if (! is_object($compare)) {
            return 'No method on non-object [' . $dumper->describeValue($compare) . ']';
        }
        $method = $this->method;
        return "Object [" . $dumper->describeValue($compare) .
                "] should contain method [$method]";
    }
}
?>