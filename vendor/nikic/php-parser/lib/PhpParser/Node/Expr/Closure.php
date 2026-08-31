<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\ClosureUse;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class Closure extends Expr implements FunctionLike {
    /** @var bool Whether the closure is static */
    public bool $static;
    /** @var bool Whether to return by reference */
    public bool $byRef;
    /** @var Node\Param[] Parameters */
    public array $params;
    /** @var ClosureUse[] use()s */
    public array $uses;
    /** @var null|Node\Identifier|Node\Name|Node\ComplexType Return type */
    public ?Node $returnType;
    /** @var Node\Stmt[] Statements */
    public array $stmts;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public array $attrGroups;

    /**
     * Constructs a lambda function node.
     *
     * @param array{
     *     static?: bool,
     *     byRef?: bool,
     *     params?: Node\Param[],
     *     uses?: ClosureUse[],
     *     returnType?: null|Node\Identifier|Node\Name|Node\ComplexType,
     *     stmts?: Node\Stmt[],
     *     attrGroups?: Node\AttributeGroup[],
     * } $subNodes Array of the following optional subnodes:
     *             'static'     => false  : Whether the closure is static
     *             'byRef'      => false  : Whether to return by reference
     *             'params'     => array(): Parameters
     *             'uses'       => array(): use()s
     *             'returnType' => null   : Return type
     *             'stmts'      => array(): Statements
     *             'attrGroups' => array(): PHP attributes groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->static = $subNodes['static'] ?? false;
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->params = $subNodes['params'] ?? [];
        $this->uses = $subNodes['uses'] ?? [];
        $this->returnType = $subNodes['returnType'] ?? null;
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'static', 'byRef', 'params', 'uses', 'returnType', 'stmts'];
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

    /** @return Node\Stmt[] */
    public function getStmts(): array {
        return $this->stmts;
    }

    public function getAttrGroups(): array {
        return $this->attrGroups;
    }

    public function getType(): string {
        return 'Expr_Closure';
    }
}
