<?php declare(strict_types=1);

namespace PhpParser;

require __DIR__ . '/compatibility_tokens.php';

class Lexer {
    /**
     * Tokenize the provided source code.
     *
     * The token array is in the same format as provided by the PhpToken::tokenize() method in
     * PHP 8.0. The tokens are instances of PhpParser\Token, to abstract over a polyfill
     * implementation in earlier PHP version.
     *
     * The token array is terminated by a sentinel token with token ID 0.
     * The token array does not discard any tokens (i.e. whitespace and comments are included).
     * The token position attributes are against this token array.
     *
     * @param string $code The source code to tokenize.
     * @param ErrorHandler|null $errorHandler Error handler to use for lexing errors. Defaults to
     *                                        ErrorHandler\Throwing.
     * @return Token[] Tokens
     */
    public function tokenize(string $code, ?ErrorHandler $errorHandler = null): array {
        if (null === $errorHandler) {
            $errorHandler = new ErrorHandler\Throwing();
        }

        $scream = ini_set('xdebug.scream', '0');

        $tokens = @Token::tokenize($code);
        $this->postprocessTokens($tokens, $errorHandler);

        if (false !== $scream) {
            ini_set('xdebug.scream', $scream);
        }

        return $tokens;
    }

    private function handleInvalidCharacter(Token $token, ErrorHandler $errorHandler): void {
        $chr = $token->text;
        if ($chr === "\0") {
            // PHP cuts error message after null byte, so need special case
            $errorMsg = 'Unexpected null byte';
        } else {
            $errorMsg = sprintf(
                'Unexpected character "%s" (ASCII %d)', $chr, ord($chr)
            );
        }

        $errorHandler->handleError(new Error($errorMsg, [
            'startLine' => $token->line,
            'endLine' => $token->line,
            'startFilePos' => $token->pos,
            'endFilePos' => $token->pos,
        ]));
    }

    private function isUnterminatedComment(Token $token): bool {
        return $token->is([\T_COMMENT, \T_DOC_COMMENT])
            && substr($token->text, 0, 2) === '/*'
            && substr($token->text, -2) !== '*/';
    }

    /**
     * @param list<Token> $tokens
     */
    protected function postprocessTokens(array &$tokens, ErrorHandler $errorHandler): void {
        // This function reports errors (bad characters and unterminated comments) in the token
        // array, and performs certain canonicalizations:
        //  * Use PHP 8.1 T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG and
        //    T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG tokens used to disambiguate intersection types.
        //  * Add a sentinel token with ID 0.

        $numTokens = \count($tokens);
        if ($numTokens === 0) {
            // Empty input edge case: Just add the sentinel token.
            $tokens[] = new Token(0, "\0", 1, 0);
            return;
        }

        for ($i = 0; $i < $numTokens; $i++) {
            $token = $tokens[$i];
            if ($token->id === \T_BAD_CHARACTER) {
                $this->handleInvalidCharacter($token, $errorHandler);
            }

            if ($token->id === \ord('&')) {
                $next = $i + 1;
                while (isset($tokens[$next]) && $tokens[$next]->id === \T_WHITESPACE) {
                    $next++;
                }
                $followedByVarOrVarArg = isset($tokens[$next]) &&
                    $tokens[$next]->is([\T_VARIABLE, \T_ELLIPSIS]);
                $token->id = $followedByVarOrVarArg
                    ? \T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG
                    : \T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG;
            }
        }

        // Check for unterminated comment
        $lastToken = $tokens[$numTokens - 1];
        if ($this->isUnterminatedComment($lastToken)) {
            $errorHandler->handleError(new Error('Unterminated comment', [
                'startLine' => $lastToken->line,
                'endLine' => $lastToken->getEndLine(),
                'startFilePos' => $lastToken->pos,
                'endFilePos' => $lastToken->getEndPos(),
            ]));
        }

        // Add sentinel token.
        $tokens[] = new Token(0, "\0", $lastToken->getEndLine(), $lastToken->getEndPos());
    }
}
