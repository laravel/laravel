<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Isset_ extends Expr {
    /** @var Expr[] Variables */
    public array $vars;

    /**
     * Constructs an array node.
     *
     * @param Expr[] $vars Variables
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = []) {
        $this->attributes = $attributes;
        $this->vars = $vars;
    }

    public function getSubNodeNames(): array {
        return ['vars'];
    }

    public function getType(): string {
        return 'Expr_Isset';
    }
}
