<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Exception;

/**
 * Exception thrown when a parameter is not valid
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 *
 * @api
 */
class InvalidParameterException extends \InvalidArgumentException implements ExceptionInterface
{
}
