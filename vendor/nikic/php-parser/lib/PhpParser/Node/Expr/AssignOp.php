<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

abstract class AssignOp extends Expr {
    /** @var Expr Variable */
    public Expr $var;
    /** @var Expr Expression */
    public Expr $expr;

    /**
     * Constructs a compound assignment operation node.
     *
     * @param Expr $var Variable
     * @param Expr $expr Expression
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $var, Expr $expr, array $attributes = []) {
        $this->attributes = $attributes;
        $this->var = $var;
        $this->expr = $expr;
    }

    public function getSubNodeNames(): array {
        return ['var', 'expr'];
    }
}
