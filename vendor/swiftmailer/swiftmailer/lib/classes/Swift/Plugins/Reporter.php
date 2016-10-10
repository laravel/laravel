<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The Reporter plugin sends pass/fail notification to a Reporter.
 *
 * @author Chris Corbyn
 */
interface Swift_Plugins_Reporter
{
    /** The recipient was accepted for delivery */
    const RESULT_PASS = 0x01;

    /** The recipient could not be accepted */
    const RESULT_FAIL = 0x10;

    /**
     * Notifies this ReportNotifier that $address failed or succeeded.
     *
     * @param Swift_Mime_Message $message
     * @param string             $address
     * @param int                $result  from {@link RESULT_PASS, RESULT_FAIL}
     */
    public function notify(Swift_Mime_Message $message, $address, $result);
}
