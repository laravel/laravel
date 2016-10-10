<?php
    /**
     *	adapter for SimpleTest to use PEAR PHPUnit test cases
     *	@package	SimpleTest
     *	@subpackage Extensions
     *	@version	$Id: pear_test_case.php 1770 2008-04-20 01:09:16Z lastcraft $
     */
    
    /**#@+
     * include SimpleTest files
     */
    require_once(dirname(__FILE__) . '/../dumper.php');
    require_once(dirname(__FILE__) . '/../compatibility.php');
    require_once(dirname(__FILE__) . '/../test_case.php');
    require_once(dirname(__FILE__) . '/../expectation.php');
	/**#@-*/
   
    /**
     *    Adapter for PEAR PHPUnit test case to allow
     *    legacy PEAR test cases to be used with SimpleTest.
     *    @package      SimpleTest
     *    @subpackage   Extensions
     */
    class PHPUnit_TestCase extends SimpleTestCase {
        private $_loosely_typed;
        
        /**
         *    Constructor. Sets the test name.
         *    @param $label        Test name to display.
         *    @public
         */
        function PHPUnit_TestCase($label = false) {
            parent::__construct($label);
            $this->_loosely_typed = false;
        }
        
        /**
         *    Will test straight equality if set to loose
         *    typing, or identity if not.
         *    @param $first          First value.
         *    @param $second         Comparison value.
         *    @param $message        Message to display.
         *    @public
         */
        function assertEquals($first, $second, $message = "%s", $delta = 0) {
            if ($this->_loosely_typed) {
                $expectation = new EqualExpectation($first);
            } else {
                $expectation = new IdenticalExpectation($first);
            }
            $this->assert($expectation, $second, $message);
        }
        
        /**
         *    Passes if the value tested is not null.
         *    @param $value          Value to test against.
         *    @param $message        Message to display.
         *    @public
         */
        function assertNotNull($value, $message = "%s") {
            parent::assert(new TrueExpectation(), isset($value), $message);
        }
        
        /**
         *    Passes if the value tested is null.
         *    @param $value          Value to test against.
         *    @param $message        Message to display.
         *    @public
         */
        function assertNull($value, $message = "%s") {
            parent::assert(new TrueExpectation(), !isset($value), $message);
        }
        
        /**
         *    Identity test tests for the same object.
         *    @param $first          First object handle.
         *    @param $second         Hopefully the same handle.
         *    @param $message        Message to display.
         *    @public
         */
        function assertSame($first, $second, $message = "%s") {
            $dumper = new SimpleDumper();
            $message = sprintf(
                    $message,
                    "[" . $dumper->describeValue($first) .
                            "] and [" . $dumper->describeValue($second) .
                            "] should reference the same object");
            return $this->assert(
					new TrueExpectation(),
                    SimpleTestCompatibility::isReference($first, $second),
                    $message);
        }
        
        /**
         *    Inverted identity test.
         *    @param $first          First object handle.
         *    @param $second         Hopefully a different handle.
         *    @param $message        Message to display.
         *    @public
         */
        function assertNotSame($first, $second, $message = "%s") {
            $dumper = new SimpleDumper();
            $message = sprintf(
                    $message,
                    "[" . $dumper->describeValue($first) .
                            "] and [" . $dumper->describeValue($second) .
                            "] should not be the same object");
            return $this->assert(
					new falseExpectation(),
                    SimpleTestCompatibility::isReference($first, $second),
                    $message);
        }
        
        /**
         *    Sends pass if the test condition resolves true,
         *    a fail otherwise.
         *    @param $condition      Condition to test true.
         *    @param $message        Message to display.
         *    @public
         */
        function assertTrue($condition, $message = "%s") {
            parent::assert(new TrueExpectation(), $condition, $message);
        }
        
        /**
         *    Sends pass if the test condition resolves false,
         *    a fail otherwise.
         *    @param $condition      Condition to test false.
         *    @param $message        Message to display.
         *    @public
         */
        function assertFalse($condition, $message = "%s") {
            parent::assert(new FalseExpectation(), $condition, $message);
        }
        
        /**
         *    Tests a regex match. Needs refactoring.
         *    @param $pattern        Regex to match.
         *    @param $subject        String to search in.
         *    @param $message        Message to display.
         *    @public
         */
        function assertRegExp($pattern, $subject, $message = "%s") {
            $this->assert(new PatternExpectation($pattern), $subject, $message);
        }
        
        /**
         *    Tests the type of a value.
         *    @param $value          Value to take type of.
         *    @param $type           Hoped for type.
         *    @param $message        Message to display.
         *    @public
         */
        function assertType($value, $type, $message = "%s") {
            parent::assert(new TrueExpectation(), gettype($value) == strtolower($type), $message);
        }
        
        /**
         *    Sets equality operation to act as a simple equal
         *    comparison only, allowing a broader range of
         *    matches.
         *    @param $loosely_typed     True for broader comparison.
         *    @public
         */
        function setLooselyTyped($loosely_typed) {
            $this->_loosely_typed = $loosely_typed;
        }

        /**
         *    For progress indication during
         *    a test amongst other things.
         *    @return            Usually one.
         *    @public
         */
        function countTestCases() {
            return $this->getSize();
        }
        
        /**
         *    Accessor for name, normally just the class
         *    name.
         *    @public
         */
        function getName() {
            return $this->getLabel();
        }
        
        /**
         *    Does nothing. For compatibility only.
         *    @param $name        Dummy
         *    @public
         */
        function setName($name) {
        }
    }
?>