<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;

class Function_ extends Node\Stmt implements FunctionLike {
    /** @var bool Whether function returns by reference */
    public bool $byRef;
    /** @var Node\Identifier Name */
    public Node\Identifier $name;
    /** @var Node\Param[] Parameters */
    public array $params;
    /** @var null|Node\Identifier|Node\Name|Node\ComplexType Return type */
    public ?Node $returnType;
    /** @var Node\Stmt[] Statements */
    public array $stmts;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public array $attrGroups;

    /** @var Node\Name|null Namespaced name (if using NameResolver) */
    public ?Node\Name $namespacedName;

    /**
     * Constructs a function node.
     *
     * @param string|Node\Identifier $name Name
     * @param array{
     *     byRef?: bool,
     *     params?: Node\Param[],
     *     returnType?: null|Node\Identifier|Node\Name|Node\ComplexType,
     *     stmts?: Node\Stmt[],
     *     attrGroups?: Node\AttributeGroup[],
     * } $subNodes Array of the following optional subnodes:
     *             'byRef'      => false  : Whether to return by reference
     *             'params'     => array(): Parameters
     *             'returnType' => null   : Return type
     *             'stmts'      => array(): Statements
     *             'attrGroups' => array(): PHP attribute groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->params = $subNodes['params'] ?? [];
        $this->returnType = $subNodes['returnType'] ?? null;
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'byRef', 'name', 'params', 'returnType', 'stmts'];
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

    /** @return Node\Stmt[] */
    public function getStmts(): array {
        return $this->stmts;
    }

    public function getType(): string {
        return 'Stmt_Function';
    }
}
