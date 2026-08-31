<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\PropertyItem;

class Property extends Node\Stmt {
    /** @var int Modifiers */
    public int $flags;
    /** @var PropertyItem[] Properties */
    public array $props;
    /** @var null|Identifier|Name|ComplexType Type declaration */
    public ?Node $type;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public array $attrGroups;
    /** @var Node\PropertyHook[] Property hooks */
    public array $hooks;

    /**
     * Constructs a class property list node.
     *
     * @param int $flags Modifiers
     * @param PropertyItem[] $props Properties
     * @param array<string, mixed> $attributes Additional attributes
     * @param null|Identifier|Name|ComplexType $type Type declaration
     * @param Node\AttributeGroup[] $attrGroups PHP attribute groups
     * @param Node\PropertyHook[] $hooks Property hooks
     */
    public function __construct(int $flags, array $props, array $attributes = [], ?Node $type = null, array $attrGroups = [], array $hooks = []) {
        $this->attributes = $attributes;
        $this->flags = $flags;
        $this->props = $props;
        $this->type = $type;
        $this->attrGroups = $attrGroups;
        $this->hooks = $hooks;
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'flags', 'type', 'props', 'hooks'];
    }

    /**
     * Whether the property is explicitly or implicitly public.
     */
    public function isPublic(): bool {
        return ($this->flags & Modifiers::PUBLIC) !== 0
            || ($this->flags & Modifiers::VISIBILITY_MASK) === 0;
    }

    /**
     * Whether the property is protected.
     */
    public function isProtected(): bool {
        return (bool) ($this->flags & Modifiers::PROTECTED);
    }

    /**
     * Whether the property is private.
     */
    public function isPrivate(): bool {
        return (bool) ($this->flags & Modifiers::PRIVATE);
    }

    /**
     * Whether the property is static.
     */
    public function isStatic(): bool {
        return (bool) ($this->flags & Modifiers::STATIC);
    }

    /**
     * Whether the property is readonly.
     */
    public function isReadonly(): bool {
        return (bool) ($this->flags & Modifiers::READONLY);
    }

    /**
     * Whether the property is abstract.
     */
    public function isAbstract(): bool {
        return (bool) ($this->flags & Modifiers::ABSTRACT);
    }

    /**
     * Whether the property is final.
     */
    public function isFinal(): bool {
        return (bool) ($this->flags & Modifiers::FINAL);
    }

    /**
     * Whether the property has explicit public(set) visibility.
     */
    public function isPublicSet(): bool {
        return (bool) ($this->flags & Modifiers::PUBLIC_SET);
    }

    /**
     * Whether the property has explicit protected(set) visibility.
     */
    public function isProtectedSet(): bool {
        return (bool) ($this->flags & Modifiers::PROTECTED_SET);
    }

    /**
     * Whether the property has explicit private(set) visibility.
     */
    public function isPrivateSet(): bool {
        return (bool) ($this->flags & Modifiers::PRIVATE_SET);
    }

    public function getType(): string {
        return 'Stmt_Property';
    }
}
