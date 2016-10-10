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

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\Dumper\DumperPrefixCollection;
use Symfony\Component\Routing\Matcher\Dumper\DumperRoute;
use Symfony\Component\Routing\Matcher\Dumper\DumperCollection;

class DumperPrefixCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddPrefixRoute()
    {
        $coll = new DumperPrefixCollection();
        $coll->setPrefix('');

        $route = new DumperRoute('bar', new Route('/foo/bar'));
        $coll = $coll->addPrefixRoute($route);

        $route = new DumperRoute('bar2', new Route('/foo/bar'));
        $coll = $coll->addPrefixRoute($route);

        $route = new DumperRoute('qux', new Route('/foo/qux'));
        $coll = $coll->addPrefixRoute($route);

        $route = new DumperRoute('bar3', new Route('/foo/bar'));
        $coll = $coll->addPrefixRoute($route);

        $route = new DumperRoute('bar4', new Route(''));
        $result = $coll->addPrefixRoute($route);

        $expect = <<<'EOF'
            |-coll /
            | |-coll /f
            | | |-coll /fo
            | | | |-coll /foo
            | | | | |-coll /foo/
            | | | | | |-coll /foo/b
            | | | | | | |-coll /foo/ba
            | | | | | | | |-coll /foo/bar
            | | | | | | | | |-route bar /foo/bar
            | | | | | | | | |-route bar2 /foo/bar
            | | | | | |-coll /foo/q
            | | | | | | |-coll /foo/qu
            | | | | | | | |-coll /foo/qux
            | | | | | | | | |-route qux /foo/qux
            | | | | | |-coll /foo/b
            | | | | | | |-coll /foo/ba
            | | | | | | | |-coll /foo/bar
            | | | | | | | | |-route bar3 /foo/bar
            | |-route bar4 /

EOF;

        $this->assertSame($expect, $this->collectionToString($result->getRoot(), '            '));
    }

    public function testMergeSlashNodes()
    {
        $coll = new DumperPrefixCollection();
        $coll->setPrefix('');

        $route = new DumperRoute('bar', new Route('/foo/bar'));
        $coll = $coll->addPrefixRoute($route);

        $route = new DumperRoute('bar2', new Route('/foo/bar'));
        $coll = $coll->addPrefixRoute($route);

        $route = new DumperRoute('qux', new Route('/foo/qux'));
        $coll = $coll->addPrefixRoute($route);

        $route = new DumperRoute('bar3', new Route('/foo/bar'));
        $result = $coll->addPrefixRoute($route);

        $result->getRoot()->mergeSlashNodes();

        $expect = <<<'EOF'
            |-coll /f
            | |-coll /fo
            | | |-coll /foo
            | | | |-coll /foo/b
            | | | | |-coll /foo/ba
            | | | | | |-coll /foo/bar
            | | | | | | |-route bar /foo/bar
            | | | | | | |-route bar2 /foo/bar
            | | | |-coll /foo/q
            | | | | |-coll /foo/qu
            | | | | | |-coll /foo/qux
            | | | | | | |-route qux /foo/qux
            | | | |-coll /foo/b
            | | | | |-coll /foo/ba
            | | | | | |-coll /foo/bar
            | | | | | | |-route bar3 /foo/bar

EOF;

        $this->assertSame($expect, $this->collectionToString($result->getRoot(), '            '));
    }

    private function collectionToString(DumperCollection $collection, $prefix)
    {
        $string = '';
        foreach ($collection as $route) {
            if ($route instanceof DumperCollection) {
                $string .= sprintf("%s|-coll %s\n", $prefix, $route->getPrefix());
                $string .= $this->collectionToString($route, $prefix.'| ');
            } else {
                $string .= sprintf("%s|-route %s %s\n", $prefix, $route->getName(), $route->getRoute()->getPath());
            }
        }

        return $string;
    }
}
