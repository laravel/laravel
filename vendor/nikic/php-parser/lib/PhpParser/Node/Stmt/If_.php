<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class If_ extends Node\Stmt {
    /** @var Node\Expr Condition expression */
    public Node\Expr $cond;
    /** @var Node\Stmt[] Statements */
    public array $stmts;
    /** @var ElseIf_[] Elseif clauses */
    public array $elseifs;
    /** @var null|Else_ Else clause */
    public ?Else_ $else;

    /**
     * Constructs an if node.
     *
     * @param Node\Expr $cond Condition
     * @param array{
     *     stmts?: Node\Stmt[],
     *     elseifs?: ElseIf_[],
     *     else?: Else_|null,
     * } $subNodes Array of the following optional subnodes:
     *             'stmts'   => array(): Statements
     *             'elseifs' => array(): Elseif clauses
     *             'else'    => null   : Else clause
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->elseifs = $subNodes['elseifs'] ?? [];
        $this->else = $subNodes['else'] ?? null;
    }

    public function getSubNodeNames(): array {
        return ['cond', 'stmts', 'elseifs', 'else'];
    }

    public function getType(): string {
        return 'Stmt_If';
    }
}
