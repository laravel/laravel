<?php

class PHPParser_NodeVisitor_NameResolver extends PHPParser_NodeVisitorAbstract
{
    /**
     * @var null|PHPParser_Node_Name Current namespace
     */
    protected $namespace;

    /**
     * @var array Currently defined namespace and class aliases
     */
    protected $aliases;

    public function beforeTraverse(array $nodes) {
        $this->namespace = null;
        $this->aliases   = array();
    }

    public function enterNode(PHPParser_Node $node) {
        if ($node instanceof PHPParser_Node_Stmt_Namespace) {
            $this->namespace = $node->name;
            $this->aliases   = array();
        } elseif ($node instanceof PHPParser_Node_Stmt_UseUse) {
            $aliasName = strtolower($node->alias);
            if (isset($this->aliases[$aliasName])) {
                throw new PHPParser_Error(
                    sprintf(
                        'Cannot use "%s" as "%s" because the name is already in use',
                        $node->name, $node->alias
                    ),
                    $node->getLine()
                );
            }

            $this->aliases[$aliasName] = $node->name;
        } elseif ($node instanceof PHPParser_Node_Stmt_Class) {
            if (null !== $node->extends) {
                $node->extends = $this->resolveClassName($node->extends);
            }

            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->addNamespacedName($node);
        } elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
            foreach ($node->extends as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->addNamespacedName($node);
        } elseif ($node instanceof PHPParser_Node_Stmt_Trait) {
            $this->addNamespacedName($node);
        } elseif ($node instanceof PHPParser_Node_Stmt_Function) {
            $this->addNamespacedName($node);
        } elseif ($node instanceof PHPParser_Node_Stmt_Const) {
            foreach ($node->consts as $const) {
                $this->addNamespacedName($const);
            }
        } elseif ($node instanceof PHPParser_Node_Expr_StaticCall
                  || $node instanceof PHPParser_Node_Expr_StaticPropertyFetch
                  || $node instanceof PHPParser_Node_Expr_ClassConstFetch
                  || $node instanceof PHPParser_Node_Expr_New
                  || $node instanceof PHPParser_Node_Expr_Instanceof
        ) {
            if ($node->class instanceof PHPParser_Node_Name) {
                $node->class = $this->resolveClassName($node->class);
            }
        } elseif ($node instanceof PHPParser_Node_Stmt_Catch) {
            $node->type = $this->resolveClassName($node->type);
        } elseif ($node instanceof PHPParser_Node_Expr_FuncCall
                  || $node instanceof PHPParser_Node_Expr_ConstFetch
        ) {
            if ($node->name instanceof PHPParser_Node_Name) {
                $node->name = $this->resolveOtherName($node->name);
            }
        } elseif ($node instanceof PHPParser_Node_Stmt_TraitUse) {
            foreach ($node->traits as &$trait) {
                $trait = $this->resolveClassName($trait);
            }
        } elseif ($node instanceof PHPParser_Node_Param
                  && $node->type instanceof PHPParser_Node_Name
        ) {
            $node->type = $this->resolveClassName($node->type);
        }
    }

    protected function resolveClassName(PHPParser_Node_Name $name) {
        // don't resolve special class names
        if (in_array((string) $name, array('self', 'parent', 'static'))) {
            return $name;
        }

        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }

        // resolve aliases (for non-relative names)
        $aliasName = strtolower($name->getFirst());
        if (!$name->isRelative() && isset($this->aliases[$aliasName])) {
            $name->setFirst($this->aliases[$aliasName]);
        // if no alias exists prepend current namespace
        } elseif (null !== $this->namespace) {
            $name->prepend($this->namespace);
        }

        return new PHPParser_Node_Name_FullyQualified($name->parts, $name->getAttributes());
    }

    protected function resolveOtherName(PHPParser_Node_Name $name) {
        // fully qualified names are already resolved and we can't do anything about unqualified
        // ones at compiler-time
        if ($name->isFullyQualified() || $name->isUnqualified()) {
            return $name;
        }

        // resolve aliases for qualified names
        $aliasName = strtolower($name->getFirst());
        if ($name->isQualified() && isset($this->aliases[$aliasName])) {
            $name->setFirst($this->aliases[$aliasName]);
        // prepend namespace for relative names
        } elseif (null !== $this->namespace) {
            $name->prepend($this->namespace);
        }

        return new PHPParser_Node_Name_FullyQualified($name->parts, $name->getAttributes());
    }

    protected function addNamespacedName(PHPParser_Node $node) {
        if (null !== $this->namespace) {
            $node->namespacedName = clone $this->namespace;
            $node->namespacedName->append($node->name);
        } else {
            $node->namespacedName = new PHPParser_Node_Name($node->name, $node->getAttributes());
        }
    }
}
