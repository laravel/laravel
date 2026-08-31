<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\ExecutionLoop;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter;
use ReflectionClass;

/**
 * AST visitor that reloads code definitions using uopz.
 *
 * Traverses the parsed AST and uses uopz to reload:
 * - Class methods (via uopz_set_return with closure)
 * - Functions (via uopz_set_return)
 * - Class and global constants (via uopz_redefine)
 *
 * Safety checks:
 * - Conditional code (functions/constants inside if blocks) is skipped by default
 *   because reloading may not match runtime conditions
 * - Static variables in functions/methods trigger a warning (state will reset)
 * - Structural changes (new properties, inheritance) cannot be applied
 *
 * When force-reload is enabled (via `yolo` command), safety checks are bypassed
 * and the code is reloaded anyway.
 */
class UopzReloaderVisitor extends NodeVisitorAbstract
{
    private PrettyPrinter\Standard $printer;

    /** @var bool Whether to bypass safety warnings */
    private bool $forceReload;

    private string $namespace = '';
    private ?string $currentClass = null;
    private ?string $currentFunction = null;

    /** @var string[] Warning messages generated during traversal */
    private array $warnings = [];

    /** @var bool Whether any elements were skipped (not force-reloaded) */
    private bool $hasSkips = false;

    /** @var int Nesting depth inside conditional/control structures */
    private int $conditionalDepth = 0;

    /**
     * @param bool $forceReload Whether to bypass safety warnings
     */
    public function __construct(PrettyPrinter\Standard $printer, bool $forceReload = false)
    {
        $this->printer = $printer;
        $this->forceReload = $forceReload;
    }

    /**
     * Check if any warnings were generated during reloading.
     */
    public function hasWarnings(): bool
    {
        return \count($this->warnings) > 0;
    }

    /**
     * Get all warnings generated during reloading.
     *
     * @return string[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Check if any elements were skipped during reloading.
     */
    public function hasSkips(): bool
    {
        return $this->hasSkips;
    }

    /**
     * Add a warning message.
     */
    private function addWarning(string $message): void
    {
        $this->warnings[] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        // Track namespace
        if ($node instanceof Stmt\Namespace_) {
            $this->namespace = $node->name ? $node->name->toString() : '';
        }

        // Track current class and check for limitations
        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Interface_ || $node instanceof Stmt\Trait_) {
            $name = $node->name ? $node->name->toString() : null;
            $this->currentClass = $name ? $this->getFullyQualifiedName($name) : null;

            if ($this->currentClass) {
                $this->checkClassLimitations($this->currentClass, $node);
            }
        }

        // Track when we enter conditional/control structures at global scope
        if ($this->currentClass === null && $this->currentFunction === null && $this->isControlStructure($node)) {
            $this->conditionalDepth++;
        }

        // Detect side effects at global/namespace scope (but not if we're already tracking it as conditional)
        if ($this->currentClass === null && $this->currentFunction === null && $this->conditionalDepth === 0) {
            $this->checkForSideEffects($node);
        }

        // Reload class methods
        if ($node instanceof Stmt\ClassMethod && $this->currentClass) {
            $this->reloadMethod($this->currentClass, $node);
        }

        // Reload functions (skip if inside conditional, unless force mode)
        if ($node instanceof Stmt\Function_) {
            // Track that we're entering a function
            $this->currentFunction = $node->name->toString();

            if ($this->conditionalDepth > 0 && $this->currentClass === null) {
                $funcName = $node->name->toString();
                $snippet = \sprintf('if (...) { function %s() ... }', $funcName);

                if ($this->forceReload) {
                    $this->addWarning(\sprintf('YOLO: Force-reloaded %s', $snippet));
                    $this->reloadFunction($node);
                } else {
                    $this->addWarning(\sprintf('Skipped conditional: %s (use `yolo` to force)', $snippet));
                    $this->hasSkips = true;
                }
            } else {
                $this->reloadFunction($node);
            }
        }

        // Reload constants
        if ($node instanceof Stmt\ClassConst && $this->currentClass) {
            $this->reloadClassConstants($this->currentClass, $node);
        }

