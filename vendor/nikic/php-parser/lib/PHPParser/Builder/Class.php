<?php

class PHPParser_Builder_Class extends PHPParser_BuilderAbstract
{
    protected $name;

    protected $extends;
    protected $implements;
    protected $type;

    protected $uses;
    protected $constants;
    protected $properties;
    protected $methods;

    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     */
    public function __construct($name) {
        $this->name = $name;

        $this->type = 0;
        $this->extends = null;
        $this->implements = array();

        $this->uses = $this->constants = $this->properties = $this->methods = array();
    }

    /**
     * Extends a class.
     *
     * @param PHPParser_Node_Name|string $class Name of class to extend
     *
     * @return PHPParser_Builder_Class The builder instance (for fluid interface)
     */
    public function extend($class) {
        $this->extends = $this->normalizeName($class);

        return $this;
    }

    /**
     * Implements one or more interfaces.
     *
     * @param PHPParser_Node_Name|string $interface Name of interface to implement
     * @param PHPParser_Node_Name|string $...       More interfaces to implement
     *
     * @return PHPParser_Builder_Class The builder instance (for fluid interface)
     */
    public function implement() {
        foreach (func_get_args() as $interface) {
            $this->implements[] = $this->normalizeName($interface);
        }

        return $this;
    }

    /**
     * Makes the class abstract.
     *
     * @return PHPParser_Builder_Class The builder instance (for fluid interface)
     */
    public function makeAbstract() {
        $this->setModifier(PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT);

        return $this;
    }

    /**
     * Makes the class final.
     *
     * @return PHPParser_Builder_Class The builder instance (for fluid interface)
     */
    public function makeFinal() {
        $this->setModifier(PHPParser_Node_Stmt_Class::MODIFIER_FINAL);

        return $this;
    }

    /**
     * Adds a statement.
     *
     * @param PHPParser_Node_Stmt|PHPParser_Builder $stmt The statement to add
     *
     * @return PHPParser_Builder_Class The builder instance (for fluid interface)
     */
    public function addStmt($stmt) {
        $stmt = $this->normalizeNode($stmt);

        $targets = array(
            'Stmt_TraitUse'    => &$this->uses,
            'Stmt_ClassConst'  => &$this->constants,
            'Stmt_Property'    => &$this->properties,
            'Stmt_ClassMethod' => &$this->methods,
        );

        $type = $stmt->getType();
        if (!isset($targets[$type])) {
            throw new LogicException(sprintf('Unexpected node of type "%s"', $type));
        }

        $targets[$type][] = $stmt;

        return $this;
    }

    /**
     * Adds multiple statements.
     *
     * @param array $stmts The statements to add
     *
     * @return PHPParser_Builder_Class The builder instance (for fluid interface)
     */
    public function addStmts(array $stmts) {
        foreach ($stmts as $stmt) {
            $this->addStmt($stmt);
        }

        return $this;
    }

    /**
     * Returns the built class node.
     *
     * @return PHPParser_Node_Stmt_Class The built class node
     */
    public function getNode() {
        return new PHPParser_Node_Stmt_Class($this->name, array(
            'type' => $this->type,
            'extends' => $this->extends,
            'implements' => $this->implements,
            'stmts' => array_merge($this->uses, $this->constants, $this->properties, $this->methods),
        ));
    }
}