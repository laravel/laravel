<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Class_ extends Declaration {
    protected string $name;
    protected ?Name $extends = null;
    /** @var list<Name> */
    protected array $implements = [];
    protected int $flags = 0;
    /** @var list<Stmt\TraitUse> */
    protected array $uses = [];
    /** @var list<Stmt\ClassConst> */
    protected array $constants = [];
    /** @var list<Stmt\Property> */
    protected array $properties = [];
    /** @var list<Stmt\ClassMethod> */
    protected array $methods = [];
    /** @var list<Node\AttributeGroup> */
    protected array $attributeGroups = [];

    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * Extends a class.
     *
     * @param Name|string $class Name of class to extend
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function extend($class) {
        $this->extends = BuilderHelpers::normalizeName($class);

        return $this;
    }

    /**
     * Implements one or more interfaces.
     *
     * @param Name|string ...$interfaces Names of interfaces to implement
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function implement(...$interfaces) {
        foreach ($interfaces as $interface) {
            $this->implements[] = BuilderHelpers::normalizeName($interface);
        }

        return $this;
    }

    /**
     * Makes the class abstract.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeAbstract() {
        $this->flags = BuilderHelpers::addClassModifier($this->flags, Modifiers::ABSTRACT);

        return $this;
    }

    /**
     * Makes the class final.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeFinal() {
        $this->flags = BuilderHelpers::addClassModifier($this->flags, Modifiers::FINAL);

        return $this;
    }

    /**
     * Makes the class readonly.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeReadonly() {
        $this->flags = BuilderHelpers::addClassModifier($this->flags, Modifiers::READONLY);

        return $this;
    }

    /**
     * Adds a statement.
     *
     * @param Stmt|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt) {
        $stmt = BuilderHelpers::normalizeNode($stmt);

        if ($stmt instanceof Stmt\Property) {
            $this->properties[] = $stmt;
        } elseif ($stmt instanceof Stmt\ClassMethod) {
            $this->methods[] = $stmt;
        } elseif ($stmt instanceof Stmt\TraitUse) {
            $this->uses[] = $stmt;
        } elseif ($stmt instanceof Stmt\ClassConst) {
            $this->constants[] = $stmt;
        } else {
            throw new \LogicException(sprintf('Unexpected node of type "%s"', $stmt->getType()));
        }

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
     * Returns the built class node.
     *
     * @return Stmt\Class_ The built class node
     */
    public function getNode(): PhpParser\Node {
        return new Stmt\Class_($this->name, [
            'flags' => $this->flags,
            'extends' => $this->extends,
            'implements' => $this->implements,
            'stmts' => array_merge($this->uses, $this->constants, $this->properties, $this->methods),
            'attrGroups' => $this->attributeGroups,
        ], $this->attributes);
    }
}