        if ($node instanceof Stmt\Const_) {
            if ($this->conditionalDepth > 0 && $this->currentClass === null) {
                $constNode = $node->consts[0] ?? null;
                $constName = $constNode ? $constNode->name->toString() : 'CONST';
                $snippet = \sprintf('if (...) { const %s = ...; }', $constName);

                if ($this->forceReload) {
                    $this->addWarning(\sprintf('YOLO: Force-reloaded %s', $snippet));
                    $this->reloadGlobalConstants($node);
                } else {
                    $this->addWarning(\sprintf('Skipped conditional: %s (use `yolo` to force)', $snippet));
                    $this->hasSkips = true;
                }
            } else {
                $this->reloadGlobalConstants($node);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        // Clear current class when leaving class/interface/trait
        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Interface_ || $node instanceof Stmt\Trait_) {
            $this->currentClass = null;
        }

        // Clear current function when leaving function
        if ($node instanceof Stmt\Function_) {
            $this->currentFunction = null;
        }

        // Track when we leave conditional/control structures at global scope
        if ($this->currentClass === null && $this->currentFunction === null && $this->isControlStructure($node)) {
            $this->conditionalDepth--;
        }

        return null;
    }

    /**
     * Reload a class method using uopz_set_return.
     */
    private function reloadMethod(string $className, Stmt\ClassMethod $method): void
    {
        $methodName = $method->name->toString();

        // Skip abstract methods
        if ($method->isAbstract()) {
            return;
        }

        // Check for static variables in method body
        if ($this->hasStaticVariables($method->stmts)) {
            $snippet = \sprintf('%s::%s() { static $var = ...; }', $className, $methodName);
            $this->addWarning(\sprintf('Static vars will reset: %s', $snippet));
        }

        $closure = $this->createClosure($method->params, $method->stmts, $method->returnType);
        if ($closure !== null) {
            try {
                \uopz_set_return($className, $methodName, $closure, true);
            } catch (\Throwable $e) {
                $this->addWarning(\sprintf('Failed to reload %s::%s(): %s', $className, $methodName, $e->getMessage()));
            }
        }
    }

    /**
     * Reload a function using uopz_set_return.
     */
    private function reloadFunction(Stmt\Function_ $function): void
    {
        $functionName = $this->getFullyQualifiedName($function->name->toString());

        // New function; just define it via eval
        if (!\function_exists($functionName)) {
            try {
                $code = '';
                if ($this->namespace !== '') {
                    $code .= 'namespace '.$this->namespace.'; ';
                }
                $code .= $this->printer->prettyPrint([$function]);
                eval($code);
            } catch (\Throwable $e) {
                $this->addWarning(\sprintf('Failed to add %s(): %s', $functionName, $e->getMessage()));
            }

            return;
        }

        // Existing function; check for static variables (state will reset on reload)
        if ($this->hasStaticVariables($function->stmts)) {
            $snippet = \sprintf('%s() { static $var = ...; }', $functionName);
            $this->addWarning(\sprintf('Static vars will reset: %s', $snippet));
        }

        // Use uopz to override existing function
        $closure = $this->createClosure($function->params, $function->stmts, $function->returnType);
        if ($closure !== null) {
            try {
                \uopz_set_return($functionName, $closure, true);
            } catch (\Throwable $e) {
                $this->addWarning(\sprintf('Failed to reload %s(): %s', $functionName, $e->getMessage()));
            }
        }
    }

