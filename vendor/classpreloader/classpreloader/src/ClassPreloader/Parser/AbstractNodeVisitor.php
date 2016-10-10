<?php

namespace ClassPreloader\Parser;

/**
 * Abstract node visitor used to track the filename
 */
abstract class AbstractNodeVisitor extends \PHPParser_NodeVisitorAbstract
{
    /**
     * @var string Current file being parsed
     */
    protected $filename = '';

    /**
     * Set the full path to the current file being parsed
     *
     * @param string $filename Filename being parser
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the full path to the current file being parsed
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the directory of the current file being parsed
     *
     * @return string
     */
    public function getDir()
    {
        return dirname($this->getFilename());
    }
}
