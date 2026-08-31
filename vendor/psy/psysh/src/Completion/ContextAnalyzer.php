<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion;

use PhpParser\Error as PhpParserError;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard as Printer;
use Psy\CodeCleaner;
use Psy\ParserFactory;

/**
 * Analyzes input to determine completion context.
 *
 * This stage provides the parser-derived starting point for completion. Its
 * job is to describe the PHP syntax at the cursor, not to decide every higher-
 * level completion mode built on top of that syntax.
 */
class ContextAnalyzer
{
    private Parser $parser;
    private Printer $printer;
    private ?CodeCleaner $cleaner;

    public function __construct(?CodeCleaner $cleaner = null)
    {
        $this->parser = (new ParserFactory())->createParser();
        $this->printer = new Printer();
        $this->cleaner = $cleaner;
    }

    /**
     * Analyze input and return the coarse parser-derived context.
     */
    public function analyze(string $input, int $cursor, array $readlineInfo = []): AnalysisResult
    {
        // Cursor is in code-point units, so use mb_substr
        $inputToCursor = \mb_substr($input, 0, $cursor);
        $cursorAtEnd = ($cursor >= \mb_strlen($input));
        $analysis = $this->tryParse($inputToCursor, $cursorAtEnd);
        $parseSucceeded = $analysis !== null;
        $analysis = $analysis ?? new AnalysisResult(CompletionKind::UNKNOWN, '');
        $analysis->parseSucceeded = $parseSucceeded;

        $analysis->input = $inputToCursor;
        $analysis->tokens = @\token_get_all('<?php '.$inputToCursor);
        $analysis->readlineInfo = $readlineInfo;

        return $analysis;
    }

    /**
     * Try to parse input and analyze the resulting AST.
     */
    private function tryParse(string $input, bool $cursorAtEnd): ?AnalysisResult
    {
        $code = '<?php '.$input;

        // Try with semicolon first (most common case), then without
        try {
            $stmts = $this->parser->parse($code.';');
        } catch (PhpParserError $e) {
            try {
                $stmts = $this->parser->parse($code);
            } catch (PhpParserError $e2) {
                return null;
            }
        }

        if (empty($stmts)) {
            return new AnalysisResult(CompletionKind::UNKNOWN, '');
        }

        $visitor = new DeepestNodeVisitor();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($stmts);

        $node = $visitor->getDeepestNode();
        if ($node === null) {
            return new AnalysisResult(CompletionKind::UNKNOWN, '');
        }

        // Trailing whitespace means the user has moved past this token
        if ($cursorAtEnd && $input !== \rtrim($input)) {
            return new AnalysisResult(CompletionKind::UNKNOWN, '');
        }

        return $this->analyzeNode($node);
    }

    /**
     * Analyze a specific AST node to determine completion context.
     */
    private function analyzeNode(Node $node): AnalysisResult
    {
        // $foo->bar, $foo->bar(), $foo?->bar, $foo?->bar()
        if (
            $node instanceof MethodCall
            || $node instanceof PropertyFetch
            || $node instanceof NullsafeMethodCall
            || $node instanceof NullsafePropertyFetch
        ) {
            $leftSide = $this->extractExpression($node->var);
            $prefix = $node->name instanceof Identifier ? $node->name->name : '';
            $result = new AnalysisResult(CompletionKind::OBJECT_MEMBER, $prefix, $leftSide);
            $result->leftSideNode = $node->var;

            return $result;
        }

        // Foo::bar(), Foo::BAR, Foo::$bar
        if (
            $node instanceof StaticCall
            || $node instanceof ClassConstFetch
            || $node instanceof StaticPropertyFetch
        ) {
            $leftSide = $this->extractExpression($node->class);
            $prefix = $node->name instanceof Identifier ? $node->name->name : '';
            $result = new AnalysisResult(CompletionKind::STATIC_MEMBER, $prefix, $leftSide);
            $result->leftSideNode = $node->class;

            return $result;
        }

        // new Foo
        if ($node instanceof New_) {
            $prefix = $node->class instanceof Name ? $node->class->toString() : '';

            return new AnalysisResult(CompletionKind::CLASS_NAME, $prefix);
        }

        // $foo
        if ($node instanceof Variable) {
            $prefix = \is_string($node->name) ? $node->name : '';

            return new AnalysisResult(CompletionKind::VARIABLE, $prefix);
        }

        // foo()
        if ($node instanceof FuncCall && $node->name instanceof Name) {
            return new AnalysisResult(CompletionKind::FUNCTION_NAME, $node->name->toString());
        }

        // FOO (could be a constant, function, or class reference)
        if ($node instanceof ConstFetch && $node->name instanceof Name) {
            return new AnalysisResult(CompletionKind::SYMBOL, $node->name->toString());
        }

        if ($node instanceof Identifier) {
            return new AnalysisResult(CompletionKind::UNKNOWN, $node->name);
        }

        // Bare name: foo or Foo\Bar
        if ($node instanceof Name) {
            return new AnalysisResult(CompletionKind::SYMBOL, $node->toString());
        }

        return new AnalysisResult(CompletionKind::UNKNOWN, '');
    }

    /**
     * Extract a string representation of an expression.
     *
     * Uses the printer to convert the AST node back to code.
     */
    private function extractExpression($expr): string
    {
        if ($expr instanceof Variable && \is_string($expr->name)) {
            return '$'.$expr->name;
        }

        if ($expr instanceof Name) {
            return $this->resolveClassName($expr);
        }

        if ($expr instanceof Node\Expr) {
            return $this->printer->prettyPrintExpr($expr);
        }

        return '';
    }

    /**
     * Resolve a class name using CodeCleaner's namespace context.
     *
     * This ensures we use the same use statements and namespace as the code
     * being executed.
     */
    private function resolveClassName(Name $name): string
    {
        if ($this->cleaner === null) {
            return $name->toString();
        }

        return $this->cleaner->resolveClassName($name->toString());
    }
}
