<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A reporter which "collects" failures for the Reporter plugin.
 *
 * @author Chris Corbyn
 */
class Swift_Plugins_Reporters_HitReporter implements Swift_Plugins_Reporter
{
    /**
     * The list of failures.
     *
     * @var array
     */
    private $_failures = array();

    private $_failures_cache = array();

    /**
     * Notifies this ReportNotifier that $address failed or succeeded.
     *
     * @param Swift_Mime_Message $message
     * @param string             $address
     * @param int                $result  from {@link RESULT_PASS, RESULT_FAIL}
     */
    public function notify(Swift_Mime_Message $message, $address, $result)
    {
        if (self::RESULT_FAIL == $result && !isset($this->_failures_cache[$address])) {
            $this->_failures[] = $address;
            $this->_failures_cache[$address] = true;
        }
    }

    /**
     * Get an array of addresses for which delivery failed.
     *
     * @return array
     */
    public function getFailedRecipients()
    {
        return $this->_failures;
    }

    /**
     * Clear the buffer (empty the list).
     */
    public function clear()
    {
        $this->_failures = $this->_failures_cache = array();
    }
}
