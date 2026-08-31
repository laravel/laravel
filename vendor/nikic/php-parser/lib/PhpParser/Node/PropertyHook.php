<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class PropertyHook extends NodeAbstract implements FunctionLike {
    /** @var AttributeGroup[] PHP attribute groups */
    public array $attrGroups;
    /** @var int Modifiers */
    public int $flags;
    /** @var bool Whether hook returns by reference */
    public bool $byRef;
    /** @var Identifier Hook name */
    public Identifier $name;
    /** @var Param[] Parameters */
    public array $params;
    /** @var null|Expr|Stmt[] Hook body */
    public $body;

    /**
     * Constructs a property hook node.
     *
     * @param string|Identifier $name Hook name
     * @param null|Expr|Stmt[] $body Hook body
     * @param array{
     *     flags?: int,
     *     byRef?: bool,
     *     params?: Param[],
     *     attrGroups?: AttributeGroup[],
     * } $subNodes Array of the following optional subnodes:
     *             'flags       => 0      : Flags
     *             'byRef'      => false  : Whether hook returns by reference
     *             'params'     => array(): Parameters
     *             'attrGroups' => array(): PHP attribute groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, $body, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
        $this->body = $body;
        $this->flags = $subNodes['flags'] ?? 0;
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->params = $subNodes['params'] ?? [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function returnsByRef(): bool {
        return $this->byRef;
    }

    public function getParams(): array {
        return $this->params;
    }

    public function getReturnType() {
        return null;
    }

    /**
     * Whether the property hook is final.
     */
    public function isFinal(): bool {
        return (bool) ($this->flags & Modifiers::FINAL);
    }

    public function getStmts(): ?array {
        if ($this->body instanceof Expr) {
            $name = $this->name->toLowerString();
            if ($name === 'get') {
                return [new Return_($this->body)];
            }
            if ($name === 'set') {
                if (!$this->hasAttribute('propertyName')) {
                    throw new \LogicException(
                        'Can only use getStmts() on a "set" hook if the "propertyName" attribute is set');
                }

                $propName = $this->getAttribute('propertyName');
                $prop = new PropertyFetch(new Variable('this'), (string) $propName);
                return [new Expression(new Assign($prop, $this->body))];
            }
            throw new \LogicException('Unknown property hook "' . $name . '"');
        }
        return $this->body;
    }

    public function getAttrGroups(): array {
        return $this->attrGroups;
    }

    public function getType(): string {
        return 'PropertyHook';
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'flags', 'byRef', 'name', 'params', 'body'];
    }
}
