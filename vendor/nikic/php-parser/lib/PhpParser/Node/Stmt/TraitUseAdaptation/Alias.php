<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt\TraitUseAdaptation;

use PhpParser\Node;

class Alias extends Node\Stmt\TraitUseAdaptation {
    /** @var null|int New modifier */
    public ?int $newModifier;
    /** @var null|Node\Identifier New name */
    public ?Node\Identifier $newName;

    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param null|Node\Name $trait Trait name
     * @param string|Node\Identifier $method Method name
     * @param null|int $newModifier New modifier
     * @param null|string|Node\Identifier $newName New name
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(?Node\Name $trait, $method, ?int $newModifier, $newName, array $attributes = []) {
        $this->attributes = $attributes;
        $this->trait = $trait;
        $this->method = \is_string($method) ? new Node\Identifier($method) : $method;
        $this->newModifier = $newModifier;
        $this->newName = \is_string($newName) ? new Node\Identifier($newName) : $newName;
    }

    public function getSubNodeNames(): array {
        return ['trait', 'method', 'newModifier', 'newName'];
    }

    public function getType(): string {
        return 'Stmt_TraitUseAdaptation_Alias';
    }
}
