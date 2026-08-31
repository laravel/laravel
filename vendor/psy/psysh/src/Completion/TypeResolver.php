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
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Parser;
use Psy\CodeCleaner;
use Psy\Context;
use Psy\ParserFactory;
use Psy\Util\Docblock;

/**
 * Resolves types from expressions.
 *
 * Uses runtime reflection (via Context) to determine types of variables
 * and expressions for type-aware completions.
 */
class TypeResolver
{
    private const SCALAR_TYPES = [
        'null', 'bool', 'boolean', 'int', 'integer', 'float', 'double',
        'string', 'array', 'object', 'resource', 'mixed', 'void',
        'callable', 'iterable', 'never',
    ];

    private Context $context;
    private ?CodeCleaner $cleaner;
    private Parser $parser;

    public function __construct(Context $context, ?CodeCleaner $cleaner = null)
    {
        $this->context = $context;
        $this->cleaner = $cleaner;
        $this->parser = (new ParserFactory())->createParser();
    }

    /**
     * Resolve all possible types of an expression (supports union types).
     *
     * For expressions that resolve to a union type (e.g., Address|City),
     * returns all non-scalar types in the union.
     *
     * @return string[] Array of fully-qualified class names
     */
    public function resolveTypes(string $expression): array
    {
        $expression = \trim($expression);

        if ($expression === '') {
            return [];
        }

        $parsedTypes = $this->resolveTypesFromParsedExpression($expression);
        if (!empty($parsedTypes)) {
            return $parsedTypes;
        }

        $cleanedExpression = $this->cleanIncompleteExpression($expression);
        if ($cleanedExpression === '') {
            return [];
        }

        if ($cleanedExpression !== $expression) {
            $cleanedTypes = $this->resolveTypesFromParsedExpression($cleanedExpression);
            if (!empty($cleanedTypes)) {
                return $cleanedTypes;
            }
        }

        if ($cleanedExpression[0] === '$') {
            $type = $this->resolveVariableType($cleanedExpression);

            if ($type !== null && !$this->isScalarOrNull($type)) {
                return [$type];
            }

            return [];
        }

        // Special keywords must come before isClassName check
        if (\in_array(\strtolower($cleanedExpression), ['self', 'static', 'parent'])) {
            $type = $this->resolveSpecialKeyword($cleanedExpression);

            return $type !== null ? [$type] : [];
        }

        if ($this->isClassName($cleanedExpression)) {
            return [$this->resolveClassName($cleanedExpression)];
        }

        return [];
    }

    /**
     * Resolve all possible types from an AST node.
     *
     * Falls back to string parsing for tokenizer-only contexts where we don't
     * have a parseable node.
     *
     * @param Node        $node               Parsed expression node
     * @param string|null $fallbackExpression Fallback expression for non-parse cases
     *
     * @return string[] Array of fully-qualified class names
     */
    public function resolveNodeTypes(Node $node, ?string $fallbackExpression = null): array
    {
        $types = $this->resolveExpressionTypes($node);
        if (!empty($types)) {
            return $types;
        }

        if ($fallbackExpression !== null && $fallbackExpression !== '') {
            return $this->resolveTypes($fallbackExpression);
        }

        return [];
    }

