<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Assign extends Expr {
    /** @var Expr Variable */
    public Expr $var;
    /** @var Expr Expression */
    public Expr $expr;

    /**
     * Constructs an assignment node.
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

    public function getType(): string {
        return 'Expr_Assign';
    }
}
