<?php

interface PHPParser_Builder
{
    /**
     * Returns the built node.
     *
     * @return PHPParser_Node The built node
     */
    public function getNode();
}