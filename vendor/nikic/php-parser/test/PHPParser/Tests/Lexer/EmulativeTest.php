<?php

class PHPParser_Tests_Lexer_EmulativeTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPParser_Lexer_Emulative */
    protected $lexer;

    protected function setUp() {
        $this->lexer = new PHPParser_Lexer_Emulative;
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testReplaceKeywords($keyword, $expectedToken) {
        $this->lexer->startLexing('<?php ' . $keyword);

        $this->assertEquals($expectedToken, $this->lexer->getNextToken());
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestReplaceKeywords
     */
    public function testNoReplaceKeywordsAfterObjectOperator($keyword) {
        $this->lexer->startLexing('<?php ->' . $keyword);

        $this->assertEquals(PHPParser_Parser::T_OBJECT_OPERATOR, $this->lexer->getNextToken());
        $this->assertEquals(PHPParser_Parser::T_STRING, $this->lexer->getNextToken());
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    public function provideTestReplaceKeywords() {
        return array(
            // PHP 5.5
            array('finally',       PHPParser_Parser::T_FINALLY),
            array('yield',         PHPParser_Parser::T_YIELD),

            // PHP 5.4
            array('callable',      PHPParser_Parser::T_CALLABLE),
            array('insteadof',     PHPParser_Parser::T_INSTEADOF),
            array('trait',         PHPParser_Parser::T_TRAIT),
            array('__TRAIT__',     PHPParser_Parser::T_TRAIT_C),

            // PHP 5.3
            array('__DIR__',       PHPParser_Parser::T_DIR),
            array('goto',          PHPParser_Parser::T_GOTO),
            array('namespace',     PHPParser_Parser::T_NAMESPACE),
            array('__NAMESPACE__', PHPParser_Parser::T_NS_C),
        );
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLexNewFeatures($code, array $expectedTokens) {
        $this->lexer->startLexing('<?php ' . $code);

        foreach ($expectedTokens as $expectedToken) {
            list($expectedTokenType, $expectedTokenText) = $expectedToken;
            $this->assertEquals($expectedTokenType, $this->lexer->getNextToken($text));
            $this->assertEquals($expectedTokenText, $text);
        }
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    /**
     * @dataProvider provideTestLexNewFeatures
     */
    public function testLeaveStuffAloneInStrings($code) {
        $stringifiedToken = '"' . addcslashes($code, '"\\') . '"';
        $this->lexer->startLexing('<?php ' . $stringifiedToken);

        $this->assertEquals(PHPParser_Parser::T_CONSTANT_ENCAPSED_STRING, $this->lexer->getNextToken($text));
        $this->assertEquals($stringifiedToken, $text);
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    public function provideTestLexNewFeatures() {
        return array(
            array('0b1010110', array(
                array(PHPParser_Parser::T_LNUMBER, '0b1010110'),
            )),
            array('0b1011010101001010110101010010101011010101010101101011001110111100', array(
                array(PHPParser_Parser::T_DNUMBER, '0b1011010101001010110101010010101011010101010101101011001110111100'),
            )),
            array('\\', array(
                array(PHPParser_Parser::T_NS_SEPARATOR, '\\'),
            )),
            array("<<<'NOWDOC'\nNOWDOC;\n", array(
                array(PHPParser_Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(PHPParser_Parser::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
            array("<<<'NOWDOC'\nFoobar\nNOWDOC;\n", array(
                array(PHPParser_Parser::T_START_HEREDOC, "<<<'NOWDOC'\n"),
                array(PHPParser_Parser::T_ENCAPSED_AND_WHITESPACE, "Foobar\n"),
                array(PHPParser_Parser::T_END_HEREDOC, 'NOWDOC'),
                array(ord(';'), ';'),
            )),
        );
    }
}