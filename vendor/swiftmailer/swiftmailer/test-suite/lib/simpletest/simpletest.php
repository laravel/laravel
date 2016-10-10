<?php
/**
 *  Global state for SimpleTest and kicker script in future versions.
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: simpletest.php 1786 2008-04-26 17:32:20Z pp11 $
 */

/**#@+
 * include SimpleTest files
 */
if (version_compare(phpversion(), '5') >= 0) {
    require_once(dirname(__FILE__) . '/reflection_php5.php');
} else {
    require_once(dirname(__FILE__) . '/reflection_php4.php');
}
require_once(dirname(__FILE__) . '/default_reporter.php');
require_once(dirname(__FILE__) . '/compatibility.php');
/**#@-*/

/**
 *    Registry and test context. Includes a few
 *    global options that I'm slowly getting rid of.
 *    @package  SimpleTest
 *    @subpackage   UnitTester
 */
class SimpleTest {

    /**
     *    Reads the SimpleTest version from the release file.
     *    @return string        Version string.
     *    @access public
     */
    static function getVersion() {
        $content = file(dirname(__FILE__) . '/VERSION');
        return trim($content[0]);
    }

    /**
     *    Sets the name of a test case to ignore, usually
     *    because the class is an abstract case that should
     *    not be run. Once PHP4 is dropped this will disappear
     *    as a public method and "abstract" will rule.
     *    @param string $class        Add a class to ignore.
     *    @access public
     */
    static function ignore($class) {
        $registry = &SimpleTest::getRegistry();
        $registry['IgnoreList'][strtolower($class)] = true;
    }

    /**
     *    Scans the now complete ignore list, and adds
     *    all parent classes to the list. If a class
     *    is not a runnable test case, then it's parents
     *    wouldn't be either. This is syntactic sugar
     *    to cut down on ommissions of ignore()'s or
     *    missing abstract declarations. This cannot
     *    be done whilst loading classes wiithout forcing
     *    a particular order on the class declarations and
     *    the ignore() calls. It's just nice to have the ignore()
     *    calls at the top of the file before the actual declarations.
     *    @param array $classes     Class names of interest.
     *    @access public
     */
    static function ignoreParentsIfIgnored($classes) {
        $registry = &SimpleTest::getRegistry();
        foreach ($classes as $class) {
            if (SimpleTest::isIgnored($class)) {
                $reflection = new SimpleReflection($class);
                if ($parent = $reflection->getParent()) {
                    SimpleTest::ignore($parent);
                }
            }
        }
    }

    /**
     *   Puts the object to the global pool of 'preferred' objects
     *   which can be retrieved with SimpleTest :: preferred() method.
     *   Instances of the same class are overwritten.
     *   @param object $object      Preferred object
     *   @access public
     *   @see preferred()
     */
    static function prefer($object) {
        $registry = &SimpleTest::getRegistry();
        $registry['Preferred'][] = $object;
    }

    /**
     *   Retrieves 'preferred' objects from global pool. Class filter
     *   can be applied in order to retrieve the object of the specific
     *   class
     *   @param array|string $classes       Allowed classes or interfaces.
     *   @access public
     *   @return array|object|null
     *   @see prefer()
     */
    static function preferred($classes) {
        if (! is_array($classes)) {
            $classes = array($classes);
        }
        $registry = &SimpleTest::getRegistry();
        for ($i = count($registry['Preferred']) - 1; $i >= 0; $i--) {
            foreach ($classes as $class) {
                if (SimpleTestCompatibility::isA($registry['Preferred'][$i], $class)) {
                    return $registry['Preferred'][$i];
                }
            }
        }
        return null;
    }

    /**
     *    Test to see if a test case is in the ignore
     *    list. Quite obviously the ignore list should
     *    be a separate object and will be one day.
     *    This method is internal to SimpleTest. Don't
     *    use it.
     *    @param string $class        Class name to test.
     *    @return boolean             True if should not be run.
     *    @access public
     */
    static function isIgnored($class) {
        $registry = &SimpleTest::getRegistry();
        return isset($registry['IgnoreList'][strtolower($class)]);
    }

    /**
     *    Sets proxy to use on all requests for when
     *    testing from behind a firewall. Set host
     *    to false to disable. This will take effect
     *    if there are no other proxy settings.
     *    @param string $proxy     Proxy host as URL.
     *    @param string $username  Proxy username for authentication.
     *    @param string $password  Proxy password for authentication.
     *    @access public
     */
    static function useProxy($proxy, $username = false, $password = false) {
        $registry = &SimpleTest::getRegistry();
        $registry['DefaultProxy'] = $proxy;
        $registry['DefaultProxyUsername'] = $username;
        $registry['DefaultProxyPassword'] = $password;
    }

    /**
     *    Accessor for default proxy host.
     *    @return string       Proxy URL.
     *    @access public
     */
    static function getDefaultProxy() {
        $registry = &SimpleTest::getRegistry();
        return $registry['DefaultProxy'];
    }

    /**
     *    Accessor for default proxy username.
     *    @return string    Proxy username for authentication.
     *    @access public
     */
    static function getDefaultProxyUsername() {
        $registry = &SimpleTest::getRegistry();
        return $registry['DefaultProxyUsername'];
    }

