<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class PostDec extends Expr {
    /** @var Expr Variable */
    public Expr $var;

    /**
     * Constructs a post decrement node.
     *
     * @param Expr $var Variable
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $var, array $attributes = []) {
        $this->attributes = $attributes;
        $this->var = $var;
    }

    public function getSubNodeNames(): array {
        return ['var'];
    }

    public function getType(): string {
        return 'Expr_PostDec';
    }
}
