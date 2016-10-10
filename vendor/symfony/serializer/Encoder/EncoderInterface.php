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

use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Defines the interface of encoders.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface EncoderInterface
{
    /**
     * Encodes data into the given format.
     *
     * @param mixed  $data    Data to encode
     * @param string $format  Format name
     * @param array  $context options that normalizers/encoders have access to.
     *
     * @return scalar
     *
     * @throws UnexpectedValueException
     */
    public function encode($data, $format, array $context = array());

    /**
     * Checks whether the serializer can encode to given format.
     *
     * @param string $format format name
     *
     * @return bool
     */
    public function supportsEncoding($format);
}
