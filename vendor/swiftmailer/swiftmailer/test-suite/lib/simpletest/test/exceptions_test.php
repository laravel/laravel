<?php
// $Id: exceptions_test.php 1618 2007-12-29 22:52:30Z lastcraft $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../exceptions.php');
require_once(dirname(__FILE__) . '/../expectation.php');
require_once(dirname(__FILE__) . '/../test_case.php');
Mock::generate('SimpleTestCase');
Mock::generate('SimpleExpectation');

class MyTestException extends Exception {}
class HigherTestException extends MyTestException {}
class OtherTestException extends Exception {}

class TestOfExceptionExpectation extends UnitTestCase {

    function testExceptionClassAsStringWillMatchExceptionsRootedOnThatClass() {
        $expectation = new ExceptionExpectation('MyTestException');
        $this->assertTrue($expectation->test(new MyTestException()));
        $this->assertTrue($expectation->test(new HigherTestException()));
        $this->assertFalse($expectation->test(new OtherTestException()));
    }

    function testMatchesClassAndMessageWhenExceptionExpected() {
        $expectation = new ExceptionExpectation(new MyTestException('Hello'));
        $this->assertTrue($expectation->test(new MyTestException('Hello')));
        $this->assertFalse($expectation->test(new HigherTestException('Hello')));
        $this->assertFalse($expectation->test(new OtherTestException('Hello')));
        $this->assertFalse($expectation->test(new MyTestException('Goodbye')));
        $this->assertFalse($expectation->test(new MyTestException()));
    }

    function testMessagelessExceptionMatchesOnlyOnClass() {
        $expectation = new ExceptionExpectation(new MyTestException());
        $this->assertTrue($expectation->test(new MyTestException()));
        $this->assertFalse($expectation->test(new HigherTestException()));
    }
}

class TestOfExceptionTrap extends UnitTestCase {

    function testNoExceptionsInQueueMeansNoTestMessages() {
        $test = new MockSimpleTestCase();
        $test->expectNever('assert');
        $queue = new SimpleExceptionTrap();
        $this->assertFalse($queue->isExpected($test, new Exception()));
    }

    function testMatchingExceptionGivesTrue() {
        $expectation = new MockSimpleExpectation();
        $expectation->setReturnValue('test', true);
        $test = new MockSimpleTestCase();
        $test->setReturnValue('assert', true);
        $queue = new SimpleExceptionTrap();
        $queue->expectException($expectation, 'message');
        $this->assertTrue($queue->isExpected($test, new Exception()));
    }

    function testMatchingExceptionTriggersAssertion() {
        $test = new MockSimpleTestCase();
        $test->expectOnce('assert', array(
                '*',
                new ExceptionExpectation(new Exception()),
                'message'));
        $queue = new SimpleExceptionTrap();
        $queue->expectException(new ExceptionExpectation(new Exception()), 'message');
        $queue->isExpected($test, new Exception());
    }
}

class TestOfCatchingExceptions extends UnitTestCase {

    function testCanCatchAnyExpectedException() {
        $this->expectException();
        throw new Exception();
    }

    function testCanMatchExceptionByClass() {
        $this->expectException('MyTestException');
        throw new HigherTestException();
    }

    function testCanMatchExceptionExactly() {
        $this->expectException(new Exception('Ouch'));
        throw new Exception('Ouch');
    }

    function testLastListedExceptionIsTheOneThatCounts() {
        $this->expectException('OtherTestException');
        $this->expectException('MyTestException');
        throw new HigherTestException();
    }
}

class TestOfCallingTearDownAfterExceptions extends UnitTestCase {
    private $debri = 0;
    
    function tearDown() {
        $this->debri--;
    }

    function testLeaveSomeDebri() { 
        $this->debri++;
        $this->expectException();
        throw new Exception(__FUNCTION__);
    }

	function testDebriWasRemovedOnce() {
        $this->assertEqual($this->debri, 0);
	}
}

class TestOfExceptionThrownInSetUpDoesNotRunTestBody extends UnitTestCase {

	function setUp() {
        $this->expectException();
        throw new Exception();
	}
	
	function testShouldNotBeRun() {
        $this->fail('This test body should not be run');
	}

	function testShouldNotBeRunEither() {
        $this->fail('This test body should not be run either');
	}
}

class TestOfExpectExceptionWithSetUp extends UnitTestCase {

	function setUp() {
        $this->expectException();
	}
	
	function testThisExceptionShouldBeCaught() {
        throw new Exception();
	}

	function testJustThrowingMyTestException() {
        throw new MyTestException();
	}
}

class TestOfThrowingExceptionsInTearDown extends UnitTestCase {
    
    function tearDown() {
        throw new Exception();
    }
    
    function testDoesntFatal() {
        $this->expectException();
    }
}
?>