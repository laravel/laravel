<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Internal\DiffElem;
use PhpParser\Internal\Differ;
use PhpParser\Internal\PrintableNewAnonClassNode;
use PhpParser\Internal\TokenStream;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Param;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\Node\UnionType;

abstract class PrettyPrinterAbstract implements PrettyPrinter {
    protected const FIXUP_PREC_LEFT = 0; // LHS operand affected by precedence
    protected const FIXUP_PREC_RIGHT = 1; // RHS operand affected by precedence
    protected const FIXUP_PREC_UNARY = 2; // Only operand affected by precedence
    protected const FIXUP_CALL_LHS = 3; // LHS of call
    protected const FIXUP_DEREF_LHS = 4; // LHS of dereferencing operation
    protected const FIXUP_STATIC_DEREF_LHS = 5; // LHS of static dereferencing operation
    protected const FIXUP_BRACED_NAME  = 6; // Name operand that may require bracing
    protected const FIXUP_VAR_BRACED_NAME = 7; // Name operand that may require ${} bracing
    protected const FIXUP_ENCAPSED = 8; // Encapsed string part
    protected const FIXUP_NEW = 9; // New/instanceof operand

    protected const MAX_PRECEDENCE = 1000;

    /** @var array<class-string, array{int, int, int}> */
    protected array $precedenceMap = [
        // [precedence, precedenceLHS, precedenceRHS]
        // Where the latter two are the precedences to use for the LHS and RHS of a binary operator,
        // where 1 is added to one of the sides depending on associativity. This information is not
        // used for unary operators and set to -1.
        Expr\Clone_::class             => [-10,   0,   1],
        BinaryOp\Pow::class            => [  0,   0,   1],
        Expr\BitwiseNot::class         => [ 10,  -1,  -1],
        Expr\UnaryPlus::class          => [ 10,  -1,  -1],
        Expr\UnaryMinus::class         => [ 10,  -1,  -1],
        Cast\Int_::class               => [ 10,  -1,  -1],
        Cast\Double::class             => [ 10,  -1,  -1],
        Cast\String_::class            => [ 10,  -1,  -1],
        Cast\Array_::class             => [ 10,  -1,  -1],
        Cast\Object_::class            => [ 10,  -1,  -1],
        Cast\Bool_::class              => [ 10,  -1,  -1],
        Cast\Unset_::class             => [ 10,  -1,  -1],
        Expr\ErrorSuppress::class      => [ 10,  -1,  -1],
        Expr\Instanceof_::class        => [ 20,  -1,  -1],
        Expr\BooleanNot::class         => [ 30,  -1,  -1],
        BinaryOp\Mul::class            => [ 40,  41,  40],
        BinaryOp\Div::class            => [ 40,  41,  40],
        BinaryOp\Mod::class            => [ 40,  41,  40],
        BinaryOp\Plus::class           => [ 50,  51,  50],
        BinaryOp\Minus::class          => [ 50,  51,  50],
        // FIXME: This precedence is incorrect for PHP 8.
        BinaryOp\Concat::class         => [ 50,  51,  50],
        BinaryOp\ShiftLeft::class      => [ 60,  61,  60],
        BinaryOp\ShiftRight::class     => [ 60,  61,  60],
        BinaryOp\Pipe::class           => [ 65,  66,  65],
        BinaryOp\Smaller::class        => [ 70,  70,  70],
        BinaryOp\SmallerOrEqual::class => [ 70,  70,  70],
        BinaryOp\Greater::class        => [ 70,  70,  70],
        BinaryOp\GreaterOrEqual::class => [ 70,  70,  70],
        BinaryOp\Equal::class          => [ 80,  80,  80],
        BinaryOp\NotEqual::class       => [ 80,  80,  80],
        BinaryOp\Identical::class      => [ 80,  80,  80],
        BinaryOp\NotIdentical::class   => [ 80,  80,  80],
        BinaryOp\Spaceship::class      => [ 80,  80,  80],
        BinaryOp\BitwiseAnd::class     => [ 90,  91,  90],
        BinaryOp\BitwiseXor::class     => [100, 101, 100],
        BinaryOp\BitwiseOr::class      => [110, 111, 110],
        BinaryOp\BooleanAnd::class     => [120, 121, 120],
        BinaryOp\BooleanOr::class      => [130, 131, 130],
        BinaryOp\Coalesce::class       => [140, 140, 141],
        Expr\Ternary::class            => [150, 150, 150],
        Expr\Assign::class             => [160,  -1,  -1],
        Expr\AssignRef::class          => [160,  -1,  -1],
        AssignOp\Plus::class           => [160,  -1,  -1],
        AssignOp\Minus::class          => [160,  -1,  -1],
        AssignOp\Mul::class            => [160,  -1,  -1],
        AssignOp\Div::class            => [160,  -1,  -1],
        AssignOp\Concat::class         => [160,  -1,  -1],
        AssignOp\Mod::class            => [160,  -1,  -1],
        AssignOp\BitwiseAnd::class     => [160,  -1,  -1],
        AssignOp\BitwiseOr::class      => [160,  -1,  -1],
        AssignOp\BitwiseXor::class     => [160,  -1,  -1],
        AssignOp\ShiftLeft::class      => [160,  -1,  -1],
        AssignOp\ShiftRight::class     => [160,  -1,  -1],
        AssignOp\Pow::class            => [160,  -1,  -1],
        AssignOp\Coalesce::class       => [160,  -1,  -1],
        Expr\YieldFrom::class          => [170,  -1,  -1],
        Expr\Yield_::class             => [175,  -1,  -1],
        Expr\Print_::class             => [180,  -1,  -1],
        BinaryOp\LogicalAnd::class     => [190, 191, 190],
        BinaryOp\LogicalXor::class     => [200, 201, 200],
        BinaryOp\LogicalOr::class      => [210, 211, 210],
        Expr\Include_::class           => [220,  -1,  -1],
        Expr\ArrowFunction::class      => [230,  -1,  -1],
        Expr\Throw_::class             => [240,  -1,  -1],
        Expr\Cast\Void_::class         => [250,  -1,  -1],
    ];

    /** @var int Current indentation level. */
    protected int $indentLevel;
    /** @var string String for single level of indentation */
    private string $indent;
    /** @var int Width in spaces to indent by. */
    private int $indentWidth;
    /** @var bool Whether to use tab indentation. */
    private bool $useTabs;
    /** @var int Width in spaces of one tab. */
    private int $tabWidth = 4;

    /** @var string Newline style. Does not include current indentation. */
    protected string $newline;
    /** @var string Newline including current indentation. */
    protected string $nl;
    /** @var string|null Token placed at end of doc string to ensure it is followed by a newline.
     *                   Null if flexible doc strings are used. */
    protected ?string $docStringEndToken;
    /** @var bool Whether semicolon namespaces can be used (i.e. no global namespace is used) */
    protected bool $canUseSemicolonNamespaces;
    /** @var bool Whether to use short array syntax if the node specifies no preference */
    protected bool $shortArraySyntax;
    /** @var PhpVersion PHP version to target */
    protected PhpVersion $phpVersion;

    /** @var TokenStream|null Original tokens for use in format-preserving pretty print */
    protected ?TokenStream $origTokens;
    /** @var Internal\Differ<Node> Differ for node lists */
    protected Differ $nodeListDiffer;
    /** @var array<string, bool> Map determining whether a certain character is a label character */
    protected array $labelCharMap;
    /**
     * @var array<string, array<string, int>> Map from token classes and subnode names to FIXUP_* constants.
     *                                        This is used during format-preserving prints to place additional parens/braces if necessary.
     */
    protected array $fixupMap;
    /**
     * @var array<string, array{left?: int|string, right?: int|string}> Map from "{$node->getType()}->{$subNode}"
     *                                                                  to ['left' => $l, 'right' => $r], where $l and $r specify the token type that needs to be stripped
     *                                                                  when removing this node.
     */
    protected array $removalMap;
    /**
     * @var array<string, array{int|string|null, bool, string|null, string|null}> Map from
     *                                                                            "{$node->getType()}->{$subNode}" to [$find, $beforeToken, $extraLeft, $extraRight].
     *                                                                            $find is an optional token after which the insertion occurs. $extraLeft/Right
     *                                                                            are optionally added before/after the main insertions.
     */
    protected array $insertionMap;
    /**
     * @var array<string, string> Map From "{$class}->{$subNode}" to string that should be inserted
     *                            between elements of this list subnode.
     */
    protected array $listInsertionMap;

