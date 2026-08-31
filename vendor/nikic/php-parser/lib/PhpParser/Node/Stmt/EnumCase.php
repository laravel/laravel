<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\AttributeGroup;

class EnumCase extends Node\Stmt {
    /** @var Node\Identifier Enum case name */
    public Node\Identifier $name;
    /** @var Node\Expr|null Enum case expression */
    public ?Node\Expr $expr;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public array $attrGroups;

    /**
     * @param string|Node\Identifier $name Enum case name
     * @param Node\Expr|null $expr Enum case expression
     * @param list<AttributeGroup> $attrGroups PHP attribute groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, ?Node\Expr $expr = null, array $attrGroups = [], array $attributes = []) {
        parent::__construct($attributes);
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->expr = $expr;
        $this->attrGroups = $attrGroups;
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'name', 'expr'];
    }

    public function getType(): string {
        return 'Stmt_EnumCase';
    }
}
