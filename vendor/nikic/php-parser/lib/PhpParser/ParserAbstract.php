<?php declare(strict_types=1);

namespace PhpParser;

/*
 * This parser is based on a skeleton written by Moriyoshi Koizumi, which in
 * turn is based on work by Masato Bito.
 */

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Identifier;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\UseItem;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeVisitor\CommentAnnotatingVisitor;

abstract class ParserAbstract implements Parser {
    private const SYMBOL_NONE = -1;

    /** @var Lexer Lexer that is used when parsing */
    protected Lexer $lexer;
    /** @var PhpVersion PHP version to target on a best-effort basis */
    protected PhpVersion $phpVersion;

    /*
     * The following members will be filled with generated parsing data:
     */

    /** @var int Size of $tokenToSymbol map */
    protected int $tokenToSymbolMapSize;
    /** @var int Size of $action table */
    protected int $actionTableSize;
    /** @var int Size of $goto table */
    protected int $gotoTableSize;

    /** @var int Symbol number signifying an invalid token */
    protected int $invalidSymbol;
    /** @var int Symbol number of error recovery token */
    protected int $errorSymbol;
    /** @var int Action number signifying default action */
    protected int $defaultAction;
    /** @var int Rule number signifying that an unexpected token was encountered */
    protected int $unexpectedTokenRule;

    protected int $YY2TBLSTATE;
    /** @var int Number of non-leaf states */
    protected int $numNonLeafStates;

    /** @var int[] Map of PHP token IDs to internal symbols */
    protected array $phpTokenToSymbol;
    /** @var array<int, bool> Map of PHP token IDs to drop */
    protected array $dropTokens;
    /** @var int[] Map of external symbols (static::T_*) to internal symbols */
    protected array $tokenToSymbol;
    /** @var string[] Map of symbols to their names */
    protected array $symbolToName;
    /** @var array<int, string> Names of the production rules (only necessary for debugging) */
    protected array $productions;

    /** @var int[] Map of states to a displacement into the $action table. The corresponding action for this
     *             state/symbol pair is $action[$actionBase[$state] + $symbol]. If $actionBase[$state] is 0, the
     *             action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected array $actionBase;
    /** @var int[] Table of actions. Indexed according to $actionBase comment. */
    protected array $action;
    /** @var int[] Table indexed analogously to $action. If $actionCheck[$actionBase[$state] + $symbol] != $symbol
     *             then the action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected array $actionCheck;
    /** @var int[] Map of states to their default action */
    protected array $actionDefault;
    /** @var callable[] Semantic action callbacks */
    protected array $reduceCallbacks;

    /** @var int[] Map of non-terminals to a displacement into the $goto table. The corresponding goto state for this
     *             non-terminal/state pair is $goto[$gotoBase[$nonTerminal] + $state] (unless defaulted) */
    protected array $gotoBase;
    /** @var int[] Table of states to goto after reduction. Indexed according to $gotoBase comment. */
    protected array $goto;
    /** @var int[] Table indexed analogously to $goto. If $gotoCheck[$gotoBase[$nonTerminal] + $state] != $nonTerminal
     *             then the goto state is defaulted, i.e. $gotoDefault[$nonTerminal] should be used. */
    protected array $gotoCheck;
    /** @var int[] Map of non-terminals to the default state to goto after their reduction */
    protected array $gotoDefault;

    /** @var int[] Map of rules to the non-terminal on their left-hand side, i.e. the non-terminal to use for
     *             determining the state to goto after reduction. */
    protected array $ruleToNonTerminal;
    /** @var int[] Map of rules to the length of their right-hand side, which is the number of elements that have to
     *             be popped from the stack(s) on reduction. */
    protected array $ruleToLength;

    /*
     * The following members are part of the parser state:
     */

    /** @var mixed Temporary value containing the result of last semantic action (reduction) */
    protected $semValue;
    /** @var mixed[] Semantic value stack (contains values of tokens and semantic action results) */
    protected array $semStack;
    /** @var int[] Token start position stack */
    protected array $tokenStartStack;
    /** @var int[] Token end position stack */
    protected array $tokenEndStack;

    /** @var ErrorHandler Error handler */
    protected ErrorHandler $errorHandler;
    /** @var int Error state, used to avoid error floods */
    protected int $errorState;

    /** @var \SplObjectStorage<Array_, null>|null Array nodes created during parsing, for postprocessing of empty elements. */
    protected ?\SplObjectStorage $createdArrays;

    /** @var \SplObjectStorage<Expr\ArrowFunction, null>|null
     *       Arrow functions that are wrapped in parentheses, to enforce the pipe operator parentheses requirements.
     */
    protected ?\SplObjectStorage $parenthesizedArrowFunctions;

    /** @var Token[] Tokens for the current parse */
    protected array $tokens;
    /** @var int Current position in token array */
    protected int $tokenPos;

    /**
     * Initialize $reduceCallbacks map.
     */
    abstract protected function initReduceCallbacks(): void;

    /**
     * Creates a parser instance.
     *
     * Options:
     *  * phpVersion: ?PhpVersion,
     *
     * @param Lexer $lexer A lexer
     * @param PhpVersion $phpVersion PHP version to target, defaults to latest supported. This
     *                               option is best-effort: Even if specified, parsing will generally assume the latest
     *                               supported version and only adjust behavior in minor ways, for example by omitting
     *                               errors in older versions and interpreting type hints as a name or identifier depending
     *                               on version.
     */
    public function __construct(Lexer $lexer, ?PhpVersion $phpVersion = null) {
        $this->lexer = $lexer;
        $this->phpVersion = $phpVersion ?? PhpVersion::getNewestSupported();

        $this->initReduceCallbacks();
        $this->phpTokenToSymbol = $this->createTokenMap();
        $this->dropTokens = array_fill_keys(
            [\T_WHITESPACE, \T_OPEN_TAG, \T_COMMENT, \T_DOC_COMMENT, \T_BAD_CHARACTER], true
        );
    }

