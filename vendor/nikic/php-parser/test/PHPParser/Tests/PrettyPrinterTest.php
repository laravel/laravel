<?php

require_once dirname(__FILE__) . '/CodeTestAbstract.php';

class PHPParser_Tests_PrettyPrinterTest extends PHPParser_Tests_CodeTestAbstract
{
    protected function doTestPrettyPrintMethod($method, $name, $code, $dump) {
        $parser = new PHPParser_Parser(new PHPParser_Lexer_Emulative);
        $prettyPrinter = new PHPParser_PrettyPrinter_Default;

        $stmts = $parser->parse($code);
        $this->assertEquals(
            $this->canonicalize($dump),
            $this->canonicalize($prettyPrinter->$method($stmts)),
            $name
        );
    }

    /**
     * @dataProvider provideTestPrettyPrint
     * @covers PHPParser_PrettyPrinter_Default<extended>
     */
    public function testPrettyPrint($name, $code, $dump) {
        $this->doTestPrettyPrintMethod('prettyPrint', $name, $code, $dump);
    }

    /**
     * @dataProvider provideTestPrettyPrintFile
     * @covers PHPParser_PrettyPrinter_Default<extended>
     */
    public function testPrettyPrintFile($name, $code, $dump) {
        $this->doTestPrettyPrintMethod('prettyPrintFile', $name, $code, $dump);
    }

    public function provideTestPrettyPrint() {
        return $this->getTests(dirname(__FILE__) . '/../../code/prettyPrinter', 'test');
    }

    public function provideTestPrettyPrintFile() {
        return $this->getTests(dirname(__FILE__) . '/../../code/prettyPrinter', 'file-test');
    }
}