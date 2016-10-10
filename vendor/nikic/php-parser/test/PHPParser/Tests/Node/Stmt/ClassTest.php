<?php

class PHPParser_Tests_Node_Stmt_ClassTest extends PHPUnit_Framework_TestCase
{
    public function testIsAbstract() {
        $class = new PHPParser_Node_Stmt_Class('Foo', array('type' => PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT));
        $this->assertTrue($class->isAbstract());

        $class = new PHPParser_Node_Stmt_Class('Foo');
        $this->assertFalse($class->isAbstract());
    }

    public function testIsFinal() {
        $class = new PHPParser_Node_Stmt_Class('Foo', array('type' => PHPParser_Node_Stmt_Class::MODIFIER_FINAL));
        $this->assertTrue($class->isFinal());

        $class = new PHPParser_Node_Stmt_Class('Foo');
        $this->assertFalse($class->isFinal());
    }

    public function testGetMethods() {
        $methods = array(
            new PHPParser_Node_Stmt_ClassMethod('foo'),
            new PHPParser_Node_Stmt_ClassMethod('bar'),
            new PHPParser_Node_Stmt_ClassMethod('fooBar'),
        );
        $class = new PHPParser_Node_Stmt_Class('Foo', array(
            'stmts' => array(
                new PHPParser_Node_Stmt_TraitUse(array()),
                $methods[0],
                new PHPParser_Node_Stmt_Const(array()),
                $methods[1],
                new PHPParser_Node_Stmt_Property(0, array()),
                $methods[2],
            )
        ));

        $this->assertEquals($methods, $class->getMethods());
    }
}