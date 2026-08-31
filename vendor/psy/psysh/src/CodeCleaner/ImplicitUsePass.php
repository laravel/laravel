<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeCleaner;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified as FullyQualifiedName;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseItem;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Psy\CodeCleaner;

/**
 * Automatically add use statements for unqualified class references.
 *
 * When a user references a class by its short name (e.g., `User`), this pass attempts to find a
 * fully-qualified class name that matches. A use statement is added if:
 *
 * - There is no unqualified name (class/function/constant) with that short name
 * - There is no existing use statement or alias with that short name
 * - There is exactly one matching class/interface/trait in the configured namespaces
 *
 * For example, in a project with `App\Model\User` and `App\View\User` classes, if configured with
 * 'includeNamespaces' => ['App\Model'], `new User` would become `use App\Model\User; new User;`
 * even though there's also an `App\View\User` class.
 *
 * Works great with autoload warming (--warm-autoload) to pre-load classes.
 */
class ImplicitUsePass extends CodeCleanerPass
{
    private ?array $shortNameMap = null;
    private array $implicitUses = [];
    private array $seenNames = [];
    private array $existingAliases = [];
    private array $includeNamespaces = [];
    private array $excludeNamespaces = [];
    private ?string $currentNamespace = null;
    private ?CodeCleaner $cleaner = null;
    private ?PrettyPrinter $printer = null;

