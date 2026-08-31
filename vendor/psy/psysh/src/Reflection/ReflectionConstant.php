<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Reflection;

/**
 * Somehow the standard reflection library doesn't include constants.
 *
 * ReflectionConstant corrects that omission.
 */
class ReflectionConstant implements \Reflector
{
    public $name;
    /** @var mixed */
    private $value;

    private const MAGIC_CONSTANTS = [
        '__LINE__',
        '__FILE__',
        '__DIR__',
        '__FUNCTION__',
        '__CLASS__',
        '__TRAIT__',
        '__METHOD__',
        '__NAMESPACE__',
        '__COMPILER_HALT_OFFSET__',
    ];

    /**
     * Construct a ReflectionConstant object.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        if (!\defined($name) && !self::isMagicConstant($name)) {
            throw new \InvalidArgumentException('Unknown constant: '.$name);
        }

        if (!self::isMagicConstant($name)) {
            $this->value = @\constant($name);
        }
    }

    /**
     * Exports a reflection.
     *
     * @param string $name
     * @param bool   $return pass true to return the export, as opposed to emitting it
     *
     * @return string|null
     */
    public static function export(string $name, bool $return = false)
    {
        $refl = new self($name);
        $value = $refl->getValue();

        $str = \sprintf('Constant [ %s %s ] { %s }', \gettype($value), $refl->getName(), $value);

        if ($return) {
            return $str;
        }

        echo $str."\n";

        return null;
    }

    public static function isMagicConstant($name)
    {
        return \in_array($name, self::MAGIC_CONSTANTS);
    }

    /**
     * Get the constant's docblock.
     *
     * @return false
     */
    public function getDocComment(): bool
    {
        return false;
    }

    /**
     * Gets the constant name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the namespace name.
     *
     * Returns '' when the constant is not namespaced.
     */
    public function getNamespaceName(): string
    {
        if (!$this->inNamespace()) {
            return '';
        }

        return \preg_replace('/\\\\[^\\\\]+$/', '', $this->name);
    }

    /**
     * Gets the value of the constant.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Checks if this constant is defined in a namespace.
     */
    public function inNamespace(): bool
    {
        return \strpos($this->name, '\\') !== false;
    }

    /**
     * To string.
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Gets the constant's file name.
     *
     * Currently returns null, because if it returns a file name the signature
     * formatter will barf.
     */
    public function getFileName()
    {
        return;
        // return $this->class->getFileName();
    }

    /**
     * Get the code start line.
     *
     * @throws \RuntimeException
     */
    public function getStartLine()
    {
        throw new \RuntimeException('Not yet implemented because it\'s unclear what I should do here :)');
    }

    /**
     * Get the code end line.
     *
     * @throws \RuntimeException
     */
    public function getEndLine()
    {
        return $this->getStartLine();
    }
}
