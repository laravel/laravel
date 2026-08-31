<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;

class ClassMethod extends Node\Stmt implements FunctionLike {
    /** @var int Flags */
    public int $flags;
    /** @var bool Whether to return by reference */
    public bool $byRef;
    /** @var Node\Identifier Name */
    public Node\Identifier $name;
    /** @var Node\Param[] Parameters */
    public array $params;
    /** @var null|Node\Identifier|Node\Name|Node\ComplexType Return type */
    public ?Node $returnType;
    /** @var Node\Stmt[]|null Statements */
    public ?array $stmts;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public array $attrGroups;

    /** @var array<string, bool> */
    private static array $magicNames = [
        '__construct'   => true,
        '__destruct'    => true,
        '__call'        => true,
        '__callstatic'  => true,
        '__get'         => true,
        '__set'         => true,
        '__isset'       => true,
        '__unset'       => true,
        '__sleep'       => true,
        '__wakeup'      => true,
        '__tostring'    => true,
        '__set_state'   => true,
        '__clone'       => true,
        '__invoke'      => true,
        '__debuginfo'   => true,
        '__serialize'   => true,
        '__unserialize' => true,
    ];

    /**
     * Constructs a class method node.
     *
     * @param string|Node\Identifier $name Name
     * @param array{
     *     flags?: int,
     *     byRef?: bool,
     *     params?: Node\Param[],
     *     returnType?: null|Node\Identifier|Node\Name|Node\ComplexType,
     *     stmts?: Node\Stmt[]|null,
     *     attrGroups?: Node\AttributeGroup[],
     * } $subNodes Array of the following optional subnodes:
     *             'flags       => 0              : Flags
     *             'byRef'      => false          : Whether to return by reference
     *             'params'     => array()        : Parameters
     *             'returnType' => null           : Return type
     *             'stmts'      => array()        : Statements
     *             'attrGroups' => array()        : PHP attribute groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->flags = $subNodes['flags'] ?? $subNodes['type'] ?? 0;
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->params = $subNodes['params'] ?? [];
        $this->returnType = $subNodes['returnType'] ?? null;
        $this->stmts = array_key_exists('stmts', $subNodes) ? $subNodes['stmts'] : [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'flags', 'byRef', 'name', 'params', 'returnType', 'stmts'];
    }

    public function returnsByRef(): bool {
        return $this->byRef;
    }

    public function getParams(): array {
        return $this->params;
    }

    public function getReturnType() {
        return $this->returnType;
    }

    public function getStmts(): ?array {
        return $this->stmts;
    }

    public function getAttrGroups(): array {
        return $this->attrGroups;
    }

    /**
     * Whether the method is explicitly or implicitly public.
     */
    public function isPublic(): bool {
        return ($this->flags & Modifiers::PUBLIC) !== 0
            || ($this->flags & Modifiers::VISIBILITY_MASK) === 0;
    }

    /**
     * Whether the method is protected.
     */
    public function isProtected(): bool {
        return (bool) ($this->flags & Modifiers::PROTECTED);
    }

    /**
     * Whether the method is private.
     */
    public function isPrivate(): bool {
        return (bool) ($this->flags & Modifiers::PRIVATE);
    }

    /**
     * Whether the method is abstract.
     */
    public function isAbstract(): bool {
        return (bool) ($this->flags & Modifiers::ABSTRACT);
    }

    /**
     * Whether the method is final.
     */
    public function isFinal(): bool {
        return (bool) ($this->flags & Modifiers::FINAL);
    }

    /**
     * Whether the method is static.
     */
    public function isStatic(): bool {
        return (bool) ($this->flags & Modifiers::STATIC);
    }

    /**
     * Whether the method is magic.
     */
    public function isMagic(): bool {
        return isset(self::$magicNames[$this->name->toLowerString()]);
    }

    public function getType(): string {
        return 'Stmt_ClassMethod';
    }
}
