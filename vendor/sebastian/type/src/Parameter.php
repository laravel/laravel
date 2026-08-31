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

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final readonly class Parameter
{
    /**
     * @var non-empty-string
     */
    private string $name;
    private Type $type;

    /**
     * @param non-empty-string $name
     */
    public function __construct(string $name, Type $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function type(): Type
    {
        return $this->type;
    }
}
