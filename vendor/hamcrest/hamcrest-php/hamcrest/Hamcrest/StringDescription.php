<?php
namespace Hamcrest;

/*
 Copyright (c) 2009 hamcrest.org
 */

/**
 * A {@link Hamcrest\Description} that is stored as a string.
 */
class StringDescription extends BaseDescription
{

    private $_out;

    public function __construct($out = '')
    {
        $this->_out = (string) $out;
    }

    public function __toString()
    {
        return $this->_out;
    }

    /**
     * Return the description of a {@link Hamcrest\SelfDescribing} object as a
     * String.
     *
     * @param \Hamcrest\SelfDescribing $selfDescribing
     *   The object to be described.
     *
     * @return string
     *   The description of the object.
     */
    public static function toString(SelfDescribing $selfDescribing)
    {
        $self = new self();

        return (string) $self->appendDescriptionOf($selfDescribing);
    }

    /**
     * Alias for {@link toString()}.
     */
    public static function asString(SelfDescribing $selfDescribing)
    {
        return self::toString($selfDescribing);
    }

    // -- Protected Methods

    protected function append($str)
    {
        $this->_out .= $str;
    }
}
