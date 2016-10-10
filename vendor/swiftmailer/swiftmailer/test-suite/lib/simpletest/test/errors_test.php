<?php
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../errors.php');
require_once(dirname(__FILE__) . '/../expectation.php');
require_once(dirname(__FILE__) . '/../test_case.php');
Mock::generate('SimpleTestCase');
Mock::generate('SimpleExpectation');
SimpleTest::ignore('MockSimpleTestCase');

class TestOfErrorQueue extends UnitTestCase {

    function setUp() {
        $context = SimpleTest::getContext();
        $queue = $context->get('SimpleErrorQueue');
        $queue->clear();
    }

    function tearDown() {
        $context = SimpleTest::getContext();
        $queue = $context->get('SimpleErrorQueue');
        $queue->clear();
    }

    function testExpectationMatchCancelsIncomingError() {
        $test = new MockSimpleTestCase();
        $test->expectOnce('assert', array(
                new IdenticalExpectation(new AnythingExpectation()),
                'B',
                'a message'));
        $test->setReturnValue('assert', true);
        $test->expectNever('error');
        $queue = new SimpleErrorQueue();
        $queue->setTestCase($test);
        $queue->expectError(new AnythingExpectation(), 'a message');
        $queue->add(1024, 'B', 'b.php', 100);
    }
}

class TestOfErrorTrap extends UnitTestCase {
    private $old;

    function setUp() {
        $this->old = error_reporting(E_ALL);
        set_error_handler('SimpleTestErrorHandler');
    }

    function tearDown() {
        restore_error_handler();
        error_reporting($this->old);
    }

    function testQueueStartsEmpty() {
        $context = SimpleTest::getContext();
        $queue = $context->get('SimpleErrorQueue');
        $this->assertFalse($queue->extract());
    }

    function testErrorsAreSwallowedByMatchingExpectation() {
        $this->expectError('Ouch!');
        trigger_error('Ouch!');
    }

    function testErrorsAreSwallowedInOrder() {
        $this->expectError('a');
        $this->expectError('b');
        trigger_error('a');
        trigger_error('b');
    }

    function testAnyErrorCanBeSwallowed() {
        $this->expectError();
        trigger_error('Ouch!');
    }

    function testErrorCanBeSwallowedByPatternMatching() {
        $this->expectError(new PatternExpectation('/ouch/i'));
        trigger_error('Ouch!');
    }

    function testErrorWithPercentsPassesWithNoSprintfError() {
        $this->expectError("%");
        trigger_error('%');
    }
}

class TestOfErrors extends UnitTestCase {
    private $old;

    function setUp() {
        $this->old = error_reporting(E_ALL);
    }

    function tearDown() {
        error_reporting($this->old);
    }

    function testDefaultWhenAllReported() {
        error_reporting(E_ALL);
        $this->expectError('Ouch!');
        trigger_error('Ouch!');
    }

    function testNoticeWhenReported() {
        error_reporting(E_ALL);
        $this->expectError('Ouch!');
        trigger_error('Ouch!', E_USER_NOTICE);
    }

    function testWarningWhenReported() {
        error_reporting(E_ALL);
        $this->expectError('Ouch!');
        trigger_error('Ouch!', E_USER_WARNING);
    }

    function testErrorWhenReported() {
        error_reporting(E_ALL);
        $this->expectError('Ouch!');
        trigger_error('Ouch!', E_USER_ERROR);
    }

    function testNoNoticeWhenNotReported() {
        error_reporting(0);
        trigger_error('Ouch!', E_USER_NOTICE);
    }

    function testNoWarningWhenNotReported() {
        error_reporting(0);
        trigger_error('Ouch!', E_USER_WARNING);
    }

    function testNoticeSuppressedWhenReported() {
        error_reporting(E_ALL);
        @trigger_error('Ouch!', E_USER_NOTICE);
    }

    function testWarningSuppressedWhenReported() {
        error_reporting(E_ALL);
        @trigger_error('Ouch!', E_USER_WARNING);
    }

    function testErrorWithPercentsReportedWithNoSprintfError() {
        $this->expectError('%');
        trigger_error('%');
    }
}

class TestOfPHP52RecoverableErrors extends UnitTestCase {
    function skip() {
        $this->skipIf(
                version_compare(phpversion(), '5.2', '<'),
                'E_RECOVERABLE_ERROR not tested for PHP below 5.2');
    }

    function testError() {
        eval('
            class RecoverableErrorTestingStub {
                function ouch(RecoverableErrorTestingStub $obj) {
                }
            }
        ');

        $stub = new RecoverableErrorTestingStub();
        $this->expectError(new PatternExpectation('/must be an instance of RecoverableErrorTestingStub/i'));
        $stub->ouch(new stdClass());
    }
}

class TestOfErrorsExcludingPHP52AndAbove extends UnitTestCase {
    function skip() {
        $this->skipIf(
                version_compare(phpversion(), '5.2', '>='),
                'E_USER_ERROR not tested for PHP 5.2 and above');
    }

    function testNoErrorWhenNotReported() {
        error_reporting(0);
        trigger_error('Ouch!', E_USER_ERROR);
    }

    function testErrorSuppressedWhenReported() {
        error_reporting(E_ALL);
        @trigger_error('Ouch!', E_USER_ERROR);
    }
}

SimpleTest::ignore('TestOfNotEnoughErrors');
/**
 * This test is ignored as it is used by {@link TestRunnerForLeftOverAndNotEnoughErrors}
 * to verify that it fails as expected.
 *
 * @ignore
 */
class TestOfNotEnoughErrors extends UnitTestCase {
    function testExpectTwoErrorsThrowOne() {
        $this->expectError('Error 1');
        trigger_error('Error 1');
        $this->expectError('Error 2');
    }
}

SimpleTest::ignore('TestOfLeftOverErrors');
/**
 * This test is ignored as it is used by {@link TestRunnerForLeftOverAndNotEnoughErrors}
 * to verify that it fails as expected.
 *
 * @ignore
 */
class TestOfLeftOverErrors extends UnitTestCase {
    function testExpectOneErrorGetTwo() {
        $this->expectError('Error 1');
        trigger_error('Error 1');
        trigger_error('Error 2');
    }
}

class TestRunnerForLeftOverAndNotEnoughErrors extends UnitTestCase {
    function testRunLeftOverErrorsTestCase() {
        $test = new TestOfLeftOverErrors();
        $this->assertFalse($test->run(new SimpleReporter()));
    }

    function testRunNotEnoughErrors() {
        $test = new TestOfNotEnoughErrors();
        $this->assertFalse($test->run(new SimpleReporter()));
    }
}

// TODO: Add stacked error handler test
?>