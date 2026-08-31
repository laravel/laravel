<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Inspector;

interface InspectorInterface
{
    /**
     * @return \Throwable
     */
    public function getException();

    /**
     * @return string
     */
    public function getExceptionName();

    /**
     * @return string
     */
    public function getExceptionMessage();

    /**
     * @return string[]
     */
    public function getPreviousExceptionMessages();

    /**
     * @return int[]
     */
    public function getPreviousExceptionCodes();

    /**
     * Returns a url to the php-manual related to the underlying error - when available.
     *
     * @return string|null
     */
    public function getExceptionDocrefUrl();

    /**
     * Does the wrapped Exception has a previous Exception?
     * @return bool
     */
    public function hasPreviousException();

    /**
     * Returns an Inspector for a previous Exception, if any.
     * @todo   Clean this up a bit, cache stuff a bit better.
     * @return InspectorInterface
     */
    public function getPreviousExceptionInspector();

    /**
     * Returns an array of all previous exceptions for this inspector's exception
     * @return \Throwable[]
     */
    public function getPreviousExceptions();

    /**
     * Returns an iterator for the inspected exception's
     * frames.
     * 
     * @param array<callable> $frameFilters
     * 
     * @return \Whoops\Exception\FrameCollection
     */
    public function getFrames(array $frameFilters = []);
}
