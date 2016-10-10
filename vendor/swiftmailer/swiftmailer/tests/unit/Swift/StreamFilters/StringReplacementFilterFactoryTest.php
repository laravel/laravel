<?php

class Swift_StreamFilters_StringReplacementFilterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstancesOfStringReplacementFilterAreCreated()
    {
        $factory = $this->_createFactory();
        $this->assertInstanceof(
            'Swift_StreamFilters_StringReplacementFilter',
            $factory->createFilter('a', 'b')
        );
    }

    public function testSameInstancesAreCached()
    {
        $factory = $this->_createFactory();
        $filter1 = $factory->createFilter('a', 'b');
        $filter2 = $factory->createFilter('a', 'b');
        $this->assertSame($filter1, $filter2, '%s: Instances should be cached');
    }

    public function testDifferingInstancesAreNotCached()
    {
        $factory = $this->_createFactory();
        $filter1 = $factory->createFilter('a', 'b');
        $filter2 = $factory->createFilter('a', 'c');
        $this->assertNotEquals($filter1, $filter2,
            '%s: Differing instances should not be cached'
            );
    }

    // -- Creation methods

    private function _createFactory()
    {
        return new Swift_StreamFilters_StringReplacementFilterFactory();
    }
}
