<?php

class MimeEntityFixture implements Swift_Mime_MimeEntity
{
    private $level;
    private $string;
    private $contentType;

    public function __construct($level = null, $string = '', $contentType = null)
    {
        $this->level = $level;
        $this->string = $string;
        $this->contentType = $contentType;
    }

    public function getNestingLevel()
    {
        return $this->level;
    }

    public function toString()
    {
        return $this->string;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    // These methods are here to account for the implemented interfaces
    public function getId()
    {
    }
    public function getHeaders()
    {
    }
    public function getBody()
    {
    }
    public function setBody($body, $contentType = null)
    {
    }
    public function toByteStream(Swift_InputByteStream $is)
    {
    }
    public function charsetChanged($charset)
    {
    }
    public function encoderChanged(Swift_Mime_ContentEncoder $encoder)
    {
    }
    public function getChildren()
    {
    }
    public function setChildren(array $children)
    {
    }
}
