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
use PhpParser\Node\Stmt\Namespace_;
use Psy\CodeCleaner;

/**
 * Provide implicit namespaces for subsequent execution.
 *
 * The namespace pass remembers the last standalone namespace line encountered:
 *
 *     namespace Foo\Bar;
 *
 * ... which it then applies implicitly to all future evaluated code, until the
 * namespace is replaced by another namespace. To reset to the top level
 * namespace, enter `namespace {}`. This is a bit ugly, but it does the trick :)
 */
class NamespacePass extends NamespaceAwarePass
{
    /**
     * @param ?CodeCleaner $cleaner deprecated parameter, use setCleaner() instead
     *
     * @phpstan-ignore-next-line method.unused
     */
    public function __construct(?CodeCleaner $cleaner = null)
    {
        // No-op, since cleaner is provided by NamespaceAwarePass
    }

    /**
     * If this is a standalone namespace line, remember it for later.
     *
     * Otherwise, apply remembered namespaces to the code until a new namespace
     * is encountered.
     *
     * @param array $nodes
     *
     * @return Node[]|null Array of nodes
     */
    public function beforeTraverse(array $nodes)
    {
        if (empty($nodes)) {
            return $nodes;
        }

        $last = \end($nodes);

        if ($last instanceof Namespace_) {
            $kind = $last->getAttribute('kind');

            if ($kind === Namespace_::KIND_SEMICOLON) {
                // Save the current namespace for open namespaces
                $this->setNamespace($last->name);
            } else {
                // Clear the current namespace after a braced namespace
                $this->setNamespace(null);
            }

            return $nodes;
        }

        // Wrap in current namespace if one is set
        $currentNamespace = $this->getCurrentNamespace();

        if (!$currentNamespace) {
            return $nodes;
        }

        // Mark as re-injected so UseStatementPass knows it can re-inject use statements
        return [new Namespace_($currentNamespace, $nodes, ['psyshReinjected' => true])];
    }

    /**
     * Get the current namespace as a Name node.
     *
     * This is more complicated than it needs to be, because we're not storing namespace as a Name.
     *
     * @return Name|null
     */
    private function getCurrentNamespace(): ?Name
    {
        $namespace = $this->cleaner->getNamespace();

        return $namespace ? new Name($namespace) : null;
    }

    /**
     * Update the namespace in CodeCleaner and clear aliases.
     *
     * @param Name|null $namespace
     */
    private function setNamespace(?Name $namespace)
    {
        $this->cleaner->setNamespace($namespace);

        // Always clear aliases when changing namespace
        $this->cleaner->setAliasesByTypeForNamespace($namespace, []);
    }

    /**
     * @deprecated unused and will be removed in a future version
     */
    protected function getParts(Name $name): array
    {
        return \method_exists($name, 'getParts') ? $name->getParts() : $name->parts;
    }
}
