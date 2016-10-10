<?php

interface PHPParser_NodeVisitor
{
    /**
     * Called once before traversal.
     *
     * Return value semantics:
     *  * null:      $nodes stays as-is
     *  * otherwise: $nodes is set to the return value
     *
     * @param PHPParser_Node[] $nodes Array of nodes
     *
     * @return null|PHPParser_Node[] Array of nodes
     */
    public function beforeTraverse(array $nodes);

    /**
     * Called when entering a node.
     *
     * Return value semantics:
     *  * null:      $node stays as-is
     *  * otherwise: $node is set to the return value
     *
     * @param PHPParser_Node $node Node
     *
     * @return null|PHPParser_Node Node
     */
    public function enterNode(PHPParser_Node $node);

    /**
     * Called when leaving a node.
     *
     * Return value semantics:
     *  * null:      $node stays as-is
     *  * false:     $node is removed from the parent array
     *  * array:     The return value is merged into the parent array (at the position of the $node)
     *  * otherwise: $node is set to the return value
     *
     * @param PHPParser_Node $node Node
     *
     * @return null|PHPParser_Node|false|PHPParser_Node[] Node
     */
    public function leaveNode(PHPParser_Node $node);

    /**
     * Called once after traversal.
     *
     * Return value semantics:
     *  * null:      $nodes stays as-is
     *  * otherwise: $nodes is set to the return value
     *
     * @param PHPParser_Node[] $nodes Array of nodes
     *
     * @return null|PHPParser_Node[] Array of nodes
     */
    public function afterTraverse(array $nodes);
}