<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\PropertyItem;

abstract class ClassLike extends Node\Stmt {
    /** @var Node\Identifier|null Name */
    public ?Node\Identifier $name;
    /** @var Node\Stmt[] Statements */
    public array $stmts;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public array $attrGroups;

    /** @var Node\Name|null Namespaced name (if using NameResolver) */
    public ?Node\Name $namespacedName;

    /**
     * @return list<TraitUse>
     */
    public function getTraitUses(): array {
        $traitUses = [];
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof TraitUse) {
                $traitUses[] = $stmt;
            }
        }
        return $traitUses;
    }

    /**
     * @return list<ClassConst>
     */
    public function getConstants(): array {
        $constants = [];
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof ClassConst) {
                $constants[] = $stmt;
            }
        }
        return $constants;
    }

    /**
     * @return list<Property>
     */
    public function getProperties(): array {
        $properties = [];
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof Property) {
                $properties[] = $stmt;
            }
        }
        return $properties;
    }

    /**
     * Gets property with the given name defined directly in this class/interface/trait.
     *
     * @param string $name Name of the property
     *
     * @return Property|null Property node or null if the property does not exist
     */
    public function getProperty(string $name): ?Property {
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $prop) {
                    if ($prop instanceof PropertyItem && $name === $prop->name->toString()) {
                        return $stmt;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Gets all methods defined directly in this class/interface/trait
     *
     * @return list<ClassMethod>
     */
    public function getMethods(): array {
        $methods = [];
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                $methods[] = $stmt;
            }
        }
        return $methods;
    }

    /**
     * Gets method with the given name defined directly in this class/interface/trait.
     *
     * @param string $name Name of the method (compared case-insensitively)
     *
     * @return ClassMethod|null Method node or null if the method does not exist
     */
    public function getMethod(string $name): ?ClassMethod {
        $lowerName = strtolower($name);
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $lowerName === $stmt->name->toLowerString()) {
                return $stmt;
            }
        }
        return null;
    }
}
