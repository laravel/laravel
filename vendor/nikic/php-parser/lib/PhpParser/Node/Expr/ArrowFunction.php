<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class ArrowFunction extends Expr implements FunctionLike {
    /** @var bool Whether the closure is static */
    public bool $static;

    /** @var bool Whether to return by reference */
    public bool $byRef;

    /** @var Node\Param[] */
    public array $params = [];

    /** @var null|Node\Identifier|Node\Name|Node\ComplexType */
    public ?Node $returnType;

    /** @var Expr Expression body */
    public Expr $expr;
    /** @var Node\AttributeGroup[] */
    public array $attrGroups;

    /**
     * @param array{
     *     expr: Expr,
     *     static?: bool,
     *     byRef?: bool,
     *     params?: Node\Param[],
     *     returnType?: null|Node\Identifier|Node\Name|Node\ComplexType,
     *     attrGroups?: Node\AttributeGroup[]
     * } $subNodes Array of the following subnodes:
     *             'expr'                  : Expression body
     *             'static'     => false   : Whether the closure is static
     *             'byRef'      => false   : Whether to return by reference
     *             'params'     => array() : Parameters
     *             'returnType' => null    : Return type
     *             'attrGroups' => array() : PHP attribute groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $subNodes, array $attributes = []) {
        $this->attributes = $attributes;
        $this->static = $subNodes['static'] ?? false;
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->params = $subNodes['params'] ?? [];
        $this->returnType = $subNodes['returnType'] ?? null;
        $this->expr = $subNodes['expr'];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'static', 'byRef', 'params', 'returnType', 'expr'];
    }

    public function returnsByRef(): bool {
        return $this->byRef;
    }

    public function getParams(): array {
        return $this->params;
    }

    public function getReturnType() {
        return $this->returnType;
    }

    public function getAttrGroups(): array {
        return $this->attrGroups;
    }

    /**
     * @return Node\Stmt\Return_[]
     */
    public function getStmts(): array {
        return [new Node\Stmt\Return_($this->expr)];
    }

    public function getType(): string {
        return 'Expr_ArrowFunction';
    }
}
