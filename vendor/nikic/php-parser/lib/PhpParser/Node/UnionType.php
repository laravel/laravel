<?php declare(strict_types=1);

namespace PhpParser\Node;

class UnionType extends ComplexType {
    /** @var (Identifier|Name|IntersectionType)[] Types */
    public array $types;

    /**
     * Constructs a union type.
     *
     * @param (Identifier|Name|IntersectionType)[] $types Types
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $types, array $attributes = []) {
        $this->attributes = $attributes;
        $this->types = $types;
    }

    public function getSubNodeNames(): array {
        return ['types'];
    }

    public function getType(): string {
        return 'UnionType';
    }
}