    /**
     * @param array            $config  Configuration array with 'includeNamespaces' and/or 'excludeNamespaces'
     * @param CodeCleaner|null $cleaner CodeCleaner instance for logging
     */
    public function __construct(array $config = [], ?CodeCleaner $cleaner = null)
    {
        $this->includeNamespaces = $this->normalizeNamespaces($config['includeNamespaces'] ?? []);
        $this->excludeNamespaces = $this->normalizeNamespaces($config['excludeNamespaces'] ?? []);
        $this->cleaner = $cleaner;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeTraverse(array $nodes)
    {
        if (empty($this->includeNamespaces) && empty($this->excludeNamespaces)) {
            return null;
        }

        $this->buildShortNameMap();

        // Reset state for this traversal
        $this->implicitUses = [];
        $this->seenNames = [];
        $this->existingAliases = [];
        $this->currentNamespace = null;

        $modified = false;

        // Collect use statements and seen names for each namespace
        foreach ($nodes as $node) {
            if ($node instanceof Namespace_) {
                $this->currentNamespace = $node->name ? $node->name->toString() : null;

                $perNamespaceAliases = [];
                $perNamespaceUses = [];
                $perNamespaceSeen = [];

                if ($node->stmts !== null) {
                    $this->collectAliasesInNodes($node->stmts, $perNamespaceAliases);
                    $this->collectNamesInNodes($node->stmts, $perNamespaceSeen, $perNamespaceAliases, $perNamespaceUses);
                }

                if (!empty($perNamespaceUses)) {
                    $this->logAddedUses($perNamespaceUses);
                    $node->stmts = \array_merge($this->createUseStatements($perNamespaceUses), $node->stmts ?? []);
                    $modified = true;
                }
            }
        }

        $hasNamespace = false;
        foreach ($nodes as $node) {
            if ($node instanceof Namespace_) {
                $hasNamespace = true;
                break;
            }
        }

        // Collect use statements and seen names for top-level namespace
        if (!$hasNamespace) {
            $this->currentNamespace = null;
            $topLevelAliases = [];
            $topLevelUses = [];
            $topLevelSeen = [];

            $this->collectAliasesInNodes($nodes, $topLevelAliases);
            $this->collectNamesInNodes($nodes, $topLevelSeen, $topLevelAliases, $topLevelUses);

            if (!empty($topLevelUses)) {
                $this->logAddedUses($topLevelUses);

                return \array_merge($this->createUseStatements($topLevelUses), $nodes);
            }
        }

        return $modified ? $nodes : null;
    }

    /**
     * Collect aliases in a set of nodes.
     *
     * @param array $nodes   Array of Node objects
     * @param array $aliases Associative array mapping lowercase alias names to true
     */
    private function collectAliasesInNodes(array $nodes, array &$aliases): void
    {
        foreach ($nodes as $node) {
            if ($node instanceof Use_ || $node instanceof GroupUse) {
                foreach ($node->uses as $useItem) {
                    $alias = $useItem->getAlias();
                    if ($alias !== null) {
                        $aliasStr = $alias instanceof Name ? $alias->toString() : (string) $alias;
                        $aliases[\strtolower($aliasStr)] = true;
                    } else {
                        $aliases[\strtolower($this->getShortName($useItem->name))] = true;
                    }
                }
            }
        }
    }

    /**
     * Collect unqualified names in nodes.
     *
     * @param array $nodes   Array of Node objects to traverse
     * @param array $seen    Lowercase short names already processed
     * @param array $aliases Lowercase alias names that exist in this namespace
     * @param array $uses    Map of short names to FQNs for implicit use statements
     */
    private function collectNamesInNodes(array $nodes, array &$seen, array $aliases, array &$uses): void
    {
        foreach ($nodes as $node) {
            if (!$node instanceof Node || $node instanceof Use_) {
                continue;
            }

            if ($node instanceof Name && !$node instanceof FullyQualifiedName) {
                if (!$this->isQualified($node)) {
                    $shortName = $this->getShortName($node);
                    $shortNameLower = \strtolower($shortName);

                    if (isset($seen[$shortNameLower])) {
                        continue;
                    }

                    $seen[$shortNameLower] = true;

                    if ($this->shouldAddImplicitUseInContext($shortName, $shortNameLower, $aliases)) {
                        // @phan-suppress-next-line PhanTypeArraySuspiciousNullable - shortNameMap is initialized in beforeTraverse
                        $uses[$shortName] = $this->shortNameMap[$shortNameLower];
                    }
                }
            }

            foreach ($node->getSubNodeNames() as $subNodeName) {
                $subNode = $node->$subNodeName;
                if ($subNode instanceof Node) {
                    $subNode = [$subNode];
                }

                if (\is_array($subNode)) {
                    $this->collectNamesInNodes($subNode, $seen, $aliases, $uses);
                }
            }
        }
    }

    /**
     * Create Use_ statement nodes from uses array.
     *
     * @param array $uses Associative array mapping short names to FQNs
     *
     * @return Use_[]
     */
    private function createUseStatements(array $uses): array
    {
        \asort($uses);

        $useStatements = [];
        foreach ($uses as $fqn) {
            $useItem = \class_exists(UseItem::class) ? new UseItem(new Name($fqn)) : new UseUse(new Name($fqn));
            $useStatements[] = new Use_([$useItem]);
        }

        return $useStatements;
    }

    /**
     * Check if we should add an implicit use statement for this name in current context.
     *
     * @param string $shortName      Original case short name
     * @param string $shortNameLower Lowercase short name for comparison
     * @param array  $aliases        Lowercase alias names that exist in this namespace
     */
    private function shouldAddImplicitUseInContext(string $shortName, string $shortNameLower, array $aliases): bool
    {
        // Rule 1: No existing unqualified name (class/interface/trait) with that short name
        if (\class_exists($shortName, false) || \interface_exists($shortName, false) || \trait_exists($shortName, false)) {
            return false;
        }

        // Rule 2: No existing use statement or alias with that short name
        if (isset($aliases[$shortNameLower])) {
            return false;
        }

        // Rule 3: Exactly one matching short class/interface/trait in configured namespaces
        if (!isset($this->shortNameMap[$shortNameLower]) || $this->shortNameMap[$shortNameLower] === null) {
            return false;
        }

        // Rule 4: Don't add use statement if the class exists in the current namespace
        if ($this->currentNamespace !== null) {
            $expectedFqn = \trim($this->currentNamespace, '\\').'\\'.$shortName;

            if (\class_exists($expectedFqn, false) || \interface_exists($expectedFqn, false) || \trait_exists($expectedFqn, false)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build a map of short class names to fully-qualified names.
     *
     * Uses get_declared_classes(), get_declared_interfaces(), and get_declared_traits()
     * to find all currently loaded classes. Only includes classes matching the configured
     * namespace filters. Detects ambiguous short names (multiple FQNs with same short name
     * within the filtered namespaces) and marks them as null.
     */
    private function buildShortNameMap(): void
    {
        $this->shortNameMap = [];

        $allClasses = [
            ...\get_declared_classes(),
            ...\get_declared_interfaces(),
            ...\get_declared_traits(),
        ];

        // First pass: collect all matching classes
        $candidatesByShortName = [];
        foreach ($allClasses as $fqn) {
            if (!$this->shouldIncludeClass($fqn)) {
                continue;
            }

            $parts = \explode('\\', $fqn);
            $shortName = \strtolower(\end($parts));

            if (!isset($candidatesByShortName[$shortName])) {
                $candidatesByShortName[$shortName] = [];
            }
            $candidatesByShortName[$shortName][] = $fqn;
        }

        // Second pass: determine if each short name is unique or ambiguous
        foreach ($candidatesByShortName as $shortName => $fqns) {
            $uniqueFqns = \array_unique($fqns);
            // Mark as null if ambiguous (multiple FQNs with same short name)
            $this->shortNameMap[$shortName] = (\count($uniqueFqns) === 1) ? $uniqueFqns[0] : null;
        }
    }

    /**
     * Check if a class should be aliased based on namespace filters.
     *
     * @param string $fqn Fully-qualified class name
     */
    private function shouldIncludeClass(string $fqn): bool
    {
        if (\strpos($fqn, '\\') === false) {
            return false;
        }

        if (empty($this->includeNamespaces) && empty($this->excludeNamespaces)) {
            return false;
        }

        foreach ($this->excludeNamespaces as $namespace) {
            if (\stripos($fqn, $namespace) === 0) {
                return false;
            }
        }

        if (empty($this->includeNamespaces)) {
            return true;
        }

        foreach ($this->includeNamespaces as $namespace) {
            if (\stripos($fqn, $namespace) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize namespace prefixes.
     *
     * Removes leading backslash and ensures trailing backslash.
     *
     * @param string[] $namespaces
     *
     * @return string[]
     */
    private function normalizeNamespaces(array $namespaces): array
    {
        return \array_map(fn ($namespace) => \trim($namespace, '\\').'\\', $namespaces);
    }

    /**
     * Get short name from a Name node.
     */
    private function getShortName(Name $name): string
    {
        $parts = $this->getParts($name);

        return \end($parts);
    }

    /**
     * Check if a name is qualified (contains namespace separator).
     */
    private function isQualified(Name $name): bool
    {
        return \count($this->getParts($name)) > 1;
    }

    /**
     * Backwards compatibility shim for PHP-Parser 4.x.
     *
     * @return string[]
     */
    private function getParts(Name $name): array
    {
        return \method_exists($name, 'getParts') ? $name->getParts() : $name->parts;
    }

    /**
     * Log added use statements to the CodeCleaner.
     *
     * @param array $uses Associative array mapping short names to FQNs
     */
    private function logAddedUses(array $uses): void
    {
        if ($this->cleaner === null || empty($uses)) {
            return;
        }

        if ($this->printer === null) {
            $this->printer = new PrettyPrinter();
        }

        $useStmts = $this->createUseStatements($uses);
        $this->cleaner->log($this->printer->prettyPrint($useStmts));
    }
}
