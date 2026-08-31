<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Case_ extends Node\Stmt {
    /** @var null|Node\Expr Condition (null for default) */
    public ?Node\Expr $cond;
    /** @var Node\Stmt[] Statements */
    public array $stmts;

    /**
     * Constructs a case node.
     *
     * @param null|Node\Expr $cond Condition (null for default)
     * @param Node\Stmt[] $stmts Statements
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(?Node\Expr $cond, array $stmts = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames(): array {
        return ['cond', 'stmts'];
    }

    public function getType(): string {
        return 'Stmt_Case';
    }
}
