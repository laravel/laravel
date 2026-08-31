<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class For_ extends Node\Stmt {
    /** @var Node\Expr[] Init expressions */
    public array $init;
    /** @var Node\Expr[] Loop conditions */
    public array $cond;
    /** @var Node\Expr[] Loop expressions */
    public array $loop;
    /** @var Node\Stmt[] Statements */
    public array $stmts;

    /**
     * Constructs a for loop node.
     *
     * @param array{
     *     init?: Node\Expr[],
     *     cond?: Node\Expr[],
     *     loop?: Node\Expr[],
     *     stmts?: Node\Stmt[],
     * } $subNodes Array of the following optional subnodes:
     *             'init'  => array(): Init expressions
     *             'cond'  => array(): Loop conditions
     *             'loop'  => array(): Loop expressions
     *             'stmts' => array(): Statements
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->init = $subNodes['init'] ?? [];
        $this->cond = $subNodes['cond'] ?? [];
        $this->loop = $subNodes['loop'] ?? [];
        $this->stmts = $subNodes['stmts'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['init', 'cond', 'loop', 'stmts'];
    }

    public function getType(): string {
        return 'Stmt_For';
    }
}
