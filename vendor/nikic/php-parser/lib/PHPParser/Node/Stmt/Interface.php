<?php

/**
 * @property string                $name    Name
 * @property PHPParser_Node_Name[] $extends Extended interfaces
 * @property PHPParser_Node[]      $stmts   Statements
 */
class PHPParser_Node_Stmt_Interface extends PHPParser_Node_Stmt
{
    protected static $specialNames = array(
        'self'   => true,
        'parent' => true,
        'static' => true,
    );

    /**
     * Constructs a class node.
     *
     * @param string $name       Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'extends' => array(): Name of extended interfaces
     *                           'stmts'   => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            $subNodes + array(
                'extends' => array(),
                'stmts'   => array(),
            ),
            $attributes
        );
        $this->name = $name;

        if (isset(self::$specialNames[(string) $this->name])) {
            throw new PHPParser_Error(sprintf('Cannot use \'%s\' as class name as it is reserved', $this->name));
        }

        foreach ($this->extends as $interface) {
            if (isset(self::$specialNames[(string) $interface])) {
                throw new PHPParser_Error(sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface));
            }
        }
    }
}
