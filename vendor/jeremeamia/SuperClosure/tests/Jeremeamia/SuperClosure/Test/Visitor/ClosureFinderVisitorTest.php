<?php

namespace Jeremeamia\SuperClosure\Test\Visitor;

use Jeremeamia\SuperClosure\Visitor\ClosureFinderVisitor;

/**
 * @covers Jeremeamia\SuperClosure\Visitor\ClosureFinderVisitor
 */
class ClosureFinderVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testClosureNodeIsDiscoveredByVisitor()
    {
        $closure = function () {}; // Take the line number here and set it as the "startLine"
        $reflectedClosure = new \ReflectionFunction($closure);
        $closureFinder = new ClosureFinderVisitor($reflectedClosure);
        $closureNode = new \PHPParser_Node_Expr_Closure(array(), array('startLine' => 14));
        $closureFinder->enterNode($closureNode);

        $this->assertSame($closureNode, $closureFinder->getClosureNode());
    }

    public function testClosureNodeIsAmbiguousIfMultipleClosuresOnLine()
    {
        $this->setExpectedException('RuntimeException');

        $closure = function () {}; function () {}; // Take the line number here and set it as the "startLine"
        $closureFinder = new ClosureFinderVisitor(new \ReflectionFunction($closure));
        $closureFinder->enterNode(new \PHPParser_Node_Expr_Closure(array(), array('startLine' => 27)));
        $closureFinder->enterNode(new \PHPParser_Node_Expr_Closure(array(), array('startLine' => 27)));
    }

    public function testCalculatesClosureLocation()
    {
        $closure = function () {}; // Take the line number here and set it as the "startLine"
        $closureFinder = new ClosureFinderVisitor(new \ReflectionFunction($closure));

        $closureFinder->beforeTraverse(array());

        $node = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name(array('Foo', 'Bar')));
        $closureFinder->enterNode($node);
        $closureFinder->leaveNode($node);

        $node = new \PHPParser_Node_Stmt_Trait('Fizz');
        $closureFinder->enterNode($node);
        $closureFinder->leaveNode($node);

        $node = new \PHPParser_Node_Stmt_Class('Buzz');
        $closureFinder->enterNode($node);
        $closureFinder->leaveNode($node);

        $closureFinder->afterTraverse(array());

        $setProperties = array_filter(get_object_vars($closureFinder->getLocation()));
        $this->assertEquals(array('directory', 'file', 'function', 'line'), array_keys($setProperties));
    }
}
