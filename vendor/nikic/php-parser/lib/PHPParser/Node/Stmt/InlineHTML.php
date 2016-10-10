<?php

/**
 * @property string $value String
 */
class PHPParser_Node_Stmt_InlineHTML extends PHPParser_Node_Stmt
{
    /**
     * Constructs an inline HTML node.
     *
     * @param string $value      String
     * @param array  $attributes Additional attributes
     */
    public function __construct($value, array $attributes = array()) {
        parent::__construct(
            array(
                'value' => $value,
            ),
            $attributes
        );
    }
}