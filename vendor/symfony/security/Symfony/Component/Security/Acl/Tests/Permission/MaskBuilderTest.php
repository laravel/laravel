<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Tests\Permission;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class MaskBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getInvalidConstructorData
     */
    public function testConstructorWithNonInteger($invalidMask)
    {
        new MaskBuilder($invalidMask);
    }

    public function getInvalidConstructorData()
    {
        return array(
            array(234.463),
            array('asdgasdf'),
            array(array()),
            array(new \stdClass()),
        );
    }

    public function testConstructorWithoutArguments()
    {
        $builder = new MaskBuilder();

        $this->assertEquals(0, $builder->get());
    }

    public function testConstructor()
    {
        $builder = new MaskBuilder(123456);

        $this->assertEquals(123456, $builder->get());
    }

    public function testAddAndRemove()
    {
        $builder = new MaskBuilder();

        $builder
            ->add('view')
            ->add('eDiT')
            ->add('ownEr')
        ;
        $mask = $builder->get();

        $this->assertEquals(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
        $this->assertEquals(MaskBuilder::MASK_EDIT, $mask & MaskBuilder::MASK_EDIT);
        $this->assertEquals(MaskBuilder::MASK_OWNER, $mask & MaskBuilder::MASK_OWNER);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_MASTER);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_CREATE);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_DELETE);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_UNDELETE);

        $builder->remove('edit')->remove('OWner');
        $mask = $builder->get();
        $this->assertEquals(0, $mask & MaskBuilder::MASK_EDIT);
        $this->assertEquals(0, $mask & MaskBuilder::MASK_OWNER);
        $this->assertEquals(MaskBuilder::MASK_VIEW, $mask & MaskBuilder::MASK_VIEW);
    }

    public function testGetPattern()
    {
        $builder = new MaskBuilder();
        $this->assertEquals(MaskBuilder::ALL_OFF, $builder->getPattern());

        $builder->add('view');
        $this->assertEquals(str_repeat('.', 31).'V', $builder->getPattern());

        $builder->add('owner');
        $this->assertEquals(str_repeat('.', 24).'N......V', $builder->getPattern());

        $builder->add(1 << 10);
        $this->assertEquals(str_repeat('.', 21).MaskBuilder::ON.'..N......V', $builder->getPattern());
    }

    public function testReset()
    {
        $builder = new MaskBuilder();
        $this->assertEquals(0, $builder->get());

        $builder->add('view');
        $this->assertTrue($builder->get() > 0);

        $builder->reset();
        $this->assertEquals(0, $builder->get());
    }
}
