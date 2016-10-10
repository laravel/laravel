<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Flash;

use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag as FlashBag;

/**
 * AutoExpireFlashBagTest
 *
 * @author Drak <drak@zikula.org>
 */
class AutoExpireFlashBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag
     */
    private $bag;

    /**
     * @var array
     */
    protected $array = array();

    protected function setUp()
    {
        parent::setUp();
        $this->bag = new FlashBag();
        $this->array = array('new' => array('notice' => array('A previous flash message')));
        $this->bag->initialize($this->array);
    }

    public function tearDown()
    {
        $this->bag = null;
        parent::tearDown();
    }

    public function testInitialize()
    {
        $bag = new FlashBag();
        $array = array('new' => array('notice' => array('A previous flash message')));
        $bag->initialize($array);
        $this->assertEquals(array('A previous flash message'), $bag->peek('notice'));
        $array = array('new' => array(
                'notice' => array('Something else'),
                'error' => array('a'),
            ));
        $bag->initialize($array);
        $this->assertEquals(array('Something else'), $bag->peek('notice'));
        $this->assertEquals(array('a'), $bag->peek('error'));
    }

    public function testGetStorageKey()
    {
        $this->assertEquals('_sf2_flashes', $this->bag->getStorageKey());
        $attributeBag = new FlashBag('test');
        $this->assertEquals('test', $attributeBag->getStorageKey());
    }

    public function testGetSetName()
    {
        $this->assertEquals('flashes', $this->bag->getName());
        $this->bag->setName('foo');
        $this->assertEquals('foo', $this->bag->getName());
    }

    public function testPeek()
    {
        $this->assertEquals(array(), $this->bag->peek('non_existing'));
        $this->assertEquals(array('default'), $this->bag->peek('non_existing', array('default')));
        $this->assertEquals(array('A previous flash message'), $this->bag->peek('notice'));
        $this->assertEquals(array('A previous flash message'), $this->bag->peek('notice'));
    }

    public function testSet()
    {
        $this->bag->set('notice', 'Foo');
        $this->assertEquals(array('A previous flash message'), $this->bag->peek('notice'));
    }

    public function testHas()
    {
        $this->assertFalse($this->bag->has('nothing'));
        $this->assertTrue($this->bag->has('notice'));
    }

    public function testKeys()
    {
        $this->assertEquals(array('notice'), $this->bag->keys());
    }

    public function testPeekAll()
    {
        $array = array(
            'new' => array(
                'notice' => 'Foo',
                'error' => 'Bar',
            ),
        );

        $this->bag->initialize($array);
        $this->assertEquals(array(
            'notice' => 'Foo',
            'error' => 'Bar',
            ), $this->bag->peekAll()
        );

        $this->assertEquals(array(
            'notice' => 'Foo',
            'error' => 'Bar',
            ), $this->bag->peekAll()
        );
    }

    public function testGet()
    {
        $this->assertEquals(array(), $this->bag->get('non_existing'));
        $this->assertEquals(array('default'), $this->bag->get('non_existing', array('default')));
        $this->assertEquals(array('A previous flash message'), $this->bag->get('notice'));
        $this->assertEquals(array(), $this->bag->get('notice'));
    }

    public function testSetAll()
    {
        $this->bag->setAll(array('a' => 'first', 'b' => 'second'));
        $this->assertFalse($this->bag->has('a'));
        $this->assertFalse($this->bag->has('b'));
    }

    public function testAll()
    {
        $this->bag->set('notice', 'Foo');
        $this->bag->set('error', 'Bar');
        $this->assertEquals(array(
            'notice' => array('A previous flash message'),
            ), $this->bag->all()
        );

        $this->assertEquals(array(), $this->bag->all());
    }

    public function testClear()
    {
        $this->assertEquals(array('notice' => array('A previous flash message')), $this->bag->clear());
    }
}
