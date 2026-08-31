<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Arg extends NodeAbstract {
    /** @var Identifier|null Parameter name (for named parameters) */
    public ?Identifier $name;
    /** @var Expr Value to pass */
    public Expr $value;
    /** @var bool Whether to pass by ref */
    public bool $byRef;
    /** @var bool Whether to unpack the argument */
    public bool $unpack;

    /**
     * Constructs a function call argument node.
     *
     * @param Expr $value Value to pass
     * @param bool $byRef Whether to pass by ref
     * @param bool $unpack Whether to unpack the argument
     * @param array<string, mixed> $attributes Additional attributes
     * @param Identifier|null $name Parameter name (for named parameters)
     */
    public function __construct(
        Expr $value, bool $byRef = false, bool $unpack = false, array $attributes = [],
        ?Identifier $name = null
    ) {
        $this->attributes = $attributes;
        $this->name = $name;
        $this->value = $value;
        $this->byRef = $byRef;
        $this->unpack = $unpack;
    }

    public function getSubNodeNames(): array {
        return ['name', 'value', 'byRef', 'unpack'];
    }

    public function getType(): string {
        return 'Arg';
    }
}