    /**
     * Parses PHP code into a node tree.
     *
     * If a non-throwing error handler is used, the parser will continue parsing after an error
     * occurred and attempt to build a partial AST.
     *
     * @param string $code The source code to parse
     * @param ErrorHandler|null $errorHandler Error handler to use for lexer/parser errors, defaults
     *                                        to ErrorHandler\Throwing.
     *
     * @return Node\Stmt[]|null Array of statements (or null non-throwing error handler is used and
     *                          the parser was unable to recover from an error).
     */
    public function parse(string $code, ?ErrorHandler $errorHandler = null): ?array {
        $this->errorHandler = $errorHandler ?: new ErrorHandler\Throwing();
        $this->createdArrays = new \SplObjectStorage();
        $this->parenthesizedArrowFunctions = new \SplObjectStorage();

        $this->tokens = $this->lexer->tokenize($code, $this->errorHandler);
        $result = $this->doParse();

        // Report errors for any empty elements used inside arrays. This is delayed until after the main parse,
        // because we don't know a priori whether a given array expression will be used in a destructuring context
        // or not.
        foreach ($this->createdArrays as $node) {
            foreach ($node->items as $item) {
                if ($item->value instanceof Expr\Error) {
                    $this->errorHandler->handleError(
                        new Error('Cannot use empty array elements in arrays', $item->getAttributes()));
                }
            }
        }

        // Clear out some of the interior state, so we don't hold onto unnecessary
        // memory between uses of the parser
        $this->tokenStartStack = [];
        $this->tokenEndStack = [];
        $this->semStack = [];
        $this->semValue = null;
        $this->createdArrays = null;
        $this->parenthesizedArrowFunctions = null;

        if ($result !== null) {
            $traverser = new NodeTraverser(new CommentAnnotatingVisitor($this->tokens));
            $traverser->traverse($result);
        }

        return $result;
    }

    public function getTokens(): array {
        return $this->tokens;
    }

