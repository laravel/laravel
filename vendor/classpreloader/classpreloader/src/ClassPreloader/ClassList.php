<?php

namespace ClassPreloader;

/**
 * Maintains a list of classes using a sort of doubly-linked list
 */
class ClassList
{
    /**
     * @var ClassNode The head node of the list
     */
    protected $head;

    /**
     * @var ClassNode The current node of the list
     */
    protected $current;

    public function __construct()
    {
        $this->clear();
    }

    /**
     * Clear the contents of the list and reset the head node and current node
     */
    public function clear()
    {
        $this->head = new ClassNode(null);
        $this->current = $this->head;
    }

    /**
     * Traverse to the next node in the list
     */
    public function next()
    {
        if (isset($this->current->next)) {
            $this->current = $this->current->next;
        } else {
            $this->current->next = new ClassNode(null, $this->current);
            $this->current = $this->current->next;
        }
    }

    /**
     * Insert a value at the current position in the list. Any currently set
     * value at this position will be pushed back in the list after the new
     * value
     *
     * @param mixed $value Value to insert
     */
    public function push($value)
    {
        if (!$this->current->value) {
            $this->current->value = $value;
        } else {
            $temp = $this->current;
            $this->current = new ClassNode($value, $temp->prev);
            $this->current->next = $temp;
            $temp->prev = $this->current;
            if ($temp === $this->head) {
                $this->head = $this->current;
            } else {
                $this->current->prev->next = $this->current;
            }
        }
    }

    /**
     * Traverse the ClassList and return a list of classes
     *
     * @return array
     */
    public function getClasses()
    {
        $classes = array();
        $current = $this->head;
        while ($current && $current->value) {
            $classes[] = $current->value;
            $current = $current->next;
        }

        return array_filter($classes);
    }
}
