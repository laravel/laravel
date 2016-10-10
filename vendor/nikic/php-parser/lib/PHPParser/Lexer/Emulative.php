<?php

/**
 * ATTENTION: This code is WRITE-ONLY. Do not try to read it.
 */
class PHPParser_Lexer_Emulative extends PHPParser_Lexer
{
    protected $newKeywords;
    protected $inObjectAccess;

    public function __construct() {
        parent::__construct();

        $newKeywordsPerVersion = array(
            '5.5.0-dev' => array(
                'finally'       => PHPParser_Parser::T_FINALLY,
                'yield'         => PHPParser_Parser::T_YIELD,
            ),
            '5.4.0-dev' => array(
                'callable'      => PHPParser_Parser::T_CALLABLE,
                'insteadof'     => PHPParser_Parser::T_INSTEADOF,
                'trait'         => PHPParser_Parser::T_TRAIT,
                '__trait__'     => PHPParser_Parser::T_TRAIT_C,
            ),
            '5.3.0-dev' => array(
                '__dir__'       => PHPParser_Parser::T_DIR,
                'goto'          => PHPParser_Parser::T_GOTO,
                'namespace'     => PHPParser_Parser::T_NAMESPACE,
                '__namespace__' => PHPParser_Parser::T_NS_C,
            ),
        );

        $this->newKeywords = array();
        foreach ($newKeywordsPerVersion as $version => $newKeywords) {
            if (version_compare(PHP_VERSION, $version, '>=')) {
                break;
            }

            $this->newKeywords += $newKeywords;
        }
    }

    public function startLexing($code) {
        $this->inObjectAccess = false;

        // on PHP 5.4 don't do anything
        if (version_compare(PHP_VERSION, '5.4.0RC1', '>=')) {
            parent::startLexing($code);
        } else {
            $code = $this->preprocessCode($code);
            parent::startLexing($code);
            $this->postprocessTokens();
        }
    }

    /*
     * Replaces new features in the code by ~__EMU__{NAME}__{DATA}__~ sequences.
     * ~LABEL~ is never valid PHP code, that's why we can (to some degree) safely
     * use it here.
     * Later when preprocessing the tokens these sequences will either be replaced
     * by real tokens or replaced with their original content (e.g. if they occured
     * inside a string, i.e. a place where they don't have a special meaning).
     */
    protected function preprocessCode($code) {
        // binary notation (0b010101101001...)
        $code = preg_replace('(\b0b[01]+\b)', '~__EMU__BINARY__$0__~', $code);

        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            // namespace separator (backslash not followed by some special characters,
            // which are not valid after a NS separator, but would cause problems with
            // escape sequence parsing if one would replace the backslash there)
            $code = preg_replace('(\\\\(?!["\'`${\\\\]))', '~__EMU__NS__~', $code);

            // nowdoc (<<<'ABC'\ncontent\nABC;)
            $code = preg_replace_callback(
                '((*BSR_ANYCRLF)        # set \R to (?>\r\n|\r|\n)
                  (b?<<<[\t ]*\'([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\'\R) # opening token
                  ((?:(?!\2;?\R).*\R)*) # content
                  (\2)                  # closing token
                  (?=;?\R)              # must be followed by newline (with optional semicolon)
                 )x',
                array($this, 'encodeNowdocCallback'),
                $code
            );
        }

