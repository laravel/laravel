<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Enum_ extends Declaration {
    protected string $name;
    protected ?Identifier $scalarType = null;
    /** @var list<Name> */
    protected array $implements = [];
    /** @var list<Stmt\TraitUse> */
    protected array $uses = [];
    /** @var list<Stmt\EnumCase> */
    protected array $enumCases = [];
    /** @var list<Stmt\ClassConst> */
    protected array $constants = [];
    /** @var list<Stmt\ClassMethod> */
    protected array $methods = [];
    /** @var list<Node\AttributeGroup> */
    protected array $attributeGroups = [];

    /**
     * Creates an enum builder.
     *
     * @param string $name Name of the enum
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * Sets the scalar type.
     *
     * @param string|Identifier $scalarType
     *
     * @return $this
     */
    public function setScalarType($scalarType) {
        $this->scalarType = BuilderHelpers::normalizeType($scalarType);

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
     * Adds a statement.
     *
     * @param Stmt|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt) {
        $stmt = BuilderHelpers::normalizeNode($stmt);

        if ($stmt instanceof Stmt\EnumCase) {
            $this->enumCases[] = $stmt;
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
     * @return Stmt\Enum_ The built enum node
     */
    public function getNode(): PhpParser\Node {
        return new Stmt\Enum_($this->name, [
            'scalarType' => $this->scalarType,
            'implements' => $this->implements,
            'stmts' => array_merge($this->uses, $this->enumCases, $this->constants, $this->methods),
            'attrGroups' => $this->attributeGroups,
        ], $this->attributes);
    }
}
