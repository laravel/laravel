<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Encoder;

/**
 * Defines the interface of encoders that will normalize data themselves.
 *
 * Implementing this interface essentially just tells the Serializer that the
 * data should not be pre-normalized before being passed to this Encoder.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface NormalizationAwareInterface
{
}
