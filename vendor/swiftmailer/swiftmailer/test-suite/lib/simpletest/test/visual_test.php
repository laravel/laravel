<?php
    // $Id: visual_test.php 1787 2008-04-26 20:35:39Z pp11 $

    // NOTE:
    // Some of these tests are designed to fail! Do not be alarmed.
    //                         ----------------

    // The following tests are a bit hacky. Whilst Kent Beck tried to
    // build a unit tester with a unit tester, I am not that brave.
    // Instead I have just hacked together odd test scripts until
    // I have enough of a tester to procede more formally.
    //
    // The proper tests start in all_tests.php
    require_once('../unit_tester.php');
    require_once('../shell_tester.php');
    require_once('../mock_objects.php');
    require_once('../reporter.php');
    require_once('../xml.php');

    class TestDisplayClass {
        private $a;

        function TestDisplayClass($a) {
            $this->a = $a;
        }
    }

    class PassingUnitTestCaseOutput extends UnitTestCase {

        function testOfResults() {
            $this->pass('Pass');
        }

        function testTrue() {
            $this->assertTrue(true);
        }

        function testFalse() {
            $this->assertFalse(false);
        }

        function testExpectation() {
            $expectation = &new EqualExpectation(25, 'My expectation message: %s');
            $this->assert($expectation, 25, 'My assert message : %s');
        }

        function testNull() {
            $this->assertNull(null, "%s -> Pass");
            $this->assertNotNull(false, "%s -> Pass");
        }

        function testType() {
            $this->assertIsA("hello", "string", "%s -> Pass");
            $this->assertIsA($this, "PassingUnitTestCaseOutput", "%s -> Pass");
            $this->assertIsA($this, "UnitTestCase", "%s -> Pass");
        }

        function testTypeEquality() {
            $this->assertEqual("0", 0, "%s -> Pass");
        }

        function testNullEquality() {
            $this->assertNotEqual(null, 1, "%s -> Pass");
            $this->assertNotEqual(1, null, "%s -> Pass");
        }

        function testIntegerEquality() {
            $this->assertNotEqual(1, 2, "%s -> Pass");
        }

        function testStringEquality() {
            $this->assertEqual("a", "a", "%s -> Pass");
            $this->assertNotEqual("aa", "ab", "%s -> Pass");
        }

        function testHashEquality() {
            $this->assertEqual(array("a" => "A", "b" => "B"), array("b" => "B", "a" => "A"), "%s -> Pass");
        }

        function testWithin() {
            $this->assertWithinMargin(5, 5.4, 0.5, "%s -> Pass");
        }

        function testOutside() {
            $this->assertOutsideMargin(5, 5.6, 0.5, "%s -> Pass");
        }

        function testStringIdentity() {
            $a = "fred";
            $b = $a;
            $this->assertIdentical($a, $b, "%s -> Pass");
        }

        function testTypeIdentity() {
            $a = "0";
            $b = 0;
            $this->assertNotIdentical($a, $b, "%s -> Pass");
        }

        function testNullIdentity() {
            $this->assertNotIdentical(null, 1, "%s -> Pass");
            $this->assertNotIdentical(1, null, "%s -> Pass");
        }

        function testHashIdentity() {
        }

        function testObjectEquality() {
            $this->assertEqual(new TestDisplayClass(4), new TestDisplayClass(4), "%s -> Pass");
            $this->assertNotEqual(new TestDisplayClass(4), new TestDisplayClass(5), "%s -> Pass");
        }

        function testObjectIndentity() {
            $this->assertIdentical(new TestDisplayClass(false), new TestDisplayClass(false), "%s -> Pass");
            $this->assertNotIdentical(new TestDisplayClass(false), new TestDisplayClass(0), "%s -> Pass");
        }

        function testReference() {
            $a = "fred";
            $b = &$a;
            $this->assertReference($a, $b, "%s -> Pass");
        }

        function testCloneOnDifferentObjects() {
            $a = "fred";
            $b = $a;
            $c = "Hello";
            $this->assertClone($a, $b, "%s -> Pass");
        }

        function testPatterns() {
            $this->assertPattern('/hello/i', "Hello there", "%s -> Pass");
            $this->assertNoPattern('/hello/', "Hello there", "%s -> Pass");
        }

        function testLongStrings() {
            $text = "";
            for ($i = 0; $i < 10; $i++) {
                $text .= "0123456789";
            }
            $this->assertEqual($text, $text);
        }
    }

    class FailingUnitTestCaseOutput extends UnitTestCase {

        function testOfResults() {
            $this->fail('Fail');        // Fail.
        }

        function testTrue() {
            $this->assertTrue(false);        // Fail.
        }

        function testFalse() {
            $this->assertFalse(true);        // Fail.
        }

        function testExpectation() {
            $expectation = &new EqualExpectation(25, 'My expectation message: %s');
            $this->assert($expectation, 24, 'My assert message : %s');        // Fail.
        }

        function testNull() {
            $this->assertNull(false, "%s -> Fail");        // Fail.
            $this->assertNotNull(null, "%s -> Fail");        // Fail.
        }

        function testType() {
            $this->assertIsA(14, "string", "%s -> Fail");        // Fail.
            $this->assertIsA(14, "TestOfUnitTestCaseOutput", "%s -> Fail");        // Fail.
            $this->assertIsA($this, "TestReporter", "%s -> Fail");        // Fail.
        }

        function testTypeEquality() {
            $this->assertNotEqual("0", 0, "%s -> Fail");        // Fail.
        }

        function testNullEquality() {
            $this->assertEqual(null, 1, "%s -> Fail");        // Fail.
            $this->assertEqual(1, null, "%s -> Fail");        // Fail.
        }

        function testIntegerEquality() {
            $this->assertEqual(1, 2, "%s -> Fail");        // Fail.
        }

        function testStringEquality() {
            $this->assertNotEqual("a", "a", "%s -> Fail");    // Fail.
            $this->assertEqual("aa", "ab", "%s -> Fail");        // Fail.
        }

        function testHashEquality() {
            $this->assertEqual(array("a" => "A", "b" => "B"), array("b" => "B", "a" => "Z"), "%s -> Fail");
        }

        function testWithin() {
            $this->assertWithinMargin(5, 5.6, 0.5, "%s -> Fail");   // Fail.
        }

        function testOutside() {
            $this->assertOutsideMargin(5, 5.4, 0.5, "%s -> Fail");   // Fail.
        }

        function testStringIdentity() {
            $a = "fred";
            $b = $a;
            $this->assertNotIdentical($a, $b, "%s -> Fail");       // Fail.
        }

        function testTypeIdentity() {
            $a = "0";
            $b = 0;
            $this->assertIdentical($a, $b, "%s -> Fail");        // Fail.
        }

        function testNullIdentity() {
            $this->assertIdentical(null, 1, "%s -> Fail");        // Fail.
            $this->assertIdentical(1, null, "%s -> Fail");        // Fail.
        }

        function testHashIdentity() {
            $this->assertIdentical(array("a" => "A", "b" => "B"), array("b" => "B", "a" => "A"), "%s -> fail");        // Fail.
        }

        function testObjectEquality() {
            $this->assertNotEqual(new TestDisplayClass(4), new TestDisplayClass(4), "%s -> Fail");    // Fail.
            $this->assertEqual(new TestDisplayClass(4), new TestDisplayClass(5), "%s -> Fail");        // Fail.
        }

        function testObjectIndentity() {
            $this->assertNotIdentical(new TestDisplayClass(false), new TestDisplayClass(false), "%s -> Fail");    // Fail.
            $this->assertIdentical(new TestDisplayClass(false), new TestDisplayClass(0), "%s -> Fail");        // Fail.
        }

        function testReference() {
            $a = "fred";
            $b = &$a;
            $this->assertClone($a, $b, "%s -> Fail");        // Fail.
        }

        function testCloneOnDifferentObjects() {
            $a = "fred";
            $b = $a;
            $c = "Hello";
            $this->assertClone($a, $c, "%s -> Fail");        // Fail.
        }

        function testPatterns() {
            $this->assertPattern('/hello/', "Hello there", "%s -> Fail");            // Fail.
            $this->assertNoPattern('/hello/i', "Hello there", "%s -> Fail");      // Fail.
        }

        function testLongStrings() {
            $text = "";
            for ($i = 0; $i < 10; $i++) {
                $text .= "0123456789";
            }
            $this->assertEqual($text . $text, $text . "a" . $text);        // Fail.
        }
}

    class Dummy {
        function Dummy() {
        }

        function a() {
        }
    }
    Mock::generate('Dummy');

    class TestOfMockObjectsOutput extends UnitTestCase {

        function testCallCounts() {
            $dummy = &new MockDummy();
            $dummy->expectCallCount('a', 1, 'My message: %s');
            $dummy->a();
            $dummy->a();
        }

        function testMinimumCallCounts() {
            $dummy = &new MockDummy();
            $dummy->expectMinimumCallCount('a', 2, 'My message: %s');
            $dummy->a();
            $dummy->a();
        }

        function testEmptyMatching() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array());
            $dummy->a();
            $dummy->a(null);        // Fail.
        }

        function testEmptyMatchingWithCustomMessage() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array(), 'My expectation message: %s');
            $dummy->a();
            $dummy->a(null);        // Fail.
        }

        function testNullMatching() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array(null));
            $dummy->a(null);
            $dummy->a();        // Fail.
        }

        function testBooleanMatching() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array(true, false));
            $dummy->a(true, false);
            $dummy->a(true, true);        // Fail.
        }

        function testIntegerMatching() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array(32, 33));
            $dummy->a(32, 33);
            $dummy->a(32, 34);        // Fail.
        }

        function testFloatMatching() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array(3.2, 3.3));
            $dummy->a(3.2, 3.3);
            $dummy->a(3.2, 3.4);        // Fail.
        }

        function testStringMatching() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array('32', '33'));
            $dummy->a('32', '33');
            $dummy->a('32', '34');        // Fail.
        }

        function testEmptyMatchingWithCustomExpectationMessage() {
            $dummy = &new MockDummy();
            $dummy->expect(
                    'a',
                    array(new EqualExpectation('A', 'My part expectation message: %s')),
                    'My expectation message: %s');
            $dummy->a('A');
            $dummy->a('B');        // Fail.
        }

        function testArrayMatching() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array(array(32), array(33)));
            $dummy->a(array(32), array(33));
            $dummy->a(array(32), array('33'));        // Fail.
        }

        function testObjectMatching() {
            $a = new Dummy();
            $a->a = 'a';
            $b = new Dummy();
            $b->b = 'b';
            $dummy = &new MockDummy();
            $dummy->expect('a', array($a, $b));
            $dummy->a($a, $b);
            $dummy->a($a, $a);        // Fail.
        }

        function testBigList() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array(false, 0, 1, 1.0));
            $dummy->a(false, 0, 1, 1.0);
            $dummy->a(true, false, 2, 2.0);        // Fail.
        }
    }

    class TestOfPastBugs extends UnitTestCase {

        function testMixedTypes() {
            $this->assertEqual(array(), null, "%s -> Pass");
            $this->assertIdentical(array(), null, "%s -> Fail");    // Fail.
        }

        function testMockWildcards() {
            $dummy = &new MockDummy();
            $dummy->expect('a', array('*', array(33)));
            $dummy->a(array(32), array(33));
            $dummy->a(array(32), array('33'));        // Fail.
        }
    }

    class TestOfVisualShell extends ShellTestCase {

        function testDump() {
            $this->execute('ls');
            $this->dumpOutput();
            $this->execute('dir');
            $this->dumpOutput();
        }

        function testDumpOfList() {
            $this->execute('ls');
            $this->dump($this->getOutputAsList());
        }
    }

    class PassesAsWellReporter extends HtmlReporter {

        protected function getCss() {
            return parent::getCss() . ' .pass { color: darkgreen; }';
        }

        function paintPass($message) {
            parent::paintPass($message);
            print "<span class=\"pass\">Pass</span>: ";
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            print implode(" -&gt; ", $breadcrumb);
            print " -&gt; " . htmlentities($message) . "<br />\n";
        }

        function paintSignal($type, &$payload) {
            print "<span class=\"fail\">$type</span>: ";
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            print implode(" -&gt; ", $breadcrumb);
            print " -&gt; " . htmlentities(serialize($payload)) . "<br />\n";
        }
    }
    
    class TestOfSkippingNoMatterWhat extends UnitTestCase {
        function skip() {
            $this->skipIf(true, 'Always skipped -> %s');
        }
        
        function testFail() {
            $this->fail('This really shouldn\'t have happened');
        }
    }
    
    class TestOfSkippingOrElse extends UnitTestCase {
        function skip() {
            $this->skipUnless(false, 'Always skipped -> %s');
        }
        
        function testFail() {
            $this->fail('This really shouldn\'t have happened');
        }
    }
    
    class TestOfSkippingTwiceOver extends UnitTestCase {
        function skip() {
            $this->skipIf(true, 'First reason -> %s');
            $this->skipIf(true, 'Second reason -> %s');
        }
        
        function testFail() {
            $this->fail('This really shouldn\'t have happened');
        }
    }
    
    class TestThatShouldNotBeSkipped extends UnitTestCase {
        function skip() {
            $this->skipIf(false);
            $this->skipUnless(true);
        }
        
        function testFail() {
            $this->fail('We should see this message');
        }
        
        function testPass() {
            $this->pass('We should see this message');
        }
    }

    $test = &new TestSuite('Visual test with 46 passes, 47 fails and 0 exceptions');
    $test->add(new PassingUnitTestCaseOutput());
    $test->add(new FailingUnitTestCaseOutput());
    $test->add(new TestOfMockObjectsOutput());
    $test->add(new TestOfPastBugs());
    $test->add(new TestOfVisualShell());
    $test->add(new TestOfSkippingNoMatterWhat());
    $test->add(new TestOfSkippingOrElse());
    $test->add(new TestOfSkippingTwiceOver());
    $test->add(new TestThatShouldNotBeSkipped());

    if (isset($_GET['xml']) || in_array('xml', (isset($argv) ? $argv : array()))) {
        $reporter = new XmlReporter();
    } elseif (TextReporter::inCli()) {
        $reporter = new TextReporter();
    } else {
        $reporter = new PassesAsWellReporter();
    }
    if (isset($_GET['dry']) || in_array('dry', (isset($argv) ? $argv : array()))) {
        $reporter->makeDry();
    }
    exit ($test->run($reporter) ? 0 : 1);
?>