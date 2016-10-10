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

use Symfony\Component\Serializer\Exception\RuntimeException;

/**
 * Encoder delegating the decoding to a chain of encoders.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class ChainEncoder implements EncoderInterface
{
    protected $encoders = array();
    protected $encoderByFormat = array();

    public function __construct(array $encoders = array())
    {
        $this->encoders = $encoders;
    }

    /**
     * {@inheritdoc}
     */
    final public function encode($data, $format, array $context = array())
    {
        return $this->getEncoder($format)->encode($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        try {
            $this->getEncoder($format);
        } catch (RuntimeException $e) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the normalization is needed for the given format.
     *
     * @param string $format
     *
     * @return bool
     */
    public function needsNormalization($format)
    {
        $encoder = $this->getEncoder($format);

        if (!$encoder instanceof NormalizationAwareInterface) {
            return true;
        }

        if ($encoder instanceof self) {
            return $encoder->needsNormalization($format);
        }

        return false;
    }

    /**
     * Gets the encoder supporting the format.
     *
     * @param string $format
     *
     * @return EncoderInterface
     *
     * @throws RuntimeException if no encoder is found
     */
    private function getEncoder($format)
    {
        if (isset($this->encoderByFormat[$format])
            && isset($this->encoders[$this->encoderByFormat[$format]])
        ) {
            return $this->encoders[$this->encoderByFormat[$format]];
        }

        foreach ($this->encoders as $i => $encoder) {
            if ($encoder->supportsEncoding($format)) {
                $this->encoderByFormat[$format] = $i;

                return $encoder;
            }
        }

        throw new RuntimeException(sprintf('No encoder found for format "%s".', $format));
    }
}
