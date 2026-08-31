<?php
namespace Hamcrest\Core;

/*
 Copyright (c) 2009 hamcrest.org
 */
use Hamcrest\BaseMatcher;
use Hamcrest\Description;

/**
 * Is the value null?
 */
class IsNull extends BaseMatcher
{

    private static $_INSTANCE;
    private static $_NOT_INSTANCE;

    public function matches($item)
    {
        return is_null($item);
    }

    public function describeTo(Description $description)
    {
        $description->appendText('null');
    }

    /**
     * Matches if value is null.
     *
     * @factory
     */
    public static function nullValue()
    {
        if (!self::$_INSTANCE) {
            self::$_INSTANCE = new self();
        }

        return self::$_INSTANCE;
    }

    /**
     * Matches if value is not null.
     *
     * @factory
     */
    public static function notNullValue()
    {
        if (!self::$_NOT_INSTANCE) {
            self::$_NOT_INSTANCE = IsNot::not(self::nullValue());
        }

        return self::$_NOT_INSTANCE;
    }
}
