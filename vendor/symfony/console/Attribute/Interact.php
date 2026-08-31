<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Attribute;

use Symfony\Component\Console\Exception\LogicException;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Interact implements InteractiveAttributeInterface
{
    private \ReflectionMethod $method;

    /**
     * @internal
     */
    public static function tryFrom(\ReflectionMethod $method): ?self
    {
        /** @var self|null $self */
        if (!$self = ($method->getAttributes(self::class)[0] ?? null)?->newInstance()) {
            return null;
        }

        if (!$method->isPublic() || $method->isStatic()) {
            throw new LogicException(\sprintf('The interactive method "%s::%s()" must be public and non-static.', $method->getDeclaringClass()->getName(), $method->getName()));
        }

        if ('__invoke' === $method->getName()) {
            throw new LogicException(\sprintf('The "%s::__invoke()" method cannot be used as an interactive method.', $method->getDeclaringClass()->getName()));
        }

        $self->method = $method;

        return $self;
    }

    /**
     * @internal
     */
    public function getFunction(object $instance): \ReflectionFunction
    {
        return new \ReflectionFunction($this->method->getClosure($instance));
    }
}
