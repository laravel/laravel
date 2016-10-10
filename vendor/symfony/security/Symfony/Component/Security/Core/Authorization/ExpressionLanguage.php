<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authorization;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

/**
 * Adds some function to the default ExpressionLanguage.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExpressionLanguage extends BaseExpressionLanguage
{
    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register('is_anonymous', function () {
            return '$trust_resolver->isAnonymous($token)';
        }, function (array $variables) {
            return $variables['trust_resolver']->isAnonymous($variables['token']);
        });

        $this->register('is_authenticated', function () {
            return '$token && !$trust_resolver->isAnonymous($token)';
        }, function (array $variables) {
            return $variables['token'] && !$variables['trust_resolver']->isAnonymous($variables['token']);
        });

        $this->register('is_fully_authenticated', function () {
            return '$trust_resolver->isFullFledged($token)';
        }, function (array $variables) {
            return $variables['trust_resolver']->isFullFledged($variables['token']);
        });

        $this->register('is_remember_me', function () {
            return '$trust_resolver->isRememberMe($token)';
        }, function (array $variables) {
            return $variables['trust_resolver']->isRememberMe($variables['token']);
        });

        $this->register('has_role', function ($role) {
            return sprintf('in_array(%s, $roles)', $role);
        }, function (array $variables, $role) {
            return in_array($role, $variables['roles']);
        });
    }
}
