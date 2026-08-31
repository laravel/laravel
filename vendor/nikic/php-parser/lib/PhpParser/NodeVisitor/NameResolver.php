<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\ErrorHandler;
use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;

class NameResolver extends NodeVisitorAbstract {
    /** @var NameContext Naming context */
    protected NameContext $nameContext;

    /** @var bool Whether to preserve original names */
    protected bool $preserveOriginalNames;

    /** @var bool Whether to replace resolved nodes in place, or to add resolvedNode attributes */
    protected bool $replaceNodes;

    /**
     * Constructs a name resolution visitor.
     *
     * Options:
     *  * preserveOriginalNames (default false): An "originalName" attribute will be added to
     *    all name nodes that underwent resolution.
     *  * replaceNodes (default true): Resolved names are replaced in-place. Otherwise, a
     *    resolvedName attribute is added. (Names that cannot be statically resolved receive a
     *    namespacedName attribute, as usual.)
     *
     * @param ErrorHandler|null $errorHandler Error handler
     * @param array{preserveOriginalNames?: bool, replaceNodes?: bool} $options Options
     */
    public function __construct(?ErrorHandler $errorHandler = null, array $options = []) {
        $this->nameContext = new NameContext($errorHandler ?? new ErrorHandler\Throwing());
        $this->preserveOriginalNames = $options['preserveOriginalNames'] ?? false;
        $this->replaceNodes = $options['replaceNodes'] ?? true;
    }

    /**
     * Get name resolution context.
     */
    public function getNameContext(): NameContext {
        return $this->nameContext;
    }

    public function beforeTraverse(array $nodes): ?array {
        $this->nameContext->startNamespace();
        return null;
    }

