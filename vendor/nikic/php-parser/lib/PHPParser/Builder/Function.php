<?php

class PHPParser_Builder_Function extends PHPParser_BuilderAbstract
{
    protected $name;

    protected $returnByRef;
    protected $params;
    protected $stmts;

    /**
     * Creates a function builder.
     *
     * @param string $name Name of the function
     */
    public function __construct($name) {
        $this->name = $name;

        $this->returnByRef = false;
        $this->params = array();
        $this->stmts = array();
    }

    /**
     * Make the function return by reference.
     *
     * @return PHPParser_Builder_Function The builder instance (for fluid interface)
     */
    public function makeReturnByRef() {
        $this->returnByRef = true;

        return $this;
    }

    /**
     * Adds a parameter.
     *
     * @param PHPParser_Node_Param|PHPParser_Builder_Param $param The parameter to add
     *
     * @return PHPParser_Builder_Function The builder instance (for fluid interface)
     */
    public function addParam($param) {
        $param = $this->normalizeNode($param);

        if (!$param instanceof PHPParser_Node_Param) {
            throw new LogicException(sprintf('Expected parameter node, got "%s"', $param->getType()));
        }

        $this->params[] = $param;

        return $this;
    }

    /**
     * Adds multiple parameters.
     *
     * @param array $params The parameters to add
     *
     * @return PHPParser_Builder_Function The builder instance (for fluid interface)
     */
    public function addParams(array $params) {
        foreach ($params as $param) {
            $this->addParam($param);
        }

        return $this;
    }

    /**
     * Adds a statement.
     *
     * @param PHPParser_Node|PHPParser_Builder $stmt The statement to add
     *
     * @return PHPParser_Builder_Function The builder instance (for fluid interface)
     */
    public function addStmt($stmt) {
        $this->stmts[] = $this->normalizeNode($stmt);

        return $this;
    }

    /**
     * Adds multiple statements.
     *
     * @param array $stmts The statements to add
     *
     * @return PHPParser_Builder_Function The builder instance (for fluid interface)
     */
    public function addStmts(array $stmts) {
        foreach ($stmts as $stmt) {
            $this->addStmt($stmt);
        }

        return $this;
    }

    /**
     * Returns the built function node.
     *
     * @return PHPParser_Node_Stmt_Function The built function node
     */
    public function getNode() {
        return new PHPParser_Node_Stmt_Function($this->name, array(
            'byRef'  => $this->returnByRef,
            'params' => $this->params,
            'stmts'  => $this->stmts,
        ));
    }
}