    /**
     * Create a closure from parameters and statements.
     *
     * @param Node\Param[] $params
     * @param Stmt[]|null  $stmts
     * @param Node|null    $returnType
     *
     * @return \Closure|null
     */
    private function createClosure(array $params, ?array $stmts, ?Node $returnType = null): ?\Closure
    {
        $paramStrs = [];
        foreach ($params as $param) {
            $paramStr = '';

            if ($param->type) {
                $paramStr .= $this->printer->prettyPrint([$param->type]).' ';
            }

            if ($param->variadic) {
                $paramStr .= '...';
            }

            if ($param->byRef) {
                $paramStr .= '&';
            }

            $paramStr .= '$'.$param->var->name;

            if ($param->default) {
                $paramStr .= ' = '.$this->printer->prettyPrintExpr($param->default);
            }

            $paramStrs[] = $paramStr;
        }

        $paramList = \implode(', ', $paramStrs);

        $returnTypeStr = '';
        if ($returnType !== null) {
            $returnTypeStr = ': '.$this->printer->prettyPrint([$returnType]);
        }

        $body = '';
        if ($stmts) {
            $bodyStmts = [];
            foreach ($stmts as $stmt) {
                $bodyStmts[] = $this->printer->prettyPrint([$stmt]);
            }
            $body = \implode("\n", $bodyStmts);
        }

        $closureCode = \sprintf("return function(%s)%s {\n%s\n};", $paramList, $returnTypeStr, $body);

        try {
            return eval($closureCode);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Reload class constants using uopz_redefine.
     */
    private function reloadClassConstants(string $className, Stmt\ClassConst $const): void
    {
        foreach ($const->consts as $constNode) {
            $constName = $constNode->name->toString();
            $value = $this->evaluateConstValue($constNode->value);

            try {
                \uopz_redefine($className, $constName, $value);
            } catch (\Throwable $e) {
                $this->addWarning(\sprintf('Failed to reload %s::%s: %s', $className, $constName, $e->getMessage()));
            }
        }
    }

    /**
     * Reload global constants using uopz_redefine.
     */
    private function reloadGlobalConstants(Stmt\Const_ $const): void
    {
        foreach ($const->consts as $constNode) {
            $constName = $this->getFullyQualifiedName($constNode->name->toString());
            $value = $this->evaluateConstValue($constNode->value);

            try {
                \uopz_redefine($constName, $value);
            } catch (\Throwable $e) {
                $this->addWarning(\sprintf('Failed to reload %s: %s', $constName, $e->getMessage()));
            }
        }
    }

    /**
     * Evaluate a constant value from AST node.
     *
     * @return mixed
     */
    private function evaluateConstValue(Expr $expr)
    {
        // For simple scalar values, we can evaluate directly
        try {
            $code = '<?php return '.$this->printer->prettyPrintExpr($expr).';';

            return eval(\substr($code, 6));
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get a fully-qualified name (class, function, constant, etc).
     */
    private function getFullyQualifiedName(string $name): string
    {
        if ($this->namespace && \strpos($name, '\\') !== 0) {
            return $this->namespace.'\\'.$name;
        }

        return $name;
    }

    /**
     * Check if a node is a control structure.
     */
    private function isControlStructure(Node $node): bool
    {
        return $node instanceof Stmt\If_ ||
               $node instanceof Stmt\Switch_ ||
               $node instanceof Stmt\For_ ||
               $node instanceof Stmt\Foreach_ ||
               $node instanceof Stmt\While_ ||
               $node instanceof Stmt\Do_ ||
               $node instanceof Stmt\TryCatch;
    }

    /**
     * Check if statements contain static variable declarations.
     *
     * @param Stmt[]|null $stmts
     */
    private function hasStaticVariables(?array $stmts): bool
    {
        if ($stmts === null) {
            return false;
        }

        foreach ($stmts as $stmt) {
            // Direct static declaration
            if ($stmt instanceof Stmt\Static_) {
                return true;
            }

            // Recursively check nested structures (if/for/while/etc)
            if ($stmt instanceof Stmt\If_) {
                if ($this->hasStaticVariables($stmt->stmts)) {
                    return true;
                }
                foreach ($stmt->elseifs as $elseif) {
                    if ($this->hasStaticVariables($elseif->stmts)) {
                        return true;
                    }
                }
                if ($stmt->else && $this->hasStaticVariables($stmt->else->stmts)) {
                    return true;
                }
            }

            if ($stmt instanceof Stmt\For_ ||
                $stmt instanceof Stmt\Foreach_ ||
                $stmt instanceof Stmt\While_ ||
                $stmt instanceof Stmt\Do_) {
                if ($this->hasStaticVariables($stmt->stmts)) {
                    return true;
                }
            }

            if ($stmt instanceof Stmt\Switch_) {
                foreach ($stmt->cases as $case) {
                    if ($this->hasStaticVariables($case->stmts)) {
                        return true;
                    }
                }
            }

            if ($stmt instanceof Stmt\TryCatch) {
                if ($this->hasStaticVariables($stmt->stmts)) {
                    return true;
                }
                foreach ($stmt->catches as $catch) {
                    if ($this->hasStaticVariables($catch->stmts)) {
                        return true;
                    }
                }
                if ($stmt->finally && $this->hasStaticVariables($stmt->finally->stmts)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check for side effects that won't be re-executed on reload.
     *
     * Detects top-level code that has side effects (function calls, variable
     * assignments, etc.) which will not be re-run when the file is reloaded.
     */
    private function checkForSideEffects(Node $node): void
    {
        // Skip declarations (these are handled by uopz)
        if ($node instanceof Stmt\Class_ ||
            $node instanceof Stmt\Interface_ ||
            $node instanceof Stmt\Trait_ ||
            $node instanceof Stmt\Function_ ||
            $node instanceof Stmt\Const_ ||
            $node instanceof Stmt\Namespace_ ||
            $node instanceof Stmt\Use_) {
            return;
        }

        // Only check statements, not expressions (to avoid duplicate warnings)
        // Expression statements contain the actual expression
        if ($node instanceof Stmt\Expression) {
            $expr = $node->expr;
            $snippet = $this->printer->prettyPrintExpr($expr);

            // Truncate long snippets
            if (\strlen($snippet) > 50) {
                $snippet = \substr($snippet, 0, 47).'...';
            }

            $this->addWarning(\sprintf('Not re-run: %s', $snippet));

            return;
        }

        // Echo/print statements
        if ($node instanceof Stmt\Echo_) {
            $firstExpr = $node->exprs[0] ?? null;
            $snippet = $firstExpr !== null
                ? 'echo '.$this->printer->prettyPrintExpr($firstExpr)
                : 'echo ...';
            if (\strlen($snippet) > 50) {
                $snippet = \substr($snippet, 0, 47).'...';
            }
            $this->addWarning(\sprintf('Not re-run: %s', $snippet));

            return;
        }

        // Global variable declarations
        if ($node instanceof Stmt\Global_) {
            $varNames = [];
            foreach ($node->vars as $var) {
                if ($var instanceof Expr\Variable) {
                    $varNames[] = '$'.$var->name;
                }
            }
            $snippet = 'global '.\implode(', ', $varNames);
            $this->addWarning(\sprintf('Not re-run: %s', $snippet));

            return;
        }

        // Static variable declarations (inside functions are OK, but top-level would be unusual)
        if ($node instanceof Stmt\Static_) {
            $varNames = [];
            foreach ($node->vars as $var) {
                $varNames[] = '$'.$var->var->name;
            }
            $snippet = 'static '.\implode(', ', $varNames);
            $this->addWarning(\sprintf('Not re-run: %s', $snippet));

            return;
        }

        // If/switch/for/while/etc control structures at top level
        if ($this->isControlStructure($node)) {
            $type = 'if';
            if ($node instanceof Stmt\Switch_) {
                $type = 'switch';
            } elseif ($node instanceof Stmt\For_) {
                $type = 'for';
            } elseif ($node instanceof Stmt\Foreach_) {
                $type = 'foreach';
            } elseif ($node instanceof Stmt\While_) {
                $type = 'while';
            } elseif ($node instanceof Stmt\Do_) {
                $type = 'do-while';
            } elseif ($node instanceof Stmt\TryCatch) {
                $type = 'try-catch';
            }

            $this->addWarning(\sprintf('Not re-run: %s (...) { ... }', $type));

            return;
        }
    }

    /**
     * Check for known limitations when reloading a class.
     */
    private function checkClassLimitations(string $className, Node $node): void
    {
        // Check if class already exists
        if (!\class_exists($className, false) && !\interface_exists($className, false) && !\trait_exists($className, false)) {
            // New class/interface/trait - uopz cannot add these
            $type = $node instanceof Stmt\Interface_ ? 'interface' : ($node instanceof Stmt\Trait_ ? 'trait' : 'class');
            $this->addWarning(\sprintf('Cannot add %s %s', $type, $className));

            return;
        }

        // For existing classes, check for structural changes
        if (!($node instanceof Stmt\Class_)) {
            return;
        }

        // Check for new properties (cannot be added)
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Stmt\Property) {
                foreach ($stmt->props as $prop) {
                    $propName = $prop->name->toString();
                    if (!\property_exists($className, $propName)) {
                        $visibility = $stmt->isPublic() ? 'public' : ($stmt->isProtected() ? 'protected' : 'private');
                        $static = $stmt->isStatic() ? 'static ' : '';
                        $this->addWarning(\sprintf('Cannot add %s$%s', $static.$visibility.' ', $propName));
                    }
                }
            }

            // Check for new methods (will try to add but may fail silently)
            if ($stmt instanceof Stmt\ClassMethod) {
                $methodName = $stmt->name->toString();
                if (!\method_exists($className, $methodName)) {
                    $this->addWarning(\sprintf('Cannot add %s::%s()', $className, $methodName));
                }
            }
        }

        // Check for inheritance changes
        if ($node->extends) {
            $newParent = $node->extends->toString();
            $reflection = new ReflectionClass($className);
            $currentParent = $reflection->getParentClass();

            if ($currentParent && $currentParent->getName() !== $this->getFullyQualifiedName($newParent)) {
                $this->addWarning(\sprintf('Cannot change parent of %s', $className));
            }
        }

        // Check for interface changes
        if ($node->implements) {
            $newInterfaces = \array_map(function ($interface) {
                return $this->getFullyQualifiedName($interface->toString());
            }, $node->implements);

            $reflection = new ReflectionClass($className);
            $currentInterfaces = $reflection->getInterfaceNames();

            $added = \array_diff($newInterfaces, $currentInterfaces);
            $removed = \array_diff($currentInterfaces, $newInterfaces);

            if (\count($added) > 0 || \count($removed) > 0) {
                $this->addWarning(\sprintf('Cannot change interfaces of %s', $className));
            }
        }
    }
}
