<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Dumper\ContextProvider;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\VarDumper\Caster\ReflectionCaster;
use Symfony\Component\VarDumper\Cloner\VarCloner;

/**
 * Tries to provide context from a request.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
final class RequestContextProvider implements ContextProviderInterface
{
    private VarCloner $cloner;

    public function __construct(
        private RequestStack $requestStack,
    ) {
        $this->cloner = new VarCloner();
        $this->cloner->setMaxItems(0);
        $this->cloner->addCasters(ReflectionCaster::UNSET_CLOSURE_FILE_INFO);
    }

    public function getContext(): ?array
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return null;
        }

        $controller = $request->attributes->get('_controller');

        return [
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'controller' => $controller ? $this->cloner->cloneVar($controller) : $controller,
            'identifier' => hash('xxh128', spl_object_id($request).'@'.$_SERVER['REQUEST_TIME_FLOAT']),
        ];
    }
}
