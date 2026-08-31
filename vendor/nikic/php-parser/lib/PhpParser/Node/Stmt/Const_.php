<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Const_ extends Node\Stmt {
    /** @var Node\Const_[] Constant declarations */
    public array $consts;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public array $attrGroups;

    /**
     * Constructs a const list node.
     *
     * @param Node\Const_[] $consts Constant declarations
     * @param array<string, mixed> $attributes Additional attributes
     * @param list<Node\AttributeGroup> $attrGroups PHP attribute groups
     */
    public function __construct(
        array $consts,
        array $attributes = [],
        array $attrGroups = []
    ) {
        $this->attributes = $attributes;
        $this->attrGroups = $attrGroups;
        $this->consts = $consts;
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'consts'];
    }

    public function getType(): string {
        return 'Stmt_Const';
    }
}
