<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Contracts\Service;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\Attribute\SubscribedService;

/**
 * Implementation of ServiceSubscriberInterface that determines subscribed services
 * from methods that have the #[SubscribedService] attribute.
 *
 * Service ids are available as "ClassName::methodName" so that the implementation
 * of subscriber methods can be just `return $this->container->get(__METHOD__);`.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ServiceMethodsSubscriberTrait
{
    protected ContainerInterface $container;

    public static function getSubscribedServices(): array
    {
        $services = method_exists(get_parent_class(self::class) ?: '', __FUNCTION__) ? parent::getSubscribedServices() : [];

        $reflectors = [
            ...(new \ReflectionClass(self::class))->getMethods(),
            ...(new \ReflectionClass(self::class))->getProperties(),
        ];

        foreach ($reflectors as $reflector) {
            if (self::class !== $reflector->class) {
                continue;
            }

            if (!$reflectionAttribute = $reflector->getAttributes(SubscribedService::class)[0] ?? null) {
                continue;
            }

            if ($reflector instanceof \ReflectionMethod) {
                if ($reflector->isStatic() || $reflector->isAbstract() || $reflector->isGenerator() || $reflector->isInternal() || $reflector->getNumberOfRequiredParameters()) {
                    throw new \LogicException(\sprintf('Cannot use "%s" on method "%s::%s()" (can only be used on non-static, non-abstract methods with no parameters).', SubscribedService::class, self::class, $reflector->name));
                }

                if (!$autowireType = $reflector->getReturnType()) {
                    throw new \LogicException(\sprintf('Cannot use "%s" on methods without a return type in "%s::%s()".', SubscribedService::class, $reflector->name, self::class));
                }

                $defaultKey = self::class.'::'.$reflector->name;
            } elseif ($reflector instanceof \ReflectionProperty) {
                if (\PHP_VERSION_ID < 80400 || !$reflector->hasHook(\PropertyHookType::Get)) {
                    throw new \LogicException(\sprintf('Cannot use "%s" on property "%s::$%s" (can only be used on properties with a get hook).', SubscribedService::class, self::class, $reflector->name));
                }

                if (!$autowireType = $reflector->getType()) {
                    throw new \LogicException(\sprintf('Cannot use "%s" on properties without a type in "%s::$%s".', SubscribedService::class, $reflector->name, self::class));
                }

                $defaultKey = self::class.'::$'.$reflector->name.'::get';
            } else {
                throw new \LogicException('Unexpected reflector: '.get_debug_type($reflector));
            }

            /* @var SubscribedService $attribute */
            $attribute = $reflectionAttribute->newInstance();
            $attribute->key ??= $defaultKey;
            $attribute->type ??= $autowireType instanceof \ReflectionNamedType ? $autowireType->getName() : (string) $autowireType;
            $attribute->nullable = $attribute->nullable ?: $autowireType->allowsNull();

            if ($attribute->attributes) {
                $services[] = $attribute;
            } else {
                $services[$attribute->key] = ($attribute->nullable ? '?' : '').$attribute->type;
            }
        }

        return $services;
    }

    #[Required]
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $ret = null;
        if (method_exists(get_parent_class(self::class) ?: '', __FUNCTION__)) {
            $ret = parent::setContainer($container);
        }

        $this->container = $container;

        return $ret;
    }
}
