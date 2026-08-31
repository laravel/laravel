<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class UnaryPlus extends Expr {
    /** @var Expr Expression */
    public Expr $expr;

    /**
     * Constructs a unary plus node.
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

    public function getType(): string {
        return 'Expr_UnaryPlus';
    }
}
