<?php
// $Id: eclipse_test.php 1739 2008-04-09 20:48:37Z edwardzyang $

//To run this from the eclipse plugin...you need to make sure that the 
//SimpleTest path in the preferences is the same as the location of the 
//eclipse.php file below otherwise you end up with two "different" eclipse.php 
//files included and that does not work...

include_once(dirname(__FILE__) . '/../eclipse.php');
Mock::generate('SimpleSocket');

class TestOfEclipse extends UnitTestCase {
	
	function testPass() {
		$listener = &new MockSimpleSocket();
		
		$fullpath = realpath(dirname(__FILE__).'/support/test1.php');
		$testpath = EclipseReporter::escapeVal($fullpath);
		$expected = "{status:\"pass\",message:\"pass1 at [$testpath line 4]\",group:\"$testpath\",case:\"test1\",method:\"test_pass\"}";
		//this should work...but it doesn't so the next line and the last line are the hacks
		//$listener->expectOnce('write',array($expected));
		$listener->setReturnValue('write',-1);
		
		$pathparts = pathinfo($fullpath);
		$filename = $pathparts['basename'];
		$test= &new TestSuite($filename);
		$test->addTestFile($fullpath);
		$test->run(new EclipseReporter($listener));
		$this->assertEqual($expected,$listener->output);
	}
}
?>