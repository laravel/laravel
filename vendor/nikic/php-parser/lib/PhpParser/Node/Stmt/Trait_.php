<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Trait_ extends ClassLike {
    /**
     * Constructs a trait node.
     *
     * @param string|Node\Identifier $name Name
     * @param array{
     *     stmts?: Node\Stmt[],
     *     attrGroups?: Node\AttributeGroup[],
     * } $subNodes Array of the following optional subnodes:
     *             'stmts'      => array(): Statements
     *             'attrGroups' => array(): PHP attribute groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'name', 'stmts'];
    }

    public function getType(): string {
        return 'Stmt_Trait';
    }
}
