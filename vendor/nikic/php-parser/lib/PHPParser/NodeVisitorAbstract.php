<?php

/**
 * @codeCoverageIgnore
 */
class PHPParser_NodeVisitorAbstract implements PHPParser_NodeVisitor
{
    public function beforeTraverse(array $nodes)    { }
    public function enterNode(PHPParser_Node $node) { }
    public function leaveNode(PHPParser_Node $node) { }
    public function afterTraverse(array $nodes)     { }
}