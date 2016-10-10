<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\OptionsResolver\Exception;

/**
 * Thrown when trying to read an option that has no value set.
 *
 * When accessing optional options from within a lazy option or normalizer you should first
 * check whether the optional option is set. You can do this with `isset($options['optional'])`.
 * In contrast to the {@link UndefinedOptionsException}, this is a runtime exception that can
 * occur when evaluating lazy options.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class NoSuchOptionException extends \OutOfBoundsException implements ExceptionInterface
{
}