    /**
     *    Accessor for default proxy password.
     *    @return string    Proxy password for authentication.
     *    @access public
     */
    static function getDefaultProxyPassword() {
        $registry = &SimpleTest::getRegistry();
        return $registry['DefaultProxyPassword'];
    }

    /**
     *    Accessor for global registry of options.
     *    @return hash           All stored values.
     *    @access private
     */
    protected static function &getRegistry() {
        static $registry = false;
        if (! $registry) {
            $registry = SimpleTest::getDefaults();
        }
        return $registry;
    }

    /**
     *    Accessor for the context of the current
     *    test run.
     *    @return SimpleTestContext    Current test run.
     *    @access public
     */
    static function getContext() {
        static $context = false;
        if (! $context) {
            $context = new SimpleTestContext();
        }
        return $context;
    }

    /**
     *    Constant default values.
     *    @return hash       All registry defaults.
     *    @access private
     */
    protected static function getDefaults() {
        return array(
                'MockBaseClass' => 'SimpleMock',
                'IgnoreList' => array(),
                'DefaultProxy' => false,
                'DefaultProxyUsername' => false,
                'DefaultProxyPassword' => false,
                'Preferred' => array(new HtmlReporter(), new TextReporter(), new XmlReporter()));
    }
    
    /**
     *    @deprecated
     */
    static function setMockBaseClass($mock_base) {
        $registry = &SimpleTest::getRegistry();
        $registry['MockBaseClass'] = $mock_base;
    }

    /**
     *    @deprecated
     */
    static function getMockBaseClass() {
        $registry = &SimpleTest::getRegistry();
        return $registry['MockBaseClass'];
    }
}

/**
 *    Container for all components for a specific
 *    test run. Makes things like error queues
 *    available to PHP event handlers, and also
 *    gets around some nasty reference issues in
 *    the mocks.
 *    @package  SimpleTest
 */
class SimpleTestContext {
    private $test;
    private $reporter;
    private $resources;

    /**
     *    Clears down the current context.
     *    @access public
     */
    function clear() {
        $this->resources = array();
    }

    /**
     *    Sets the current test case instance. This
     *    global instance can be used by the mock objects
     *    to send message to the test cases.
     *    @param SimpleTestCase $test        Test case to register.
     *    @access public
     */
    function setTest($test) {
        $this->clear();
        $this->test = $test;
    }

    /**
     *    Accessor for currently running test case.
     *    @return SimpleTestCase    Current test.
     *    @access public
     */
    function getTest() {
        return $this->test;
    }

    /**
     *    Sets the current reporter. This
     *    global instance can be used by the mock objects
     *    to send messages.
     *    @param SimpleReporter $reporter     Reporter to register.
     *    @access public
     */
    function setReporter($reporter) {
        $this->clear();
        $this->reporter = $reporter;
    }

    /**
     *    Accessor for current reporter.
     *    @return SimpleReporter    Current reporter.
     *    @access public
     */
    function getReporter() {
        return $this->reporter;
    }

    /**
     *    Accessor for the Singleton resource.
     *    @return object       Global resource.
     *    @access public
     */
    function get($resource) {
        if (! isset($this->resources[$resource])) {
            $this->resources[$resource] = new $resource();
        }
        return $this->resources[$resource];
    }
}

/**
 *    Interrogates the stack trace to recover the
 *    failure point.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class SimpleStackTrace {
    private $prefixes;

    /**
     *    Stashes the list of target prefixes.
     *    @param array $prefixes      List of method prefixes
     *                                to search for.
     */
    function __construct($prefixes) {
        $this->prefixes = $prefixes;
    }

    /**
     *    Extracts the last method name that was not within
     *    Simpletest itself. Captures a stack trace if none given.
     *    @param array $stack      List of stack frames.
     *    @return string           Snippet of test report with line
     *                             number and file.
     *    @access public
     */
    function traceMethod($stack = false) {
        $stack = $stack ? $stack : $this->captureTrace();
        foreach ($stack as $frame) {
            if ($this->frameLiesWithinSimpleTestFolder($frame)) {
                continue;
            }
            if ($this->frameMatchesPrefix($frame)) {
                return ' at [' . $frame['file'] . ' line ' . $frame['line'] . ']';
            }
        }
        return '';
    }

    /**
     *    Test to see if error is generated by SimpleTest itself.
     *    @param array $frame     PHP stack frame.
     *    @return boolean         True if a SimpleTest file.
     *    @access private
     */
    protected function frameLiesWithinSimpleTestFolder($frame) {
        if (isset($frame['file'])) {
            $path = substr(SIMPLE_TEST, 0, -1);
            if (strpos($frame['file'], $path) === 0) {
                if (dirname($frame['file']) == $path) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *    Tries to determine if the method call is an assert, etc.
     *    @param array $frame     PHP stack frame.
     *    @return boolean         True if matches a target.
     *    @access private
     */
    protected function frameMatchesPrefix($frame) {
        foreach ($this->prefixes as $prefix) {
            if (strncmp($frame['function'], $prefix, strlen($prefix)) == 0) {
                return true;
            }
        }
        return false;
    }

    /**
     *    Grabs a current stack trace.
     *    @return array        Fulle trace.
     *    @access private
     */
    protected function captureTrace() {
        if (function_exists('debug_backtrace')) {
            return array_reverse(debug_backtrace());
        }
        return array();
    }
}
?>