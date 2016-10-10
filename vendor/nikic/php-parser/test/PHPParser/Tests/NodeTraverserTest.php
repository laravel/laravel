<?php

class PHPParser_Tests_NodeTraverserTest extends PHPUnit_Framework_TestCase
{
    public function testNonModifying() {
        $str1Node = new PHPParser_Node_Scalar_String('Foo');
        $str2Node = new PHPParser_Node_Scalar_String('Bar');
        $echoNode = new PHPParser_Node_Stmt_Echo(array($str1Node, $str2Node));
        $stmts    = array($echoNode);

        $visitor = $this->getMock('PHPParser_NodeVisitor');

        $visitor->expects($this->at(0))->method('beforeTraverse')->with($stmts);
        $visitor->expects($this->at(1))->method('enterNode')->with($echoNode);
        $visitor->expects($this->at(2))->method('enterNode')->with($str1Node);
        $visitor->expects($this->at(3))->method('leaveNode')->with($str1Node);
        $visitor->expects($this->at(4))->method('enterNode')->with($str2Node);
        $visitor->expects($this->at(5))->method('leaveNode')->with($str2Node);
        $visitor->expects($this->at(6))->method('leaveNode')->with($echoNode);
        $visitor->expects($this->at(7))->method('afterTraverse')->with($stmts);

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testModifying() {
        $str1Node  = new PHPParser_Node_Scalar_String('Foo');
        $str2Node  = new PHPParser_Node_Scalar_String('Bar');
        $printNode = new PHPParser_Node_Expr_Print($str1Node);

        // first visitor changes the node, second verifies the change
        $visitor1 = $this->getMock('PHPParser_NodeVisitor');
        $visitor2 = $this->getMock('PHPParser_NodeVisitor');

        // replace empty statements with string1 node
        $visitor1->expects($this->at(0))->method('beforeTraverse')->with(array())
                 ->will($this->returnValue(array($str1Node)));
        $visitor2->expects($this->at(0))->method('beforeTraverse')->with(array($str1Node));

        // replace string1 node with print node
        $visitor1->expects($this->at(1))->method('enterNode')->with($str1Node)
                 ->will($this->returnValue($printNode));
        $visitor2->expects($this->at(1))->method('enterNode')->with($printNode);

        // replace string1 node with string2 node
        $visitor1->expects($this->at(2))->method('enterNode')->with($str1Node)
                 ->will($this->returnValue($str2Node));
        $visitor2->expects($this->at(2))->method('enterNode')->with($str2Node);

        // replace string2 node with string1 node again
        $visitor1->expects($this->at(3))->method('leaveNode')->with($str2Node)
                 ->will($this->returnValue($str1Node));
        $visitor2->expects($this->at(3))->method('leaveNode')->with($str1Node);

        // replace print node with string1 node again
        $visitor1->expects($this->at(4))->method('leaveNode')->with($printNode)
                 ->will($this->returnValue($str1Node));
        $visitor2->expects($this->at(4))->method('leaveNode')->with($str1Node);

        // replace string1 node with empty statements again
        $visitor1->expects($this->at(5))->method('afterTraverse')->with(array($str1Node))
                 ->will($this->returnValue(array()));
        $visitor2->expects($this->at(5))->method('afterTraverse')->with(array());

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);

        // as all operations are reversed we end where we start
        $this->assertEquals(array(), $traverser->traverse(array()));
    }

    public function testRemove() {
        $str1Node = new PHPParser_Node_Scalar_String('Foo');
        $str2Node = new PHPParser_Node_Scalar_String('Bar');

        $visitor = $this->getMock('PHPParser_NodeVisitor');

        // remove the string1 node, leave the string2 node
        $visitor->expects($this->at(2))->method('leaveNode')->with($str1Node)
                ->will($this->returnValue(false));

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals(array($str2Node), $traverser->traverse(array($str1Node, $str2Node)));
    }

    public function testMerge() {
        $strStart  = new PHPParser_Node_Scalar_String('Start');
        $strMiddle = new PHPParser_Node_Scalar_String('End');
        $strEnd    = new PHPParser_Node_Scalar_String('Middle');
        $strR1     = new PHPParser_Node_Scalar_String('Replacement 1');
        $strR2     = new PHPParser_Node_Scalar_String('Replacement 2');

        $visitor = $this->getMock('PHPParser_NodeVisitor');

        // replace strMiddle with strR1 and strR2 by merge
        $visitor->expects($this->at(4))->method('leaveNode')->with($strMiddle)
                ->will($this->returnValue(array($strR1, $strR2)));

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals(
            array($strStart, $strR1, $strR2, $strEnd),
            $traverser->traverse(array($strStart, $strMiddle, $strEnd))
        );
    }

    public function testDeepArray() {
        $strNode = new PHPParser_Node_Scalar_String('Foo');
        $stmts = array(array(array($strNode)));

        $visitor = $this->getMock('PHPParser_NodeVisitor');
        $visitor->expects($this->at(1))->method('enterNode')->with($strNode);

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testRemovingVisitor() {
        $visitor1 = $this->getMock('PHPParser_NodeVisitor');
        $visitor2 = $this->getMock('PHPParser_NodeVisitor');
        $visitor3 = $this->getMock('PHPParser_NodeVisitor');

        $traverser = new PHPParser_NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);
        $traverser->addVisitor($visitor3);

        $preExpected = array($visitor1, $visitor2, $visitor3);
        $this->assertAttributeSame($preExpected, 'visitors', $traverser, 'The appropriate visitors have not been added');

        $traverser->removeVisitor($visitor2);

        $postExpected = array(0 => $visitor1, 2 => $visitor3);
        $this->assertAttributeSame($postExpected, 'visitors', $traverser, 'The appropriate visitors are not present after removal');
    }
}