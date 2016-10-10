<?php

class PHPParser_Lexer
{
    protected $code;
    protected $tokens;
    protected $pos;
    protected $line;

    protected $tokenMap;
    protected $dropTokens;

    /**
     * Creates a Lexer.
     */
    public function __construct() {
        // map from internal tokens to PHPParser tokens
        $this->tokenMap = $this->createTokenMap();

        // map of tokens to drop while lexing (the map is only used for isset lookup,
        // that's why the value is simply set to 1; the value is never actually used.)
        $this->dropTokens = array_fill_keys(array(T_WHITESPACE, T_OPEN_TAG), 1);
    }

    /**
     * Initializes the lexer for lexing the provided source code.
     *
     * @param string $code The source code to lex
     *
     * @throws PHPParser_Error on lexing errors (unterminated comment or unexpected character)
     */
    public function startLexing($code) {
        $scream = ini_set('xdebug.scream', 0);

        $this->resetErrors();
        $this->tokens = @token_get_all($code);
        $this->handleErrors();

        ini_set('xdebug.scream', $scream);

        $this->code = $code; // keep the code around for __halt_compiler() handling
        $this->pos  = -1;
        $this->line =  1;
    }

    protected function resetErrors() {
        // set error_get_last() to defined state by forcing an undefined variable error
        set_error_handler(array($this, 'dummyErrorHandler'), 0);
        @$undefinedVariable;
        restore_error_handler();
    }

    private function dummyErrorHandler() { return false; }

    protected function handleErrors() {
        $error = error_get_last();

        if (preg_match(
            '~^Unterminated comment starting line ([0-9]+)$~',
            $error['message'], $matches
        )) {
            throw new PHPParser_Error('Unterminated comment', $matches[1]);
        }

        if (preg_match(
            '~^Unexpected character in input:  \'(.)\' \(ASCII=([0-9]+)\)~s',
            $error['message'], $matches
        )) {
            throw new PHPParser_Error(sprintf(
                'Unexpected character "%s" (ASCII %d)',
                $matches[1], $matches[2]
            ));
        }

        // PHP cuts error message after null byte, so need special case
        if (preg_match('~^Unexpected character in input:  \'$~', $error['message'])) {
            throw new PHPParser_Error('Unexpected null byte');
        }
    }

    /**
     * Fetches the next token.
     *
     * @param mixed $value           Variable to store token content in
     * @param mixed $startAttributes Variable to store start attributes in
     * @param mixed $endAttributes   Variable to store end attributes in
     *
     * @return int Token id
     */
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $startAttributes = array();
        $endAttributes   = array();

        while (isset($this->tokens[++$this->pos])) {
            $token = $this->tokens[$this->pos];

            if (is_string($token)) {
                $startAttributes['startLine'] = $this->line;
                $endAttributes['endLine']     = $this->line;

                // bug in token_get_all
                if ('b"' === $token) {
                    $value = 'b"';
                    return ord('"');
                } else {
                    $value = $token;
                    return ord($token);
                }
            } else {
                $this->line += substr_count($token[1], "\n");

                if (T_COMMENT === $token[0]) {
                    $startAttributes['comments'][] = new PHPParser_Comment($token[1], $token[2]);
                } elseif (T_DOC_COMMENT === $token[0]) {
                    $startAttributes['comments'][] = new PHPParser_Comment_Doc($token[1], $token[2]);
                } elseif (!isset($this->dropTokens[$token[0]])) {
                    $value = $token[1];
                    $startAttributes['startLine'] = $token[2];
                    $endAttributes['endLine']     = $this->line;

                    return $this->tokenMap[$token[0]];
                }
            }
        }

        $startAttributes['startLine'] = $this->line;

        // 0 is the EOF token
        return 0;
    }

    /**
     * Handles __halt_compiler() by returning the text after it.
     *
     * @return string Remaining text
     */
    public function handleHaltCompiler() {
        // get the length of the text before the T_HALT_COMPILER token
        $textBefore = '';
        for ($i = 0; $i <= $this->pos; ++$i) {
            if (is_string($this->tokens[$i])) {
                $textBefore .= $this->tokens[$i];
            } else {
                $textBefore .= $this->tokens[$i][1];
            }
        }

        // text after T_HALT_COMPILER, still including ();
        $textAfter = substr($this->code, strlen($textBefore));

        // ensure that it is followed by ();
        // this simplifies the situation, by not allowing any comments
        // in between of the tokens.
        if (!preg_match('~\s*\(\s*\)\s*(?:;|\?>\r?\n?)~', $textAfter, $matches)) {
            throw new PHPParser_Error('__HALT_COMPILER must be followed by "();"');
        }

        // prevent the lexer from returning any further tokens
        $this->pos = count($this->tokens);

        // return with (); removed
        return (string) substr($textAfter, strlen($matches[0])); // (string) converts false to ''
    }

    /**
     * Creates the token map.
     *
     * The token map maps the PHP internal token identifiers
     * to the identifiers used by the Parser. Additionally it
     * maps T_OPEN_TAG_WITH_ECHO to T_ECHO and T_CLOSE_TAG to ';'.
     *
     * @return array The token map
     */
    protected function createTokenMap() {
        $tokenMap = array();

        // 256 is the minimum possible token number, as everything below
        // it is an ASCII value
        for ($i = 256; $i < 1000; ++$i) {
            // T_DOUBLE_COLON is equivalent to T_PAAMAYIM_NEKUDOTAYIM
            if (T_DOUBLE_COLON === $i) {
                $tokenMap[$i] = PHPParser_Parser::T_PAAMAYIM_NEKUDOTAYIM;
            // T_OPEN_TAG_WITH_ECHO with dropped T_OPEN_TAG results in T_ECHO
            } elseif(T_OPEN_TAG_WITH_ECHO === $i) {
                $tokenMap[$i] = PHPParser_Parser::T_ECHO;
            // T_CLOSE_TAG is equivalent to ';'
            } elseif(T_CLOSE_TAG === $i) {
                $tokenMap[$i] = ord(';');
            // and the others can be mapped directly
            } elseif ('UNKNOWN' !== ($name = token_name($i))
                      && defined($name = 'PHPParser_Parser::' . $name)
            ) {
                $tokenMap[$i] = constant($name);
            }
        }

        return $tokenMap;
    }
}
