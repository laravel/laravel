<?php

namespace Jeremeamia\SuperClosure\Test\Visitor;

use Jeremeamia\SuperClosure\Visitor\MagicConstantVisitor;
use Jeremeamia\SuperClosure\ClosureLocation;

/**
 * @covers Jeremeamia\SuperClosure\Visitor\MagicConstantVisitor
 */
class MagicConstantVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testDataFromClosureLocationGetsUsed()
    {
        $location = new ClosureLocation();
        $location->class = '[class]';
        $location->directory = '[directory]';
        $location->file = '[file]';
        $location->function = '[function]';
        $location->line = '[line]';
        $location->method = '[method]';
        $location->namespace = '[namespace]';
        $location->trait = '[trait]';

        $nodes = array(
            'PHPParser_Node_Scalar_LineConst'   => 'PHPParser_Node_Scalar_LNumber',
            'PHPParser_Node_Scalar_FileConst'   => 'PHPParser_Node_Scalar_String',
            'PHPParser_Node_Scalar_DirConst'    => 'PHPParser_Node_Scalar_String',
            'PHPParser_Node_Scalar_FuncConst'   => 'PHPParser_Node_Scalar_String',
            'PHPParser_Node_Scalar_NSConst'     => 'PHPParser_Node_Scalar_String',
            'PHPParser_Node_Scalar_ClassConst'  => 'PHPParser_Node_Scalar_String',
            'PHPParser_Node_Scalar_MethodConst' => 'PHPParser_Node_Scalar_String',
            'PHPParser_Node_Scalar_TraitConst'  => 'PHPParser_Node_Scalar_String',
            'PHPParser_Node_Scalar_String'      => 'PHPParser_Node_Scalar_String',

        );

        $visitor = new MagicConstantVisitor($location);
        foreach ($nodes as $originalNodeName => $resultNodeName) {
            $mockNode = $this->getMockBuilder($originalNodeName)
                ->disableOriginalConstructor()
                ->setMethods(array('getType', 'getAttribute'))
                ->getMock();
            $mockNode->expects($this->any())
                ->method('getAttribute')
                ->will($this->returnValue(1));
            $mockNode->expects($this->any())
                ->method('getType')
                ->will($this->returnValue(substr($originalNodeName, 15)));
            $resultNode = $visitor->leaveNode($mockNode) ?: $mockNode;
            $this->assertInstanceOf($resultNodeName, $resultNode);
        }
    }
}
