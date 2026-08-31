<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type;

use function array_pop;
use function assert;
use function explode;
use function implode;
use function substr;
use ReflectionClass;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final readonly class TypeName
{
    private ?string $namespaceName;

    /**
     * @var non-empty-string
     */
    private string $simpleName;

    /**
     * @param class-string $fullClassName
     */
    public static function fromQualifiedName(string $fullClassName): self
    {
        if ($fullClassName[0] === '\\') {
            $fullClassName = substr($fullClassName, 1);
        }

        $classNameParts = explode('\\', $fullClassName);

        $simpleName    = array_pop($classNameParts);
        $namespaceName = implode('\\', $classNameParts);

        assert($simpleName !== '');

        return new self($namespaceName, $simpleName);
    }

    /**
     * @param ReflectionClass<object> $type
     */
    public static function fromReflection(ReflectionClass $type): self
    {
        $simpleName = $type->getShortName();

        assert($simpleName !== '');

        return new self(
            $type->getNamespaceName(),
            $simpleName,
        );
    }

    /**
     * @param non-empty-string $simpleName
     */
    public function __construct(?string $namespaceName, string $simpleName)
    {
        if ($namespaceName === '') {
            $namespaceName = null;
        }

        $this->namespaceName = $namespaceName;
        $this->simpleName    = $simpleName;
    }

    public function namespaceName(): ?string
    {
        return $this->namespaceName;
    }

    /**
     * @return non-empty-string
     */
    public function simpleName(): string
    {
        return $this->simpleName;
    }

    /**
     * @return non-empty-string
     */
    public function qualifiedName(): string
    {
        return $this->namespaceName === null
             ? $this->simpleName
             : $this->namespaceName . '\\' . $this->simpleName;
    }

    public function isNamespaced(): bool
    {
        return $this->namespaceName !== null;
    }
}
