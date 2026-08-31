<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * Exposes functions defined in the request context to route conditions.
 *
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        private ServiceProviderInterface $functions,
    ) {
    }

    public function getFunctions(): array
    {
        $functions = [];

        foreach ($this->functions->getProvidedServices() as $function => $type) {
            $functions[] = new ExpressionFunction(
                $function,
                static fn (...$args) => \sprintf('($context->getParameter(\'_functions\')->get(%s)(%s))', var_export($function, true), implode(', ', $args)),
                static fn ($values, ...$args) => $values['context']->getParameter('_functions')->get($function)(...$args)
            );
        }

        return $functions;
    }

    public function get(string $function): callable
    {
        return $this->functions->get($function);
    }
}
