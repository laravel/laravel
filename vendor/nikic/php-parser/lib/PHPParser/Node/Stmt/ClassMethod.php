<?php

/**
 * @property int                    $type   Type
 * @property bool                   $byRef  Whether to return by reference
 * @property string                 $name   Name
 * @property PHPParser_Node_Param[] $params Parameters
 * @property PHPParser_Node[]       $stmts  Statements
 */
class PHPParser_Node_Stmt_ClassMethod extends PHPParser_Node_Stmt
{

    /**
     * Constructs a class method node.
     *
     * @param string      $name       Name
     * @param array       $subNodes   Array of the following optional subnodes:
     *                                'type'   => MODIFIER_PUBLIC: Type
     *                                'byRef'  => false          : Whether to return by reference
     *                                'params' => array()        : Parameters
     *                                'stmts'  => array()        : Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            $subNodes + array(
                'type'   => PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC,
                'byRef'  => false,
                'params' => array(),
                'stmts'  => array(),
            ),
            $attributes
        );
        $this->name = $name;

        if ($this->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC) {
            switch (strtolower($this->name)) {
                case '__construct':
                    throw new PHPParser_Error(sprintf('Constructor %s() cannot be static', $this->name));
                case '__destruct':
                    throw new PHPParser_Error(sprintf('Destructor %s() cannot be static', $this->name));
                case '__clone':
                    throw new PHPParser_Error(sprintf('Clone method %s() cannot be static', $this->name));
            }
        }
    }

    public function isPublic() {
        return (bool) ($this->type & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC);
    }

    public function isProtected() {
        return (bool) ($this->type & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED);
    }

    public function isPrivate() {
        return (bool) ($this->type & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE);
    }

    public function isAbstract() {
        return (bool) ($this->type & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT);
    }

    public function isFinal() {
        return (bool) ($this->type & PHPParser_Node_Stmt_Class::MODIFIER_FINAL);
    }

    public function isStatic() {
        return (bool) ($this->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC);
    }
}