    /**
     * Resolve the actual runtime value of an expression.
     *
     * Currently only resolves simple variables ($foo). Method chains and
     * complex expressions are not evaluated to avoid side effects.
     *
     * @return mixed The actual value, or null if it can't be resolved
     */
    public function resolveValue(string $expression)
    {
        $expression = \trim($expression);

        if ($expression === '' || \strpos($expression, '$') !== 0) {
            return null;
        }

        $varName = \ltrim($expression, '$');

        try {
            return $this->context->get($varName);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Clean up incomplete/malformed expressions to extract the completable part.
     *
     * Handles cases like:
     * - $user|           -> $user
     * - $user . "        -> $user
     * - [$user           -> $user
     * - if (true) { $user -> $user
     * - $x + $user       -> $user
     *
     * Uses tokenization to find the last complete variable or chain expression.
     *
     * @return string Cleaned expression (may be empty if nothing extractable)
     */
    private function cleanIncompleteExpression(string $expression): string
    {
        if (\strpos($expression, '$') === false) {
            return $expression;
        }

        // For method chains, check if they parse cleanly first. If so, return
        // as-is and let resolution handle incomplete identifiers via union types.
        if (\strpos($expression, '->') !== false) {
            try {
                $this->parser->parse('<?php '.$expression.';');

                return $expression;
            } catch (PhpParserError $e) {
                // Parse failed, needs cleaning
            }
        }

        $tokens = @\token_get_all('<?php '.$expression);
        if (empty($tokens)) {
            return '';
        }

        \array_shift($tokens);

        $variables = [];

        for ($i = 0; $i < \count($tokens); $i++) {
            $token = $tokens[$i];

            if (!(\is_array($token) && $token[0] === \T_VARIABLE)) {
                continue;
            }

            $varStart = $i;
            $chainEnd = $i;
            $hasIncompleteCall = false;

            // Walk forward through -> chains
            for ($j = $i + 1; $j < \count($tokens); $j++) {
                $nextToken = $tokens[$j];

                if (\is_array($nextToken) && $nextToken[0] === \T_WHITESPACE) {
                    continue;
                }

                if (!$this->isObjectAccessOperatorToken($nextToken)) {
                    break;
                }

                // Found ->, look for identifier
                for ($k = $j + 1; $k < \count($tokens); $k++) {
                    $afterArrow = $tokens[$k];

                    if (\is_array($afterArrow) && $afterArrow[0] === \T_WHITESPACE) {
                        continue;
                    }

                    if (!(\is_array($afterArrow) && $afterArrow[0] === \T_STRING)) {
                        break;
                    }

                    $chainEnd = $k;

                    // Check for parenthesized call
                    for ($m = $k + 1; $m < \count($tokens); $m++) {
                        $parenToken = $tokens[$m];

                        if (\is_array($parenToken) && $parenToken[0] === \T_WHITESPACE) {
                            continue;
                        }

                        if ($parenToken === '(') {
                            $depth = 1;
                            $foundClose = false;
                            for ($n = $m + 1; $n < \count($tokens); $n++) {
                                if ($tokens[$n] === '(') {
                                    $depth++;
                                } elseif ($tokens[$n] === ')') {
                                    $depth--;
                                    if ($depth === 0) {
                                        $chainEnd = $n;
                                        $foundClose = true;
                                        break;
                                    }
                                }
                            }

                            if (!$foundClose) {
                                $hasIncompleteCall = true;
                            }
                        }

                        break;
                    }

                    $i = $chainEnd;
                    break;
                }
            }

            $variables[] = [
                    'start'             => $varStart,
                    'end'               => $chainEnd,
                    'hasIncompleteCall' => $hasIncompleteCall,
                ];
        }

        if (empty($variables)) {
            return '';
        }

        $lastVar = \end($variables);
        $extractedTokens = \array_slice($tokens, $lastVar['start'], $lastVar['end'] - $lastVar['start'] + 1);

        $parts = [];
        $strippedIncompleteCall = $lastVar['hasIncompleteCall'];

        foreach ($extractedTokens as $token) {
            if (\is_array($token)) {
                $parts[] = $token[1];
                continue;
            }

            // Stop at incomplete opening paren (no matching close)
            if ($token === '(') {
                $hasClose = false;
                foreach ($extractedTokens as $t) {
                    if ($t === ')') {
                        $hasClose = true;
                        break;
                    }
                }
                if (!$hasClose) {
                    $strippedIncompleteCall = true;
                    break;
                }
            }

            $parts[] = $token;
        }

        $result = \trim(\implode('', $parts));

        // Complete stripped incomplete calls so they parse as method calls
        if ($strippedIncompleteCall && $result !== '' && \substr($result, -2) !== '()') {
            $result .= '()';
        }

        if ($result !== '' && $this->isFollowedByNonCompletableOperator($expression, $result)) {
            return '';
        }

        return $result;
    }

    /**
     * Parse an expression and resolve types directly from its AST.
     *
     * Returns null if the expression cannot be parsed.
     *
     * @return string[]|null
     */
    private function resolveTypesFromParsedExpression(string $expression): ?array
    {
        try {
            $stmts = $this->parser->parse('<?php '.$expression.';');
        } catch (PhpParserError $e) {
            return null;
        }

        if (empty($stmts) || !$stmts[0] instanceof Node\Stmt\Expression) {
            return [];
        }

        return $this->resolveExpressionTypes($stmts[0]->expr);
    }

    /**
     * Check whether a token is -> or ?->.
     *
     * @param mixed $token Token from token_get_all
     */
    private function isObjectAccessOperatorToken($token): bool
    {
        if (!\is_array($token)) {
            return false;
        }

        if ($token[0] === \T_OBJECT_OPERATOR) {
            return true;
        }

        if (\defined('T_NULLSAFE_OBJECT_OPERATOR')) {
            /** @var int $nullsafeToken */
            $nullsafeToken = \constant('T_NULLSAFE_OBJECT_OPERATOR');

            return $token[0] === $nullsafeToken;
        }

        return false;
    }

    /**
     * Check if an identifier looks like an incomplete identifier (for tab completion).
     *
     * Short identifiers or common prefixes are likely incomplete during tab completion.
     *
     * @param string $identifier The identifier to check
     *
     * @return bool True if likely incomplete
     */
    private function looksLikeIncompleteIdentifier(string $identifier): bool
    {
        // Very short identifiers (1-5 chars) are often incomplete
        if (\strlen($identifier) <= 5) {
            return true;
        }

        // Common method prefixes that might be incomplete (6-7 chars)
        // e.g., "getCit" (6 chars) vs "nonExistent" (11 chars)
        $incompletePrefixes = ['get', 'set', 'is', 'has', 'can', 'find'];

        foreach ($incompletePrefixes as $prefix) {
            if (\stripos($identifier, $prefix) === 0 && \strlen($identifier) <= 7) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an extracted expression is followed by operators that mean
     * we shouldn't provide type completion.
     *
     * E.g., "$user . \""; user is concatenating, not completing
     *
     * @param string $original  Original full expression
     * @param string $extracted Extracted variable/chain
     *
     * @return bool True if followed by non-completable operator
     */
    private function isFollowedByNonCompletableOperator(string $original, string $extracted): bool
    {
        $pos = \strpos($original, $extracted);
        if ($pos === false) {
            return false;
        }

        $afterExtracted = \ltrim(\substr($original, $pos + \strlen($extracted)));

        if ($afterExtracted === '' || \strpos($afterExtracted, '->') === 0) {
            return false;
        }

        $nonCompletableOps = ['|', '.', '+', '-', '*', '/', '%', '&', '^', '~', '<', '>', '=', '!', '?', ':'];

        return \in_array($afterExtracted[0], $nonCompletableOps, true);
    }

    /**
     * Resolve all possible types of an AST expression node (supports union types).
     *
     * This is the core method that walks the AST tree and resolves types.
     *
     * @return string[] Array of fully-qualified class names
     */
    private function resolveExpressionTypes($expr): array
    {
        if (
            $expr instanceof MethodCall
            || $expr instanceof PropertyFetch
            || $expr instanceof NullsafeMethodCall
            || $expr instanceof NullsafePropertyFetch
        ) {
            return $this->resolveObjectMemberTypes($expr);
        }

        if ($expr instanceof StaticCall) {
            $classTypes = $this->resolveExpressionTypes($expr->class);
            $methodName = $expr->name instanceof Identifier ? $expr->name->name : null;

            if (empty($classTypes) || $methodName === null) {
                return [];
            }

            return $this->resolveTypesAcrossClasses($classTypes, fn ($class) => $this->resolveMethodReturnTypes($class, $methodName));
        }

        if ($expr instanceof StaticPropertyFetch) {
            $classTypes = $this->resolveExpressionTypes($expr->class);
            $propertyName = $expr->name instanceof Identifier ? $expr->name->name : null;

            if ($propertyName === null && $expr->name instanceof Variable && \is_string($expr->name->name)) {
                $propertyName = $expr->name->name;
            }

            if (empty($classTypes) || $propertyName === null) {
                return [];
            }

            return $this->resolveTypesAcrossClasses($classTypes, fn ($class) => $this->resolvePropertyTypes($class, $propertyName));
        }

        if ($expr instanceof Variable && \is_string($expr->name)) {
            $type = $this->resolveVariableType('$'.$expr->name);

            if ($type === null || $this->isScalarOrNull($type)) {
                return [];
            }

            return [$type];
        }

        if ($expr instanceof Name) {
            $className = $expr->toString();

            if (\in_array(\strtolower($className), ['self', 'static', 'parent'])) {
                $type = $this->resolveSpecialKeyword($className);

                return $type !== null ? [$type] : [];
            }

            return [$this->resolveClassName($className)];
        }

        if ($expr instanceof Node\Expr\Ternary) {
            // Short ternary ($x ?: $y) uses the condition as the "if" branch
            $ifTypes = $expr->if !== null
                ? $this->resolveExpressionTypes($expr->if)
                : $this->resolveExpressionTypes($expr->cond);

            $elseTypes = $expr->else !== null
                ? $this->resolveExpressionTypes($expr->else)
                : [];

            return \array_values(\array_unique(\array_merge($ifTypes, $elseTypes)));
        }

        return [];
    }

    /**
     * Resolve types for an object member access (method call or property fetch).
     *
     * Handles the common pattern of resolving the object's types, looking up the
     * member, and falling back to parent types for incomplete identifiers during
     * tab completion.
     *
     * @param MethodCall|PropertyFetch|NullsafeMethodCall|NullsafePropertyFetch $expr
     *
     * @return string[] Array of fully-qualified class names
     */
    private function resolveObjectMemberTypes($expr): array
    {
        $objectTypes = $this->resolveExpressionTypes($expr->var);
        if (empty($objectTypes)) {
            return [];
        }

        $memberName = $expr->name instanceof Identifier ? $expr->name->name : null;
        if ($memberName === null) {
            return [];
        }

        $isMethod = $expr instanceof MethodCall || $expr instanceof NullsafeMethodCall;
        $reflectionCheck = $isMethod ? 'hasMethod' : 'hasProperty';

        $memberExists = false;
        foreach ($objectTypes as $objectType) {
            try {
                $reflection = new \ReflectionClass($objectType);
                if ($reflection->$reflectionCheck($memberName)) {
                    $memberExists = true;
                    break;
                }
            } catch (\ReflectionException $e) {
                // Class doesn't exist
            }
        }

        $resolver = $isMethod
            ? fn ($class) => $this->resolveMethodReturnTypes($class, $memberName)
            : fn ($class) => $this->resolvePropertyTypes($class, $memberName);

        $resolvedTypes = $this->resolveTypesAcrossClasses($objectTypes, $resolver);

        // For tab completion: if member doesn't exist and looks incomplete,
        // return the parent object types so completion can suggest matching members
        if (empty($resolvedTypes) && !$memberExists && $this->looksLikeIncompleteIdentifier($memberName)) {
            return $objectTypes;
        }

        return $resolvedTypes;
    }

    /**
     * Resolve types by applying a resolver callback across multiple class names.
     *
     * @param string[] $classTypes Array of class names to resolve across
     * @param callable $resolver   Callback that takes a class name and returns string[]
     *
     * @return string[] Array of unique fully-qualified class names
     */
    private function resolveTypesAcrossClasses(array $classTypes, callable $resolver): array
    {
        $types = [];
        foreach ($classTypes as $classType) {
            $types = \array_merge($types, $resolver($classType));
        }

        return \array_values(\array_unique($types));
    }

    /**
     * Resolve all possible return types of a method (supports union types).
     *
     * @param string $className  Fully-qualified class name
     * @param string $methodName Method name
     *
     * @return string[] Array of fully-qualified class names
     */
    private function resolveMethodReturnTypes(string $className, string $methodName): array
    {
        try {
            $reflection = new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            return [];
        }

        if (!$reflection->hasMethod($methodName)) {
            return [];
        }

        try {
            $method = $reflection->getMethod($methodName);
        } catch (\ReflectionException $e) {
            return [];
        }

        if ($method->hasReturnType()) {
            $returnType = $method->getReturnType();
            if (!$returnType instanceof \ReflectionType) {
                return [];
            }

            return $this->extractClassNamesFromType($returnType, $reflection);
        }

        return $this->resolveReturnTypesFromDocblock($method, $reflection);
    }

    /**
     * Resolve all possible types of a property (supports union types).
     *
     * @param string $className    Fully-qualified class name
     * @param string $propertyName Property name
     *
     * @return string[] Array of fully-qualified class names
     */
    private function resolvePropertyTypes(string $className, string $propertyName): array
    {
        try {
            $reflection = new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            return [];
        }

        if (!$reflection->hasProperty($propertyName)) {
            return [];
        }

        try {
            $property = $reflection->getProperty($propertyName);
        } catch (\ReflectionException $e) {
            return [];
        }

        if ($property->hasType()) {
            $type = $property->getType();
            if (!$type instanceof \ReflectionType) {
                return [];
            }

            return $this->extractClassNamesFromType($type, $reflection);
        }

        return $this->resolvePropertyTypesFromDocblock($property, $reflection);
    }

    /**
     * Extract all class names from a ReflectionType (handles all composite types).
     *
     * Recursively processes:
     * - Named types: Returns class name if not builtin
     * - Union types (A|B): Returns all class names from union members
     * - Intersection types (A&B): Returns all class names (completion shows both)
     * - DNF types ((A&B)|C): Returns all class names from nested types
     *
     * For completion purposes, we're liberal: any mentioned class is a valid
     * completion candidate, even if the actual runtime type is more restrictive.
     *
     * @param \ReflectionType  $type         The reflection type to extract from
     * @param \ReflectionClass $classContext Class context for resolving relative names
     *
     * @return string[] Array of fully-qualified class names
     */
    private function extractClassNamesFromType(\ReflectionType $type, \ReflectionClass $classContext): array
    {
        if ($type instanceof \ReflectionNamedType) {
            if ($type->isBuiltin()) {
                return [];
            }

            return [$this->resolveTypeName($type->getName(), $classContext)];
        }

        $compositeTypes = $this->getCompositeTypes($type);
        if (!empty($compositeTypes)) {
            $types = [];
            foreach ($compositeTypes as $compositeType) {
                $types = \array_merge($types, $this->extractClassNamesFromType($compositeType, $classContext));
            }

            return \array_values(\array_unique($types));
        }

        return [];
    }

    /**
     * Safely fetch child types for composite reflection types (union/intersection/DNF).
     *
     * @return \ReflectionType[]
     */
    private function getCompositeTypes(\ReflectionType $type): array
    {
        if (!\method_exists($type, 'getTypes')) {
            return [];
        }

        // @phan-suppress-next-line PhanUndeclaredMethod, available for composite types, guarded by method_exists
        $types = $type->getTypes();

        if (!\is_array($types)) {
            return [];
        }

        return \array_values(\array_filter($types, fn ($candidate) => $candidate instanceof \ReflectionType));
    }

    /**
     * Resolve a type name in the context of a class (handle relative names).
     *
     * @param string           $typeName     Type name (might be relative: "Foo" or absolute: "\Foo\Bar")
     * @param \ReflectionClass $classContext The class context for resolving relative names
     *
     * @return string Fully-qualified class name
     */
    private function resolveTypeName(string $typeName, \ReflectionClass $classContext): string
    {
        if (\in_array(\strtolower($typeName), ['self', 'static'])) {
            return $classContext->getName();
        }

        if (\strtolower($typeName) === 'parent') {
            $parent = $classContext->getParentClass();

            return $parent ? $parent->getName() : $typeName;
        }

        if ($typeName[0] === '\\') {
            return \ltrim($typeName, '\\');
        }

        // PHP ReflectionType returns FQN without leading backslash
        if (\class_exists($typeName) || \interface_exists($typeName) || \trait_exists($typeName)) {
            return $typeName;
        }

        $namespace = $classContext->getNamespaceName();
        if ($namespace) {
            $fqn = $namespace.'\\'.$typeName;
            if (\class_exists($fqn) || \interface_exists($fqn) || \trait_exists($fqn)) {
                return $fqn;
            }
        }

        return $typeName;
    }

    /**
     * Resolve return types from @return docblock (supports union types).
     *
     * @return string[] Array of fully-qualified class names
     */
    private function resolveReturnTypesFromDocblock(\ReflectionMethod $method, \ReflectionClass $classContext): array
    {
        $docblock = new Docblock($method);

        if (empty($docblock->tags['return'])) {
            return [];
        }

        $returnTag = $docblock->tags['return'][0];
        if (!isset($returnTag['type'])) {
            return [];
        }

        return $this->parseDocblockTypes($returnTag['type'], $classContext);
    }

    /**
     * Resolve property types from @var docblock (supports union types).
     *
     * @return string[] Array of fully-qualified class names
     */
    private function resolvePropertyTypesFromDocblock(\ReflectionProperty $property, \ReflectionClass $classContext): array
    {
        $docblock = new Docblock($property);

        if (empty($docblock->tags['var'])) {
            return [];
        }

        $varTag = $docblock->tags['var'][0];
        if (!isset($varTag['type'])) {
            return [];
        }

        return $this->parseDocblockTypes($varTag['type'], $classContext);
    }

    /**
     * Parse a docblock type string and resolve to fully-qualified class names (supports unions).
     *
     * Handles:
     * - Simple types: "Foo", "\\Foo\\Bar"
     * - Nullable types: "?Foo"
     * - Union types: "Foo|Bar|null" (returns ALL non-scalar types)
     * - Array types: "Foo[]" (returns empty since we can't determine array element type)
     * - Generic types: "array<Foo>" or "Collection<Foo>" (extracts Foo)
     *
     * @param string           $typeString   The type from docblock
     * @param \ReflectionClass $classContext Context for resolving relative names
     *
     * @return string[] Array of fully-qualified class names
     */
    private function parseDocblockTypes(string $typeString, \ReflectionClass $classContext): array
    {
        $typeString = \trim($typeString);
        if ($typeString === '') {
            return [];
        }

        if ($typeString[0] === '?') {
            $typeString = \substr($typeString, 1);
        }

        if (\strpos($typeString, '|') !== false) {
            $result = [];
            foreach (\explode('|', $typeString) as $type) {
                $type = \trim($type);
                if (!$this->isScalarOrNull($type)) {
                    $result = \array_merge($result, $this->parseDocblockTypes($type, $classContext));
                }
            }

            return \array_values(\array_unique($result));
        }

        if (\substr($typeString, -2) === '[]') {
            return [];
        }

        if (\preg_match('/^[\w\\\\]+<(.+)>$/', $typeString, $matches)) {
            return $this->parseDocblockTypes($matches[1], $classContext);
        }

        if ($this->isScalarOrNull($typeString)) {
            return [];
        }

        return [$this->resolveTypeName($typeString, $classContext)];
    }

    /**
     * Check if a type string represents a scalar or null.
     *
     * @return bool
     */
    private function isScalarOrNull(string $type): bool
    {
        return \in_array(\strtolower($type), self::SCALAR_TYPES, true);
    }

    /**
     * Resolve type of a variable.
     */
    private function resolveVariableType(string $variable): ?string
    {
        $varName = \ltrim($variable, '$');

        try {
            $value = $this->context->get($varName);

            return \is_object($value) ? \get_class($value) : \gettype($value);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Resolve a class name (handle namespaces and use statements).
     */
    private function resolveClassName(string $name): string
    {
        if (\strpos($name, '\\') === 0) {
            return $name;
        }

        if ($this->cleaner !== null) {
            $resolved = $this->cleaner->resolveClassName($name);
            if ($resolved !== $name) {
                return $resolved;
            }
        }

        return $name;
    }

    /**
     * Resolve special keywords (self, static, parent).
     */
    private function resolveSpecialKeyword(string $keyword): ?string
    {
        $keyword = \strtolower($keyword);

        $boundClass = $this->context->getBoundClass();
        if ($boundClass === null) {
            $boundObject = $this->context->getBoundObject();
            $boundClass = $boundObject !== null ? \get_class($boundObject) : null;
        }

        if ($boundClass === null) {
            return null;
        }

        if ($keyword === 'self' || $keyword === 'static') {
            return $boundClass;
        }

        if ($keyword === 'parent') {
            $parent = (new \ReflectionClass($boundClass))->getParentClass();

            return $parent ? $parent->getName() : null;
        }

        return null;
    }

    /**
     * Check if a string looks like a class name.
     */
    private function isClassName(string $name): bool
    {
        return \preg_match('/^[\\\\a-zA-Z_][\\\\a-zA-Z0-9_]*$/', $name) === 1;
    }
}
