<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Contracts;

interface Conditionable
{
    /**
     * Apply the callback if the given "condition" is (or resolves to) true.
     *
     * @param (callable(static): bool)|bool $condition
     * @param callable(static): (static|null) $onSuccess
     * @param ?callable(static): (static|null) $onFail
     */
    public function when(callable|bool $condition, callable $onSuccess, ?callable $onFail = null): static;
}
