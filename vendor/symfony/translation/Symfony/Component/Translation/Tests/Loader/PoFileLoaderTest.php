<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Loader;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Config\Resource\FileResource;

class PoFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/resources.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(array('foo' => 'bar'), $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals(array(new FileResource($resource)), $catalogue->getResources());
    }

    public function testLoadPlurals()
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/plurals.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(array('foo' => 'bar', 'foos' => 'bar|bars'), $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals(array(new FileResource($resource)), $catalogue->getResources());
    }

    public function testLoadDoesNothingIfEmpty()
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/empty.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(array(), $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals(array(new FileResource($resource)), $catalogue->getResources());
    }

    /**
     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
     */
    public function testLoadNonExistingResource()
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.po';
        $loader->load($resource, 'en', 'domain1');
    }

    public function testLoadEmptyTranslation()
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/empty-translation.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(array('foo' => ''), $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals(array(new FileResource($resource)), $catalogue->getResources());
    }

    public function testEscapedId()
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/escaped-id.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        $this->assertArrayHasKey('escaped "foo"', $messages);
        $this->assertEquals('escaped "bar"', $messages['escaped "foo"']);
    }

    public function testEscapedIdPlurals()
    {
        $loader = new PoFileLoader();
        $resource = __DIR__.'/../fixtures/escaped-id-plurals.po';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $messages = $catalogue->all('domain1');
        $this->assertArrayHasKey('escaped "foo"', $messages);
        $this->assertArrayHasKey('escaped "foos"', $messages);
        $this->assertEquals('escaped "bar"', $messages['escaped "foo"']);
        $this->assertEquals('escaped "bar"|escaped "bars"', $messages['escaped "foos"']);
    }
}
