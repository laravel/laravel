<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;

interface FunctionLike extends Node {
    /**
     * Whether to return by reference
     */
    public function returnsByRef(): bool;

    /**
     * List of parameters
     *
     * @return Param[]
     */
    public function getParams(): array;

    /**
     * Get the declared return type or null
     *
     * @return null|Identifier|Name|ComplexType
     */
    public function getReturnType();

    /**
     * The function body
     *
     * @return Stmt[]|null
     */
    public function getStmts(): ?array;

    /**
     * Get PHP attribute groups.
     *
     * @return AttributeGroup[]
     */
    public function getAttrGroups(): array;
}
