<?php
namespace Hamcrest;

/*
 Copyright (c) 2009 hamcrest.org
 */

/**
 * The ability of an object to describe itself.
 */
interface SelfDescribing
{

    /**
     * Generates a description of the object.  The description may be part
     * of a description of a larger object of which this is just a component,
     * so it should be worded appropriately.
     *
     * @param \Hamcrest\Description $description
     *   The description to be built or appended to.
     */
    public function describeTo(Description $description);
}
