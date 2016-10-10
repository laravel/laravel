<?php
// $Id: treemap_node_test.php 1641 2008-01-22 20:13:52Z pp11 $
require_once dirname(__FILE__) . '/../../../autorun.php';
require_once dirname(__FILE__) . '/../../treemap_reporter.php';

class TestOfTreemapDataTypes extends UnitTestCase {

	function testEmptyRootNode() {
		$node = new TreemapNode("test", "test graph");
		$this->assertEqual($node->getSize(), 0);
		$this->assertEqual($node->getTotalSize(), 0);
	}
	
	function testChildNodeDepth() {
		$root = new TreemapNode("root", "test");
		$root->putChild(new TreemapNode("child", "test"));
		$childOne = new TreemapNode("child1", "test");
		$childTwo = new TreemapNode("child2", "test");
		$childTwo->putChild(new TreemapNode("child3", "test"));
		$childOne->putChild($childTwo);
		$root->putChild($childOne);
		$this->assertEqual($root->getSize(), 2);
		$this->assertEqual($root->getTotalSize(), 4);
	}
	
	function testGraphDepthSpread() {
		$root = new TreemapNode("root", "test");
		$root->putChild(new TreemapNode("child", "test"));
		$childOne = new TreemapNode("child1", "test");
		$childTwo = new TreemapNode("child2", "test");
		$childThree = new TreemapNode("child3", "test");
		$childFour = new TreemapNode("child4", "test");
		$childFive = new TreemapNode("child5", "test");
		$childSix = new TreemapNode("child6", "test");
		$childFour->putChild($childFive);
		$childFour->putChild($childSix);
		$this->assertEqual($childFour->getSize(), 2);
		$this->assertEqual($childFour->getTotalSize(), 2);
		$childThree->putChild($childFour);
		$this->assertEqual($childThree->getSize(), 1);
		$this->assertEqual($childThree->getTotalSize(), 3);
		$childTwo->putChild($childThree);
		$this->assertEqual($childTwo->getSize(), 1);
		$this->assertEqual($childTwo->getTotalSize(), 4);
		$childOne->putChild($childTwo);
		$root->putChild($childOne);
		$this->assertEqual($root->getSize(), 2);
		$this->assertEqual($root->getTotalSize(), 7);
	}

	function testMutableStack() {
		$stack = new TreemapStack();
		$this->assertEqual($stack->size(), 0);
		$stack->push(new TreemapNode("a", "one"));
		$this->assertEqual($stack->size(), 1);
		$stack->push(new TreemapNode("b", "one"));
		$this->assertIdentical($stack->peek(), new TreemapNode("b", "one"));
		$stack->push(new TreemapNode("c", "three"));
		$stack->push(new TreemapNode("d", "four"));
		$this->assertEqual($stack->size(), 4);
		$this->assertIdentical($stack->pop(), new TreemapNode("d", "four"));
		$this->assertEqual($stack->size(), 3);
		$this->assertIdentical($stack->pop(), new TreemapNode("c", "three"));
		$this->assertEqual($stack->size(), 2);
	}
	
}

?>