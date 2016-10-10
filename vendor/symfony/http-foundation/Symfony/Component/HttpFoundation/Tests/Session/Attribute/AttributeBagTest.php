<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Attribute;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * Tests AttributeBag
 *
 * @author Drak <drak@zikula.org>
 */
class AttributeBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $array;

    /**
     * @var AttributeBag
     */
    private $bag;

    protected function setUp()
    {
        $this->array = array(
            'hello' => 'world',
            'always' => 'be happy',
            'user.login' => 'drak',
            'csrf.token' => array(
                'a' => '1234',
                'b' => '4321',
            ),
            'category' => array(
                'fishing' => array(
                    'first' => 'cod',
                    'second' => 'sole',),
                ),
        );
        $this->bag = new AttributeBag('_sf2');
        $this->bag->initialize($this->array);
    }

    protected function tearDown()
    {
        $this->bag = null;
        $this->array = array();
    }

    public function testInitialize()
    {
        $bag = new AttributeBag();
        $bag->initialize($this->array);
        $this->assertEquals($this->array, $bag->all());
        $array = array('should' => 'change');
        $bag->initialize($array);
        $this->assertEquals($array, $bag->all());
    }

    public function testGetStorageKey()
    {
        $this->assertEquals('_sf2', $this->bag->getStorageKey());
        $attributeBag = new AttributeBag('test');
        $this->assertEquals('test', $attributeBag->getStorageKey());
    }

    public function testGetSetName()
    {
        $this->assertEquals('attributes', $this->bag->getName());
        $this->bag->setName('foo');
        $this->assertEquals('foo', $this->bag->getName());
    }

    /**
     * @dataProvider attributesProvider
     */
    public function testHas($key, $value, $exists)
    {
        $this->assertEquals($exists, $this->bag->has($key));
    }

    /**
     * @dataProvider attributesProvider
     */
    public function testGet($key, $value, $expected)
    {
        $this->assertEquals($value, $this->bag->get($key));
    }

    public function testGetDefaults()
    {
        $this->assertNull($this->bag->get('user2.login'));
        $this->assertEquals('default', $this->bag->get('user2.login', 'default'));
    }

    /**
     * @dataProvider attributesProvider
     */
    public function testSet($key, $value, $expected)
    {
        $this->bag->set($key, $value);
        $this->assertEquals($value, $this->bag->get($key));
    }

    public function testAll()
    {
        $this->assertEquals($this->array, $this->bag->all());

        $this->bag->set('hello', 'fabien');
        $array = $this->array;
        $array['hello'] = 'fabien';
        $this->assertEquals($array, $this->bag->all());
    }

    public function testReplace()
    {
        $array = array();
        $array['name'] = 'jack';
        $array['foo.bar'] = 'beep';
        $this->bag->replace($array);
        $this->assertEquals($array, $this->bag->all());
        $this->assertNull($this->bag->get('hello'));
        $this->assertNull($this->bag->get('always'));
        $this->assertNull($this->bag->get('user.login'));
    }

    public function testRemove()
    {
        $this->assertEquals('world', $this->bag->get('hello'));
        $this->bag->remove('hello');
        $this->assertNull($this->bag->get('hello'));

        $this->assertEquals('be happy', $this->bag->get('always'));
        $this->bag->remove('always');
        $this->assertNull($this->bag->get('always'));

        $this->assertEquals('drak', $this->bag->get('user.login'));
        $this->bag->remove('user.login');
        $this->assertNull($this->bag->get('user.login'));
    }

    public function testClear()
    {
        $this->bag->clear();
        $this->assertEquals(array(), $this->bag->all());
    }

    public function attributesProvider()
    {
        return array(
            array('hello', 'world', true),
            array('always', 'be happy', true),
            array('user.login', 'drak', true),
            array('csrf.token', array('a' => '1234', 'b' => '4321'), true),
            array('category', array('fishing' => array('first' => 'cod', 'second' => 'sole')), true),
            array('user2.login', null, false),
            array('never', null, false),
            array('bye', null, false),
            array('bye/for/now', null, false),
        );
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag::getIterator
     */
    public function testGetIterator()
    {
        $i = 0;
        foreach ($this->bag as $key => $val) {
            $this->assertEquals($this->array[$key], $val);
            $i++;
        }

        $this->assertEquals(count($this->array), $i);
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag::count
     */
    public function testCount()
    {
        $this->assertEquals(count($this->array), count($this->bag));
    }
}
