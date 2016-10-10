<?php

class PHPParser_Tests_Node_Stmt_PropertyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier) {
        $node = new PHPParser_Node_Stmt_Property(
            constant('PHPParser_Node_Stmt_Class::MODIFIER_' . strtoupper($modifier)),
            array() // invalid
        );

        $this->assertTrue($node->{'is' . $modifier}());
    }

    /**
     * @dataProvider provideModifiers
     */
    public function testNoModifiers($modifier) {
        $node = new PHPParser_Node_Stmt_Property(0, array());

        $this->assertFalse($node->{'is' . $modifier}());
    }

    public function provideModifiers() {
        return array(
            array('public'),
            array('protected'),
            array('private'),
            array('static'),
        );
    }
}