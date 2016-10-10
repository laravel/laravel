<?php

class PHPParser_Tests_Builder_MethodTest extends PHPUnit_Framework_TestCase
{
    public function createMethodBuilder($name) {
        return new PHPParser_Builder_Method($name);
    }

    public function testModifiers() {
        $node = $this->createMethodBuilder('test')
            ->makePublic()
            ->makeAbstract()
            ->makeStatic()
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_ClassMethod('test', array(
                'type' => PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC
                        | PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT
                        | PHPParser_Node_Stmt_Class::MODIFIER_STATIC,
                'stmts' => null,
            )),
            $node
        );

        $node = $this->createMethodBuilder('test')
            ->makeProtected()
            ->makeFinal()
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_ClassMethod('test', array(
                'type' => PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED
                        | PHPParser_Node_Stmt_Class::MODIFIER_FINAL
            )),
            $node
        );

        $node = $this->createMethodBuilder('test')
            ->makePrivate()
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_ClassMethod('test', array(
                'type' => PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE
            )),
            $node
        );
    }

    public function testReturnByRef() {
        $node = $this->createMethodBuilder('test')
            ->makeReturnByRef()
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_ClassMethod('test', array(
                'byRef' => true
            )),
            $node
        );
    }

    public function testParams() {
        $param1 = new PHPParser_Node_Param('test1');
        $param2 = new PHPParser_Node_Param('test2');
        $param3 = new PHPParser_Node_Param('test3');

        $node = $this->createMethodBuilder('test')
            ->addParam($param1)
            ->addParams(array($param2, $param3))
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_ClassMethod('test', array(
                'params' => array($param1, $param2, $param3)
            )),
            $node
        );
    }

    public function testStmts() {
        $stmt1 = new PHPParser_Node_Expr_Print(new PHPParser_Node_Scalar_String('test1'));
        $stmt2 = new PHPParser_Node_Expr_Print(new PHPParser_Node_Scalar_String('test2'));
        $stmt3 = new PHPParser_Node_Expr_Print(new PHPParser_Node_Scalar_String('test3'));

        $node = $this->createMethodBuilder('test')
            ->addStmt($stmt1)
            ->addStmts(array($stmt2, $stmt3))
            ->getNode()
        ;

        $this->assertEquals(
            new PHPParser_Node_Stmt_ClassMethod('test', array(
                'stmts' => array($stmt1, $stmt2, $stmt3)
            )),
            $node
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Cannot add statements to an abstract method
     */
    public function testAddStmtToAbstractMethodError() {
        $this->createMethodBuilder('test')
            ->makeAbstract()
            ->addStmt(new PHPParser_Node_Expr_Print(new PHPParser_Node_Scalar_String('test')))
        ;
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Cannot make method with statements abstract
     */
    public function testMakeMethodWithStmtsAbstractError() {
        $this->createMethodBuilder('test')
            ->addStmt(new PHPParser_Node_Expr_Print(new PHPParser_Node_Scalar_String('test')))
            ->makeAbstract()
        ;
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Expected parameter node, got "Name"
     */
    public function testInvalidParamError() {
        $this->createMethodBuilder('test')
            ->addParam(new PHPParser_Node_Name('foo'))
        ;
    }
}