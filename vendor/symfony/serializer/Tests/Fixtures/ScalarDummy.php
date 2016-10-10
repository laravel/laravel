<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Fixtures;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ScalarDummy implements NormalizableInterface, DenormalizableInterface
{
    public $foo;
    public $xmlFoo;

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        return $format === 'xml' ? $this->xmlFoo : $this->foo;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
    {
        if ($format === 'xml') {
            $this->xmlFoo = $data;
        } else {
            $this->foo = $data;
        }
    }
}
