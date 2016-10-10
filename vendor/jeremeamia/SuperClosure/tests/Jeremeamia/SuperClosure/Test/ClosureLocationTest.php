<?php

namespace Jeremeamia\SuperClosure\Test;

use Jeremeamia\SuperClosure\ClosureLocation;

class ClosureLocationTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateClosureLocationFromClosureReflection()
    {
        $reflection = new \ReflectionFunction(function () {});
        $location = ClosureLocation::fromReflection($reflection);
        $setProperties = array_filter(get_object_vars($location));

        $this->assertEquals(array('directory', 'file', 'function', 'line'), array_keys($setProperties));
    }

    public function testCanFinalizeLocation()
    {
        $location = new ClosureLocation();
        $location->function = '[function]';
        $location->trait = '[trait]';

        $r = new \ReflectionObject($location);
        $p = $r->getProperty('closureScopeClass');
        $p->setAccessible(true);
        $p->setValue($location, '[class]');

        $location->finalize();
        $this->assertEquals('[trait]::[function]', $location->method);
        $this->assertEquals('[class]', $location->class);
    }
}