    /**
     * @var array<string, array{int|string|null, string, string}>
     */
    protected array $emptyListInsertionMap;
    /** @var array<string, array{string, int, int}>
     *       Map from "{$class}->{$subNode}" to [$printFn, $skipToken, $findToken] where $printFn is the function to
     *       print the modifiers, $skipToken is the token to skip at the start and $findToken is the token before which
     *       the modifiers should be reprinted. */
    protected array $modifierChangeMap;

    /**
     * Creates a pretty printer instance using the given options.
     *
     * Supported options:
     *  * PhpVersion $phpVersion: The PHP version to target (default to PHP 7.4). This option
     *                            controls compatibility of the generated code with older PHP
     *                            versions in cases where a simple stylistic choice exists (e.g.
     *                            array() vs []). It is safe to pretty-print an AST for a newer
     *                            PHP version while specifying an older target (but the result will
     *                            of course not be compatible with the older version in that case).
     *  * string $newline:        The newline style to use. Should be "\n" (default) or "\r\n".
     *  * string $indent:         The indentation to use. Should either be all spaces or a single
     *                            tab. Defaults to four spaces ("    ").
     *  * bool $shortArraySyntax: Whether to use [] instead of array() as the default array
     *                            syntax, if the node does not specify a format. Defaults to whether
     *                            the phpVersion support short array syntax.
     *
     * @param array{
     *     phpVersion?: PhpVersion, newline?: string, indent?: string, shortArraySyntax?: bool
     * } $options Dictionary of formatting options
     */
    public function __construct(array $options = []) {
        $this->phpVersion = $options['phpVersion'] ?? PhpVersion::fromComponents(7, 4);

        $this->newline = $options['newline'] ?? "\n";
        if ($this->newline !== "\n" && $this->newline != "\r\n") {
            throw new \LogicException('Option "newline" must be one of "\n" or "\r\n"');
        }

        $this->shortArraySyntax =
            $options['shortArraySyntax'] ?? $this->phpVersion->supportsShortArraySyntax();
        $this->docStringEndToken =
            $this->phpVersion->supportsFlexibleHeredoc() ? null : '_DOC_STRING_END_' . mt_rand();

        $this->indent = $indent = $options['indent'] ?? '    ';
        if ($indent === "\t") {
            $this->useTabs = true;
            $this->indentWidth = $this->tabWidth;
        } elseif ($indent === \str_repeat(' ', \strlen($indent))) {
            $this->useTabs = false;
            $this->indentWidth = \strlen($indent);
        } else {
            throw new \LogicException('Option "indent" must either be all spaces or a single tab');
        }
    }

    /**
     * Reset pretty printing state.
     */
    protected function resetState(): void {
        $this->indentLevel = 0;
        $this->nl = $this->newline;
        $this->origTokens = null;
    }

    /**
     * Set indentation level
     *
     * @param int $level Level in number of spaces
     */
    protected function setIndentLevel(int $level): void {
        $this->indentLevel = $level;
        if ($this->useTabs) {
            $tabs = \intdiv($level, $this->tabWidth);
            $spaces = $level % $this->tabWidth;
            $this->nl = $this->newline . \str_repeat("\t", $tabs) . \str_repeat(' ', $spaces);
        } else {
            $this->nl = $this->newline . \str_repeat(' ', $level);
        }
    }

    /**
     * Increase indentation level.
     */
    protected function indent(): void {
        $this->indentLevel += $this->indentWidth;
        $this->nl .= $this->indent;
    }

    /**
     * Decrease indentation level.
     */
    protected function outdent(): void {
        assert($this->indentLevel >= $this->indentWidth);
        $this->setIndentLevel($this->indentLevel - $this->indentWidth);
    }

    /**
     * Pretty prints an array of statements.
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrint(array $stmts): string {
        $this->resetState();
        $this->preprocessNodes($stmts);

        return ltrim($this->handleMagicTokens($this->pStmts($stmts, false)));
    }

    /**
     * Pretty prints an expression.
     *
     * @param Expr $node Expression node
     *
     * @return string Pretty printed node
     */
    public function prettyPrintExpr(Expr $node): string {
        $this->resetState();
        return $this->handleMagicTokens($this->p($node));
    }

    /**
     * Pretty prints a file of statements (includes the opening <?php tag if it is required).
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrintFile(array $stmts): string {
        if (!$stmts) {
            return "<?php" . $this->newline . $this->newline;
        }

        $p = "<?php" . $this->newline . $this->newline . $this->prettyPrint($stmts);

        if ($stmts[0] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/^<\?php\s+\?>\r?\n?/', '', $p);
        }
        if ($stmts[count($stmts) - 1] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/<\?php$/', '', rtrim($p));
        }

        return $p;
    }

    /**
     * Preprocesses the top-level nodes to initialize pretty printer state.
     *
     * @param Node[] $nodes Array of nodes
     */
    protected function preprocessNodes(array $nodes): void {
        /* We can use semicolon-namespaces unless there is a global namespace declaration */
        $this->canUseSemicolonNamespaces = true;
        foreach ($nodes as $node) {
            if ($node instanceof Stmt\Namespace_ && null === $node->name) {
                $this->canUseSemicolonNamespaces = false;
                break;
            }
        }
    }

