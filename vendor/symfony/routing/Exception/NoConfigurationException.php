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
 * Exception thrown when no routes are configured.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class NoConfigurationException extends ResourceNotFoundException
{
}
