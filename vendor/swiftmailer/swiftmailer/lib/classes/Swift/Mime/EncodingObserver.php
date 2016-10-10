<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Observes changes for a Mime entity's ContentEncoder.
 *
 * @author Chris Corbyn
 */
interface Swift_Mime_EncodingObserver
{
    /**
     * Notify this observer that the observed entity's ContentEncoder has changed.
     *
     * @param Swift_Mime_ContentEncoder $encoder
     */
    public function encoderChanged(Swift_Mime_ContentEncoder $encoder);
}
