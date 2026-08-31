<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;
use PhpParser\Node\Stmt\Use_;

class UseItem extends NodeAbstract {
    /**
     * @var Use_::TYPE_* One of the Stmt\Use_::TYPE_* constants. Will only differ from TYPE_UNKNOWN for mixed group uses
     */
    public int $type;
    /** @var Node\Name Namespace, class, function or constant to alias */
    public Name $name;
    /** @var Identifier|null Alias */
    public ?Identifier $alias;

    /**
     * Constructs an alias (use) item node.
     *
     * @param Node\Name $name Namespace/Class to alias
     * @param null|string|Identifier $alias Alias
     * @param Use_::TYPE_* $type Type of the use element (for mixed group use only)
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Name $name, $alias = null, int $type = Use_::TYPE_UNKNOWN, array $attributes = []) {
        $this->attributes = $attributes;
        $this->type = $type;
        $this->name = $name;
        $this->alias = \is_string($alias) ? new Identifier($alias) : $alias;
    }

    public function getSubNodeNames(): array {
        return ['type', 'name', 'alias'];
    }

    /**
     * Get alias. If not explicitly given this is the last component of the used name.
     */
    public function getAlias(): Identifier {
        if (null !== $this->alias) {
            return $this->alias;
        }

        return new Identifier($this->name->getLast());
    }

    public function getType(): string {
        return 'UseItem';
    }
}

// @deprecated compatibility alias
class_alias(UseItem::class, Stmt\UseUse::class);
