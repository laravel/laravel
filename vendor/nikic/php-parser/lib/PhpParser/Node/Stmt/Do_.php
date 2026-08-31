<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Do_ extends Node\Stmt {
    /** @var Node\Stmt[] Statements */
    public array $stmts;
    /** @var Node\Expr Condition */
    public Node\Expr $cond;

    /**
     * Constructs a do while node.
     *
     * @param Node\Expr $cond Condition
     * @param Node\Stmt[] $stmts Statements
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $stmts = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames(): array {
        return ['stmts', 'cond'];
    }

    public function getType(): string {
        return 'Stmt_Do';
    }
}
