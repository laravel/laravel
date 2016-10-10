<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Matcher\Dumper;

use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class PhpMatcherDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testDumpWhenSchemeIsUsedWithoutAProperDumper()
    {
        $collection = new RouteCollection();
        $collection->add('secure', new Route(
            '/secure',
            array(),
            array('_scheme' => 'https')
        ));
        $dumper = new PhpMatcherDumper($collection);
        $dumper->dump();
    }

    /**
     * @dataProvider getRouteCollections
     */
    public function testDump(RouteCollection $collection, $fixture, $options = array())
    {
        $basePath = __DIR__.'/../../Fixtures/dumper/';

        $dumper = new PhpMatcherDumper($collection);
        $this->assertStringEqualsFile($basePath.$fixture, $dumper->dump($options), '->dump() correctly dumps routes as optimized PHP code.');
    }

    public function getRouteCollections()
    {
        /* test case 1 */

        $collection = new RouteCollection();

        $collection->add('overridden', new Route('/overridden'));

        // defaults and requirements
        $collection->add('foo', new Route(
            '/foo/{bar}',
            array('def' => 'test'),
            array('bar' => 'baz|symfony')
        ));
        // method requirement
        $collection->add('bar', new Route(
            '/bar/{foo}',
            array(),
            array('_method' => 'GET|head')
        ));
        // GET method requirement automatically adds HEAD as valid
        $collection->add('barhead', new Route(
            '/barhead/{foo}',
            array(),
            array('_method' => 'GET')
        ));
        // simple
        $collection->add('baz', new Route(
            '/test/baz'
        ));
        // simple with extension
        $collection->add('baz2', new Route(
            '/test/baz.html'
        ));
        // trailing slash
        $collection->add('baz3', new Route(
            '/test/baz3/'
        ));
        // trailing slash with variable
        $collection->add('baz4', new Route(
            '/test/{foo}/'
        ));
        // trailing slash and method
        $collection->add('baz5', new Route(
            '/test/{foo}/',
            array(),
            array('_method' => 'post')
        ));
        // complex name
        $collection->add('baz.baz6', new Route(
            '/test/{foo}/',
            array(),
            array('_method' => 'put')
        ));
        // defaults without variable
        $collection->add('foofoo', new Route(
            '/foofoo',
            array('def' => 'test')
        ));
        // pattern with quotes
        $collection->add('quoter', new Route(
            '/{quoter}',
            array(),
            array('quoter' => '[\']+')
        ));
        // space in pattern
        $collection->add('space', new Route(
            '/spa ce'
        ));

        // prefixes
        $collection1 = new RouteCollection();
        $collection1->add('overridden', new Route('/overridden1'));
        $collection1->add('foo1', new Route('/{foo}'));
        $collection1->add('bar1', new Route('/{bar}'));
        $collection1->addPrefix('/b\'b');
        $collection2 = new RouteCollection();
        $collection2->addCollection($collection1);
        $collection2->add('overridden', new Route('/{var}', array(), array('var' => '.*')));
        $collection1 = new RouteCollection();
        $collection1->add('foo2', new Route('/{foo1}'));
        $collection1->add('bar2', new Route('/{bar1}'));
        $collection1->addPrefix('/b\'b');
        $collection2->addCollection($collection1);
        $collection2->addPrefix('/a');
        $collection->addCollection($collection2);

        // overridden through addCollection() and multiple sub-collections with no own prefix
        $collection1 = new RouteCollection();
        $collection1->add('overridden2', new Route('/old'));
        $collection1->add('helloWorld', new Route('/hello/{who}', array('who' => 'World!')));
        $collection2 = new RouteCollection();
        $collection3 = new RouteCollection();
        $collection3->add('overridden2', new Route('/new'));
        $collection3->add('hey', new Route('/hey/'));
        $collection2->addCollection($collection3);
        $collection1->addCollection($collection2);
        $collection1->addPrefix('/multi');
        $collection->addCollection($collection1);

        // "dynamic" prefix
        $collection1 = new RouteCollection();
        $collection1->add('foo3', new Route('/{foo}'));
        $collection1->add('bar3', new Route('/{bar}'));
        $collection1->addPrefix('/b');
        $collection1->addPrefix('{_locale}');
        $collection->addCollection($collection1);

        // route between collections
        $collection->add('ababa', new Route('/ababa'));

        // collection with static prefix but only one route
        $collection1 = new RouteCollection();
        $collection1->add('foo4', new Route('/{foo}'));
        $collection1->addPrefix('/aba');
        $collection->addCollection($collection1);

        // prefix and host

        $collection1 = new RouteCollection();

        $route1 = new Route('/route1', array(), array(), array(), 'a.example.com');
        $collection1->add('route1', $route1);

        $collection2 = new RouteCollection();

        $route2 = new Route('/c2/route2', array(), array(), array(), 'a.example.com');
        $collection1->add('route2', $route2);

        $route3 = new Route('/c2/route3', array(), array(), array(), 'b.example.com');
        $collection1->add('route3', $route3);

        $route4 = new Route('/route4', array(), array(), array(), 'a.example.com');
        $collection1->add('route4', $route4);

        $route5 = new Route('/route5', array(), array(), array(), 'c.example.com');
        $collection1->add('route5', $route5);

        $route6 = new Route('/route6', array(), array(), array(), null);
        $collection1->add('route6', $route6);

        $collection->addCollection($collection1);

        // host and variables

        $collection1 = new RouteCollection();

        $route11 = new Route('/route11', array(), array(), array(), '{var1}.example.com');
        $collection1->add('route11', $route11);

        $route12 = new Route('/route12', array('var1' => 'val'), array(), array(), '{var1}.example.com');
        $collection1->add('route12', $route12);

        $route13 = new Route('/route13/{name}', array(), array(), array(), '{var1}.example.com');
        $collection1->add('route13', $route13);

        $route14 = new Route('/route14/{name}', array('var1' => 'val'), array(), array(), '{var1}.example.com');
        $collection1->add('route14', $route14);

        $route15 = new Route('/route15/{name}', array(), array(), array(), 'c.example.com');
        $collection1->add('route15', $route15);

        $route16 = new Route('/route16/{name}', array('var1' => 'val'), array(), array(), null);
        $collection1->add('route16', $route16);

        $route17 = new Route('/route17', array(), array(), array(), null);
        $collection1->add('route17', $route17);

        $collection->addCollection($collection1);

        // multiple sub-collections with a single route and a prefix each
        $collection1 = new RouteCollection();
        $collection1->add('a', new Route('/a...'));
        $collection2 = new RouteCollection();
        $collection2->add('b', new Route('/{var}'));
        $collection3 = new RouteCollection();
        $collection3->add('c', new Route('/{var}'));
        $collection3->addPrefix('/c');
        $collection2->addCollection($collection3);
        $collection2->addPrefix('/b');
        $collection1->addCollection($collection2);
        $collection1->addPrefix('/a');
        $collection->addCollection($collection1);

        /* test case 2 */

        $redirectCollection = clone $collection;

        // force HTTPS redirection
        $redirectCollection->add('secure', new Route(
            '/secure',
            array(),
            array('_scheme' => 'https')
        ));

        // force HTTP redirection
        $redirectCollection->add('nonsecure', new Route(
            '/nonsecure',
            array(),
            array('_scheme' => 'http')
        ));

        /* test case 3 */

        $rootprefixCollection = new RouteCollection();
        $rootprefixCollection->add('static', new Route('/test'));
        $rootprefixCollection->add('dynamic', new Route('/{var}'));
        $rootprefixCollection->addPrefix('rootprefix');
        $route = new Route('/with-condition');
        $route->setCondition('context.getMethod() == "GET"');
        $rootprefixCollection->add('with-condition', $route);

        return array(
           array($collection, 'url_matcher1.php', array()),
           array($redirectCollection, 'url_matcher2.php', array('base_class' => 'Symfony\Component\Routing\Tests\Fixtures\RedirectableUrlMatcher')),
           array($rootprefixCollection, 'url_matcher3.php', array()),
        );
    }
}
