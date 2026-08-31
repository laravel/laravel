<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

abstract class Cast extends Expr {
    /** @var Expr Expression */
    public Expr $expr;

    /**
     * Constructs a cast node.
     *
     * @param Expr $expr Expression
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $expr, array $attributes = []) {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }

    public function getSubNodeNames(): array {
        return ['expr'];
    }
}