    public function enterNode(Node $node) {
        if ($node instanceof Stmt\Namespace_) {
            $this->nameContext->startNamespace($node->name);
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, null);
            }
        } elseif ($node instanceof Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, $node->prefix);
            }
        } elseif ($node instanceof Stmt\Class_) {
            if (null !== $node->extends) {
                $node->extends = $this->resolveClassName($node->extends);
            }

            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->resolveAttrGroups($node);
            if (null !== $node->name) {
                $this->addNamespacedName($node);
            } else {
                $node->namespacedName = null;
            }
        } elseif ($node instanceof Stmt\Interface_) {
            foreach ($node->extends as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->resolveAttrGroups($node);
            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Enum_) {
            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->resolveAttrGroups($node);
            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Trait_) {
            $this->resolveAttrGroups($node);
            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Function_) {
            $this->resolveSignature($node);
            $this->resolveAttrGroups($node);
            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\ClassMethod
                  || $node instanceof Expr\Closure
                  || $node instanceof Expr\ArrowFunction
        ) {
            $this->resolveSignature($node);
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof Stmt\Property) {
            if (null !== $node->type) {
                $node->type = $this->resolveType($node->type);
            }
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof Node\PropertyHook) {
            foreach ($node->params as $param) {
                $param->type = $this->resolveType($param->type);
                $this->resolveAttrGroups($param);
            }
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof Stmt\Const_) {
            foreach ($node->consts as $const) {
                $this->addNamespacedName($const);
            }
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof Stmt\ClassConst) {
            if (null !== $node->type) {
                $node->type = $this->resolveType($node->type);
            }
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof Stmt\EnumCase) {
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof Expr\StaticCall
                  || $node instanceof Expr\StaticPropertyFetch
                  || $node instanceof Expr\ClassConstFetch
                  || $node instanceof Expr\New_
                  || $node instanceof Expr\Instanceof_
        ) {
            if ($node->class instanceof Name) {
                $node->class = $this->resolveClassName($node->class);
            }
        } elseif ($node instanceof Stmt\Catch_) {
            foreach ($node->types as &$type) {
                $type = $this->resolveClassName($type);
            }
        } elseif ($node instanceof Expr\FuncCall) {
            if ($node->name instanceof Name) {
                $node->name = $this->resolveName($node->name, Stmt\Use_::TYPE_FUNCTION);
            }
        } elseif ($node instanceof Expr\ConstFetch) {
            $node->name = $this->resolveName($node->name, Stmt\Use_::TYPE_CONSTANT);
        } elseif ($node instanceof Stmt\TraitUse) {
            foreach ($node->traits as &$trait) {
                $trait = $this->resolveClassName($trait);
            }

            foreach ($node->adaptations as $adaptation) {
                if (null !== $adaptation->trait) {
                    $adaptation->trait = $this->resolveClassName($adaptation->trait);
                }

                if ($adaptation instanceof Stmt\TraitUseAdaptation\Precedence) {
                    foreach ($adaptation->insteadof as &$insteadof) {
                        $insteadof = $this->resolveClassName($insteadof);
                    }
                }
            }
        }

        return null;
    }

    /** @param Stmt\Use_::TYPE_* $type */
    private function addAlias(Node\UseItem $use, int $type, ?Name $prefix = null): void {
        // Add prefix for group uses
        $name = $prefix ? Name::concat($prefix, $use->name) : $use->name;
        // Type is determined either by individual element or whole use declaration
        $type |= $use->type;

        $this->nameContext->addAlias(
            $name, (string) $use->getAlias(), $type, $use->getAttributes()
        );
    }

    /** @param Stmt\Function_|Stmt\ClassMethod|Expr\Closure|Expr\ArrowFunction $node */
    private function resolveSignature($node): void {
        foreach ($node->params as $param) {
            $param->type = $this->resolveType($param->type);
            $this->resolveAttrGroups($param);
        }
        $node->returnType = $this->resolveType($node->returnType);
    }

    /**
     * @template T of Node\Identifier|Name|Node\ComplexType|null
     * @param T $node
     * @return T
     */
    private function resolveType(?Node $node): ?Node {
        if ($node instanceof Name) {
            return $this->resolveClassName($node);
        }
        if ($node instanceof Node\NullableType) {
            $node->type = $this->resolveType($node->type);
            return $node;
        }
        if ($node instanceof Node\UnionType || $node instanceof Node\IntersectionType) {
            foreach ($node->types as &$type) {
                $type = $this->resolveType($type);
            }
            return $node;
        }
        return $node;
    }

    /**
     * Resolve name, according to name resolver options.
     *
     * @param Name $name Function or constant name to resolve
     * @param Stmt\Use_::TYPE_* $type One of Stmt\Use_::TYPE_*
     *
     * @return Name Resolved name, or original name with attribute
     */
    protected function resolveName(Name $name, int $type): Name {
        if (!$this->replaceNodes) {
            $resolvedName = $this->nameContext->getResolvedName($name, $type);
            if (null !== $resolvedName) {
                $name->setAttribute('resolvedName', $resolvedName);
            } else {
                $name->setAttribute('namespacedName', FullyQualified::concat(
                    $this->nameContext->getNamespace(), $name, $name->getAttributes()));
            }
            return $name;
        }

        if ($this->preserveOriginalNames) {
            // Save the original name
            $originalName = $name;
            $name = clone $originalName;
            $name->setAttribute('originalName', $originalName);
        }

        $resolvedName = $this->nameContext->getResolvedName($name, $type);
        if (null !== $resolvedName) {
            return $resolvedName;
        }

        // unqualified names inside a namespace cannot be resolved at compile-time
        // add the namespaced version of the name as an attribute
        $name->setAttribute('namespacedName', FullyQualified::concat(
            $this->nameContext->getNamespace(), $name, $name->getAttributes()));
        return $name;
    }

    protected function resolveClassName(Name $name): Name {
        return $this->resolveName($name, Stmt\Use_::TYPE_NORMAL);
    }

    protected function addNamespacedName(Node $node): void {
        $node->namespacedName = Name::concat(
            $this->nameContext->getNamespace(), (string) $node->name);
    }

    protected function resolveAttrGroups(Node $node): void {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attr->name = $this->resolveClassName($attr->name);
            }
        }
    }
}
