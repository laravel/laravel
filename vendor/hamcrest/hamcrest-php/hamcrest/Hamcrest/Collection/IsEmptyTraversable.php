<?php
namespace Hamcrest\Collection;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\BaseMatcher;
use Hamcrest\Description;

/**
 * Matches if traversable is empty or non-empty.
 */
class IsEmptyTraversable extends BaseMatcher
{

    private static $_INSTANCE;
    private static $_NOT_INSTANCE;

    private $_empty;

    public function __construct($empty = true)
    {
        $this->_empty = $empty;
    }

    public function matches($item)
    {
        if (!$item instanceof \Traversable) {
            return false;
        }

        foreach ($item as $value) {
            return !$this->_empty;
        }

        return $this->_empty;
    }

    public function describeTo(Description $description)
    {
        $description->appendText($this->_empty ? 'an empty traversable' : 'a non-empty traversable');
    }

    /**
     * Returns true if traversable is empty.
     *
     * @factory
     */
    public static function emptyTraversable()
    {
        if (!self::$_INSTANCE) {
            self::$_INSTANCE = new self;
        }

        return self::$_INSTANCE;
    }

    /**
     * Returns true if traversable is not empty.
     *
     * @factory
     */
    public static function nonEmptyTraversable()
    {
        if (!self::$_NOT_INSTANCE) {
            self::$_NOT_INSTANCE = new self(false);
        }

        return self::$_NOT_INSTANCE;
    }
}
