<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Stub;

use function array_pop;
use function count;
use function is_array;
use PHPUnit\Framework\MockObject\Invocation;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ReturnValueMap implements Stub
{
    /**
     * @var array<mixed>
     */
    private array $valueMap;

    /**
     * @param array<mixed> $valueMap
     */
    public function __construct(array $valueMap)
    {
        $this->valueMap = $valueMap;
    }

    public function invoke(Invocation $invocation): mixed
    {
        $parameterCount = count($invocation->parameters());

        foreach ($this->valueMap as $map) {
            if (!is_array($map) || $parameterCount !== (count($map) - 1)) {
                continue;
            }

            $return = array_pop($map);

            if ($invocation->parameters() === $map) {
                return $return;
            }
        }

        return null;
    }
}
