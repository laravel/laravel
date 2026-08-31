<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class MatchArm extends NodeAbstract {
    /** @var null|list<Node\Expr> */
    public ?array $conds;
    public Expr $body;

    /**
     * @param null|list<Node\Expr> $conds
     */
    public function __construct(?array $conds, Node\Expr $body, array $attributes = []) {
        $this->conds = $conds;
        $this->body = $body;
        $this->attributes = $attributes;
    }

    public function getSubNodeNames(): array {
        return ['conds', 'body'];
    }

    public function getType(): string {
        return 'MatchArm';
    }
}
