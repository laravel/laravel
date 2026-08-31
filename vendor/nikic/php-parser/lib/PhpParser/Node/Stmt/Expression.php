<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
 * Represents statements of type "expr;"
 */
class Expression extends Node\Stmt {
    /** @var Node\Expr Expression */
    public Node\Expr $expr;

    /**
     * Constructs an expression statement.
     *
     * @param Node\Expr $expr Expression
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr, array $attributes = []) {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }

    public function getSubNodeNames(): array {
        return ['expr'];
    }

    public function getType(): string {
        return 'Stmt_Expression';
    }
}
