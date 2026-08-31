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

/**
 * A ServiceProviderInterface that is also countable and iterable.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template-covariant T of mixed
 *
 * @extends ServiceProviderInterface<T>
 * @extends \IteratorAggregate<string, T>
 */
interface ServiceCollectionInterface extends ServiceProviderInterface, \Countable, \IteratorAggregate
{
}
