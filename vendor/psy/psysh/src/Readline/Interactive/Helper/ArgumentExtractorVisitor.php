<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Helper;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard as Printer;

/**
 * AST visitor to extract function/method call arguments.
 *
 * Returns an array where the first element is the callable (function/method name)
 * and subsequent elements are the arguments.
 */
class ArgumentExtractorVisitor extends NodeVisitorAbstract
{
    private Printer $printer;
    /** @var string[] Extracted arguments */
    private array $arguments = [];
    private bool $foundCall = false;

    public function __construct(Printer $printer)
    {
        $this->printer = $printer;
    }

    public function enterNode(Node $node)
    {
        if ($this->foundCall) {
            return null;
        }

        $callable = $this->resolveCallable($node);
        if ($callable !== null) {
            $this->foundCall = true;
            $this->arguments[] = $callable;

            if ($node instanceof Node\Expr\FuncCall || $node instanceof Node\Expr\MethodCall || $node instanceof Node\Expr\StaticCall || $node instanceof Node\Expr\New_) {
                $this->extractArgs($node);
            }
        }

        return null;
    }

    /**
     * Resolve the callable name for a function/method call or new expression.
     */
    private function resolveCallable(Node $node): ?string
    {
        if ($node instanceof Node\Expr\FuncCall) {
            if ($node->name instanceof Node\Name) {
                return $node->name->toString();
            }

            return $this->printer->prettyPrintExpr($node->name);
        }

        if ($node instanceof Node\Expr\MethodCall) {
            return $this->printer->prettyPrintExpr($node->var).'->'.$this->resolveMethodName($node->name);
        }

        if ($node instanceof Node\Expr\StaticCall) {
            return $this->resolveClassName($node->class).'::'.$this->resolveMethodName($node->name);
        }

        if ($node instanceof Node\Expr\New_) {
            return 'new '.$this->resolveClassName($node->class);
        }

        return null;
    }

    /**
     * Resolve a class name from a Name node or expression.
     *
     * @param Node\Name|Node\Expr $class
     */
    private function resolveClassName(Node $class): string
    {
        if ($class instanceof Node\Name) {
            return $class->toString();
        }

        return $this->printer->prettyPrintExpr($class);
    }

    /**
     * Resolve a method name from an Identifier node or expression.
     *
     * @param Node\Identifier|Node\Expr $name
     */
    private function resolveMethodName(Node $name): string
    {
        if ($name instanceof Node\Identifier) {
            return $name->name;
        }

        return $this->printer->prettyPrintExpr($name);
    }

    /**
     * @param Node\Expr\FuncCall|Node\Expr\MethodCall|Node\Expr\StaticCall|Node\Expr\New_ $node
     */
    private function extractArgs($node): void
    {
        if (!\is_array($node->args)) {
            return;
        }

        foreach ($node->args as $arg) {
            if ($arg instanceof Node\Arg) {
                $this->arguments[] = $this->printer->prettyPrintExpr($arg->value);
            }
        }
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
