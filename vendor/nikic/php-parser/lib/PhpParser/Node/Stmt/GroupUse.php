<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\UseItem;

class GroupUse extends Stmt {
    /**
     * @var Use_::TYPE_* Type of group use
     */
    public int $type;
    /** @var Name Prefix for uses */
    public Name $prefix;
    /** @var UseItem[] Uses */
    public array $uses;

    /**
     * Constructs a group use node.
     *
     * @param Name $prefix Prefix for uses
     * @param UseItem[] $uses Uses
     * @param Use_::TYPE_* $type Type of group use
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Name $prefix, array $uses, int $type = Use_::TYPE_NORMAL, array $attributes = []) {
        $this->attributes = $attributes;
        $this->type = $type;
        $this->prefix = $prefix;
        $this->uses = $uses;
    }

    public function getSubNodeNames(): array {
        return ['type', 'prefix', 'uses'];
    }

    public function getType(): string {
        return 'Stmt_GroupUse';
    }
}
