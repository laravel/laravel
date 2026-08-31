<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeCleaner;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\While_;
use Psy\Exception\FatalErrorException;

/**
 * Validate that classes exist.
 *
 * This pass throws a FatalErrorException rather than letting PHP run
 * headfirst into a real fatal error and die.
 */
class ValidClassNamePass extends NamespaceAwarePass
{
    const CLASS_TYPE = 'class';
    const INTERFACE_TYPE = 'interface';
    const TRAIT_TYPE = 'trait';

    private int $conditionalScopes = 0;

    /**
     * Validate class, interface and trait definitions.
     *
     * Validate them upon entering the node, so that we know about their
     * presence and can validate constant fetches and static calls in class or
     * trait methods.
     *
     * @param Node $node
     *
     * @return int|Node|null Replacement node (or special return value)
     */
    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if (self::isConditional($node)) {
            $this->conditionalScopes++;

            return null;
        }

        if ($this->conditionalScopes === 0) {
            if ($node instanceof Class_) {
                $this->validateClassStatement($node);
            } elseif ($node instanceof Interface_) {
                $this->validateInterfaceStatement($node);
            } elseif ($node instanceof Trait_) {
                $this->validateTraitStatement($node);
            }
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return int|Node|Node[]|null Replacement node (or special return value)
     */
    public function leaveNode(Node $node)
    {
        if (self::isConditional($node)) {
            $this->conditionalScopes--;
        }

        return null;
    }

    private static function isConditional(Node $node): bool
    {
        return $node instanceof If_ ||
            $node instanceof While_ ||
            $node instanceof Do_ ||
            $node instanceof Switch_ ||
            $node instanceof Ternary;
    }

    /**
     * Validate a class definition statement.
     *
     * @param Class_ $stmt
     */
    protected function validateClassStatement(Class_ $stmt)
    {
        $this->ensureCanDefine($stmt, self::CLASS_TYPE);
        if (isset($stmt->extends)) {
            $this->ensureClassExists($this->getFullyQualifiedName($stmt->extends), $stmt);
        }
        $this->ensureInterfacesExist($stmt->implements, $stmt);
    }

    /**
     * Validate an interface definition statement.
     *
     * @param Interface_ $stmt
     */
    protected function validateInterfaceStatement(Interface_ $stmt)
    {
        $this->ensureCanDefine($stmt, self::INTERFACE_TYPE);
        $this->ensureInterfacesExist($stmt->extends, $stmt);
    }

    /**
     * Validate a trait definition statement.
     *
     * @param Trait_ $stmt
     */
    protected function validateTraitStatement(Trait_ $stmt)
    {
        $this->ensureCanDefine($stmt, self::TRAIT_TYPE);
    }

    /**
     * Ensure that no class, interface or trait name collides with a new definition.
     *
     * @throws FatalErrorException
     *
     * @param Stmt   $stmt
     * @param string $scopeType
     */
    protected function ensureCanDefine(Stmt $stmt, string $scopeType = self::CLASS_TYPE)
    {
        // Anonymous classes don't have a name, and uniqueness shouldn't be enforced.
        if (!\property_exists($stmt, 'name') || $stmt->name === null) {
            return;
        }

        $name = $this->getFullyQualifiedName($stmt->name);

        // check for name collisions
        $errorType = null;
        if ($this->classExists($name)) {
            $errorType = self::CLASS_TYPE;
        } elseif ($this->interfaceExists($name)) {
            $errorType = self::INTERFACE_TYPE;
        } elseif ($this->traitExists($name)) {
            $errorType = self::TRAIT_TYPE;
        }

        if ($errorType !== null) {
            throw $this->createError(\sprintf('%s named %s already exists', \ucfirst($errorType), $name), $stmt);
        }

        // Store creation for the rest of this code snippet so we can find local
        // issue too
        $this->currentScope[\strtolower($name)] = $scopeType;
    }

    /**
     * Ensure that a referenced class exists.
     *
     * @throws FatalErrorException
     *
     * @param string $name
     * @param Stmt   $stmt
     */
    protected function ensureClassExists(string $name, Stmt $stmt)
    {
        if (!$this->classExists($name)) {
            throw $this->createError(\sprintf('Class \'%s\' not found', $name), $stmt);
        }
    }

    /**
     * Ensure that a referenced class _or interface_ exists.
     *
     * @throws FatalErrorException
     *
     * @param string $name
     * @param Stmt   $stmt
     */
    protected function ensureClassOrInterfaceExists(string $name, Stmt $stmt)
    {
        if (!$this->classExists($name) && !$this->interfaceExists($name)) {
            throw $this->createError(\sprintf('Class \'%s\' not found', $name), $stmt);
        }
    }

    /**
     * Ensure that a referenced class _or trait_ exists.
     *
     * @throws FatalErrorException
     *
     * @param string $name
     * @param Stmt   $stmt
     */
    protected function ensureClassOrTraitExists(string $name, Stmt $stmt)
    {
        if (!$this->classExists($name) && !$this->traitExists($name)) {
            throw $this->createError(\sprintf('Class \'%s\' not found', $name), $stmt);
        }
    }

    /**
     * Ensure that a statically called method exists.
     *
     * @throws FatalErrorException
     *
     * @param string $class
     * @param string $name
     * @param Stmt   $stmt
     */
    protected function ensureMethodExists(string $class, string $name, Stmt $stmt)
    {
        $this->ensureClassOrTraitExists($class, $stmt);

        // let's pretend all calls to self, parent and static are valid
        if (\in_array(\strtolower($class), ['self', 'parent', 'static'])) {
            return;
        }

        // ... and all calls to classes defined right now
        if ($this->findInScope($class) === self::CLASS_TYPE) {
            return;
        }

        // if method name is an expression, give it a pass for now
        if ($name instanceof Expr) {
            return;
        }

        if (!\method_exists($class, $name) && !\method_exists($class, '__callStatic')) {
            throw $this->createError(\sprintf('Call to undefined method %s::%s()', $class, $name), $stmt);
        }
    }

    /**
     * Ensure that a referenced interface exists.
     *
     * @throws FatalErrorException
     *
     * @param Interface_[] $interfaces
     * @param Stmt         $stmt
     */
    protected function ensureInterfacesExist(array $interfaces, Stmt $stmt)
    {
        foreach ($interfaces as $interface) {
            /** @var string $name */
            $name = $this->getFullyQualifiedName($interface);
            if (!$this->interfaceExists($name)) {
                throw $this->createError(\sprintf('Interface \'%s\' not found', $name), $stmt);
            }
        }
    }

    /**
     * Check whether a class exists, or has been defined in the current code snippet.
     *
     * Gives `self`, `static` and `parent` a free pass.
     *
     * @param string $name
     */
    protected function classExists(string $name): bool
    {
        // Give `self`, `static` and `parent` a pass. This will actually let
        // some errors through, since we're not checking whether the keyword is
        // being used in a class scope.
        if (\in_array(\strtolower($name), ['self', 'static', 'parent'])) {
            return true;
        }

        return \class_exists($name) || $this->findInScope($name) === self::CLASS_TYPE;
    }

    /**
     * Check whether an interface exists, or has been defined in the current code snippet.
     *
     * @param string $name
     */
    protected function interfaceExists(string $name): bool
    {
        return \interface_exists($name) || $this->findInScope($name) === self::INTERFACE_TYPE;
    }

    /**
     * Check whether a trait exists, or has been defined in the current code snippet.
     *
     * @param string $name
     */
    protected function traitExists(string $name): bool
    {
        return \trait_exists($name) || $this->findInScope($name) === self::TRAIT_TYPE;
    }

    /**
     * Find a symbol in the current code snippet scope.
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function findInScope(string $name)
    {
        $name = \strtolower($name);
        if (isset($this->currentScope[$name])) {
            return $this->currentScope[$name];
        }

        return null;
    }

    /**
     * Error creation factory.
     *
     * @param string $msg
     * @param Stmt   $stmt
     */
    protected function createError(string $msg, Stmt $stmt): FatalErrorException
    {
        return new FatalErrorException($msg, 0, \E_ERROR, null, $stmt->getStartLine());
    }
}
