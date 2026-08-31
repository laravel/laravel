<?php declare(strict_types=1);

namespace PhpParser;

interface Parser {
    /**
     * Parses PHP code into a node tree.
     *
     * @param string $code The source code to parse
     * @param ErrorHandler|null $errorHandler Error handler to use for lexer/parser errors, defaults
     *                                        to ErrorHandler\Throwing.
     *
     * @return Node\Stmt[]|null Array of statements (or null non-throwing error handler is used and
     *                          the parser was unable to recover from an error).
     */
    public function parse(string $code, ?ErrorHandler $errorHandler = null): ?array;

    /**
     * Return tokens for the last parse.
     *
     * @return Token[]
     */
    public function getTokens(): array;
}