    /** @return Stmt[]|null */
    protected function doParse(): ?array {
        // We start off with no lookahead-token
        $symbol = self::SYMBOL_NONE;
        $tokenValue = null;
        $this->tokenPos = -1;

        // Keep stack of start and end attributes
        $this->tokenStartStack = [];
        $this->tokenEndStack = [0];

        // Start off in the initial state and keep a stack of previous states
        $state = 0;
        $stateStack = [$state];

        // Semantic value stack (contains values of tokens and semantic action results)
        $this->semStack = [];

        // Current position in the stack(s)
        $stackPos = 0;

        $this->errorState = 0;

        for (;;) {
            //$this->traceNewState($state, $symbol);

            if ($this->actionBase[$state] === 0) {
                $rule = $this->actionDefault[$state];
            } else {
                if ($symbol === self::SYMBOL_NONE) {
                    do {
                        $token = $this->tokens[++$this->tokenPos];
                        $tokenId = $token->id;
                    } while (isset($this->dropTokens[$tokenId]));

                    // Map the lexer token id to the internally used symbols.
                    $tokenValue = $token->text;
                    if (!isset($this->phpTokenToSymbol[$tokenId])) {
                        throw new \RangeException(sprintf(
                            'The lexer returned an invalid token (id=%d, value=%s)',
                            $tokenId, $tokenValue
                        ));
                    }
                    $symbol = $this->phpTokenToSymbol[$tokenId];

                    //$this->traceRead($symbol);
                }

                $idx = $this->actionBase[$state] + $symbol;
                if ((($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol)
                     || ($state < $this->YY2TBLSTATE
                         && ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) >= 0
                         && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol))
                    && ($action = $this->action[$idx]) !== $this->defaultAction) {
                    /*
                     * >= numNonLeafStates: shift and reduce
                     * > 0: shift
                     * = 0: accept
                     * < 0: reduce
                     * = -YYUNEXPECTED: error
                     */
                    if ($action > 0) {
                        /* shift */
                        //$this->traceShift($symbol);

                        ++$stackPos;
                        $stateStack[$stackPos] = $state = $action;
                        $this->semStack[$stackPos] = $tokenValue;
                        $this->tokenStartStack[$stackPos] = $this->tokenPos;
                        $this->tokenEndStack[$stackPos] = $this->tokenPos;
                        $symbol = self::SYMBOL_NONE;

                        if ($this->errorState) {
                            --$this->errorState;
                        }

                        if ($action < $this->numNonLeafStates) {
                            continue;
                        }

                        /* $yyn >= numNonLeafStates means shift-and-reduce */
                        $rule = $action - $this->numNonLeafStates;
                    } else {
                        $rule = -$action;
                    }
                } else {
                    $rule = $this->actionDefault[$state];
                }
            }

            for (;;) {
                if ($rule === 0) {
                    /* accept */
                    //$this->traceAccept();
                    return $this->semValue;
                }
                if ($rule !== $this->unexpectedTokenRule) {
                    /* reduce */
                    //$this->traceReduce($rule);

                    $ruleLength = $this->ruleToLength[$rule];
                    try {
                        $callback = $this->reduceCallbacks[$rule];
                        if ($callback !== null) {
                            $callback($this, $stackPos);
                        } elseif ($ruleLength > 0) {
                            $this->semValue = $this->semStack[$stackPos - $ruleLength + 1];
                        }
                    } catch (Error $e) {
                        if (-1 === $e->getStartLine()) {
                            $e->setStartLine($this->tokens[$this->tokenPos]->line);
                        }

                        $this->emitError($e);
                        // Can't recover from this type of error
                        return null;
                    }

                    /* Goto - shift nonterminal */
                    $lastTokenEnd = $this->tokenEndStack[$stackPos];
                    $stackPos -= $ruleLength;
                    $nonTerminal = $this->ruleToNonTerminal[$rule];
                    $idx = $this->gotoBase[$nonTerminal] + $stateStack[$stackPos];
                    if ($idx >= 0 && $idx < $this->gotoTableSize && $this->gotoCheck[$idx] === $nonTerminal) {
                        $state = $this->goto[$idx];
                    } else {
                        $state = $this->gotoDefault[$nonTerminal];
                    }

                    ++$stackPos;
                    $stateStack[$stackPos]     = $state;
                    $this->semStack[$stackPos] = $this->semValue;
                    $this->tokenEndStack[$stackPos] = $lastTokenEnd;
                    if ($ruleLength === 0) {
                        // Empty productions use the start attributes of the lookahead token.
                        $this->tokenStartStack[$stackPos] = $this->tokenPos;
                    }
                } else {
                    /* error */
                    switch ($this->errorState) {
                        case 0:
                            $msg = $this->getErrorMessage($symbol, $state);
                            $this->emitError(new Error($msg, $this->getAttributesForToken($this->tokenPos)));
                            // Break missing intentionally
                            // no break
                        case 1:
                        case 2:
                            $this->errorState = 3;

                            // Pop until error-expecting state uncovered
                            while (!(
                                (($idx = $this->actionBase[$state] + $this->errorSymbol) >= 0
                                    && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
                                || ($state < $this->YY2TBLSTATE
                                    && ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $this->errorSymbol) >= 0
                                    && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $this->errorSymbol)
                            ) || ($action = $this->action[$idx]) === $this->defaultAction) { // Not totally sure about this
                                if ($stackPos <= 0) {
                                    // Could not recover from error
                                    return null;
                                }
                                $state = $stateStack[--$stackPos];
                                //$this->tracePop($state);
                            }

                            //$this->traceShift($this->errorSymbol);
                            ++$stackPos;
                            $stateStack[$stackPos] = $state = $action;

                            // We treat the error symbol as being empty, so we reset the end attributes
                            // to the end attributes of the last non-error symbol
                            $this->tokenStartStack[$stackPos] = $this->tokenPos;
                            $this->tokenEndStack[$stackPos] = $this->tokenEndStack[$stackPos - 1];
                            break;

                        case 3:
                            if ($symbol === 0) {
                                // Reached EOF without recovering from error
                                return null;
                            }

                            //$this->traceDiscard($symbol);
                            $symbol = self::SYMBOL_NONE;
                            break 2;
                    }
                }

                if ($state < $this->numNonLeafStates) {
                    break;
                }

                /* >= numNonLeafStates means shift-and-reduce */
                $rule = $state - $this->numNonLeafStates;
            }
        }
    }

    protected function emitError(Error $error): void {
        $this->errorHandler->handleError($error);
    }

    /**
     * Format error message including expected tokens.
     *
     * @param int $symbol Unexpected symbol
     * @param int $state State at time of error
     *
     * @return string Formatted error message
     */
    protected function getErrorMessage(int $symbol, int $state): string {
        $expectedString = '';
        if ($expected = $this->getExpectedTokens($state)) {
            $expectedString = ', expecting ' . implode(' or ', $expected);
        }

        return 'Syntax error, unexpected ' . $this->symbolToName[$symbol] . $expectedString;
    }

    /**
     * Get limited number of expected tokens in given state.
     *
     * @param int $state State
     *
     * @return string[] Expected tokens. If too many, an empty array is returned.
     */
    protected function getExpectedTokens(int $state): array {
        $expected = [];

        $base = $this->actionBase[$state];
        foreach ($this->symbolToName as $symbol => $name) {
            $idx = $base + $symbol;
            if ($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
                || $state < $this->YY2TBLSTATE
                && ($idx = $this->actionBase[$state + $this->numNonLeafStates] + $symbol) >= 0
                && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
            ) {
                if ($this->action[$idx] !== $this->unexpectedTokenRule
                    && $this->action[$idx] !== $this->defaultAction
                    && $symbol !== $this->errorSymbol
                ) {
                    if (count($expected) === 4) {
                        /* Too many expected tokens */
                        return [];
                    }

                    $expected[] = $name;
                }
            }
        }

        return $expected;
    }

    /**
     * Get attributes for a node with the given start and end token positions.
     *
     * @param int $tokenStartPos Token position the node starts at
     * @param int $tokenEndPos Token position the node ends at
     * @return array<string, mixed> Attributes
     */
    protected function getAttributes(int $tokenStartPos, int $tokenEndPos): array {
        $startToken = $this->tokens[$tokenStartPos];
        $afterEndToken = $this->tokens[$tokenEndPos + 1];
        return [
            'startLine' => $startToken->line,
            'startTokenPos' => $tokenStartPos,
            'startFilePos' => $startToken->pos,
            'endLine' => $afterEndToken->line,
            'endTokenPos' => $tokenEndPos,
            'endFilePos' => $afterEndToken->pos - 1,
        ];
    }

    /**
     * Get attributes for a single token at the given token position.
     *
     * @return array<string, mixed> Attributes
     */
    protected function getAttributesForToken(int $tokenPos): array {
        if ($tokenPos < \count($this->tokens) - 1) {
            return $this->getAttributes($tokenPos, $tokenPos);
        }

        // Get attributes for the sentinel token.
        $token = $this->tokens[$tokenPos];
        return [
            'startLine' => $token->line,
            'startTokenPos' => $tokenPos,
            'startFilePos' => $token->pos,
            'endLine' => $token->line,
            'endTokenPos' => $tokenPos,
            'endFilePos' => $token->pos,
        ];
    }

    /*
     * Tracing functions used for debugging the parser.
     */

    /*
    protected function traceNewState($state, $symbol): void {
        echo '% State ' . $state
            . ', Lookahead ' . ($symbol == self::SYMBOL_NONE ? '--none--' : $this->symbolToName[$symbol]) . "\n";
    }

    protected function traceRead($symbol): void {
        echo '% Reading ' . $this->symbolToName[$symbol] . "\n";
    }

    protected function traceShift($symbol): void {
        echo '% Shift ' . $this->symbolToName[$symbol] . "\n";
    }

    protected function traceAccept(): void {
        echo "% Accepted.\n";
    }

    protected function traceReduce($n): void {
        echo '% Reduce by (' . $n . ') ' . $this->productions[$n] . "\n";
    }

    protected function tracePop($state): void {
        echo '% Recovering, uncovered state ' . $state . "\n";
    }

    protected function traceDiscard($symbol): void {
        echo '% Discard ' . $this->symbolToName[$symbol] . "\n";
    }
    */

    /*
     * Helper functions invoked by semantic actions
     */

    /**
     * Moves statements of semicolon-style namespaces into $ns->stmts and checks various error conditions.
     *
     * @param Node\Stmt[] $stmts
     * @return Node\Stmt[]
     */
    protected function handleNamespaces(array $stmts): array {
        $hasErrored = false;
        $style = $this->getNamespacingStyle($stmts);
        if (null === $style) {
            // not namespaced, nothing to do
            return $stmts;
        }
        if ('brace' === $style) {
            // For braced namespaces we only have to check that there are no invalid statements between the namespaces
            $afterFirstNamespace = false;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Namespace_) {
                    $afterFirstNamespace = true;
                } elseif (!$stmt instanceof Node\Stmt\HaltCompiler
                        && !$stmt instanceof Node\Stmt\Nop
                        && $afterFirstNamespace && !$hasErrored) {
                    $this->emitError(new Error(
                        'No code may exist outside of namespace {}', $stmt->getAttributes()));
                    $hasErrored = true; // Avoid one error for every statement
                }
            }
            return $stmts;
        } else {
            // For semicolon namespaces we have to move the statements after a namespace declaration into ->stmts
            $resultStmts = [];
            $targetStmts = &$resultStmts;
            $lastNs = null;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Namespace_) {
                    if ($lastNs !== null) {
                        $this->fixupNamespaceAttributes($lastNs);
                    }
                    if ($stmt->stmts === null) {
                        $stmt->stmts = [];
                        $targetStmts = &$stmt->stmts;
                        $resultStmts[] = $stmt;
                    } else {
                        // This handles the invalid case of mixed style namespaces
                        $resultStmts[] = $stmt;
                        $targetStmts = &$resultStmts;
                    }
                    $lastNs = $stmt;
                } elseif ($stmt instanceof Node\Stmt\HaltCompiler) {
                    // __halt_compiler() is not moved into the namespace
                    $resultStmts[] = $stmt;
                } else {
                    $targetStmts[] = $stmt;
                }
            }
            if ($lastNs !== null) {
                $this->fixupNamespaceAttributes($lastNs);
            }
            return $resultStmts;
        }
    }

    private function fixupNamespaceAttributes(Node\Stmt\Namespace_ $stmt): void {
        // We moved the statements into the namespace node, as such the end of the namespace node
        // needs to be extended to the end of the statements.
        if (empty($stmt->stmts)) {
            return;
        }

        // We only move the builtin end attributes here. This is the best we can do with the
        // knowledge we have.
        $endAttributes = ['endLine', 'endFilePos', 'endTokenPos'];
        $lastStmt = $stmt->stmts[count($stmt->stmts) - 1];
        foreach ($endAttributes as $endAttribute) {
            if ($lastStmt->hasAttribute($endAttribute)) {
                $stmt->setAttribute($endAttribute, $lastStmt->getAttribute($endAttribute));
            }
        }
    }

    /** @return array<string, mixed> */
    private function getNamespaceErrorAttributes(Namespace_ $node): array {
        $attrs = $node->getAttributes();
        // Adjust end attributes to only cover the "namespace" keyword, not the whole namespace.
        if (isset($attrs['startLine'])) {
            $attrs['endLine'] = $attrs['startLine'];
        }
        if (isset($attrs['startTokenPos'])) {
            $attrs['endTokenPos'] = $attrs['startTokenPos'];
        }
        if (isset($attrs['startFilePos'])) {
            $attrs['endFilePos'] = $attrs['startFilePos'] + \strlen('namespace') - 1;
        }
        return $attrs;
    }

    /**
     * Determine namespacing style (semicolon or brace)
     *
     * @param Node[] $stmts Top-level statements.
     *
     * @return null|string One of "semicolon", "brace" or null (no namespaces)
     */
    private function getNamespacingStyle(array $stmts): ?string {
        $style = null;
        $hasNotAllowedStmts = false;
        foreach ($stmts as $i => $stmt) {
            if ($stmt instanceof Node\Stmt\Namespace_) {
                $currentStyle = null === $stmt->stmts ? 'semicolon' : 'brace';
                if (null === $style) {
                    $style = $currentStyle;
                    if ($hasNotAllowedStmts) {
                        $this->emitError(new Error(
                            'Namespace declaration statement has to be the very first statement in the script',
                            $this->getNamespaceErrorAttributes($stmt)
                        ));
                    }
                } elseif ($style !== $currentStyle) {
                    $this->emitError(new Error(
                        'Cannot mix bracketed namespace declarations with unbracketed namespace declarations',
                        $this->getNamespaceErrorAttributes($stmt)
                    ));
                    // Treat like semicolon style for namespace normalization
                    return 'semicolon';
                }
                continue;
            }

            /* declare(), __halt_compiler() and nops can be used before a namespace declaration */
            if ($stmt instanceof Node\Stmt\Declare_
                || $stmt instanceof Node\Stmt\HaltCompiler
                || $stmt instanceof Node\Stmt\Nop) {
                continue;
            }

            /* There may be a hashbang line at the very start of the file */
            if ($i === 0 && $stmt instanceof Node\Stmt\InlineHTML && preg_match('/\A#!.*\r?\n\z/', $stmt->value)) {
                continue;
            }

            /* Everything else if forbidden before namespace declarations */
            $hasNotAllowedStmts = true;
        }
        return $style;
    }

    /** @return Name|Identifier */
    protected function handleBuiltinTypes(Name $name) {
        if (!$name->isUnqualified()) {
            return $name;
        }

        $lowerName = $name->toLowerString();
        if (!$this->phpVersion->supportsBuiltinType($lowerName)) {
            return $name;
        }

        return new Node\Identifier($lowerName, $name->getAttributes());
    }

    /**
     * Get combined start and end attributes at a stack location
     *
     * @param int $stackPos Stack location
     *
     * @return array<string, mixed> Combined start and end attributes
     */
    protected function getAttributesAt(int $stackPos): array {
        return $this->getAttributes($this->tokenStartStack[$stackPos], $this->tokenEndStack[$stackPos]);
    }

    protected function getFloatCastKind(string $cast): int {
        $cast = strtolower($cast);
        if (strpos($cast, 'float') !== false) {
            return Double::KIND_FLOAT;
        }

        if (strpos($cast, 'real') !== false) {
            return Double::KIND_REAL;
        }

        return Double::KIND_DOUBLE;
    }

    protected function getIntCastKind(string $cast): int {
        $cast = strtolower($cast);
        if (strpos($cast, 'integer') !== false) {
            return Expr\Cast\Int_::KIND_INTEGER;
        }

        return Expr\Cast\Int_::KIND_INT;
    }

    protected function getBoolCastKind(string $cast): int {
        $cast = strtolower($cast);
        if (strpos($cast, 'boolean') !== false) {
            return Expr\Cast\Bool_::KIND_BOOLEAN;
        }

        return Expr\Cast\Bool_::KIND_BOOL;
    }

    protected function getStringCastKind(string $cast): int {
        $cast = strtolower($cast);
        if (strpos($cast, 'binary') !== false) {
            return Expr\Cast\String_::KIND_BINARY;
        }

        return Expr\Cast\String_::KIND_STRING;
    }

    /** @param array<string, mixed> $attributes */
    protected function parseLNumber(string $str, array $attributes, bool $allowInvalidOctal = false): Int_ {
        try {
            return Int_::fromString($str, $attributes, $allowInvalidOctal);
        } catch (Error $error) {
            $this->emitError($error);
            // Use dummy value
            return new Int_(0, $attributes);
        }
    }

    /**
     * Parse a T_NUM_STRING token into either an integer or string node.
     *
     * @param string $str Number string
     * @param array<string, mixed> $attributes Attributes
     *
     * @return Int_|String_ Integer or string node.
     */
    protected function parseNumString(string $str, array $attributes) {
        if (!preg_match('/^(?:0|-?[1-9][0-9]*)$/', $str)) {
            return new String_($str, $attributes);
        }

        $num = +$str;
        if (!is_int($num)) {
            return new String_($str, $attributes);
        }

        return new Int_($num, $attributes);
    }

    /** @param array<string, mixed> $attributes */
    protected function stripIndentation(
        string $string, int $indentLen, string $indentChar,
        bool $newlineAtStart, bool $newlineAtEnd, array $attributes
    ): string {
        if ($indentLen === 0) {
            return $string;
        }

        $start = $newlineAtStart ? '(?:(?<=\n)|\A)' : '(?<=\n)';
        $end = $newlineAtEnd ? '(?:(?=[\r\n])|\z)' : '(?=[\r\n])';
        $regex = '/' . $start . '([ \t]*)(' . $end . ')?/';
        return preg_replace_callback(
            $regex,
            function ($matches) use ($indentLen, $indentChar, $attributes) {
                $prefix = substr($matches[1], 0, $indentLen);
                if (false !== strpos($prefix, $indentChar === " " ? "\t" : " ")) {
                    $this->emitError(new Error(
                        'Invalid indentation - tabs and spaces cannot be mixed', $attributes
                    ));
                } elseif (strlen($prefix) < $indentLen && !isset($matches[2])) {
                    $this->emitError(new Error(
                        'Invalid body indentation level ' .
                        '(expecting an indentation level of at least ' . $indentLen . ')',
                        $attributes
                    ));
                }
                return substr($matches[0], strlen($prefix));
            },
            $string
        );
    }

    /**
     * @param string|(Expr|InterpolatedStringPart)[] $contents
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $endTokenAttributes
     */
    protected function parseDocString(
        string $startToken, $contents, string $endToken,
        array $attributes, array $endTokenAttributes, bool $parseUnicodeEscape
    ): Expr {
        $kind = strpos($startToken, "'") === false
            ? String_::KIND_HEREDOC : String_::KIND_NOWDOC;

        $regex = '/\A[bB]?<<<[ \t]*[\'"]?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[\'"]?(?:\r\n|\n|\r)\z/';
        $result = preg_match($regex, $startToken, $matches);
        assert($result === 1);
        $label = $matches[1];

        $result = preg_match('/\A[ \t]*/', $endToken, $matches);
        assert($result === 1);
        $indentation = $matches[0];

        $attributes['kind'] = $kind;
        $attributes['docLabel'] = $label;
        $attributes['docIndentation'] = $indentation;

        $indentHasSpaces = false !== strpos($indentation, " ");
        $indentHasTabs = false !== strpos($indentation, "\t");
        if ($indentHasSpaces && $indentHasTabs) {
            $this->emitError(new Error(
                'Invalid indentation - tabs and spaces cannot be mixed',
                $endTokenAttributes
            ));

            // Proceed processing as if this doc string is not indented
            $indentation = '';
        }

        $indentLen = \strlen($indentation);
        $indentChar = $indentHasSpaces ? " " : "\t";

        if (\is_string($contents)) {
            if ($contents === '') {
                $attributes['rawValue'] = $contents;
                return new String_('', $attributes);
            }

            $contents = $this->stripIndentation(
                $contents, $indentLen, $indentChar, true, true, $attributes
            );
            $contents = preg_replace('~(\r\n|\n|\r)\z~', '', $contents);
            $attributes['rawValue'] = $contents;

            if ($kind === String_::KIND_HEREDOC) {
                $contents = String_::parseEscapeSequences($contents, null, $parseUnicodeEscape);
            }

            return new String_($contents, $attributes);
        } else {
            assert(count($contents) > 0);
            if (!$contents[0] instanceof Node\InterpolatedStringPart) {
                // If there is no leading encapsed string part, pretend there is an empty one
                $this->stripIndentation(
                    '', $indentLen, $indentChar, true, false, $contents[0]->getAttributes()
                );
            }

            $newContents = [];
            foreach ($contents as $i => $part) {
                if ($part instanceof Node\InterpolatedStringPart) {
                    $isLast = $i === \count($contents) - 1;
                    $part->value = $this->stripIndentation(
                        $part->value, $indentLen, $indentChar,
                        $i === 0, $isLast, $part->getAttributes()
                    );
                    if ($isLast) {
                        $part->value = preg_replace('~(\r\n|\n|\r)\z~', '', $part->value);
                    }
                    $part->setAttribute('rawValue', $part->value);
                    $part->value = String_::parseEscapeSequences($part->value, null, $parseUnicodeEscape);
                    if ('' === $part->value) {
                        continue;
                    }
                }
                $newContents[] = $part;
            }
            return new InterpolatedString($newContents, $attributes);
        }
    }

    protected function createCommentFromToken(Token $token, int $tokenPos): Comment {
        assert($token->id === \T_COMMENT || $token->id == \T_DOC_COMMENT);
        return \T_DOC_COMMENT === $token->id
            ? new Comment\Doc($token->text, $token->line, $token->pos, $tokenPos,
                $token->getEndLine(), $token->getEndPos() - 1, $tokenPos)
            : new Comment($token->text, $token->line, $token->pos, $tokenPos,
                $token->getEndLine(), $token->getEndPos() - 1, $tokenPos);
    }

    /**
     * Get last comment before the given token position, if any
     */
    protected function getCommentBeforeToken(int $tokenPos): ?Comment {
        while (--$tokenPos >= 0) {
            $token = $this->tokens[$tokenPos];
            if (!isset($this->dropTokens[$token->id])) {
                break;
            }

            if ($token->id === \T_COMMENT || $token->id === \T_DOC_COMMENT) {
                return $this->createCommentFromToken($token, $tokenPos);
            }
        }
        return null;
    }

    /**
     * Create a zero-length nop to capture preceding comments, if any.
     */
    protected function maybeCreateZeroLengthNop(int $tokenPos): ?Nop {
        $comment = $this->getCommentBeforeToken($tokenPos);
        if ($comment === null) {
            return null;
        }

        $commentEndLine = $comment->getEndLine();
        $commentEndFilePos = $comment->getEndFilePos();
        $commentEndTokenPos = $comment->getEndTokenPos();
        $attributes = [
            'startLine' => $commentEndLine,
            'endLine' => $commentEndLine,
            'startFilePos' => $commentEndFilePos + 1,
            'endFilePos' => $commentEndFilePos,
            'startTokenPos' => $commentEndTokenPos + 1,
            'endTokenPos' => $commentEndTokenPos,
        ];
        return new Nop($attributes);
    }

    protected function maybeCreateNop(int $tokenStartPos, int $tokenEndPos): ?Nop {
        if ($this->getCommentBeforeToken($tokenStartPos) === null) {
            return null;
        }
        return new Nop($this->getAttributes($tokenStartPos, $tokenEndPos));
    }

    protected function handleHaltCompiler(): string {
        // Prevent the lexer from returning any further tokens.
        $nextToken = $this->tokens[$this->tokenPos + 1];
        $this->tokenPos = \count($this->tokens) - 2;

        // Return text after __halt_compiler.
        return $nextToken->id === \T_INLINE_HTML ? $nextToken->text : '';
    }

    protected function inlineHtmlHasLeadingNewline(int $stackPos): bool {
        $tokenPos = $this->tokenStartStack[$stackPos];
        $token = $this->tokens[$tokenPos];
        assert($token->id == \T_INLINE_HTML);
        if ($tokenPos > 0) {
            $prevToken = $this->tokens[$tokenPos - 1];
            assert($prevToken->id == \T_CLOSE_TAG);
            return false !== strpos($prevToken->text, "\n")
                || false !== strpos($prevToken->text, "\r");
        }
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function createEmptyElemAttributes(int $tokenPos): array {
        return $this->getAttributesForToken($tokenPos);
    }

    protected function fixupArrayDestructuring(Array_ $node): Expr\List_ {
        $this->createdArrays->offsetUnset($node);
        return new Expr\List_(array_map(function (Node\ArrayItem $item) {
            if ($item->value instanceof Expr\Error) {
                // We used Error as a placeholder for empty elements, which are legal for destructuring.
                return null;
            }
            if ($item->value instanceof Array_) {
                return new Node\ArrayItem(
                    $this->fixupArrayDestructuring($item->value),
                    $item->key, $item->byRef, $item->getAttributes());
            }
            return $item;
        }, $node->items), ['kind' => Expr\List_::KIND_ARRAY] + $node->getAttributes());
    }

    protected function postprocessList(Expr\List_ $node): void {
        foreach ($node->items as $i => $item) {
            if ($item->value instanceof Expr\Error) {
                // We used Error as a placeholder for empty elements, which are legal for destructuring.
                $node->items[$i] = null;
            }
        }
    }

    /** @param ElseIf_|Else_ $node */
    protected function fixupAlternativeElse($node): void {
        // Make sure a trailing nop statement carrying comments is part of the node.
        $numStmts = \count($node->stmts);
        if ($numStmts !== 0 && $node->stmts[$numStmts - 1] instanceof Nop) {
            $nopAttrs = $node->stmts[$numStmts - 1]->getAttributes();
            if (isset($nopAttrs['endLine'])) {
                $node->setAttribute('endLine', $nopAttrs['endLine']);
            }
            if (isset($nopAttrs['endFilePos'])) {
                $node->setAttribute('endFilePos', $nopAttrs['endFilePos']);
            }
            if (isset($nopAttrs['endTokenPos'])) {
                $node->setAttribute('endTokenPos', $nopAttrs['endTokenPos']);
            }
        }
    }

    protected function checkClassModifier(int $a, int $b, int $modifierPos): void {
        try {
            Modifiers::verifyClassModifier($a, $b);
        } catch (Error $error) {
            $error->setAttributes($this->getAttributesAt($modifierPos));
            $this->emitError($error);
        }
    }

    protected function checkModifier(int $a, int $b, int $modifierPos): void {
        // Jumping through some hoops here because verifyModifier() is also used elsewhere
        try {
            Modifiers::verifyModifier($a, $b);
        } catch (Error $error) {
            $error->setAttributes($this->getAttributesAt($modifierPos));
            $this->emitError($error);
        }
    }

    protected function checkParam(Param $node): void {
        if ($node->variadic && null !== $node->default) {
            $this->emitError(new Error(
                'Variadic parameter cannot have a default value',
                $node->default->getAttributes()
            ));
        }

        if ($node->type instanceof Identifier && $node->type->name === 'void') {
            $this->emitError(new Error(
                'void cannot be used as a parameter type',
                $node->type->getAttributes()
            ));
        }
    }

    protected function checkTryCatch(TryCatch $node): void {
        if (empty($node->catches) && null === $node->finally) {
            $this->emitError(new Error(
                'Cannot use try without catch or finally', $node->getAttributes()
            ));
        }
    }

    protected function checkNamespace(Namespace_ $node): void {
        if (null !== $node->stmts) {
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Namespace_) {
                    $this->emitError(new Error(
                        'Namespace declarations cannot be nested', $stmt->getAttributes()
                    ));
                }
            }
        }
    }

    private function checkClassName(?Identifier $name, int $namePos): void {
        if (null !== $name && $name->isSpecialClassName()) {
            $this->emitError(new Error(
                sprintf('Cannot use \'%s\' as class name as it is reserved', $name),
                $this->getAttributesAt($namePos)
            ));
        }
    }

    /** @param Name[] $interfaces */
    private function checkImplementedInterfaces(array $interfaces): void {
        foreach ($interfaces as $interface) {
            if ($interface->isSpecialClassName()) {
                $this->emitError(new Error(
                    sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface),
                    $interface->getAttributes()
                ));
            }
        }
    }

    protected function checkClass(Class_ $node, int $namePos): void {
        $this->checkClassName($node->name, $namePos);

        if ($node->extends && $node->extends->isSpecialClassName()) {
            $this->emitError(new Error(
                sprintf('Cannot use \'%s\' as class name as it is reserved', $node->extends),
                $node->extends->getAttributes()
            ));
        }

        $this->checkImplementedInterfaces($node->implements);
    }

    protected function checkInterface(Interface_ $node, int $namePos): void {
        $this->checkClassName($node->name, $namePos);
        $this->checkImplementedInterfaces($node->extends);
    }

    protected function checkEnum(Enum_ $node, int $namePos): void {
        $this->checkClassName($node->name, $namePos);
        $this->checkImplementedInterfaces($node->implements);
    }

    protected function checkClassMethod(ClassMethod $node, int $modifierPos): void {
        if ($node->flags & Modifiers::STATIC) {
            switch ($node->name->toLowerString()) {
                case '__construct':
                    $this->emitError(new Error(
                        sprintf('Constructor %s() cannot be static', $node->name),
                        $this->getAttributesAt($modifierPos)));
                    break;
                case '__destruct':
                    $this->emitError(new Error(
                        sprintf('Destructor %s() cannot be static', $node->name),
                        $this->getAttributesAt($modifierPos)));
                    break;
                case '__clone':
                    $this->emitError(new Error(
                        sprintf('Clone method %s() cannot be static', $node->name),
                        $this->getAttributesAt($modifierPos)));
                    break;
            }
        }

        if ($node->flags & Modifiers::READONLY) {
            $this->emitError(new Error(
                sprintf('Method %s() cannot be readonly', $node->name),
                $this->getAttributesAt($modifierPos)));
        }
    }

    protected function checkClassConst(ClassConst $node, int $modifierPos): void {
        foreach ([Modifiers::STATIC, Modifiers::ABSTRACT, Modifiers::READONLY] as $modifier) {
            if ($node->flags & $modifier) {
                $this->emitError(new Error(
                    "Cannot use '" . Modifiers::toString($modifier) . "' as constant modifier",
                    $this->getAttributesAt($modifierPos)));
            }
        }
    }

    protected function checkUseUse(UseItem $node, int $namePos): void {
        if ($node->alias && $node->alias->isSpecialClassName()) {
            $this->emitError(new Error(
                sprintf(
                    'Cannot use %s as %s because \'%2$s\' is a special class name',
                    $node->name, $node->alias
                ),
                $this->getAttributesAt($namePos)
            ));
        }
    }

    protected function checkPropertyHooksForMultiProperty(Property $property, int $hookPos): void {
        if (count($property->props) > 1) {
            $this->emitError(new Error(
                'Cannot use hooks when declaring multiple properties', $this->getAttributesAt($hookPos)));
        }
    }

    /** @param PropertyHook[] $hooks */
    protected function checkEmptyPropertyHookList(array $hooks, int $hookPos): void {
        if (empty($hooks)) {
            $this->emitError(new Error(
                'Property hook list cannot be empty', $this->getAttributesAt($hookPos)));
        }
    }

    protected function checkPropertyHook(PropertyHook $hook, ?int $paramListPos): void {
        $name = $hook->name->toLowerString();
        if ($name !== 'get' && $name !== 'set') {
            $this->emitError(new Error(
                'Unknown hook "' . $hook->name . '", expected "get" or "set"',
                $hook->name->getAttributes()));
        }
        if ($name === 'get' && $paramListPos !== null) {
            $this->emitError(new Error(
                'get hook must not have a parameter list', $this->getAttributesAt($paramListPos)));
        }
    }

    protected function checkPropertyHookModifiers(int $a, int $b, int $modifierPos): void {
        try {
            Modifiers::verifyModifier($a, $b);
        } catch (Error $error) {
            $error->setAttributes($this->getAttributesAt($modifierPos));
            $this->emitError($error);
        }

        if ($b != Modifiers::FINAL) {
            $this->emitError(new Error(
                'Cannot use the ' . Modifiers::toString($b) . ' modifier on a property hook',
                $this->getAttributesAt($modifierPos)));
        }
    }

    protected function checkConstantAttributes(Const_ $node): void {
        if ($node->attrGroups !== [] && count($node->consts) > 1) {
            $this->emitError(new Error(
                'Cannot use attributes on multiple constants at once', $node->getAttributes()));
        }
    }

    protected function checkPipeOperatorParentheses(Expr $node): void {
        if ($node instanceof Expr\ArrowFunction && !$this->parenthesizedArrowFunctions->offsetExists($node)) {
            $this->emitError(new Error(
                'Arrow functions on the right hand side of |> must be parenthesized', $node->getAttributes()));
        }
    }

    /**
     * @param Property|Param $node
     */
    protected function addPropertyNameToHooks(Node $node): void {
        if ($node instanceof Property) {
            $name = $node->props[0]->name->toString();
        } else {
            $name = $node->var->name;
        }
        foreach ($node->hooks as $hook) {
            $hook->setAttribute('propertyName', $name);
        }
    }

    /** @param array<Node\Arg|Node\VariadicPlaceholder> $args */
    private function isSimpleExit(array $args): bool {
        if (\count($args) === 0) {
            return true;
        }
        if (\count($args) === 1) {
            $arg = $args[0];
            return $arg instanceof Arg && $arg->name === null &&
                   $arg->byRef === false && $arg->unpack === false;
        }
        return false;
    }

    /**
     * @param array<Node\Arg|Node\VariadicPlaceholder> $args
     * @param array<string, mixed> $attrs
     */
    protected function createExitExpr(string $name, int $namePos, array $args, array $attrs): Expr {
        if ($this->isSimpleExit($args)) {
            // Create Exit node for backwards compatibility.
            $attrs['kind'] = strtolower($name) === 'exit' ? Expr\Exit_::KIND_EXIT : Expr\Exit_::KIND_DIE;
            return new Expr\Exit_(\count($args) === 1 ? $args[0]->value : null, $attrs);
        }
        return new Expr\FuncCall(new Name($name, $this->getAttributesAt($namePos)), $args, $attrs);
    }

    /**
     * Creates the token map.
     *
     * The token map maps the PHP internal token identifiers
     * to the identifiers used by the Parser. Additionally it
     * maps T_OPEN_TAG_WITH_ECHO to T_ECHO and T_CLOSE_TAG to ';'.
     *
     * @return array<int, int> The token map
     */
    protected function createTokenMap(): array {
        $tokenMap = [];

        // Single-char tokens use an identity mapping.
        for ($i = 0; $i < 256; ++$i) {
            $tokenMap[$i] = $i;
        }

        foreach ($this->symbolToName as $name) {
            if ($name[0] === 'T') {
                $tokenMap[\constant($name)] = constant(static::class . '::' . $name);
            }
        }

        // T_OPEN_TAG_WITH_ECHO with dropped T_OPEN_TAG results in T_ECHO
        $tokenMap[\T_OPEN_TAG_WITH_ECHO] = static::T_ECHO;
        // T_CLOSE_TAG is equivalent to ';'
        $tokenMap[\T_CLOSE_TAG] = ord(';');

        // We have created a map from PHP token IDs to external symbol IDs.
        // Now map them to the internal symbol ID.
        $fullTokenMap = [];
        foreach ($tokenMap as $phpToken => $extSymbol) {
            $intSymbol = $this->tokenToSymbol[$extSymbol];
            if ($intSymbol === $this->invalidSymbol) {
                continue;
            }
            $fullTokenMap[$phpToken] = $intSymbol;
        }

        return $fullTokenMap;
    }
}
