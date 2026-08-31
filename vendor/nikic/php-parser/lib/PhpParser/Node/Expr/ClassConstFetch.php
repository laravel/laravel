<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

class ClassConstFetch extends Expr {
    /** @var Name|Expr Class name */
    public Node $class;
    /** @var Identifier|Expr|Error Constant name */
    public Node $name;

    /**
     * Constructs a class const fetch node.
     *
     * @param Name|Expr $class Class name
     * @param string|Identifier|Expr|Error $name Constant name
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node $class, $name, array $attributes = []) {
        $this->attributes = $attributes;
        $this->class = $class;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
    }

    public function getSubNodeNames(): array {
        return ['class', 'name'];
    }

    public function getType(): string {
        return 'Expr_ClassConstFetch';
    }
}
