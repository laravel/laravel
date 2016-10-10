<?php

class PHPParser_Tests_Builder_ClassTest extends PHPUnit_Framework_TestCase
{
    protected function createClassBuilder($class) {
        return new PHPParser_Builder_Class($class);
    }

    public function testExtendsImplements() {
        $node = $this->createClassBuilder('SomeLogger')
            ->extend('BaseLogger')
            ->implement('Namespaced\Logger', new PHPParser_Node_Name('SomeInterface'))
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_Class('SomeLogger', array(
                'extends' => new PHPParser_Node_Name('BaseLogger'),
                'implements' => array(
                    new PHPParser_Node_Name('Namespaced\Logger'),
                    new PHPParser_Node_Name('SomeInterface')
                ),
            )),
            $node
        );
    }

    public function testAbstract() {
        $node = $this->createClassBuilder('Test')
            ->makeAbstract()
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_Class('Test', array(
                'type' => PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT
            )),
            $node
        );
    }

    public function testFinal() {
        $node = $this->createClassBuilder('Test')
            ->makeFinal()
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_Class('Test', array(
                'type' => PHPParser_Node_Stmt_Class::MODIFIER_FINAL
            )),
            $node
        );
    }

    public function testStatementOrder() {
        $method = new PHPParser_Node_Stmt_ClassMethod('testMethod');
        $property = new PHPParser_Node_Stmt_Property(
            PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC,
            array(new PHPParser_Node_Stmt_PropertyProperty('testProperty'))
        );
        $const = new PHPParser_Node_Stmt_ClassConst(array(
            new PHPParser_Node_Const('TEST_CONST', new PHPParser_Node_Scalar_String('ABC'))
        ));
        $use = new PHPParser_Node_Stmt_TraitUse(array(new PHPParser_Node_Name('SomeTrait')));

        $node = $this->createClassBuilder('Test')
            ->addStmt($method)
            ->addStmt($property)
            ->addStmts(array($const, $use))
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_Class('Test', array(
                'stmts' => array($use, $const, $property, $method)
            )),
            $node
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Unexpected node of type "Stmt_Echo"
     */
    public function testInvalidStmtError() {
        $this->createClassBuilder('Test')
            ->addStmt(new PHPParser_Node_Stmt_Echo(array()))
        ;
    }
}