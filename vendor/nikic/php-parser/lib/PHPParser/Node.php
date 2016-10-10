<?php

interface PHPParser_Node
{
    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType();

    /**
     * Gets the names of the sub nodes.
     *
     * @return array Names of sub nodes
     */
    public function getSubNodeNames();

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine();

    /**
     * Sets line the node started in.
     *
     * @param int $line Line
     */
    public function setLine($line);

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     *
     * @return null|PHPParser_Comment_Doc Doc comment object or null
     */
    public function getDocComment();

    /**
     * Sets an attribute on a node.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute($key, $value);

    /**
     * Returns whether an attribute exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute($key);

    /**
     * Returns the value of an attribute.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function &getAttribute($key, $default = null);

    /**
     * Returns all attributes for the given node.
     *
     * @return array
     */
    public function getAttributes();
}