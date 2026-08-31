<?php declare(strict_types=1);

namespace PhpParser;

interface NodeVisitor {
    /**
     * If NodeVisitor::enterNode() returns DONT_TRAVERSE_CHILDREN, child nodes
     * of the current node will not be traversed for any visitors.
     *
     * For subsequent visitors enterNode() will still be called on the current
     * node and leaveNode() will also be invoked for the current node.
     */
    public const DONT_TRAVERSE_CHILDREN = 1;

    /**
     * If NodeVisitor::enterNode() or NodeVisitor::leaveNode() returns
     * STOP_TRAVERSAL, traversal is aborted.
     *
     * The afterTraverse() method will still be invoked.
     */
    public const STOP_TRAVERSAL = 2;

    /**
     * If NodeVisitor::leaveNode() returns REMOVE_NODE for a node that occurs
     * in an array, it will be removed from the array.
     *
     * For subsequent visitors leaveNode() will still be invoked for the
     * removed node.
     */
    public const REMOVE_NODE = 3;

    /**
     * If NodeVisitor::enterNode() returns DONT_TRAVERSE_CURRENT_AND_CHILDREN, child nodes
     * of the current node will not be traversed for any visitors.
     *
     * For subsequent visitors enterNode() will not be called as well.
     * leaveNode() will be invoked for visitors that has enterNode() method invoked.
     */
    public const DONT_TRAVERSE_CURRENT_AND_CHILDREN = 4;

    /**
     * If NodeVisitor::enterNode() or NodeVisitor::leaveNode() returns REPLACE_WITH_NULL,
     * the node will be replaced with null. This is not a legal return value if the node is part
     * of an array, rather than another node.
     */
    public const REPLACE_WITH_NULL = 5;

    /**
     * Called once before traversal.
     *
     * Return value semantics:
     *  * null:      $nodes stays as-is
     *  * otherwise: $nodes is set to the return value
     *
     * @param Node[] $nodes Array of nodes
     *
     * @return null|Node[] Array of nodes
     */
    public function beforeTraverse(array $nodes);

    /**
     * Called when entering a node.
     *
     * Return value semantics:
     *  * null
     *        => $node stays as-is
     *  * array (of Nodes)
     *        => The return value is merged into the parent array (at the position of the $node)
     *  * NodeVisitor::REMOVE_NODE
     *        => $node is removed from the parent array
     *  * NodeVisitor::REPLACE_WITH_NULL
     *        => $node is replaced with null
     *  * NodeVisitor::DONT_TRAVERSE_CHILDREN
     *        => Children of $node are not traversed. $node stays as-is
     *  * NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN
     *        => Further visitors for the current node are skipped, and its children are not
     *           traversed. $node stays as-is.
     *  * NodeVisitor::STOP_TRAVERSAL
     *        => Traversal is aborted. $node stays as-is
     *  * otherwise
     *        => $node is set to the return value
     *
     * @param Node $node Node
     *
     * @return null|int|Node|Node[] Replacement node (or special return value)
     */
    public function enterNode(Node $node);

    /**
     * Called when leaving a node.
     *
     * Return value semantics:
     *  * null
     *        => $node stays as-is
     *  * NodeVisitor::REMOVE_NODE
     *        => $node is removed from the parent array
     *  * NodeVisitor::REPLACE_WITH_NULL
     *        => $node is replaced with null
     *  * NodeVisitor::STOP_TRAVERSAL
     *        => Traversal is aborted. $node stays as-is
     *  * array (of Nodes)
     *        => The return value is merged into the parent array (at the position of the $node)
     *  * otherwise
     *        => $node is set to the return value
     *
     * @param Node $node Node
     *
     * @return null|int|Node|Node[] Replacement node (or special return value)
     */
    public function leaveNode(Node $node);

    /**
     * Called once after traversal.
     *
     * Return value semantics:
     *  * null:      $nodes stays as-is
     *  * otherwise: $nodes is set to the return value
     *
     * @param Node[] $nodes Array of nodes
     *
     * @return null|Node[] Array of nodes
     */
    public function afterTraverse(array $nodes);
}
