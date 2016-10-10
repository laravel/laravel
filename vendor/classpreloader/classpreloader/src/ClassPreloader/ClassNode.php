<?php

namespace ClassPreloader;

/**
 * A simple ClassNode that contains a value, previous, and next pointers
 */
class ClassNode
{
    /**
     * @var ClassNode|null Next node pointer
     */
    public $next;

    /**
     * @var ClassNode|null Previous node pointer
     */
    public $prev;

    /**
     * @var mixed Value of the ClassNode
     */
    public $value;

    /**
     * Create a new ClassNode
     *
     * @param mixed     $value Value of the class node
     * @param ClassNode $prev  Previous node pointer
     */
    public function __construct($value = null, $prev = null)
    {
        $this->value = $value;
        $this->prev = $prev;
    }
}
