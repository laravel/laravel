<?php

namespace ClassPreloader;

use Parser\AbstractNodeVisitor;

/**
 * Class loader configuration object
 */
class Config implements \IteratorAggregate
{
    /**
     * @var array Array of AbstractNodeVisitor objects that visit nodes
     */
    protected $visitors = array();

    /**
     * @var array Array of file names
     */
    protected $filenames = array();

    /**
     * @var array Array of exclusive filters
     */
    protected $exclusiveFilters = array();

    /**
     * @var array Array of inclusive filters
     */
    protected $inclusiveFilters = array();

    /**
     * Add the filename owned by the config
     *
     * @param string $filename File name
     *
     * @return self
     */
    public function addFile($filename)
    {
        $this->filenames[] = $filename;

        return $this;
    }

    /**
     * Get an array of file names that satisfy any added filters
     *
     * @return array
     */
    public function getFilenames()
    {
        $filenames = array();
        foreach ($this->filenames as $f) {
            foreach ($this->inclusiveFilters as $filter) {
                if (!preg_match($filter, $f)) {
                    continue 2;
                }
            }
            foreach ($this->exclusiveFilters as $filter) {
                if (preg_match($filter, $f)) {
                    continue 2;
                }
            }
            $filenames[] = $f;
        }

        return $filenames;
    }

    /**
     * Get an iterator for the filenames
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getFilenames());
    }

    /**
     * Add a filter used to filter out classes matching a specific pattern
     *
     * @param string $pattern Regular expression pattern
     *
     * @return self
     */
    public function addExclusiveFilter($pattern)
    {
        $this->exclusiveFilters[] = $pattern;

        return $this;
    }

    /**
     * Add a filter used to grab only file names matching the pattern
     *
     * @param string $pattern Regular expression pattern
     *
     * @return self
     */
    public function addInclusiveFilter($pattern)
    {
        $this->inclusiveFilters[] = $pattern;

        return $this;
    }

    /**
     * Add a visitor that will visit each node when traversing the node list
     * of each file.
     *
     * @param AbstractNodeVisitor $visitor Node visitor
     *
     * @return self
     */
    public function addVisitor(AbstractNodeVisitor $visitor)
    {
        $this->visitors[] = $visitor;

        return $this;
    }

    /**
     * Get an array of node visitors
     *
     * @return array
     */
    public function getVisitors()
    {
        return $this->visitors;
    }
}
