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

use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Config\Resource\FileResource;

class JsonFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Config\Loader\Loader')) {
            $this->markTestSkipped('The "Config" component is not available');
        }
    }

    public function testLoad()
    {
        $loader = new JsonFileLoader();
        $resource = __DIR__.'/../fixtures/resources.json';
        $catalogue = $loader->load($resource, 'en', 'domain1');

        $this->assertEquals(array('foo' => 'bar'), $catalogue->all('domain1'));
        $this->assertEquals('en', $catalogue->getLocale());
        $this->assertEquals(array(new FileResource($resource)), $catalogue->getResources());
    }

    public function testLoadDoesNothingIfEmpty()
    {
        $loader = new JsonFileLoader();
        $resource = __DIR__.'/../fixtures/empty.json';
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
        $loader = new JsonFileLoader();
        $resource = __DIR__.'/../fixtures/non-existing.json';
        $loader->load($resource, 'en', 'domain1');
    }

    /**
     * @expectedException           \Symfony\Component\Translation\Exception\InvalidResourceException
     * @expectedExceptionMessage    Error parsing JSON - Syntax error, malformed JSON
     */
    public function testParseException()
    {
        $loader = new JsonFileLoader();
        $resource = __DIR__.'/../fixtures/malformed.json';
        $loader->load($resource, 'en', 'domain1');
    }
}
