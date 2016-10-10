<?php

class PHPParser_Builder_Interface extends PHPParser_BuilderAbstract
{
    protected $name;
    protected $extends;
    protected $constants;
    protected $methods;

    /**
     * Creates an interface builder.
     *
     * @param string $name Name of the interface
     */
    public function __construct($name) {
        $this->name = $name;
        $this->extends = array();
        $this->constants = $this->methods = array();
    }

    /**
     * Extends one or more interfaces.
     *
     * @param PHPParser_Node_Name|string $interface Name of interface to extend
     * @param PHPParser_Node_Name|string $...       More interfaces to extend
     *
     * @return PHPParser_Builder_Interface The builder instance (for fluid interface)
     */
    public function extend() {
        foreach (func_get_args() as $interface) {
            $this->extends[] = $this->normalizeName($interface);
        }

        return $this;
    }

    /**
     * Adds a statement.
     *
     * @param PHPParser_Node_Stmt|PHPParser_Builder $stmt The statement to add
     *
     * @return PHPParser_Builder_Interface The builder instance (for fluid interface)
     */
    public function addStmt($stmt) {
        $stmt = $this->normalizeNode($stmt);

        $type = $stmt->getType();
        switch ($type) {
            case 'Stmt_ClassConst':
                $this->constants[] = $stmt;
                break;

            case 'Stmt_ClassMethod':
                // we erase all statements in the body of an interface method
                $stmt->stmts = null;
                $this->methods[] = $stmt;
                break;

            default:
                throw new LogicException(sprintf('Unexpected node of type "%s"', $type));
        }

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
     * @return PHPParser_Node_Stmt_Interface The built interface node
     */
    public function getNode() {
        return new PHPParser_Node_Stmt_Interface($this->name, array(
            'extends' => $this->extends,
            'stmts' => array_merge($this->constants, $this->methods),
        ));
    }
}