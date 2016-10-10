<?php

class PHPParser_Tests_NodeDumperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestDump
     * @covers PHPParser_NodeDumper::dump
     */
    public function testDump($node, $dump) {
        $dumper = new PHPParser_NodeDumper;

        $this->assertEquals($dump, $dumper->dump($node));
    }

    public function provideTestDump() {
        return array(
            array(
                array(),
'array(
)'
            ),
            array(
                array('Foo', 'Bar', 'Key' => 'FooBar'),
'array(
    0: Foo
    1: Bar
    Key: FooBar
)'
            ),
            array(
                new PHPParser_Node_Name(array('Hallo', 'World')),
'Name(
    parts: array(
        0: Hallo
        1: World
    )
)'
            ),
            array(
                new PHPParser_Node_Expr_Array(array(
                    new PHPParser_Node_Expr_ArrayItem(new PHPParser_Node_Scalar_String('Foo'))
                )),
'Expr_Array(
    items: array(
        0: Expr_ArrayItem(
            key: null
            value: Scalar_String(
                value: Foo
            )
            byRef: false
        )
    )
)'
            ),
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Can only dump nodes and arrays.
     */
    public function testError() {
        $dumper = new PHPParser_NodeDumper;
        $dumper->dump(new stdClass);
    }
}