<?php

class PHPParser_Builder_Param extends PHPParser_BuilderAbstract
{
    protected $name;

    protected $default;
    protected $type;
    protected $byRef;

    /**
     * Creates a parameter builder.
     *
     * @param string $name Name of the parameter
     */
    public function __construct($name) {
        $this->name = $name;

        $this->default = null;
        $this->type = null;
        $this->byRef = false;
    }

    /**
     * Sets default value for the parameter.
     *
     * @param mixed $value Default value to use
     *
     * @return PHPParser_Builder_Param The builder instance (for fluid interface)
     */
    public function setDefault($value) {
        $this->default = $this->normalizeValue($value);

        return $this;
    }

    /**
     * Sets type hint for the parameter.
     *
     * @param string|PHPParser_Node_Name $type Type hint to use
     *
     * @return PHPParser_Builder_Param The builder instance (for fluid interface)
     */
    public function setTypeHint($type) {
        if ($type === 'array' || $type === 'callable') {
            $this->type = $type;
        } else {
            $this->type = $this->normalizeName($type);
        }

        return $this;
    }

    /**
     * Make the parameter accept the value by reference.
     *
     * @return PHPParser_Builder_Param The builder instance (for fluid interface)
     */
    public function makeByRef() {
        $this->byRef = true;

        return $this;
    }

    /**
     * Returns the built parameter node.
     *
     * @return PHPParser_Node_Param The built parameter node
     */
    public function getNode() {
        return new PHPParser_Node_Param(
            $this->name, $this->default, $this->type, $this->byRef
        );
    }
}