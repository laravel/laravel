<?php

/**
 * @property string $name Name
 */
class PHPParser_Node_Stmt_Label extends PHPParser_Node_Stmt
{
    /**
     * Constructs a label node.
     *
     * @param string $name       Name
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $attributes = array()) {
        parent::__construct(
            array(
                'name' => $name,
            ),
            $attributes
        );
    }
}