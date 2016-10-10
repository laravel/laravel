<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: exceptions.php 1769 2008-04-19 14:39:00Z pp11 $
 */

/**#@+
 * Include required SimpleTest files 
 */
require_once dirname(__FILE__) . '/invoker.php';
require_once dirname(__FILE__) . '/expectation.php';
/**#@-*/

/**
 *    Extension that traps exceptions and turns them into
 *    an error message. PHP5 only.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class SimpleExceptionTrappingInvoker extends SimpleInvokerDecorator {

    /**
     *    Stores the invoker to be wrapped.
     *    @param SimpleInvoker $invoker   Test method runner.
     */
    function __construct($invoker) {
        parent::__construct($invoker);
    }

    /**
     *    Invokes a test method whilst trapping expected
     *    exceptions. Any left over unthrown exceptions
     *    are then reported as failures.
     *    @param string $method    Test method to call.
     */
    function invoke($method) {
        $trap = SimpleTest::getContext()->get('SimpleExceptionTrap');
        $trap->clear();
        try {
            $has_thrown = false;
            parent::invoke($method);
        } catch (Exception $exception) {
            $has_thrown = true;
            if (! $trap->isExpected($this->getTestCase(), $exception)) {
                $this->getTestCase()->exception($exception);
            }
            $trap->clear();
        }
        if ($message = $trap->getOutstanding()) {
            $this->getTestCase()->fail($message);
        }
        if ($has_thrown) {
            try {
                parent::getTestCase()->tearDown();
            } catch (Exception $e) { }
        }
    }
}

/**
 *    Tests exceptions either by type or the exact
 *    exception. This could be improved to accept
 *    a pattern expectation to test the error
 *    message, but that will have to come later.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class ExceptionExpectation extends SimpleExpectation {
    private $expected;

    /**
     *    Sets up the conditions to test against.
     *    If the expected value is a string, then
     *    it will act as a test of the class name.
     *    An exception as the comparison will
     *    trigger an identical match. Writing this
     *    down now makes it look doubly dumb. I hope
     *    come up with a better scheme later.
     *    @param mixed $expected   A class name or an actual
     *                             exception to compare with.
     *    @param string $message   Message to display.
     */
    function __construct($expected, $message = '%s') {
        $this->expected = $expected;
        parent::__construct($message);
    }

    /**
     *    Carry out the test.
     *    @param Exception $compare    Value to check.
     *    @return boolean              True if matched.
     */
    function test($compare) {
        if (is_string($this->expected)) {
            return ($compare instanceof $this->expected);
        }
        if (get_class($compare) != get_class($this->expected)) {
            return false;
        }
        return $compare->getMessage() == $this->expected->getMessage();
    }

    /**
     *    Create the message to display describing the test.
     *    @param Exception $compare     Exception to match.
     *    @return string                Final message.
     */
    function testMessage($compare) {
        if (is_string($this->expected)) {
            return "Exception [" . $this->describeException($compare) .
                    "] should be type [" . $this->expected . "]";
        }
        return "Exception [" . $this->describeException($compare) .
                "] should match [" .
                $this->describeException($this->expected) . "]";
    }

    /**
     *    Summary of an Exception object.
     *    @param Exception $compare     Exception to describe.
     *    @return string                Text description.
     */
    protected function describeException($exception) {
        return get_class($exception) . ": " . $exception->getMessage();
    }
}

/**
 *    Stores expected exceptions for when they
 *    get thrown. Saves the irritating try...catch
 *    block.
 *    @package  SimpleTest
 *    @subpackage   UnitTester
 */
class SimpleExceptionTrap {
    private $expected;
    private $message;

    /**
     *    Clears down the queue ready for action.
     */
    function __construct() {
        $this->clear();
    }

    /**
     *    Sets up an expectation of an exception.
     *    This has the effect of intercepting an
     *    exception that matches.
     *    @param SimpleExpectation $expected    Expected exception to match.
     *    @param string $message                Message to display.
     *    @access public
     */
    function expectException($expected = false, $message = '%s') {
        if ($expected === false) {
            $expected = new AnythingExpectation();
        }
        if (! SimpleExpectation::isExpectation($expected)) {
            $expected = new ExceptionExpectation($expected);
        }
        $this->expected = $expected;
        $this->message = $message;
    }

    /**
     *    Compares the expected exception with any
     *    in the queue. Issues a pass or fail and
     *    returns the state of the test.
     *    @param SimpleTestCase $test    Test case to send messages to.
     *    @param Exception $exception    Exception to compare.
     *    @return boolean                False on no match.
     */
    function isExpected($test, $exception) {
        if ($this->expected) {
            return $test->assert($this->expected, $exception, $this->message);
        }
        return false;
    }

    /**
     *    Tests for any left over exception.
     *    @return string/false     The failure message or false if none.
     */
    function getOutstanding() {
        return sprintf($this->message, 'Failed to trap exception');
    }

    /**
     *    Discards the contents of the error queue.
     */
    function clear() {
        $this->expected = false;
        $this->message = false;
    }
}
?>