<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Method extends FunctionLike {
    protected string $name;

    protected int $flags = 0;

    /** @var list<Stmt>|null */
    protected ?array $stmts = [];

    /** @var list<Node\AttributeGroup> */
    protected array $attributeGroups = [];

    /**
     * Creates a method builder.
     *
     * @param string $name Name of the method
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * Makes the method public.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePublic() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PUBLIC);

        return $this;
    }

    /**
     * Makes the method protected.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PROTECTED);

        return $this;
    }

    /**
     * Makes the method private.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PRIVATE);

        return $this;
    }

    /**
     * Makes the method static.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeStatic() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::STATIC);

        return $this;
    }

    /**
     * Makes the method abstract.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeAbstract() {
        if (!empty($this->stmts)) {
            throw new \LogicException('Cannot make method with statements abstract');
        }

        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::ABSTRACT);
        $this->stmts = null; // abstract methods don't have statements

        return $this;
    }

    /**
     * Makes the method final.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeFinal() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::FINAL);

        return $this;
    }

    /**
     * Adds a statement.
     *
     * @param Node|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt) {
        if (null === $this->stmts) {
            throw new \LogicException('Cannot add statements to an abstract method');
        }

        $this->stmts[] = BuilderHelpers::normalizeStmt($stmt);

        return $this;
    }

    /**
     * Adds an attribute group.
     *
     * @param Node\Attribute|Node\AttributeGroup $attribute
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addAttribute($attribute) {
        $this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

        return $this;
    }

    /**
     * Returns the built method node.
     *
     * @return Stmt\ClassMethod The built method node
     */
    public function getNode(): Node {
        return new Stmt\ClassMethod($this->name, [
            'flags'      => $this->flags,
            'byRef'      => $this->returnByRef,
            'params'     => $this->params,
            'returnType' => $this->returnType,
            'stmts'      => $this->stmts,
            'attrGroups' => $this->attributeGroups,
        ], $this->attributes);
    }
}