    /**
     * Handles (and removes) doc-string-end tokens.
     */
    protected function handleMagicTokens(string $str): string {
        if ($this->docStringEndToken !== null) {
            // Replace doc-string-end tokens with nothing or a newline
            $str = str_replace(
                $this->docStringEndToken . ';' . $this->newline,
                ';' . $this->newline,
                $str);
            $str = str_replace($this->docStringEndToken, $this->newline, $str);
        }

        return $str;
    }

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param Node[] $nodes Array of nodes
     * @param bool $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, bool $indent = true): string {
        if ($indent) {
            $this->indent();
        }

        $result = '';
        foreach ($nodes as $node) {
            $comments = $node->getComments();
            if ($comments) {
                $result .= $this->nl . $this->pComments($comments);
                if ($node instanceof Stmt\Nop) {
                    continue;
                }
            }

            $result .= $this->nl . $this->p($node);
        }

        if ($indent) {
            $this->outdent();
        }

        return $result;
    }

    /**
     * Pretty-print an infix operation while taking precedence into account.
     *
     * @param string $class Node class of operator
     * @param Node $leftNode Left-hand side node
     * @param string $operatorString String representation of the operator
     * @param Node $rightNode Right-hand side node
     * @param int $precedence Precedence of parent operator
     * @param int $lhsPrecedence Precedence for unary operator on LHS of binary operator
     *
     * @return string Pretty printed infix operation
     */
    protected function pInfixOp(
        string $class, Node $leftNode, string $operatorString, Node $rightNode,
        int $precedence, int $lhsPrecedence
    ): string {
        list($opPrecedence, $newPrecedenceLHS, $newPrecedenceRHS) = $this->precedenceMap[$class];
        $prefix = '';
        $suffix = '';
        if ($opPrecedence >= $precedence) {
            $prefix = '(';
            $suffix = ')';
            $lhsPrecedence = self::MAX_PRECEDENCE;
        }
        return $prefix . $this->p($leftNode, $newPrecedenceLHS, $newPrecedenceLHS)
            . $operatorString . $this->p($rightNode, $newPrecedenceRHS, $lhsPrecedence) . $suffix;
    }

    /**
     * Pretty-print a prefix operation while taking precedence into account.
     *
     * @param string $class Node class of operator
     * @param string $operatorString String representation of the operator
     * @param Node $node Node
     * @param int $precedence Precedence of parent operator
     * @param int $lhsPrecedence Precedence for unary operator on LHS of binary operator
     *
     * @return string Pretty printed prefix operation
     */
    protected function pPrefixOp(string $class, string $operatorString, Node $node, int $precedence, int $lhsPrecedence): string {
        $opPrecedence = $this->precedenceMap[$class][0];
        $prefix = '';
        $suffix = '';
        if ($opPrecedence >= $lhsPrecedence) {
            $prefix = '(';
            $suffix = ')';
            $lhsPrecedence = self::MAX_PRECEDENCE;
        }
        $printedArg = $this->p($node, $opPrecedence, $lhsPrecedence);
        if (($operatorString === '+' && $printedArg[0] === '+') ||
            ($operatorString === '-' && $printedArg[0] === '-')
        ) {
            // Avoid printing +(+$a) as ++$a and similar.
            $printedArg = '(' . $printedArg . ')';
        }
        return $prefix . $operatorString . $printedArg . $suffix;
    }

    /**
     * Pretty-print a postfix operation while taking precedence into account.
     *
     * @param string $class Node class of operator
     * @param string $operatorString String representation of the operator
     * @param Node $node Node
     * @param int $precedence Precedence of parent operator
     * @param int $lhsPrecedence Precedence for unary operator on LHS of binary operator
     *
     * @return string Pretty printed postfix operation
     */
    protected function pPostfixOp(string $class, Node $node, string $operatorString, int $precedence, int $lhsPrecedence): string {
        $opPrecedence = $this->precedenceMap[$class][0];
        $prefix = '';
        $suffix = '';
        if ($opPrecedence >= $precedence) {
            $prefix = '(';
            $suffix = ')';
            $lhsPrecedence = self::MAX_PRECEDENCE;
        }
        if ($opPrecedence < $lhsPrecedence) {
            $lhsPrecedence = $opPrecedence;
        }
        return $prefix . $this->p($node, $opPrecedence, $lhsPrecedence) . $operatorString . $suffix;
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @param string $glue Character to implode with
     *
     * @return string Imploded pretty printed nodes> $pre
     */
    protected function pImplode(array $nodes, string $glue = ''): string {
        $pNodes = [];
        foreach ($nodes as $node) {
            if (null === $node) {
                $pNodes[] = '';
            } else {
                $pNodes[] = $this->p($node);
            }
        }

        return implode($glue, $pNodes);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes): string {
        return $this->pImplode($nodes, ', ');
    }

    /**
     * Pretty prints a comma-separated list of nodes in multiline style, including comments.
     *
     * The result includes a leading newline and one level of indentation (same as pStmts).
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @param bool $trailingComma Whether to use a trailing comma
     *
     * @return string Comma separated pretty printed nodes in multiline style
     */
    protected function pCommaSeparatedMultiline(array $nodes, bool $trailingComma): string {
        $this->indent();

        $result = '';
        $lastIdx = count($nodes) - 1;
        foreach ($nodes as $idx => $node) {
            if ($node !== null) {
                $comments = $node->getComments();
                if ($comments) {
                    $result .= $this->nl . $this->pComments($comments);
                }

                $result .= $this->nl . $this->p($node);
            } else {
                $result .= $this->nl;
            }
            if ($trailingComma || $idx !== $lastIdx) {
                $result .= ',';
            }
        }

        $this->outdent();
        return $result;
    }

    /**
     * Prints reformatted text of the passed comments.
     *
     * @param Comment[] $comments List of comments
     *
     * @return string Reformatted text of comments
     */
    protected function pComments(array $comments): string {
        $formattedComments = [];

        foreach ($comments as $comment) {
            $formattedComments[] = str_replace("\n", $this->nl, $comment->getReformattedText());
        }

        return implode($this->nl, $formattedComments);
    }

    /**
     * Perform a format-preserving pretty print of an AST.
     *
     * The format preservation is best effort. For some changes to the AST the formatting will not
     * be preserved (at least not locally).
     *
     * In order to use this method a number of prerequisites must be satisfied:
     *  * The startTokenPos and endTokenPos attributes in the lexer must be enabled.
     *  * The CloningVisitor must be run on the AST prior to modification.
     *  * The original tokens must be provided, using the getTokens() method on the lexer.
     *
     * @param Node[] $stmts Modified AST with links to original AST
     * @param Node[] $origStmts Original AST with token offset information
     * @param Token[] $origTokens Tokens of the original code
     */
    public function printFormatPreserving(array $stmts, array $origStmts, array $origTokens): string {
        $this->initializeNodeListDiffer();
        $this->initializeLabelCharMap();
        $this->initializeFixupMap();
        $this->initializeRemovalMap();
        $this->initializeInsertionMap();
        $this->initializeListInsertionMap();
        $this->initializeEmptyListInsertionMap();
        $this->initializeModifierChangeMap();

        $this->resetState();
        $this->origTokens = new TokenStream($origTokens, $this->tabWidth);

        $this->preprocessNodes($stmts);

        $pos = 0;
        $result = $this->pArray($stmts, $origStmts, $pos, 0, 'File', 'stmts', null);
        if (null !== $result) {
            $result .= $this->origTokens->getTokenCode($pos, count($origTokens) - 1, 0);
        } else {
            // Fallback
            // TODO Add <?php properly
            $result = "<?php" . $this->newline . $this->pStmts($stmts, false);
        }

        return $this->handleMagicTokens($result);
    }

    protected function pFallback(Node $node, int $precedence, int $lhsPrecedence): string {
        return $this->{'p' . $node->getType()}($node, $precedence, $lhsPrecedence);
    }

    /**
     * Pretty prints a node.
     *
     * This method also handles formatting preservation for nodes.
     *
     * @param Node $node Node to be pretty printed
     * @param int $precedence Precedence of parent operator
     * @param int $lhsPrecedence Precedence for unary operator on LHS of binary operator
     * @param bool $parentFormatPreserved Whether parent node has preserved formatting
     *
     * @return string Pretty printed node
     */
    protected function p(
        Node $node, int $precedence = self::MAX_PRECEDENCE, int $lhsPrecedence = self::MAX_PRECEDENCE,
        bool $parentFormatPreserved = false
    ): string {
        // No orig tokens means this is a normal pretty print without preservation of formatting
        if (!$this->origTokens) {
            return $this->{'p' . $node->getType()}($node, $precedence, $lhsPrecedence);
        }

        /** @var Node|null $origNode */
        $origNode = $node->getAttribute('origNode');
        if (null === $origNode) {
            return $this->pFallback($node, $precedence, $lhsPrecedence);
        }

        $class = \get_class($node);
        \assert($class === \get_class($origNode));

        $startPos = $origNode->getStartTokenPos();
        $endPos = $origNode->getEndTokenPos();
        \assert($startPos >= 0 && $endPos >= 0);

        $fallbackNode = $node;
        if ($node instanceof Expr\New_ && $node->class instanceof Stmt\Class_) {
            // Normalize node structure of anonymous classes
            assert($origNode instanceof Expr\New_);
            $node = PrintableNewAnonClassNode::fromNewNode($node);
            $origNode = PrintableNewAnonClassNode::fromNewNode($origNode);
            $class = PrintableNewAnonClassNode::class;
        }

        // InlineHTML node does not contain closing and opening PHP tags. If the parent formatting
        // is not preserved, then we need to use the fallback code to make sure the tags are
        // printed.
        if ($node instanceof Stmt\InlineHTML && !$parentFormatPreserved) {
            return $this->pFallback($fallbackNode, $precedence, $lhsPrecedence);
        }

        $indentAdjustment = $this->indentLevel - $this->origTokens->getIndentationBefore($startPos);

        $type = $node->getType();
        $fixupInfo = $this->fixupMap[$class] ?? null;

        $result = '';
        $pos = $startPos;
        foreach ($node->getSubNodeNames() as $subNodeName) {
            $subNode = $node->$subNodeName;
            $origSubNode = $origNode->$subNodeName;

            if ((!$subNode instanceof Node && $subNode !== null)
                || (!$origSubNode instanceof Node && $origSubNode !== null)
            ) {
                if ($subNode === $origSubNode) {
                    // Unchanged, can reuse old code
                    continue;
                }

                if (is_array($subNode) && is_array($origSubNode)) {
                    // Array subnode changed, we might be able to reconstruct it
                    $listResult = $this->pArray(
                        $subNode, $origSubNode, $pos, $indentAdjustment, $class, $subNodeName,
                        $fixupInfo[$subNodeName] ?? null
                    );
                    if (null === $listResult) {
                        return $this->pFallback($fallbackNode, $precedence, $lhsPrecedence);
                    }

                    $result .= $listResult;
                    continue;
                }

                // Check if this is a modifier change
                $key = $class . '->' . $subNodeName;
                if (!isset($this->modifierChangeMap[$key])) {
                    return $this->pFallback($fallbackNode, $precedence, $lhsPrecedence);
                }

                [$printFn, $skipToken, $findToken] = $this->modifierChangeMap[$key];
                $skipWSPos = $this->origTokens->skipRight($pos, $skipToken);
                $result .= $this->origTokens->getTokenCode($pos, $skipWSPos, $indentAdjustment);
                $result .= $this->$printFn($subNode);
                $pos = $this->origTokens->findRight($skipWSPos, $findToken);
                continue;
            }

            $extraLeft = '';
            $extraRight = '';
            if ($origSubNode !== null) {
                $subStartPos = $origSubNode->getStartTokenPos();
                $subEndPos = $origSubNode->getEndTokenPos();
                \assert($subStartPos >= 0 && $subEndPos >= 0);
            } else {
                if ($subNode === null) {
                    // Both null, nothing to do
                    continue;
                }

                // A node has been inserted, check if we have insertion information for it
                $key = $type . '->' . $subNodeName;
                if (!isset($this->insertionMap[$key])) {
                    return $this->pFallback($fallbackNode, $precedence, $lhsPrecedence);
                }

                list($findToken, $beforeToken, $extraLeft, $extraRight) = $this->insertionMap[$key];
                if (null !== $findToken) {
                    $subStartPos = $this->origTokens->findRight($pos, $findToken)
                        + (int) !$beforeToken;
                } else {
                    $subStartPos = $pos;
                }

                if (null === $extraLeft && null !== $extraRight) {
                    // If inserting on the right only, skipping whitespace looks better
                    $subStartPos = $this->origTokens->skipRightWhitespace($subStartPos);
                }
                $subEndPos = $subStartPos - 1;
            }

            if (null === $subNode) {
                // A node has been removed, check if we have removal information for it
                $key = $type . '->' . $subNodeName;
                if (!isset($this->removalMap[$key])) {
                    return $this->pFallback($fallbackNode, $precedence, $lhsPrecedence);
                }

                // Adjust positions to account for additional tokens that must be skipped
                $removalInfo = $this->removalMap[$key];
                if (isset($removalInfo['left'])) {
                    $subStartPos = $this->origTokens->skipLeft($subStartPos - 1, $removalInfo['left']) + 1;
                }
                if (isset($removalInfo['right'])) {
                    $subEndPos = $this->origTokens->skipRight($subEndPos + 1, $removalInfo['right']) - 1;
                }
            }

            $result .= $this->origTokens->getTokenCode($pos, $subStartPos, $indentAdjustment);

            if (null !== $subNode) {
                $result .= $extraLeft;

                $origIndentLevel = $this->indentLevel;
                $this->setIndentLevel(max($this->origTokens->getIndentationBefore($subStartPos) + $indentAdjustment, 0));

                // If it's the same node that was previously in this position, it certainly doesn't
                // need fixup. It's important to check this here, because our fixup checks are more
                // conservative than strictly necessary.
                if (isset($fixupInfo[$subNodeName])
                    && $subNode->getAttribute('origNode') !== $origSubNode
                ) {
                    $fixup = $fixupInfo[$subNodeName];
                    $res = $this->pFixup($fixup, $subNode, $class, $subStartPos, $subEndPos);
                } else {
                    $res = $this->p($subNode, self::MAX_PRECEDENCE, self::MAX_PRECEDENCE, true);
                }

                $this->safeAppend($result, $res);
                $this->setIndentLevel($origIndentLevel);

                $result .= $extraRight;
            }

            $pos = $subEndPos + 1;
        }

        $result .= $this->origTokens->getTokenCode($pos, $endPos + 1, $indentAdjustment);
        return $result;
    }

    /**
     * Perform a format-preserving pretty print of an array.
     *
     * @param Node[] $nodes New nodes
     * @param Node[] $origNodes Original nodes
     * @param int $pos Current token position (updated by reference)
     * @param int $indentAdjustment Adjustment for indentation
     * @param string $parentNodeClass Class of the containing node.
     * @param string $subNodeName Name of array subnode.
     * @param null|int $fixup Fixup information for array item nodes
     *
     * @return null|string Result of pretty print or null if cannot preserve formatting
     */
    protected function pArray(
        array  $nodes, array $origNodes, int &$pos, int $indentAdjustment,
        string $parentNodeClass, string $subNodeName, ?int $fixup
    ): ?string {
        $diff = $this->nodeListDiffer->diffWithReplacements($origNodes, $nodes);

        $mapKey = $parentNodeClass . '->' . $subNodeName;
        $insertStr = $this->listInsertionMap[$mapKey] ?? null;
        $isStmtList = $subNodeName === 'stmts';

        $beforeFirstKeepOrReplace = true;
        $skipRemovedNode = false;
        $delayedAdd = [];
        $lastElemIndentLevel = $this->indentLevel;

        $insertNewline = false;
        if ($insertStr === "\n") {
            $insertStr = '';
            $insertNewline = true;
        }

        if ($isStmtList && \count($origNodes) === 1 && \count($nodes) !== 1) {
            $startPos = $origNodes[0]->getStartTokenPos();
            $endPos = $origNodes[0]->getEndTokenPos();
            \assert($startPos >= 0 && $endPos >= 0);
            if (!$this->origTokens->haveBraces($startPos, $endPos)) {
                // This was a single statement without braces, but either additional statements
                // have been added, or the single statement has been removed. This requires the
                // addition of braces. For now fall back.
                // TODO: Try to preserve formatting
                return null;
            }
        }

        $result = '';
        foreach ($diff as $i => $diffElem) {
            $diffType = $diffElem->type;
            /** @var Node|string|null $arrItem */
            $arrItem = $diffElem->new;
            /** @var Node|string|null $origArrItem */
            $origArrItem = $diffElem->old;

            if ($diffType === DiffElem::TYPE_KEEP || $diffType === DiffElem::TYPE_REPLACE) {
                $beforeFirstKeepOrReplace = false;

                if ($origArrItem === null || $arrItem === null) {
                    // We can only handle the case where both are null
                    if ($origArrItem === $arrItem) {
                        continue;
                    }
                    return null;
                }

                if (!$arrItem instanceof Node || !$origArrItem instanceof Node) {
                    // We can only deal with nodes. This can occur for Names, which use string arrays.
                    return null;
                }

                $itemStartPos = $origArrItem->getStartTokenPos();
                $itemEndPos = $origArrItem->getEndTokenPos();
                \assert($itemStartPos >= 0 && $itemEndPos >= 0 && $itemStartPos >= $pos);

                $origIndentLevel = $this->indentLevel;
                $lastElemIndentLevel = max($this->origTokens->getIndentationBefore($itemStartPos) + $indentAdjustment, 0);
                $this->setIndentLevel($lastElemIndentLevel);

                $comments = $arrItem->getComments();
                $origComments = $origArrItem->getComments();
                $commentStartPos = $origComments ? $origComments[0]->getStartTokenPos() : $itemStartPos;
                \assert($commentStartPos >= 0);

                if ($commentStartPos < $pos) {
                    // Comments may be assigned to multiple nodes if they start at the same position.
                    // Make sure we don't try to print them multiple times.
                    $commentStartPos = $itemStartPos;
                }

                if ($skipRemovedNode) {
                    if ($isStmtList && $this->origTokens->haveTagInRange($pos, $itemStartPos)) {
                        // We'd remove an opening/closing PHP tag.
                        // TODO: Preserve formatting.
                        $this->setIndentLevel($origIndentLevel);
                        return null;
                    }
                } else {
                    $result .= $this->origTokens->getTokenCode(
                        $pos, $commentStartPos, $indentAdjustment);
                }

                if (!empty($delayedAdd)) {
                    /** @var Node $delayedAddNode */
                    foreach ($delayedAdd as $delayedAddNode) {
                        if ($insertNewline) {
                            $delayedAddComments = $delayedAddNode->getComments();
                            if ($delayedAddComments) {
                                $result .= $this->pComments($delayedAddComments) . $this->nl;
                            }
                        }

                        $this->safeAppend($result, $this->p($delayedAddNode, self::MAX_PRECEDENCE, self::MAX_PRECEDENCE, true));

                        if ($insertNewline) {
                            $result .= $insertStr . $this->nl;
                        } else {
                            $result .= $insertStr;
                        }
                    }

                    $delayedAdd = [];
                }

                if ($comments !== $origComments) {
                    if ($comments) {
                        $result .= $this->pComments($comments) . $this->nl;
                    }
                } else {
                    $result .= $this->origTokens->getTokenCode(
                        $commentStartPos, $itemStartPos, $indentAdjustment);
                }

                // If we had to remove anything, we have done so now.
                $skipRemovedNode = false;
            } elseif ($diffType === DiffElem::TYPE_ADD) {
                if (null === $insertStr) {
                    // We don't have insertion information for this list type
                    return null;
                }

                if (!$arrItem instanceof Node) {
                    // We only support list insertion of nodes.
                    return null;
                }

                // We go multiline if the original code was multiline,
                // or if it's an array item with a comment above it.
                // Match always uses multiline formatting.
                if ($insertStr === ', ' &&
                    ($this->isMultiline($origNodes) || $arrItem->getComments() ||
                     $parentNodeClass === Expr\Match_::class)
                ) {
                    $insertStr = ',';
                    $insertNewline = true;
                }

                if ($beforeFirstKeepOrReplace) {
                    // Will be inserted at the next "replace" or "keep" element
                    $delayedAdd[] = $arrItem;
                    continue;
                }

                $itemStartPos = $pos;
                $itemEndPos = $pos - 1;

                $origIndentLevel = $this->indentLevel;
                $this->setIndentLevel($lastElemIndentLevel);

                if ($insertNewline) {
                    $result .= $insertStr . $this->nl;
                    $comments = $arrItem->getComments();
                    if ($comments) {
                        $result .= $this->pComments($comments) . $this->nl;
                    }
                } else {
                    $result .= $insertStr;
                }
            } elseif ($diffType === DiffElem::TYPE_REMOVE) {
                if (!$origArrItem instanceof Node) {
                    // We only support removal for nodes
                    return null;
                }

                $itemStartPos = $origArrItem->getStartTokenPos();
                $itemEndPos = $origArrItem->getEndTokenPos();
                \assert($itemStartPos >= 0 && $itemEndPos >= 0);

                // Consider comments part of the node.
                $origComments = $origArrItem->getComments();
                if ($origComments) {
                    $itemStartPos = $origComments[0]->getStartTokenPos();
                }

                if ($i === 0) {
                    // If we're removing from the start, keep the tokens before the node and drop those after it,
                    // instead of the other way around.
                    $result .= $this->origTokens->getTokenCode(
                        $pos, $itemStartPos, $indentAdjustment);
                    $skipRemovedNode = true;
                } else {
                    if ($isStmtList && $this->origTokens->haveTagInRange($pos, $itemStartPos)) {
                        // We'd remove an opening/closing PHP tag.
                        // TODO: Preserve formatting.
                        return null;
                    }
                }

                $pos = $itemEndPos + 1;
                continue;
            } else {
                throw new \Exception("Shouldn't happen");
            }

            if (null !== $fixup && $arrItem->getAttribute('origNode') !== $origArrItem) {
                $res = $this->pFixup($fixup, $arrItem, null, $itemStartPos, $itemEndPos);
            } else {
                $res = $this->p($arrItem, self::MAX_PRECEDENCE, self::MAX_PRECEDENCE, true);
            }
            $this->safeAppend($result, $res);

            $this->setIndentLevel($origIndentLevel);
            $pos = $itemEndPos + 1;
        }

        if ($skipRemovedNode) {
            // TODO: Support removing single node.
            return null;
        }

        if (!empty($delayedAdd)) {
            if (!isset($this->emptyListInsertionMap[$mapKey])) {
                return null;
            }

            list($findToken, $extraLeft, $extraRight) = $this->emptyListInsertionMap[$mapKey];
            if (null !== $findToken) {
                // For anon classes skip to the class keyword.
                $isAnonClassArgs = $mapKey === PrintableNewAnonClassNode::class . '->args';
                if ($isAnonClassArgs) {
                    $insertPos = $this->origTokens->findRight($pos, \T_CLASS) + 1;
                    $result .= $this->origTokens->getTokenCode($pos, $insertPos, $indentAdjustment);
                    $pos = $insertPos;
                }

                // If "new Foo" was used without arguments, we need to convert to "new Foo()".
                if (($mapKey === Expr\New_::class . '->args' || $isAnonClassArgs) &&
                    !$this->origTokens->haveTokenImmediatelyAfter($pos - 1, '(')
                ) {
                    $extraLeft = '(';
                    $extraRight = ')';
                } else {
                    $insertPos = $this->origTokens->findRight($pos, $findToken) + 1;
                    $result .= $this->origTokens->getTokenCode($pos, $insertPos, $indentAdjustment);
                    $pos = $insertPos;
                }
            }

            $first = true;
            $result .= $extraLeft;
            foreach ($delayedAdd as $delayedAddNode) {
                if (!$first) {
                    $result .= $insertStr;
                    if ($insertNewline) {
                        $result .= $this->nl;
                    }
                }
                $result .= $this->p($delayedAddNode, self::MAX_PRECEDENCE, self::MAX_PRECEDENCE, true);
                $first = false;
            }
            $result .= $extraRight === "\n" ? $this->nl : $extraRight;
        }

        return $result;
    }

    /**
     * Print node with fixups.
     *
     * Fixups here refer to the addition of extra parentheses, braces or other characters, that
     * are required to preserve program semantics in a certain context (e.g. to maintain precedence
     * or because only certain expressions are allowed in certain places).
     *
     * @param int $fixup Fixup type
     * @param Node $subNode Subnode to print
     * @param string|null $parentClass Class of parent node
     * @param int $subStartPos Original start pos of subnode
     * @param int $subEndPos Original end pos of subnode
     *
     * @return string Result of fixed-up print of subnode
     */
    protected function pFixup(int $fixup, Node $subNode, ?string $parentClass, int $subStartPos, int $subEndPos): string {
        switch ($fixup) {
            case self::FIXUP_PREC_LEFT:
                // We use a conservative approximation where lhsPrecedence == precedence.
                if (!$this->origTokens->haveParens($subStartPos, $subEndPos)) {
                    $precedence = $this->precedenceMap[$parentClass][1];
                    return $this->p($subNode, $precedence, $precedence);
                }
                break;
            case self::FIXUP_PREC_RIGHT:
                if (!$this->origTokens->haveParens($subStartPos, $subEndPos)) {
                    $precedence = $this->precedenceMap[$parentClass][2];
                    return $this->p($subNode, $precedence, $precedence);
                }
                break;
            case self::FIXUP_PREC_UNARY:
                if (!$this->origTokens->haveParens($subStartPos, $subEndPos)) {
                    $precedence = $this->precedenceMap[$parentClass][0];
                    return $this->p($subNode, $precedence, $precedence);
                }
                break;
            case self::FIXUP_CALL_LHS:
                if ($this->callLhsRequiresParens($subNode)
                    && !$this->origTokens->haveParens($subStartPos, $subEndPos)
                ) {
                    return '(' . $this->p($subNode) . ')';
                }
                break;
            case self::FIXUP_DEREF_LHS:
                if ($this->dereferenceLhsRequiresParens($subNode)
                    && !$this->origTokens->haveParens($subStartPos, $subEndPos)
                ) {
                    return '(' . $this->p($subNode) . ')';
                }
                break;
            case self::FIXUP_STATIC_DEREF_LHS:
                if ($this->staticDereferenceLhsRequiresParens($subNode)
                    && !$this->origTokens->haveParens($subStartPos, $subEndPos)
                ) {
                    return '(' . $this->p($subNode) . ')';
                }
                break;
            case self::FIXUP_NEW:
                if ($this->newOperandRequiresParens($subNode)
                    && !$this->origTokens->haveParens($subStartPos, $subEndPos)) {
                    return '(' . $this->p($subNode) . ')';
                }
                break;
            case self::FIXUP_BRACED_NAME:
            case self::FIXUP_VAR_BRACED_NAME:
                if ($subNode instanceof Expr
                    && !$this->origTokens->haveBraces($subStartPos, $subEndPos)
                ) {
                    return ($fixup === self::FIXUP_VAR_BRACED_NAME ? '$' : '')
                        . '{' . $this->p($subNode) . '}';
                }
                break;
            case self::FIXUP_ENCAPSED:
                if (!$subNode instanceof Node\InterpolatedStringPart
                    && !$this->origTokens->haveBraces($subStartPos, $subEndPos)
                ) {
                    return '{' . $this->p($subNode) . '}';
                }
                break;
            default:
                throw new \Exception('Cannot happen');
        }

        // Nothing special to do
        return $this->p($subNode);
    }

    /**
     * Appends to a string, ensuring whitespace between label characters.
     *
     * Example: "echo" and "$x" result in "echo$x", but "echo" and "x" result in "echo x".
     * Without safeAppend the result would be "echox", which does not preserve semantics.
     */
    protected function safeAppend(string &$str, string $append): void {
        if ($str === "") {
            $str = $append;
            return;
        }

        if ($append === "") {
            return;
        }

        if (!$this->labelCharMap[$append[0]]
                || !$this->labelCharMap[$str[\strlen($str) - 1]]) {
            $str .= $append;
        } else {
            $str .= " " . $append;
        }
    }

    /**
     * Determines whether the LHS of a call must be wrapped in parenthesis.
     *
     * @param Node $node LHS of a call
     *
     * @return bool Whether parentheses are required
     */
    protected function callLhsRequiresParens(Node $node): bool {
        if ($node instanceof Expr\New_) {
            return !$this->phpVersion->supportsNewDereferenceWithoutParentheses();
        }
        return !($node instanceof Node\Name
            || $node instanceof Expr\Variable
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\NullsafeMethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_);
    }

    /**
     * Determines whether the LHS of an array/object operation must be wrapped in parentheses.
     *
     * @param Node $node LHS of dereferencing operation
     *
     * @return bool Whether parentheses are required
     */
    protected function dereferenceLhsRequiresParens(Node $node): bool {
        // A constant can occur on the LHS of an array/object deref, but not a static deref.
        return $this->staticDereferenceLhsRequiresParens($node)
            && !$node instanceof Expr\ConstFetch;
    }

    /**
     * Determines whether the LHS of a static operation must be wrapped in parentheses.
     *
     * @param Node $node LHS of dereferencing operation
     *
     * @return bool Whether parentheses are required
     */
    protected function staticDereferenceLhsRequiresParens(Node $node): bool {
        if ($node instanceof Expr\New_) {
            return !$this->phpVersion->supportsNewDereferenceWithoutParentheses();
        }
        return !($node instanceof Expr\Variable
            || $node instanceof Node\Name
            || $node instanceof Expr\ArrayDimFetch
            || $node instanceof Expr\PropertyFetch
            || $node instanceof Expr\NullsafePropertyFetch
            || $node instanceof Expr\StaticPropertyFetch
            || $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\NullsafeMethodCall
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\Array_
            || $node instanceof Scalar\String_
            || $node instanceof Expr\ClassConstFetch);
    }

    /**
     * Determines whether an expression used in "new" or "instanceof" requires parentheses.
     *
     * @param Node $node New or instanceof operand
     *
     * @return bool Whether parentheses are required
     */
    protected function newOperandRequiresParens(Node $node): bool {
        if ($node instanceof Node\Name || $node instanceof Expr\Variable) {
            return false;
        }
        if ($node instanceof Expr\ArrayDimFetch || $node instanceof Expr\PropertyFetch ||
            $node instanceof Expr\NullsafePropertyFetch
        ) {
            return $this->newOperandRequiresParens($node->var);
        }
        if ($node instanceof Expr\StaticPropertyFetch) {
            return $this->newOperandRequiresParens($node->class);
        }
        return true;
    }

    /**
     * Print modifiers, including trailing whitespace.
     *
     * @param int $modifiers Modifier mask to print
     *
     * @return string Printed modifiers
     */
    protected function pModifiers(int $modifiers): string {
        return ($modifiers & Modifiers::FINAL ? 'final ' : '')
             . ($modifiers & Modifiers::ABSTRACT ? 'abstract ' : '')
             . ($modifiers & Modifiers::PUBLIC ? 'public ' : '')
             . ($modifiers & Modifiers::PROTECTED ? 'protected ' : '')
             . ($modifiers & Modifiers::PRIVATE ? 'private ' : '')
             . ($modifiers & Modifiers::PUBLIC_SET ? 'public(set) ' : '')
             . ($modifiers & Modifiers::PROTECTED_SET ? 'protected(set) ' : '')
             . ($modifiers & Modifiers::PRIVATE_SET ? 'private(set) ' : '')
             . ($modifiers & Modifiers::STATIC ? 'static ' : '')
             . ($modifiers & Modifiers::READONLY ? 'readonly ' : '');
    }

    protected function pStatic(bool $static): string {
        return $static ? 'static ' : '';
    }

    /**
     * Determine whether a list of nodes uses multiline formatting.
     *
     * @param (Node|null)[] $nodes Node list
     *
     * @return bool Whether multiline formatting is used
     */
    protected function isMultiline(array $nodes): bool {
        if (\count($nodes) < 2) {
            return false;
        }

        $pos = -1;
        foreach ($nodes as $node) {
            if (null === $node) {
                continue;
            }

            $endPos = $node->getEndTokenPos() + 1;
            if ($pos >= 0) {
                $text = $this->origTokens->getTokenCode($pos, $endPos, 0);
                if (false === strpos($text, "\n")) {
                    // We require that a newline is present between *every* item. If the formatting
                    // is inconsistent, with only some items having newlines, we don't consider it
                    // as multiline
                    return false;
                }
            }
            $pos = $endPos;
        }

        return true;
    }

    /**
     * Lazily initializes label char map.
     *
     * The label char map determines whether a certain character may occur in a label.
     */
    protected function initializeLabelCharMap(): void {
        if (isset($this->labelCharMap)) {
            return;
        }

        $this->labelCharMap = [];
        for ($i = 0; $i < 256; $i++) {
            $chr = chr($i);
            $this->labelCharMap[$chr] = (bool) preg_match('/^[a-zA-Z0-9_\x80-\xff]$/', $chr);
        }

        if ($this->phpVersion->allowsDelInIdentifiers()) {
            $this->labelCharMap["\x7f"] = true;
        }
    }

    /**
     * Lazily initializes node list differ.
     *
     * The node list differ is used to determine differences between two array subnodes.
     */
    protected function initializeNodeListDiffer(): void {
        if (isset($this->nodeListDiffer)) {
            return;
        }

        $this->nodeListDiffer = new Internal\Differ(function ($a, $b) {
            if ($a instanceof Node && $b instanceof Node) {
                return $a === $b->getAttribute('origNode');
            }
            // Can happen for array destructuring
            return $a === null && $b === null;
        });
    }

    /**
     * Lazily initializes fixup map.
     *
     * The fixup map is used to determine whether a certain subnode of a certain node may require
     * some kind of "fixup" operation, e.g. the addition of parenthesis or braces.
     */
    protected function initializeFixupMap(): void {
        if (isset($this->fixupMap)) {
            return;
        }

        $this->fixupMap = [
            Expr\Instanceof_::class => [
                'expr' => self::FIXUP_PREC_UNARY,
                'class' => self::FIXUP_NEW,
            ],
            Expr\Ternary::class => [
                'cond' => self::FIXUP_PREC_LEFT,
                'else' => self::FIXUP_PREC_RIGHT,
            ],
            Expr\Yield_::class => ['value' => self::FIXUP_PREC_UNARY],

            Expr\FuncCall::class => ['name' => self::FIXUP_CALL_LHS],
            Expr\StaticCall::class => ['class' => self::FIXUP_STATIC_DEREF_LHS],
            Expr\ArrayDimFetch::class => ['var' => self::FIXUP_DEREF_LHS],
            Expr\ClassConstFetch::class => [
                'class' => self::FIXUP_STATIC_DEREF_LHS,
                'name' => self::FIXUP_BRACED_NAME,
            ],
            Expr\New_::class => ['class' => self::FIXUP_NEW],
            Expr\MethodCall::class => [
                'var' => self::FIXUP_DEREF_LHS,
                'name' => self::FIXUP_BRACED_NAME,
            ],
            Expr\NullsafeMethodCall::class => [
                'var' => self::FIXUP_DEREF_LHS,
                'name' => self::FIXUP_BRACED_NAME,
            ],
            Expr\StaticPropertyFetch::class => [
                'class' => self::FIXUP_STATIC_DEREF_LHS,
                'name' => self::FIXUP_VAR_BRACED_NAME,
            ],
            Expr\PropertyFetch::class => [
                'var' => self::FIXUP_DEREF_LHS,
                'name' => self::FIXUP_BRACED_NAME,
            ],
            Expr\NullsafePropertyFetch::class => [
                'var' => self::FIXUP_DEREF_LHS,
                'name' => self::FIXUP_BRACED_NAME,
            ],
            Scalar\InterpolatedString::class => [
                'parts' => self::FIXUP_ENCAPSED,
            ],
        ];

        $binaryOps = [
            BinaryOp\Pow::class, BinaryOp\Mul::class, BinaryOp\Div::class, BinaryOp\Mod::class,
            BinaryOp\Plus::class, BinaryOp\Minus::class, BinaryOp\Concat::class,
            BinaryOp\ShiftLeft::class, BinaryOp\ShiftRight::class, BinaryOp\Smaller::class,
            BinaryOp\SmallerOrEqual::class, BinaryOp\Greater::class, BinaryOp\GreaterOrEqual::class,
            BinaryOp\Equal::class, BinaryOp\NotEqual::class, BinaryOp\Identical::class,
            BinaryOp\NotIdentical::class, BinaryOp\Spaceship::class, BinaryOp\BitwiseAnd::class,
            BinaryOp\BitwiseXor::class, BinaryOp\BitwiseOr::class, BinaryOp\BooleanAnd::class,
            BinaryOp\BooleanOr::class, BinaryOp\Coalesce::class, BinaryOp\LogicalAnd::class,
            BinaryOp\LogicalXor::class, BinaryOp\LogicalOr::class, BinaryOp\Pipe::class,
        ];
        foreach ($binaryOps as $binaryOp) {
            $this->fixupMap[$binaryOp] = [
                'left' => self::FIXUP_PREC_LEFT,
                'right' => self::FIXUP_PREC_RIGHT
            ];
        }

        $prefixOps = [
            Expr\Clone_::class, Expr\BitwiseNot::class, Expr\BooleanNot::class, Expr\UnaryPlus::class, Expr\UnaryMinus::class,
            Cast\Int_::class, Cast\Double::class, Cast\String_::class, Cast\Array_::class,
            Cast\Object_::class, Cast\Bool_::class, Cast\Unset_::class, Expr\ErrorSuppress::class,
            Expr\YieldFrom::class, Expr\Print_::class, Expr\Include_::class,
            Expr\Assign::class, Expr\AssignRef::class, AssignOp\Plus::class, AssignOp\Minus::class,
            AssignOp\Mul::class, AssignOp\Div::class, AssignOp\Concat::class, AssignOp\Mod::class,
            AssignOp\BitwiseAnd::class, AssignOp\BitwiseOr::class, AssignOp\BitwiseXor::class,
            AssignOp\ShiftLeft::class, AssignOp\ShiftRight::class, AssignOp\Pow::class, AssignOp\Coalesce::class,
            Expr\ArrowFunction::class, Expr\Throw_::class,
        ];
        foreach ($prefixOps as $prefixOp) {
            $this->fixupMap[$prefixOp] = ['expr' => self::FIXUP_PREC_UNARY];
        }
    }

    /**
     * Lazily initializes the removal map.
     *
     * The removal map is used to determine which additional tokens should be removed when a
     * certain node is replaced by null.
     */
    protected function initializeRemovalMap(): void {
        if (isset($this->removalMap)) {
            return;
        }

        $stripBoth = ['left' => \T_WHITESPACE, 'right' => \T_WHITESPACE];
        $stripLeft = ['left' => \T_WHITESPACE];
        $stripRight = ['right' => \T_WHITESPACE];
        $stripDoubleArrow = ['right' => \T_DOUBLE_ARROW];
        $stripColon = ['left' => ':'];
        $stripEquals = ['left' => '='];
        $this->removalMap = [
            'Expr_ArrayDimFetch->dim' => $stripBoth,
            'ArrayItem->key' => $stripDoubleArrow,
            'Expr_ArrowFunction->returnType' => $stripColon,
            'Expr_Closure->returnType' => $stripColon,
            'Expr_Exit->expr' => $stripBoth,
            'Expr_Ternary->if' => $stripBoth,
            'Expr_Yield->key' => $stripDoubleArrow,
            'Expr_Yield->value' => $stripBoth,
            'Param->type' => $stripRight,
            'Param->default' => $stripEquals,
            'Stmt_Break->num' => $stripBoth,
            'Stmt_Catch->var' => $stripLeft,
            'Stmt_ClassConst->type' => $stripRight,
            'Stmt_ClassMethod->returnType' => $stripColon,
            'Stmt_Class->extends' => ['left' => \T_EXTENDS],
            'Stmt_Enum->scalarType' => $stripColon,
            'Stmt_EnumCase->expr' => $stripEquals,
            'Expr_PrintableNewAnonClass->extends' => ['left' => \T_EXTENDS],
            'Stmt_Continue->num' => $stripBoth,
            'Stmt_Foreach->keyVar' => $stripDoubleArrow,
            'Stmt_Function->returnType' => $stripColon,
            'Stmt_If->else' => $stripLeft,
            'Stmt_Namespace->name' => $stripLeft,
            'Stmt_Property->type' => $stripRight,
            'PropertyItem->default' => $stripEquals,
            'Stmt_Return->expr' => $stripBoth,
            'Stmt_StaticVar->default' => $stripEquals,
            'Stmt_TraitUseAdaptation_Alias->newName' => $stripLeft,
            'Stmt_TryCatch->finally' => $stripLeft,
            // 'Stmt_Case->cond': Replace with "default"
            // 'Stmt_Class->name': Unclear what to do
            // 'Stmt_Declare->stmts': Not a plain node
            // 'Stmt_TraitUseAdaptation_Alias->newModifier': Not a plain node
        ];
    }

    protected function initializeInsertionMap(): void {
        if (isset($this->insertionMap)) {
            return;
        }

        // TODO: "yield" where both key and value are inserted doesn't work
        // [$find, $beforeToken, $extraLeft, $extraRight]
        $this->insertionMap = [
            'Expr_ArrayDimFetch->dim' => ['[', false, null, null],
            'ArrayItem->key' => [null, false, null, ' => '],
            'Expr_ArrowFunction->returnType' => [')', false, ': ', null],
            'Expr_Closure->returnType' => [')', false, ': ', null],
            'Expr_Ternary->if' => ['?', false, ' ', ' '],
            'Expr_Yield->key' => [\T_YIELD, false, null, ' => '],
            'Expr_Yield->value' => [\T_YIELD, false, ' ', null],
            'Param->type' => [null, false, null, ' '],
            'Param->default' => [null, false, ' = ', null],
            'Stmt_Break->num' => [\T_BREAK, false, ' ', null],
            'Stmt_Catch->var' => [null, false, ' ', null],
            'Stmt_ClassMethod->returnType' => [')', false, ': ', null],
            'Stmt_ClassConst->type' => [\T_CONST, false, ' ', null],
            'Stmt_Class->extends' => [null, false, ' extends ', null],
            'Stmt_Enum->scalarType' => [null, false, ' : ', null],
            'Stmt_EnumCase->expr' => [null, false, ' = ', null],
            'Expr_PrintableNewAnonClass->extends' => [null, false, ' extends ', null],
            'Stmt_Continue->num' => [\T_CONTINUE, false, ' ', null],
            'Stmt_Foreach->keyVar' => [\T_AS, false, null, ' => '],
            'Stmt_Function->returnType' => [')', false, ': ', null],
            'Stmt_If->else' => [null, false, ' ', null],
            'Stmt_Namespace->name' => [\T_NAMESPACE, false, ' ', null],
            'Stmt_Property->type' => [\T_VARIABLE, true, null, ' '],
            'PropertyItem->default' => [null, false, ' = ', null],
            'Stmt_Return->expr' => [\T_RETURN, false, ' ', null],
            'Stmt_StaticVar->default' => [null, false, ' = ', null],
            //'Stmt_TraitUseAdaptation_Alias->newName' => [T_AS, false, ' ', null], // TODO
            'Stmt_TryCatch->finally' => [null, false, ' ', null],

            // 'Expr_Exit->expr': Complicated due to optional ()
            // 'Stmt_Case->cond': Conversion from default to case
            // 'Stmt_Class->name': Unclear
            // 'Stmt_Declare->stmts': Not a proper node
            // 'Stmt_TraitUseAdaptation_Alias->newModifier': Not a proper node
        ];
    }

    protected function initializeListInsertionMap(): void {
        if (isset($this->listInsertionMap)) {
            return;
        }

        $this->listInsertionMap = [
            // special
            //'Expr_ShellExec->parts' => '', // TODO These need to be treated more carefully
            //'Scalar_InterpolatedString->parts' => '',
            Stmt\Catch_::class . '->types' => '|',
            UnionType::class . '->types' => '|',
            IntersectionType::class . '->types' => '&',
            Stmt\If_::class . '->elseifs' => ' ',
            Stmt\TryCatch::class . '->catches' => ' ',

            // comma-separated lists
            Expr\Array_::class . '->items' => ', ',
            Expr\ArrowFunction::class . '->params' => ', ',
            Expr\Closure::class . '->params' => ', ',
            Expr\Closure::class . '->uses' => ', ',
            Expr\FuncCall::class . '->args' => ', ',
            Expr\Isset_::class . '->vars' => ', ',
            Expr\List_::class . '->items' => ', ',
            Expr\MethodCall::class . '->args' => ', ',
            Expr\NullsafeMethodCall::class . '->args' => ', ',
            Expr\New_::class . '->args' => ', ',
            PrintableNewAnonClassNode::class . '->args' => ', ',
            Expr\StaticCall::class . '->args' => ', ',
            Stmt\ClassConst::class . '->consts' => ', ',
            Stmt\ClassMethod::class . '->params' => ', ',
            Stmt\Class_::class . '->implements' => ', ',
            Stmt\Enum_::class . '->implements' => ', ',
            PrintableNewAnonClassNode::class . '->implements' => ', ',
            Stmt\Const_::class . '->consts' => ', ',
            Stmt\Declare_::class . '->declares' => ', ',
            Stmt\Echo_::class . '->exprs' => ', ',
            Stmt\For_::class . '->init' => ', ',
            Stmt\For_::class . '->cond' => ', ',
            Stmt\For_::class . '->loop' => ', ',
            Stmt\Function_::class . '->params' => ', ',
            Stmt\Global_::class . '->vars' => ', ',
            Stmt\GroupUse::class . '->uses' => ', ',
            Stmt\Interface_::class . '->extends' => ', ',
            Expr\Match_::class . '->arms' => ', ',
            Stmt\Property::class . '->props' => ', ',
            Stmt\StaticVar::class . '->vars' => ', ',
            Stmt\TraitUse::class . '->traits' => ', ',
            Stmt\TraitUseAdaptation\Precedence::class . '->insteadof' => ', ',
            Stmt\Unset_::class .  '->vars' => ', ',
            Stmt\UseUse::class . '->uses' => ', ',
            MatchArm::class . '->conds' => ', ',
            AttributeGroup::class . '->attrs' => ', ',
            PropertyHook::class . '->params' => ', ',

            // statement lists
            Expr\Closure::class . '->stmts' => "\n",
            Stmt\Case_::class . '->stmts' => "\n",
            Stmt\Catch_::class . '->stmts' => "\n",
            Stmt\Class_::class . '->stmts' => "\n",
            Stmt\Enum_::class . '->stmts' => "\n",
            PrintableNewAnonClassNode::class . '->stmts' => "\n",
            Stmt\Interface_::class . '->stmts' => "\n",
            Stmt\Trait_::class . '->stmts' => "\n",
            Stmt\ClassMethod::class . '->stmts' => "\n",
            Stmt\Declare_::class . '->stmts' => "\n",
            Stmt\Do_::class . '->stmts' => "\n",
            Stmt\ElseIf_::class . '->stmts' => "\n",
            Stmt\Else_::class . '->stmts' => "\n",
            Stmt\Finally_::class . '->stmts' => "\n",
            Stmt\Foreach_::class . '->stmts' => "\n",
            Stmt\For_::class . '->stmts' => "\n",
            Stmt\Function_::class . '->stmts' => "\n",
            Stmt\If_::class . '->stmts' => "\n",
            Stmt\Namespace_::class . '->stmts' => "\n",
            Stmt\Block::class . '->stmts' => "\n",

            // Attribute groups
            Stmt\Class_::class . '->attrGroups' => "\n",
            Stmt\Enum_::class . '->attrGroups' => "\n",
            Stmt\EnumCase::class . '->attrGroups' => "\n",
            Stmt\Interface_::class . '->attrGroups' => "\n",
            Stmt\Trait_::class . '->attrGroups' => "\n",
            Stmt\Function_::class . '->attrGroups' => "\n",
            Stmt\ClassMethod::class . '->attrGroups' => "\n",
            Stmt\ClassConst::class . '->attrGroups' => "\n",
            Stmt\Property::class . '->attrGroups' => "\n",
            PrintableNewAnonClassNode::class . '->attrGroups' => ' ',
            Expr\Closure::class . '->attrGroups' => ' ',
            Expr\ArrowFunction::class . '->attrGroups' => ' ',
            Param::class . '->attrGroups' => ' ',
            PropertyHook::class . '->attrGroups' => ' ',

            Stmt\Switch_::class . '->cases' => "\n",
            Stmt\TraitUse::class . '->adaptations' => "\n",
            Stmt\TryCatch::class . '->stmts' => "\n",
            Stmt\While_::class . '->stmts' => "\n",
            PropertyHook::class . '->body' => "\n",
            Stmt\Property::class . '->hooks' => "\n",
            Param::class . '->hooks' => "\n",

            // dummy for top-level context
            'File->stmts' => "\n",
        ];
    }

    protected function initializeEmptyListInsertionMap(): void {
        if (isset($this->emptyListInsertionMap)) {
            return;
        }

        // TODO Insertion into empty statement lists.

        // [$find, $extraLeft, $extraRight]
        $this->emptyListInsertionMap = [
            Expr\ArrowFunction::class . '->params' => ['(', '', ''],
            Expr\Closure::class . '->uses' => [')', ' use (', ')'],
            Expr\Closure::class . '->params' => ['(', '', ''],
            Expr\FuncCall::class . '->args' => ['(', '', ''],
            Expr\MethodCall::class . '->args' => ['(', '', ''],
            Expr\NullsafeMethodCall::class . '->args' => ['(', '', ''],
            Expr\New_::class . '->args' => ['(', '', ''],
            PrintableNewAnonClassNode::class . '->args' => ['(', '', ''],
            PrintableNewAnonClassNode::class . '->implements' => [null, ' implements ', ''],
            Expr\StaticCall::class . '->args' => ['(', '', ''],
            Stmt\Class_::class . '->implements' => [null, ' implements ', ''],
            Stmt\Enum_::class . '->implements' => [null, ' implements ', ''],
            Stmt\ClassMethod::class . '->params' => ['(', '', ''],
            Stmt\Interface_::class . '->extends' => [null, ' extends ', ''],
            Stmt\Function_::class . '->params' => ['(', '', ''],
            Stmt\Interface_::class . '->attrGroups' => [null, '', "\n"],
            Stmt\Class_::class . '->attrGroups' => [null, '', "\n"],
            Stmt\ClassConst::class . '->attrGroups' => [null, '', "\n"],
            Stmt\ClassMethod::class . '->attrGroups' => [null, '', "\n"],
            Stmt\Function_::class . '->attrGroups' => [null, '', "\n"],
            Stmt\Property::class . '->attrGroups' => [null, '', "\n"],
            Stmt\Trait_::class . '->attrGroups' => [null, '', "\n"],
            Expr\ArrowFunction::class . '->attrGroups' => [null, '', ' '],
            Expr\Closure::class . '->attrGroups' => [null, '', ' '],
            Stmt\Const_::class . '->attrGroups' => [null, '', "\n"],
            PrintableNewAnonClassNode::class . '->attrGroups' => [\T_NEW, ' ', ''],

            /* These cannot be empty to start with:
             * Expr_Isset->vars
             * Stmt_Catch->types
             * Stmt_Const->consts
             * Stmt_ClassConst->consts
             * Stmt_Declare->declares
             * Stmt_Echo->exprs
             * Stmt_Global->vars
             * Stmt_GroupUse->uses
             * Stmt_Property->props
             * Stmt_StaticVar->vars
             * Stmt_TraitUse->traits
             * Stmt_TraitUseAdaptation_Precedence->insteadof
             * Stmt_Unset->vars
             * Stmt_Use->uses
             * UnionType->types
             */

            /* TODO
             * Stmt_If->elseifs
             * Stmt_TryCatch->catches
             * Expr_Array->items
             * Expr_List->items
             * Stmt_For->init
             * Stmt_For->cond
             * Stmt_For->loop
             */
        ];
    }

    protected function initializeModifierChangeMap(): void {
        if (isset($this->modifierChangeMap)) {
            return;
        }

        $this->modifierChangeMap = [
            Stmt\ClassConst::class . '->flags' => ['pModifiers', \T_WHITESPACE, \T_CONST],
            Stmt\ClassMethod::class . '->flags' => ['pModifiers', \T_WHITESPACE, \T_FUNCTION],
            Stmt\Class_::class . '->flags' => ['pModifiers', \T_WHITESPACE, \T_CLASS],
            Stmt\Property::class . '->flags' => ['pModifiers', \T_WHITESPACE, \T_VARIABLE],
            PrintableNewAnonClassNode::class . '->flags' => ['pModifiers', \T_NEW, \T_CLASS],
            Param::class . '->flags' => ['pModifiers', \T_WHITESPACE, \T_VARIABLE],
            PropertyHook::class . '->flags' => ['pModifiers', \T_WHITESPACE, \T_STRING],
            Expr\Closure::class . '->static' => ['pStatic', \T_WHITESPACE, \T_FUNCTION],
            Expr\ArrowFunction::class . '->static' => ['pStatic', \T_WHITESPACE, \T_FN],
            //Stmt\TraitUseAdaptation\Alias::class . '->newModifier' => 0, // TODO
        ];

        // List of integer subnodes that are not modifiers:
        // Expr_Include->type
        // Stmt_GroupUse->type
        // Stmt_Use->type
        // UseItem->type
    }
}
