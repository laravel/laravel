<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This is the implementation class for {@link Swift_Transport_MailInvoker}.
 *
 * @author     Chris Corbyn
 */
class Swift_Transport_SimpleMailInvoker implements Swift_Transport_MailInvoker
{
    /**
     * Send mail via the mail() function.
     *
     * This method takes the same arguments as PHP mail().
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $headers
     * @param string $extraParams
     *
     * @return bool
     */
    public function mail($to, $subject, $body, $headers = null, $extraParams = null)
    {
        if (!ini_get('safe_mode')) {
            return @mail($to, $subject, $body, $headers, $extraParams);
        } else {
            return @mail($to, $subject, $body, $headers);
        }
    }
}
