<?php

abstract class PHPParser_BuilderAbstract implements PHPParser_Builder {
    /**
     * Normalizes a node: Converts builder objects to nodes.
     *
     * @param PHPParser_Node|PHPParser_Builder $node The node to normalize
     *
     * @return PHPParser_Node The normalized node
     */
    protected function normalizeNode($node) {
        if ($node instanceof PHPParser_Builder) {
            return $node->getNode();
        } elseif ($node instanceof PHPParser_Node) {
            return $node;
        }

        throw new LogicException('Expected node or builder object');
    }

    /**
     * Normalizes a name: Converts plain string names to PHPParser_Node_Name.
     *
     * @param PHPParser_Node_Name|string $name The name to normalize
     *
     * @return PHPParser_Node_Name The normalized name
     */
    protected function normalizeName($name) {
        if ($name instanceof PHPParser_Node_Name) {
            return $name;
        } else {
            return new PHPParser_Node_Name($name);
        }
    }

    /**
     * Normalizes a value: Converts nulls, booleans, integers,
     * floats, strings and arrays into their respective nodes
     *
     * @param mixed $value The value to normalize
     *
     * @return PHPParser_Node_Expr The normalized value
     */
    protected function normalizeValue($value) {
        if ($value instanceof PHPParser_Node) {
            return $value;
        } elseif (is_null($value)) {
            return new PHPParser_Node_Expr_ConstFetch(
                new PHPParser_Node_Name('null')
            );
        } elseif (is_bool($value)) {
            return new PHPParser_Node_Expr_ConstFetch(
                new PHPParser_Node_Name($value ? 'true' : 'false')
            );
        } elseif (is_int($value)) {
            return new PHPParser_Node_Scalar_LNumber($value);
        } elseif (is_float($value)) {
            return new PHPParser_Node_Scalar_DNumber($value);
        } elseif (is_string($value)) {
            return new PHPParser_Node_Scalar_String($value);
        } elseif (is_array($value)) {
            $items = array();
            $lastKey = -1;
            foreach ($value as $itemKey => $itemValue) {
                // for consecutive, numeric keys don't generate keys
                if (null !== $lastKey && ++$lastKey === $itemKey) {
                    $items[] = new PHPParser_Node_Expr_ArrayItem(
                        $this->normalizeValue($itemValue)
                    );
                } else {
                    $lastKey = null;
                    $items[] = new PHPParser_Node_Expr_ArrayItem(
                        $this->normalizeValue($itemValue),
                        $this->normalizeValue($itemKey)
                    );
                }
            }

            return new PHPParser_Node_Expr_Array($items);
        } else {
            throw new LogicException('Invalid value');
        }
    }

    /**
     * Sets a modifier in the $this->type property.
     *
     * @param int $modifier Modifier to set
     */
    protected function setModifier($modifier) {
        PHPParser_Node_Stmt_Class::verifyModifier($this->type, $modifier);
        $this->type |= $modifier;
    }
}