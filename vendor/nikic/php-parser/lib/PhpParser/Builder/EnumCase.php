<?php

declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;

class EnumCase implements PhpParser\Builder {
    /** @var Identifier|string */
    protected $name;
    protected ?Node\Expr $value = null;
    /** @var array<string, mixed> */
    protected array $attributes = [];

    /** @var list<Node\AttributeGroup> */
    protected array $attributeGroups = [];

    /**
     * Creates an enum case builder.
     *
     * @param string|Identifier $name Name
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Sets the value.
     *
     * @param Node\Expr|string|int $value
     *
     * @return $this
     */
    public function setValue($value) {
        $this->value = BuilderHelpers::normalizeValue($value);

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
     * Returns the built enum case node.
     *
     * @return Stmt\EnumCase The built constant node
     */
    public function getNode(): PhpParser\Node {
        return new Stmt\EnumCase(
            $this->name,
            $this->value,
            $this->attributeGroups,
            $this->attributes
        );
    }
}
