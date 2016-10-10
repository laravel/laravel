<?php
/**
 *  Base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: test_case.php 1786 2008-04-26 17:32:20Z pp11 $
 */

/**#@+
 * Includes SimpleTest files and defined the root constant
 * for dependent libraries.
 */
require_once(dirname(__FILE__) . '/invoker.php');
require_once(dirname(__FILE__) . '/errors.php');
require_once(dirname(__FILE__) . '/compatibility.php');
require_once(dirname(__FILE__) . '/scorer.php');
require_once(dirname(__FILE__) . '/expectation.php');
require_once(dirname(__FILE__) . '/dumper.php');
require_once(dirname(__FILE__) . '/simpletest.php');
require_once(dirname(__FILE__) . '/exceptions.php');
require_once(dirname(__FILE__) . '/reflection_php5.php');
if (! defined('SIMPLE_TEST')) {
    /**
     * @ignore
     */
    define('SIMPLE_TEST', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
/**#@-*/

/**
 *    Basic test case. This is the smallest unit of a test
 *    suite. It searches for
 *    all methods that start with the the string "test" and
 *    runs them. Working test cases extend this class.
 *    @package      SimpleTest
 *    @subpackage   UnitTester
 */
class SimpleTestCase {
    private $label = false;
    protected $reporter;
    private $observers;
    private $should_skip = false;

    /**
     *    Sets up the test with no display.
     *    @param string $label    If no test name is given then
     *                            the class name is used.
     *    @access public
     */
    function __construct($label = false) {
        if ($label) {
            $this->label = $label;
        }
    }

    /**
     *    Accessor for the test name for subclasses.
     *    @return string           Name of the test.
     *    @access public
     */
    function getLabel() {
        return $this->label ? $this->label : get_class($this);
    }

    /**
     *    This is a placeholder for skipping tests. In this
     *    method you place skipIf() and skipUnless() calls to
     *    set the skipping state.
     *    @access public
     */
    function skip() {
    }

    /**
     *    Will issue a message to the reporter and tell the test
     *    case to skip if the incoming flag is true.
     *    @param string $should_skip    Condition causing the tests to be skipped.
     *    @param string $message        Text of skip condition.
     *    @access public
     */
    function skipIf($should_skip, $message = '%s') {
        if ($should_skip && ! $this->should_skip) {
            $this->should_skip = true;
            $message = sprintf($message, 'Skipping [' . get_class($this) . ']');
            $this->reporter->paintSkip($message . $this->getAssertionLine());
        }
    }

    /**
     *    Accessor for the private variable $_shoud_skip
     *    @access public
     */
    function shouldSkip() {
        return $this->should_skip;
    }
    
    /**
     *    Will issue a message to the reporter and tell the test
     *    case to skip if the incoming flag is false.
     *    @param string $shouldnt_skip  Condition causing the tests to be run.
     *    @param string $message        Text of skip condition.
     *    @access public
     */
    function skipUnless($shouldnt_skip, $message = false) {
        $this->skipIf(! $shouldnt_skip, $message);
    }

    /**
     *    Used to invoke the single tests.
     *    @return SimpleInvoker        Individual test runner.
     *    @access public
     */
    function createInvoker() {
        return new SimpleExceptionTrappingInvoker(
                new SimpleErrorTrappingInvoker(new SimpleInvoker($this)));
    }

    /**
     *    Uses reflection to run every method within itself
     *    starting with the string "test" unless a method
     *    is specified.
     *    @param SimpleReporter $reporter    Current test reporter.
     *    @return boolean                    True if all tests passed.
     *    @access public
     */
    function run($reporter) {
        $context = SimpleTest::getContext();
        $context->setTest($this);
        $context->setReporter($reporter);
        $this->reporter = $reporter;
        $started = false;
        foreach ($this->getTests() as $method) {
            if ($reporter->shouldInvoke($this->getLabel(), $method)) {
                $this->skip();
                if ($this->should_skip) {
                    break;
                }
                if (! $started) {
                    $reporter->paintCaseStart($this->getLabel());
                    $started = true;
                }
                $invoker = $this->reporter->createInvoker($this->createInvoker());
                $invoker->before($method);
                $invoker->invoke($method);
                $invoker->after($method);
            }
        }
        if ($started) {
            $reporter->paintCaseEnd($this->getLabel());
        }
        unset($this->reporter);
        return $reporter->getStatus();
    }

    /**
     *    Gets a list of test names. Normally that will
     *    be all internal methods that start with the
     *    name "test". This method should be overridden
     *    if you want a different rule.
     *    @return array        List of test names.
     *    @access public
     */
    function getTests() {
        $methods = array();
        foreach (get_class_methods(get_class($this)) as $method) {
            if ($this->isTest($method)) {
                $methods[] = $method;
            }
        }
        return $methods;
    }

    /**
     *    Tests to see if the method is a test that should
     *    be run. Currently any method that starts with 'test'
     *    is a candidate unless it is the constructor.
     *    @param string $method        Method name to try.
     *    @return boolean              True if test method.
     *    @access protected
     */
    protected function isTest($method) {
        if (strtolower(substr($method, 0, 4)) == 'test') {
            return ! SimpleTestCompatibility::isA($this, strtolower($method));
        }
        return false;
    }

    /**
     *    Announces the start of the test.
     *    @param string $method    Test method just started.
     *    @access public
     */
    function before($method) {
        $this->reporter->paintMethodStart($method);
        $this->observers = array();
    }

    /**
     *    Sets up unit test wide variables at the start
     *    of each test method. To be overridden in
     *    actual user test cases.
     *    @access public
     */
    function setUp() {
    }

    /**
     *    Clears the data set in the setUp() method call.
     *    To be overridden by the user in actual user test cases.
     *    @access public
     */
    function tearDown() {
    }

    /**
     *    Announces the end of the test. Includes private clean up.
     *    @param string $method    Test method just finished.
     *    @access public
     */
    function after($method) {
        for ($i = 0; $i < count($this->observers); $i++) {
            $this->observers[$i]->atTestEnd($method, $this);
        }
        $this->reporter->paintMethodEnd($method);
    }

    /**
     *    Sets up an observer for the test end.
     *    @param object $observer    Must have atTestEnd()
     *                               method.
     *    @access public
     */
    function tell($observer) {
        $this->observers[] = &$observer;
    }

    /**
     *    @deprecated
     */
    function pass($message = "Pass") {
        if (! isset($this->reporter)) {
            trigger_error('Can only make assertions within test methods');
        }
        $this->reporter->paintPass(
                $message . $this->getAssertionLine());
        return true;
    }

    /**
     *    Sends a fail event with a message.
     *    @param string $message        Message to send.
     *    @access public
     */
    function fail($message = "Fail") {
        if (! isset($this->reporter)) {
            trigger_error('Can only make assertions within test methods');
        }
        $this->reporter->paintFail(
                $message . $this->getAssertionLine());
        return false;
    }

    /**
     *    Formats a PHP error and dispatches it to the
     *    reporter.
     *    @param integer $severity  PHP error code.
     *    @param string $message    Text of error.
     *    @param string $file       File error occoured in.
     *    @param integer $line      Line number of error.
     *    @access public
     */
    function error($severity, $message, $file, $line) {
        if (! isset($this->reporter)) {
            trigger_error('Can only make assertions within test methods');
        }
        $this->reporter->paintError(
                "Unexpected PHP error [$message] severity [$severity] in [$file line $line]");
    }

    /**
     *    Formats an exception and dispatches it to the
     *    reporter.
     *    @param Exception $exception    Object thrown.
     *    @access public
     */
    function exception($exception) {
        $this->reporter->paintException($exception);
    }

    /**
     *    For user defined expansion of the available messages.
     *    @param string $type       Tag for sorting the signals.
     *    @param mixed $payload     Extra user specific information.
     */
    function signal($type, $payload) {
        if (! isset($this->reporter)) {
            trigger_error('Can only make assertions within test methods');
        }
        $this->reporter->paintSignal($type, $payload);
    }

    /**
     *    Runs an expectation directly, for extending the
     *    tests with new expectation classes.
     *    @param SimpleExpectation $expectation  Expectation subclass.
     *    @param mixed $compare               Value to compare.
     *    @param string $message                 Message to display.
     *    @return boolean                        True on pass
     *    @access public
     */
    function assert($expectation, $compare, $message = '%s') {
        if ($expectation->test($compare)) {
            return $this->pass(sprintf(
                    $message,
                    $expectation->overlayMessage($compare, $this->reporter->getDumper())));
        } else {
            return $this->fail(sprintf(
                    $message,
                    $expectation->overlayMessage($compare, $this->reporter->getDumper())));
        }
    }

    /**
     *    Uses a stack trace to find the line of an assertion.
     *    @return string           Line number of first assert*
     *                             method embedded in format string.
     *    @access public
     */
    function getAssertionLine() {
        $trace = new SimpleStackTrace(array('assert', 'expect', 'pass', 'fail', 'skip'));
        return $trace->traceMethod();
    }

    /**
     *    Sends a formatted dump of a variable to the
     *    test suite for those emergency debugging
     *    situations.
     *    @param mixed $variable    Variable to display.
     *    @param string $message    Message to display.
     *    @return mixed             The original variable.
     *    @access public
     */
    function dump($variable, $message = false) {
        $dumper = $this->reporter->getDumper();
        $formatted = $dumper->dump($variable);
        if ($message) {
            $formatted = $message . "\n" . $formatted;
        }
        $this->reporter->paintFormattedMessage($formatted);
        return $variable;
    }

    /**
     *    Accessor for the number of subtests including myelf.
     *    @return integer           Number of test cases.
     *    @access public
     */
    function getSize() {
        return 1;
    }
}

/**
 *  Helps to extract test cases automatically from a file.
 */
class SimpleFileLoader {

    /**
     *    Builds a test suite from a library of test cases.
     *    The new suite is composed into this one.
     *    @param string $test_file        File name of library with
     *                                    test case classes.
     *    @return TestSuite               The new test suite.
     *    @access public
     */
    function load($test_file) {
        $existing_classes = get_declared_classes();
        $existing_globals = get_defined_vars();
        include_once($test_file);
        $new_globals = get_defined_vars();
        $this->makeFileVariablesGlobal($existing_globals, $new_globals);
        $new_classes = array_diff(get_declared_classes(), $existing_classes);
        if (empty($new_classes)) {
            $new_classes = $this->scrapeClassesFromFile($test_file);
        }
        $classes = $this->selectRunnableTests($new_classes);
        return $this->createSuiteFromClasses($test_file, $classes);
    }
    
    /**
     *    Imports new variables into the global namespace.
     *    @param hash $existing   Variables before the file was loaded.
     *    @param hash $new        Variables after the file was loaded.
     *    @access private
     */
    protected function makeFileVariablesGlobal($existing, $new) {
        $globals = array_diff(array_keys($new), array_keys($existing));
        foreach ($globals as $global) {
            $_GLOBALS[$global] = $new[$global];
        }
    }
    
    /**
     *    Lookup classnames from file contents, in case the
     *    file may have been included before.
     *    Note: This is probably too clever by half. Figuring this
     *    out after a failed test case is going to be tricky for us,
     *    never mind the user. A test case should not be included
     *    twice anyway.
     *    @param string $test_file        File name with classes.
     *    @access private
     */
    protected function scrapeClassesFromFile($test_file) {
        preg_match_all('~^\s*class\s+(\w+)(\s+(extends|implements)\s+\w+)*\s*\{~mi',
                        file_get_contents($test_file),
                        $matches );
        return $matches[1];
    }

    /**
     *    Calculates the incoming test cases. Skips abstract
     *    and ignored classes.
     *    @param array $candidates   Candidate classes.
     *    @return array              New classes which are test
     *                               cases that shouldn't be ignored.
     *    @access public
     */
    function selectRunnableTests($candidates) {
        $classes = array();
        foreach ($candidates as $class) {
            if (TestSuite::getBaseTestCase($class)) {
                $reflection = new SimpleReflection($class);
                if ($reflection->isAbstract()) {
                    SimpleTest::ignore($class);
                } else {
                    $classes[] = $class;
                }
            }
        }
        return $classes;
    }

    /**
     *    Builds a test suite from a class list.
     *    @param string $title       Title of new group.
     *    @param array $classes      Test classes.
     *    @return TestSuite          Group loaded with the new
     *                               test cases.
     *    @access public
     */
    function createSuiteFromClasses($title, $classes) {
        if (count($classes) == 0) {
            $suite = new BadTestSuite($title, "No runnable test cases in [$title]");
            return $suite;
        }
        SimpleTest::ignoreParentsIfIgnored($classes);
        $suite = new TestSuite($title);
        foreach ($classes as $class) {
            if (! SimpleTest::isIgnored($class)) {
                $suite->add($class);
            }
        }
        return $suite;
    }
}

/**
 *    This is a composite test class for combining
 *    test cases and other RunnableTest classes into
 *    a group test.
 *    @package      SimpleTest
 *    @subpackage   UnitTester
 */
class TestSuite {
    private $label;
    private $test_cases;

    /**
     *    Sets the name of the test suite.
     *    @param string $label    Name sent at the start and end
     *                            of the test.
     *    @access public
     */
    function TestSuite($label = false) {
        $this->label = $label;
        $this->test_cases = array();
    }

    /**
     *    Accessor for the test name for subclasses. If the suite
     *    wraps a single test case the label defaults to the name of that test.
     *    @return string           Name of the test.
     *    @access public
     */
    function getLabel() {
        if (! $this->label) {
            return ($this->getSize() == 1) ?
                    get_class($this->test_cases[0]) : get_class($this);
        } else {
            return $this->label;
        }
    }

    /**
     *    Adds a test into the suite by instance or class. The class will
     *    be instantiated if it's a test suite.
     *    @param SimpleTestCase $test_case  Suite or individual test
     *                                      case implementing the
     *                                      runnable test interface.
     *    @access public
     */
    function add($test_case) {
        if (! is_string($test_case)) {
            $this->test_cases[] = $test_case;
        } elseif (TestSuite::getBaseTestCase($test_case) == 'testsuite') {
            $this->test_cases[] = new $test_case();
        } else {
            $this->test_cases[] = $test_case;
        }
    }

    /**
     *    Builds a test suite from a library of test cases.
     *    The new suite is composed into this one.
     *    @param string $test_file        File name of library with
     *                                    test case classes.
     *    @access public
     */
    function addFile($test_file) {
        $extractor = new SimpleFileLoader();
        $this->add($extractor->load($test_file));
    }

    /**
     *    Delegates to a visiting collector to add test
     *    files.
     *    @param string $path                  Path to scan from.
     *    @param SimpleCollector $collector    Directory scanner.
     *    @access public
     */
    function collect($path, $collector) {
        $collector->collect($this, $path);
    }

    /**
     *    Invokes run() on all of the held test cases, instantiating
     *    them if necessary.
     *    @param SimpleReporter $reporter    Current test reporter.
     *    @access public
     */
    function run($reporter) {
        $reporter->paintGroupStart($this->getLabel(), $this->getSize());
        for ($i = 0, $count = count($this->test_cases); $i < $count; $i++) {
            if (is_string($this->test_cases[$i])) {
                $class = $this->test_cases[$i];
                $test = new $class();
                $test->run($reporter);
                unset($test);
            } else {
                $this->test_cases[$i]->run($reporter);
            }
        }
        $reporter->paintGroupEnd($this->getLabel());
        return $reporter->getStatus();
    }

    /**
     *    Number of contained test cases.
     *    @return integer     Total count of cases in the group.
     *    @access public
     */
    function getSize() {
        $count = 0;
        foreach ($this->test_cases as $case) {
            if (is_string($case)) {
                if (! SimpleTest::isIgnored($case)) {
                    $count++;
                }
            } else {
                $count += $case->getSize();
            }
        }
        return $count;
    }

    /**
     *    Test to see if a class is derived from the
     *    SimpleTestCase class.
     *    @param string $class     Class name.
     *    @access public
     */
    static function getBaseTestCase($class) {
        while ($class = get_parent_class($class)) {
            $class = strtolower($class);
            if ($class == 'simpletestcase' || $class == 'testsuite') {
                return $class;
            }
        }
        return false;
    }
}

/**
 *    This is a failing group test for when a test suite hasn't
 *    loaded properly.
 *    @package      SimpleTest
 *    @subpackage   UnitTester
 */
class BadTestSuite {
    private $label;
    private $error;

    /**
     *    Sets the name of the test suite and error message.
     *    @param string $label    Name sent at the start and end
     *                            of the test.
     *    @access public
     */
    function BadTestSuite($label, $error) {
        $this->label = $label;
        $this->error = $error;
    }

    /**
     *    Accessor for the test name for subclasses.
     *    @return string           Name of the test.
     *    @access public
     */
    function getLabel() {
        return $this->label;
    }

    /**
     *    Sends a single error to the reporter.
     *    @param SimpleReporter $reporter    Current test reporter.
     *    @access public
     */
    function run($reporter) {
        $reporter->paintGroupStart($this->getLabel(), $this->getSize());
        $reporter->paintFail('Bad TestSuite [' . $this->getLabel() .
                '] with error [' . $this->error . ']');
        $reporter->paintGroupEnd($this->getLabel());
        return $reporter->getStatus();
    }

    /**
     *    Number of contained test cases. Always zero.
     *    @return integer     Total count of cases in the group.
     *    @access public
     */
    function getSize() {
        return 0;
    }
}
?>