<?php

/**
 * This class unit-tests the interface builder
 */
class PHPParser_Tests_Builder_InterfaceTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPParser_Builder_Interface */
    protected $builder;

    protected function setUp() {
        $this->builder = new PHPParser_Builder_Interface('Contract');
    }

    private function dump($node) {
        $pp = new PHPParser_PrettyPrinter_Default();
        return $pp->prettyPrint(array($node));
    }

    public function testEmpty() {
        $contract = $this->builder->getNode();
        $this->assertInstanceOf('PHPParser_Node_Stmt_Interface', $contract);
        $this->assertEquals('Contract', $contract->name);
    }

    public function testExtending() {
        $contract = $this->builder->extend('Space\Root1', 'Root2')->getNode();
        $this->assertEquals(
            new PHPParser_Node_Stmt_Interface('Contract', array(
                'extends' => array(
                    new PHPParser_Node_Name('Space\Root1'),
                    new PHPParser_Node_Name('Root2')
                ),
            )), $contract
        );
    }

    public function testAddMethod() {
        $method = new PHPParser_Node_Stmt_ClassMethod('doSomething');
        $contract = $this->builder->addStmt($method)->getNode();
        $this->assertEquals(array($method), $contract->stmts);
    }

    public function testAddConst() {
        $const = new PHPParser_Node_Stmt_ClassConst(array(
            new PHPParser_Node_Const('SPEED_OF_LIGHT', new PHPParser_Node_Scalar_DNumber(299792458))
        ));
        $contract = $this->builder->addStmt($const)->getNode();
        $this->assertEquals(299792458, $contract->stmts[0]->consts[0]->value->value);
    }

    public function testOrder() {
        $const = new PHPParser_Node_Stmt_ClassConst(array(
            new PHPParser_Node_Const('SPEED_OF_LIGHT', new PHPParser_Node_Scalar_DNumber(299792458))
        ));
        $method = new PHPParser_Node_Stmt_ClassMethod('doSomething');
        $contract = $this->builder
            ->addStmt($method)
            ->addStmt($const)
            ->getNode()
        ;

        $this->assertInstanceOf('PHPParser_Node_Stmt_ClassConst', $contract->stmts[0]);
        $this->assertInstanceOf('PHPParser_Node_Stmt_ClassMethod', $contract->stmts[1]);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Unexpected node of type "Stmt_PropertyProperty"
     */
    public function testInvalidStmtError() {
        $this->builder->addStmt(new PHPParser_Node_Stmt_PropertyProperty('invalid'));
    }

    public function testFullFunctional() {
        $const = new PHPParser_Node_Stmt_ClassConst(array(
            new PHPParser_Node_Const('SPEED_OF_LIGHT', new PHPParser_Node_Scalar_DNumber(299792458))
        ));
        $method = new PHPParser_Node_Stmt_ClassMethod('doSomething');
        $contract = $this->builder
            ->addStmt($method)
            ->addStmt($const)
            ->getNode()
        ;

        eval($this->dump($contract));

        $this->assertTrue(interface_exists('Contract', false));
    }
}

