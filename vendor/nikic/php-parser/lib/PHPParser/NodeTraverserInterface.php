<?php

interface PHPParser_NodeTraverserInterface
{
    /**
     * Adds a visitor.
     *
     * @param PHPParser_NodeVisitor $visitor Visitor to add
     */
    function addVisitor(PHPParser_NodeVisitor $visitor);

    /**
     * Traverses an array of nodes using the registered visitors.
     *
     * @param PHPParser_Node[] $nodes Array of nodes
     *
     * @return PHPParser_Node[] Traversed array of nodes
     */
    function traverse(array $nodes);
}

