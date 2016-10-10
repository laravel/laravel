<?php

class PHPParser_Tests_BuilderFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestFactory
     */
    public function testFactory($methodName, $className) {
        $factory = new PHPParser_BuilderFactory;
        $this->assertInstanceOf($className, $factory->$methodName('test'));
    }

    public function provideTestFactory() {
        return array(
            array('class',     'PHPParser_Builder_Class'),
            array('interface', 'PHPParser_Builder_Interface'),
            array('method',    'PHPParser_Builder_Method'),
            array('function',  'PHPParser_Builder_Function'),
            array('property',  'PHPParser_Builder_Property'),
            array('param',     'PHPParser_Builder_Param'),
        );
    }
}