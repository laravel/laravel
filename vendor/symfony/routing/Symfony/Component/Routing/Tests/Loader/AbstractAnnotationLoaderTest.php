<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Loader;

abstract class AbstractAnnotationLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function getReader()
    {
        return $this->getMockBuilder('Doctrine\Common\Annotations\Reader')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function getClassLoader($reader)
    {
        return $this->getMockBuilder('Symfony\Component\Routing\Loader\AnnotationClassLoader')
            ->setConstructorArgs(array($reader))
            ->getMockForAbstractClass()
        ;
    }
}
