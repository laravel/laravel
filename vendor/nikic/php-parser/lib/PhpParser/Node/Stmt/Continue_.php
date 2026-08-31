<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Continue_ extends Node\Stmt {
    /** @var null|Node\Expr Number of loops to continue */
    public ?Node\Expr $num;

    /**
     * Constructs a continue node.
     *
     * @param null|Node\Expr $num Number of loops to continue
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(?Node\Expr $num = null, array $attributes = []) {
        $this->attributes = $attributes;
        $this->num = $num;
    }

    public function getSubNodeNames(): array {
        return ['num'];
    }

    public function getType(): string {
        return 'Stmt_Continue';
    }
}
