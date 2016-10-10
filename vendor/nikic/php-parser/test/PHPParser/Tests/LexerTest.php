<?php

class PHPParser_Tests_LexerTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPParser_Lexer */
    protected $lexer;

    protected function setUp() {
        $this->lexer = new PHPParser_Lexer;
    }

    /**
     * @dataProvider provideTestError
     */
    public function testError($code, $message) {
        try {
            $this->lexer->startLexing($code);
        } catch (PHPParser_Error $e) {
            $this->assertEquals($message, $e->getMessage());

            return;
        }

        $this->fail('Expected PHPParser_Error');
    }

    public function provideTestError() {
        return array(
            array('<?php /*', 'Unterminated comment on line 1'),
            array('<?php ' . "\1", 'Unexpected character "' . "\1" . '" (ASCII 1) on unknown line'),
            array('<?php ' . "\0", 'Unexpected null byte on unknown line'),
        );
    }

    /**
     * @dataProvider provideTestLex
     */
    public function testLex($code, $tokens) {
        $this->lexer->startLexing($code);
        while ($id = $this->lexer->getNextToken($value, $startAttributes, $endAttributes)) {
            $token = array_shift($tokens);

            $this->assertEquals($token[0], $id);
            $this->assertEquals($token[1], $value);
            $this->assertEquals($token[2], $startAttributes);
            $this->assertEquals($token[3], $endAttributes);
        }
    }

    public function provideTestLex() {
        return array(
            // tests conversion of closing PHP tag and drop of whitespace and opening tags
            array(
                '<?php tokens ?>plaintext',
                array(
                    array(
                        PHPParser_Parser::T_STRING, 'tokens',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                    array(
                        ord(';'), '?>',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                    array(
                        PHPParser_Parser::T_INLINE_HTML, 'plaintext',
                        array('startLine' => 1), array('endLine' => 1)
                    ),
                )
            ),
            // tests line numbers
            array(
                '<?php' . "\n" . '$ token /** doc' . "\n" . 'comment */ $',
                array(
                    array(
                        ord('$'), '$',
                        array('startLine' => 2), array('endLine' => 2)
                    ),
                    array(
                        PHPParser_Parser::T_STRING, 'token',
                        array('startLine' => 2), array('endLine' => 2)
                    ),
                    array(
                        ord('$'), '$',
                        array(
                            'startLine' => 3,
                            'comments' => array(new PHPParser_Comment_Doc('/** doc' . "\n" . 'comment */', 2))
                        ),
                        array('endLine' => 3)
                    ),
                )
            ),
            // tests comment extraction
            array(
                '<?php /* comment */ // comment' . "\n" . '/** docComment 1 *//** docComment 2 */ token',
                array(
                    array(
                        PHPParser_Parser::T_STRING, 'token',
                        array(
                            'startLine' => 2,
                            'comments' => array(
                                new PHPParser_Comment('/* comment */', 1),
                                new PHPParser_Comment('// comment' . "\n", 1),
                                new PHPParser_Comment_Doc('/** docComment 1 */', 2),
                                new PHPParser_Comment_Doc('/** docComment 2 */', 2),
                            ),
                        ),
                        array('endLine' => 2)
                    ),
                )
            ),
            // tests differing start and end line
            array(
                '<?php "foo' . "\n" . 'bar"',
                array(
                    array(
                        PHPParser_Parser::T_CONSTANT_ENCAPSED_STRING, '"foo' . "\n" . 'bar"',
                        array('startLine' => 1), array('endLine' => 2)
                    ),
                )
            ),
        );
    }

    /**
     * @dataProvider provideTestHaltCompiler
     */
    public function testHandleHaltCompiler($code, $remaining) {
        $this->lexer->startLexing($code);

        while (PHPParser_Parser::T_HALT_COMPILER !== $this->lexer->getNextToken());

        $this->assertEquals($this->lexer->handleHaltCompiler(), $remaining);
        $this->assertEquals(0, $this->lexer->getNextToken());
    }

    public function provideTestHaltCompiler() {
        return array(
            array('<?php ... __halt_compiler();Remaining Text', 'Remaining Text'),
            array('<?php ... __halt_compiler ( ) ;Remaining Text', 'Remaining Text'),
            array('<?php ... __halt_compiler() ?>Remaining Text', 'Remaining Text'),
            //array('<?php ... __halt_compiler();' . "\0", "\0"),
            //array('<?php ... __halt_compiler /* */ ( ) ;Remaining Text', 'Remaining Text'),
        );
    }
}