<?php

declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;

class ClassConst implements PhpParser\Builder {
    protected int $flags = 0;
    /** @var array<string, mixed> */
    protected array $attributes = [];
    /** @var list<Const_> */
    protected array $constants = [];

    /** @var list<Node\AttributeGroup> */
    protected array $attributeGroups = [];
    /** @var Identifier|Node\Name|Node\ComplexType|null */
    protected ?Node $type = null;

    /**
     * Creates a class constant builder
     *
     * @param string|Identifier $name Name
     * @param Node\Expr|bool|null|int|float|string|array|\UnitEnum $value Value
     */
    public function __construct($name, $value) {
        $this->constants = [new Const_($name, BuilderHelpers::normalizeValue($value))];
    }

    /**
     * Add another constant to const group
     *
     * @param string|Identifier $name Name
     * @param Node\Expr|bool|null|int|float|string|array|\UnitEnum $value Value
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addConst($name, $value) {
        $this->constants[] = new Const_($name, BuilderHelpers::normalizeValue($value));

        return $this;
    }

    /**
     * Makes the constant public.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePublic() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PUBLIC);

        return $this;
    }

    /**
     * Makes the constant protected.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PROTECTED);

        return $this;
    }

    /**
     * Makes the constant private.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PRIVATE);

        return $this;
    }

    /**
     * Makes the constant final.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeFinal() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::FINAL);

        return $this;
    }

    /**
     * Sets doc comment for the constant.
     *
     * @param PhpParser\Comment\Doc|string $docComment Doc comment to set
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDocComment($docComment) {
        $this->attributes = [
            'comments' => [BuilderHelpers::normalizeDocComment($docComment)]
        ];

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
     * Sets the constant type.
     *
     * @param string|Node\Name|Identifier|Node\ComplexType $type
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = BuilderHelpers::normalizeType($type);

        return $this;
    }

    /**
     * Returns the built class node.
     *
     * @return Stmt\ClassConst The built constant node
     */
    public function getNode(): PhpParser\Node {
        return new Stmt\ClassConst(
            $this->constants,
            $this->flags,
            $this->attributes,
            $this->attributeGroups,
            $this->type
        );
    }
}
