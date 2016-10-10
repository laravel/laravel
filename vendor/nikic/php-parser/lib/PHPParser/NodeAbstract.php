<?php

abstract class PHPParser_NodeAbstract implements PHPParser_Node, IteratorAggregate
{
    protected $subNodes;
    protected $attributes;

    /**
     * Creates a Node.
     *
     * @param array $subNodes   Array of sub nodes
     * @param array $attributes Array of attributes
     */
    public function __construct(array $subNodes = array(), array $attributes = array()) {
        $this->subNodes   = $subNodes;
        $this->attributes = $attributes;
    }

    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType() {
        return substr(get_class($this), 15);
    }

    /**
     * Gets the names of the sub nodes.
     *
     * @return array Names of sub nodes
     */
    public function getSubNodeNames() {
        return array_keys($this->subNodes);
    }

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine() {
        return $this->getAttribute('startLine', -1);
    }

    /**
     * Sets line the node started in.
     *
     * @param int $line Line
     */
    public function setLine($line) {
        $this->setAttribute('startLine', (int) $line);
    }

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     *
     * @return null|PHPParser_Comment_Doc Doc comment object or null
     */
    public function getDocComment() {
        $comments = $this->getAttribute('comments');
        if (!$comments) {
            return null;
        }

        $lastComment = $comments[count($comments) - 1];
        if (!$lastComment instanceof PHPParser_Comment_Doc) {
            return null;
        }

        return $lastComment;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAttribute($key) {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function &getAttribute($key, $default = null) {
        if (!array_key_exists($key, $this->attributes)) {
            return $default;
        } else {
            return $this->attributes[$key];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /* Magic interfaces */

    public function &__get($name) {
        return $this->subNodes[$name];
    }
    public function __set($name, $value) {
        $this->subNodes[$name] = $value;
    }
    public function __isset($name) {
        return isset($this->subNodes[$name]);
    }
    public function __unset($name) {
        unset($this->subNodes[$name]);
    }
    public function getIterator() {
        return new ArrayIterator($this->subNodes);
    }
}