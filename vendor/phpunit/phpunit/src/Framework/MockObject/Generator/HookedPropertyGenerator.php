<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class HookedPropertyGenerator
{
    /**
     * @param class-string         $className
     * @param list<HookedProperty> $properties
     *
     * @return non-empty-string
     */
    public function generate(string $className, array $properties): string
    {
        $code = '';

        foreach ($properties as $property) {
            $code .= sprintf(
                <<<'EOT'

    public %s $%s {
EOT,
                $property->type()->asString(),
                $property->name(),
            );

            if ($property->hasGetHook()) {
                $code .= sprintf(
                    <<<'EOT'

        get {
            return $this->__phpunit_getInvocationHandler()->invoke(
                new \PHPUnit\Framework\MockObject\Invocation(
                    '%s', '$%s::get', [], '%s', $this, false
                )
            );
        }

EOT,
                    $className,
                    $property->name(),
                    $property->type()->asString(),
                );
            }

            if ($property->hasSetHook()) {
                $code .= sprintf(
                    <<<'EOT'

        set (%s $value) {
            $this->__phpunit_getInvocationHandler()->invoke(
                new \PHPUnit\Framework\MockObject\Invocation(
                    '%s', '$%s::set', [$value], 'void', $this, false
                )
            );
        }

EOT,
                    $property->setterType()->asString(),
                    $className,
                    $property->name(),
                );
            }

            $code .= <<<'EOT'
    }

EOT;
        }

        return $code;
    }
}