        return $code;
    }

    /*
     * As nowdocs can have arbitrary content but LABELs can only contain a certain
     * range of characters, the nowdoc content is encoded as hex and separated by
     * 'x' tokens. So the result of the encoding will look like this:
     * ~__EMU__NOWDOC__{HEX(START_TOKEN)}x{HEX(CONTENT)}x{HEX(END_TOKEN)}~
     */
    public function encodeNowdocCallback(array $matches) {
        return '~__EMU__NOWDOC__'
                . bin2hex($matches[1]) . 'x' . bin2hex($matches[3]) . 'x' . bin2hex($matches[4])
                . '__~';
    }

    /*
     * Replaces the ~__EMU__...~ sequences with real tokens or their original
     * value.
     */
    protected function postprocessTokens() {
        // we need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        for ($i = 0, $c = count($this->tokens); $i < $c; ++$i) {
            // first check that the following tokens are form ~LABEL~,
            // then match the __EMU__... sequence.
            if ('~' === $this->tokens[$i]
                && isset($this->tokens[$i + 2])
                && '~' === $this->tokens[$i + 2]
                && T_STRING === $this->tokens[$i + 1][0]
                && preg_match('(^__EMU__([A-Z]++)__(?:([A-Za-z0-9]++)__)?$)', $this->tokens[$i + 1][1], $matches)
            ) {
                if ('BINARY' === $matches[1]) {
                    // the binary number can either be an integer or a double, so return a LNUMBER
                    // or DNUMBER respectively
                    $replace = array(
                        array(is_int(bindec($matches[2])) ? T_LNUMBER : T_DNUMBER, $matches[2], $this->tokens[$i + 1][2])
                    );
                } elseif ('NS' === $matches[1]) {
                    // a \ single char token is returned here and replaced by a
                    // PHPParser_Parser::T_NS_SEPARATOR token in ->getNextToken(). This hacks around
                    // the limitations arising from T_NS_SEPARATOR not being defined on 5.3
                    $replace = array('\\');
                } elseif ('NOWDOC' === $matches[1]) {
                    // decode the encoded nowdoc payload; pack('H*' is bin2hex( for 5.3
                    list($start, $content, $end) = explode('x', $matches[2]);
                    list($start, $content, $end) = array(pack('H*', $start), pack('H*', $content), pack('H*', $end));

                    $replace = array();
                    $replace[] = array(T_START_HEREDOC, $start, $this->tokens[$i + 1][2]);
                    if ('' !== $content) {
                        $replace[] = array(T_ENCAPSED_AND_WHITESPACE, $content, -1);
                    }
                    $replace[] = array(T_END_HEREDOC, $end, -1);
                } else {
                    // just ignore all other __EMU__ sequences
                    continue;
                }

                array_splice($this->tokens, $i, 3, $replace);
                $c -= 3 - count($replace);
            // for multichar tokens (e.g. strings) replace any ~__EMU__...~ sequences
            // in their content with the original character sequence
            } elseif (is_array($this->tokens[$i])
                      && 0 !== strpos($this->tokens[$i][1], '__EMU__')
            ) {
                $this->tokens[$i][1] = preg_replace_callback(
                    '(~__EMU__([A-Z]++)__(?:([A-Za-z0-9]++)__)?~)',
                    array($this, 'restoreContentCallback'),
                    $this->tokens[$i][1]
                );
            }
        }
    }

    /*
     * This method is a callback for restoring EMU sequences in
     * multichar tokens (like strings) to their original value.
     */
    public function restoreContentCallback(array $matches) {
        if ('BINARY' === $matches[1]) {
            return $matches[2];
        } elseif ('NS' === $matches[1]) {
            return '\\';
        } elseif ('NOWDOC' === $matches[1]) {
            list($start, $content, $end) = explode('x', $matches[2]);
            return pack('H*', $start) . pack('H*', $content) . pack('H*', $end);
        } else {
            return $matches[0];
        }
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $token = parent::getNextToken($value, $startAttributes, $endAttributes);

        // replace new keywords by their respective tokens. This is not done
        // if we currently are in an object access (e.g. in $obj->namespace
        // "namespace" stays a T_STRING tokens and isn't converted to T_NAMESPACE)
        if (PHPParser_Parser::T_STRING === $token && !$this->inObjectAccess) {
            if (isset($this->newKeywords[strtolower($value)])) {
                return $this->newKeywords[strtolower($value)];
            }
        // backslashes are replaced by T_NS_SEPARATOR tokens
        } elseif (92 === $token) { // ord('\\')
            return PHPParser_Parser::T_NS_SEPARATOR;
        // keep track of whether we currently are in an object access (after ->)
        } elseif (PHPParser_Parser::T_OBJECT_OPERATOR === $token) {
            $this->inObjectAccess = true;
        } else {
            $this->inObjectAccess = false;
        }

        return $token;
    }
}