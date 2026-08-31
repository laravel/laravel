<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

class FuncCall extends CallLike {
    /** @var Node\Name|Expr Function name */
    public Node $name;
    /** @var array<Node\Arg|Node\VariadicPlaceholder> Arguments */
    public array $args;

    /**
     * Constructs a function call node.
     *
     * @param Node\Name|Expr $name Function name
     * @param array<Node\Arg|Node\VariadicPlaceholder> $args Arguments
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node $name, array $args = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = $name;
        $this->args = $args;
    }

    public function getSubNodeNames(): array {
        return ['name', 'args'];
    }

    public function getType(): string {
        return 'Expr_FuncCall';
    }

    public function getRawArgs(): array {
        return $this->args;
    }
}
