<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class ArrayItem extends NodeAbstract {
    /** @var null|Expr Key */
    public ?Expr $key;
    /** @var Expr Value */
    public Expr $value;
    /** @var bool Whether to assign by reference */
    public bool $byRef;
    /** @var bool Whether to unpack the argument */
    public bool $unpack;

    /**
     * Constructs an array item node.
     *
     * @param Expr $value Value
     * @param null|Expr $key Key
     * @param bool $byRef Whether to assign by reference
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $value, ?Expr $key = null, bool $byRef = false, array $attributes = [], bool $unpack = false) {
        $this->attributes = $attributes;
        $this->key = $key;
        $this->value = $value;
        $this->byRef = $byRef;
        $this->unpack = $unpack;
    }

    public function getSubNodeNames(): array {
        return ['key', 'value', 'byRef', 'unpack'];
    }

    public function getType(): string {
        return 'ArrayItem';
    }
}

// @deprecated compatibility alias
class_alias(ArrayItem::class, Expr\ArrayItem::class);
