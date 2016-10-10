<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Body Signer Interface used to apply Body-Based Signature to a message.
 *
 * @author Xavier De Cock <xdecock@gmail.com>
 */
interface Swift_Signers_BodySigner extends Swift_Signer
{
    /**
     * Change the Swift_Signed_Message to apply the singing.
     *
     * @param Swift_Message $message
     *
     * @return Swift_Signers_BodySigner
     */
    public function signMessage(Swift_Message $message);

    /**
     * Return the list of header a signer might tamper.
     *
     * @return array
     */
    public function getAlteredHeaders();
}
