<?php

abstract class Swift_Transport_AbstractSmtpTest extends \SwiftMailerTestCase
{
    /** Abstract test method */
    abstract protected function _getTransport($buf);

    public function testStartAccepts220ServiceGreeting()
    {
        /* -- RFC 2821, 4.2.

     Greeting = "220 " Domain [ SP text ] CRLF

     -- RFC 2822, 4.3.2.

     CONNECTION ESTABLISHMENT
         S: 220
         E: 554
        */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('readLine')
            ->once()
            ->with(0)
            ->andReturn("220 some.server.tld bleh\r\n");

        $this->_finishBuffer($buf);
        try {
            $this->assertFalse($smtp->isStarted(), '%s: SMTP should begin non-started');
            $smtp->start();
            $this->assertTrue($smtp->isStarted(), '%s: start() should have started connection');
        } catch (Exception $e) {
            $this->fail('220 is a valid SMTP greeting and should be accepted');
        }
    }

    public function testBadGreetingCausesException()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('readLine')
            ->once()
            ->with(0)
            ->andReturn("554 I'm busy\r\n");
        $this->_finishBuffer($buf);
        try {
            $this->assertFalse($smtp->isStarted(), '%s: SMTP should begin non-started');
            $smtp->start();
            $this->fail('554 greeting indicates an error and should cause an exception');
        } catch (Exception $e) {
            $this->assertFalse($smtp->isStarted(), '%s: start() should have failed');
        }
    }

    public function testStartSendsHeloToInitiate()
    {
        /* -- RFC 2821, 3.2.

            3.2 Client Initiation

         Once the server has sent the welcoming message and the client has
         received it, the client normally sends the EHLO command to the
         server, indicating the client's identity.  In addition to opening the
         session, use of EHLO indicates that the client is able to process
         service extensions and requests that the server provide a list of the
         extensions it supports.  Older SMTP systems which are unable to
         support service extensions and contemporary clients which do not
         require service extensions in the mail session being initiated, MAY
         use HELO instead of EHLO.  Servers MUST NOT return the extended
         EHLO-style response to a HELO command.  For a particular connection
         attempt, if the server returns a "command not recognized" response to
         EHLO, the client SHOULD be able to fall back and send HELO.

         In the EHLO command the host sending the command identifies itself;
         the command may be interpreted as saying "Hello, I am <domain>" (and,
         in the case of EHLO, "and I support service extension requests").

       -- RFC 2281, 4.1.1.1.

       ehlo            = "EHLO" SP Domain CRLF
       helo            = "HELO" SP Domain CRLF

       -- RFC 2821, 4.3.2.

       EHLO or HELO
           S: 250
           E: 504, 550

     */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);

        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('readLine')
            ->once()
            ->with(0)
            ->andReturn("220 some.server.tld bleh\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with('~^HELO .*?\r\n$~D')
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 ServerName'."\r\n");

        $this->_finishBuffer($buf);
        try {
            $smtp->start();
        } catch (Exception $e) {
            $this->fail('Starting SMTP should send HELO and accept 250 response');
        }
    }

    public function testInvalidHeloResponseCausesException()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);

        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('readLine')
            ->once()
            ->with(0)
            ->andReturn("220 some.server.tld bleh\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with('~^HELO .*?\r\n$~D')
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('504 WTF'."\r\n");

        $this->_finishBuffer($buf);
        try {
            $this->assertFalse($smtp->isStarted(), '%s: SMTP should begin non-started');
            $smtp->start();
            $this->fail('Non 250 HELO response should raise Exception');
        } catch (Exception $e) {
            $this->assertFalse($smtp->isStarted(), '%s: SMTP start() should have failed');
        }
    }

    public function testDomainNameIsPlacedInHelo()
    {
        /* -- RFC 2821, 4.1.4.

       The SMTP client MUST, if possible, ensure that the domain parameter
       to the EHLO command is a valid principal host name (not a CNAME or MX
       name) for its host.  If this is not possible (e.g., when the client's
       address is dynamically assigned and the client does not have an
       obvious name), an address literal SHOULD be substituted for the
       domain name and supplemental information provided that will assist in
       identifying the client.
        */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);

        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('readLine')
            ->once()
            ->with(0)
            ->andReturn("220 some.server.tld bleh\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("HELO mydomain.com\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 ServerName'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->setLocalDomain('mydomain.com');
        $smtp->start();
    }

    public function testSuccessfulMailCommand()
    {
        /* -- RFC 2821, 3.3.

        There are three steps to SMTP mail transactions.  The transaction
        starts with a MAIL command which gives the sender identification.

        .....

        The first step in the procedure is the MAIL command.

            MAIL FROM:<reverse-path> [SP <mail-parameters> ] <CRLF>

        -- RFC 2821, 4.1.1.2.

        Syntax:

            "MAIL FROM:" ("<>" / Reverse-Path)
                       [SP Mail-parameters] CRLF
        -- RFC 2821, 4.1.2.

        Reverse-path = Path
            Forward-path = Path
            Path = "<" [ A-d-l ":" ] Mailbox ">"
            A-d-l = At-domain *( "," A-d-l )
                        ; Note that this form, the so-called "source route",
                        ; MUST BE accepted, SHOULD NOT be generated, and SHOULD be
                        ; ignored.
            At-domain = "@" domain

        -- RFC 2821, 4.3.2.

        MAIL
            S: 250
            E: 552, 451, 452, 550, 553, 503
        */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();
        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<me@domain.com>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");

        $this->_finishBuffer($buf);
        try {
            $smtp->start();
            $smtp->send($message);
        } catch (Exception $e) {
            $this->fail('MAIL FROM should accept a 250 response');
        }
    }

    public function testInvalidResponseCodeFromMailCausesException()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<me@domain.com>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('553 Bad'."\r\n");

        $this->_finishBuffer($buf);
        try {
            $smtp->start();
            $smtp->send($message);
            $this->fail('MAIL FROM should accept a 250 response');
        } catch (Exception $e) {
        }
    }

    public function testSenderIsPreferredOverFrom()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getSender')
                ->once()
                ->andReturn(array('another@domain.com' => 'Someone'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<another@domain.com>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    public function testReturnPathIsPreferredOverSender()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getSender')
                ->once()
                ->andReturn(array('another@domain.com' => 'Someone'));
        $message->shouldReceive('getReturnPath')
                ->once()
                ->andReturn('more@domain.com');
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<more@domain.com>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    public function testSuccessfulRcptCommandWith250Response()
    {
        /* -- RFC 2821, 3.3.

     The second step in the procedure is the RCPT command.

            RCPT TO:<forward-path> [ SP <rcpt-parameters> ] <CRLF>

     The first or only argument to this command includes a forward-path
     (normally a mailbox and domain, always surrounded by "<" and ">"
     brackets) identifying one recipient.  If accepted, the SMTP server
     returns a 250 OK reply and stores the forward-path.  If the recipient
     is known not to be a deliverable address, the SMTP server returns a
     550 reply, typically with a string such as "no such user - " and the
     mailbox name (other circumstances and reply codes are possible).
     This step of the procedure can be repeated any number of times.

        -- RFC 2821, 4.1.1.3.

        This command is used to identify an individual recipient of the mail
        data; multiple recipients are specified by multiple use of this
        command.  The argument field contains a forward-path and may contain
        optional parameters.

        The forward-path normally consists of the required destination
        mailbox.  Sending systems SHOULD not generate the optional list of
        hosts known as a source route.

        .......

        "RCPT TO:" ("<Postmaster@" domain ">" / "<Postmaster>" / Forward-Path)
                                        [SP Rcpt-parameters] CRLF

        -- RFC 2821, 4.2.2.

            250 Requested mail action okay, completed
            251 User not local; will forward to <forward-path>
         (See section 3.4)
            252 Cannot VRFY user, but will accept message and attempt
                    delivery

        -- RFC 2821, 4.3.2.

        RCPT
            S: 250, 251 (but see section 3.4 for discussion of 251 and 551)
            E: 550, 551, 552, 553, 450, 451, 452, 503, 550
        */

        //We'll treat 252 as accepted since it isn't really a failure

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<me@domain.com>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<foo@bar>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        try {
            $smtp->start();
            $smtp->send($message);
        } catch (Exception $e) {
            $this->fail('RCPT TO should accept a 250 response');
        }
    }

    public function testMailFromCommandIsOnlySentOncePerMessage()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<me@domain.com>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<foo@bar>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->never()
            ->with("MAIL FROM:<me@domain.com>\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    public function testMultipleRecipientsSendsMultipleRcpt()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array(
                    'foo@bar' => null,
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<foo@bar>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<zip@button>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<test@domain>\r\n")
            ->andReturn(3);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(3)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    public function testCcRecipientsSendsMultipleRcpt()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $message->shouldReceive('getCc')
                ->once()
                ->andReturn(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<foo@bar>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<zip@button>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<test@domain>\r\n")
            ->andReturn(3);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(3)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    public function testSendReturnsNumberOfSuccessfulRecipients()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $message->shouldReceive('getCc')
                ->once()
                ->andReturn(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<foo@bar>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('250 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<zip@button>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('501 Nobody here'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<test@domain>\r\n")
            ->andReturn(3);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(3)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(2, $smtp->send($message),
            '%s: 1 of 3 recipients failed so 2 should be returned'
            );
    }

    public function testRsetIsSentIfNoSuccessfulRecipients()
    {
        /* --RFC 2821, 4.1.1.5.

        This command specifies that the current mail transaction will be
        aborted.  Any stored sender, recipients, and mail data MUST be
        discarded, and all buffers and state tables cleared.  The receiver
        MUST send a "250 OK" reply to a RSET command with no arguments.  A
        reset command may be issued by the client at any time.

        -- RFC 2821, 4.3.2.

        RSET
            S: 250
        */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<foo@bar>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('503 Bad'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RSET\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(0, $smtp->send($message),
            '%s: 1 of 1 recipients failed so 0 should be returned'
            );
    }

    public function testSuccessfulDataCommand()
    {
        /* -- RFC 2821, 3.3.

        The third step in the procedure is the DATA command (or some
        alternative specified in a service extension).

                    DATA <CRLF>

        If accepted, the SMTP server returns a 354 Intermediate reply and
        considers all succeeding lines up to but not including the end of
        mail data indicator to be the message text.

        -- RFC 2821, 4.1.1.4.

        The receiver normally sends a 354 response to DATA, and then treats
        the lines (strings ending in <CRLF> sequences, as described in
        section 2.3.7) following the command as mail data from the sender.
        This command causes the mail data to be appended to the mail data
        buffer.  The mail data may contain any of the 128 ASCII character
        codes, although experience has indicated that use of control
        characters other than SP, HT, CR, and LF may cause problems and
        SHOULD be avoided when possible.

        -- RFC 2821, 4.3.2.

        DATA
            I: 354 -> data -> S: 250
                                                E: 552, 554, 451, 452
            E: 451, 554, 503
        */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("DATA\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('354 Go ahead'."\r\n");

        $this->_finishBuffer($buf);
        try {
            $smtp->start();
            $smtp->send($message);
        } catch (Exception $e) {
            $this->fail('354 is the expected response to DATA');
        }
    }

    public function testBadDataResponseCausesException()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("DATA\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('451 Bad'."\r\n");

        $this->_finishBuffer($buf);
        try {
            $smtp->start();
            $smtp->send($message);
            $this->fail('354 is the expected response to DATA (not observed)');
        } catch (Exception $e) {
        }
    }

    public function testMessageIsStreamedToBufferForData()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("DATA\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('354 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("\r\n.\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('250 OK'."\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    public function testBadResponseAfterDataTransmissionCausesException()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->once()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->once()
                ->andReturn(array('foo@bar' => null));
        $buf->shouldReceive('write')
            ->once()
            ->with("DATA\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn('354 OK'."\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("\r\n.\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn('554 Error'."\r\n");

        $this->_finishBuffer($buf);
        try {
            $smtp->start();
            $smtp->send($message);
            $this->fail('250 is the expected response after a DATA transmission (not observed)');
        } catch (Exception $e) {
        }
    }

    public function testBccRecipientsAreRemovedFromHeaders()
    {
        /* -- RFC 2821, 7.2.

     Addresses that do not appear in the message headers may appear in the
     RCPT commands to an SMTP server for a number of reasons.  The two
     most common involve the use of a mailing address as a "list exploder"
     (a single address that resolves into multiple addresses) and the
     appearance of "blind copies".  Especially when more than one RCPT
     command is present, and in order to avoid defeating some of the
     purpose of these mechanisms, SMTP clients and servers SHOULD NOT copy
     the full set of RCPT command arguments into the headers, either as
     part of trace headers or as informational or private-extension
     headers.  Since this rule is often violated in practice, and cannot
     be enforced, sending SMTP systems that are aware of "bcc" use MAY
     find it helpful to send each blind copy as a separate message
     transaction containing only a single RCPT command.
     */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();
        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null));
        $message->shouldReceive('getBcc')
                ->zeroOrMoreTimes()
                ->andReturn(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $message->shouldReceive('setBcc')
                ->once()
                ->with(array());
        $message->shouldReceive('setBcc')
                ->zeroOrMoreTimes();

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    public function testEachBccRecipientIsSentASeparateMessage()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null));
        $message->shouldReceive('getBcc')
                ->zeroOrMoreTimes()
                ->andReturn(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $message->shouldReceive('setBcc')
                ->atLeast()->once()
                ->with(array());
        $message->shouldReceive('setBcc')
                ->once()
                ->with(array('zip@button' => 'Zip Button'));
        $message->shouldReceive('setBcc')
                ->once()
                ->with(array('test@domain' => 'Test user'));
        $message->shouldReceive('setBcc')
                ->atLeast()->once()
                ->with(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));

        $buf->shouldReceive('write')->once()->with("MAIL FROM:<me@domain.com>\r\n")->andReturn(1);
        $buf->shouldReceive('readLine')->once()->with(1)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("RCPT TO:<foo@bar>\r\n")->andReturn(2);
        $buf->shouldReceive('readLine')->once()->with(2)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("DATA\r\n")->andReturn(3);
        $buf->shouldReceive('readLine')->once()->with(3)->andReturn("354 OK\r\n");
        $buf->shouldReceive('write')->once()->with("\r\n.\r\n")->andReturn(4);
        $buf->shouldReceive('readLine')->once()->with(4)->andReturn("250 OK\r\n");

        $buf->shouldReceive('write')->once()->with("MAIL FROM:<me@domain.com>\r\n")->andReturn(5);
        $buf->shouldReceive('readLine')->once()->with(5)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("RCPT TO:<zip@button>\r\n")->andReturn(6);
        $buf->shouldReceive('readLine')->once()->with(6)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("DATA\r\n")->andReturn(7);
        $buf->shouldReceive('readLine')->once()->with(7)->andReturn("354 OK\r\n");
        $buf->shouldReceive('write')->once()->with("\r\n.\r\n")->andReturn(8);
        $buf->shouldReceive('readLine')->once()->with(8)->andReturn("250 OK\r\n");

        $buf->shouldReceive('write')->once()->with("MAIL FROM:<me@domain.com>\r\n")->andReturn(9);
        $buf->shouldReceive('readLine')->once()->with(9)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("RCPT TO:<test@domain>\r\n")->andReturn(10);
        $buf->shouldReceive('readLine')->once()->with(10)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("DATA\r\n")->andReturn(11);
        $buf->shouldReceive('readLine')->once()->with(11)->andReturn("354 OK\r\n");
        $buf->shouldReceive('write')->once()->with("\r\n.\r\n")->andReturn(12);
        $buf->shouldReceive('readLine')->once()->with(12)->andReturn("250 OK\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(3, $smtp->send($message));
    }

    public function testMessageStateIsRestoredOnFailure()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null));
        $message->shouldReceive('getBcc')
                ->zeroOrMoreTimes()
                ->andReturn(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $message->shouldReceive('setBcc')
                ->once()
                ->with(array());
        $message->shouldReceive('setBcc')
                ->once()
                ->with(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $buf->shouldReceive('write')
            ->once()
            ->with("MAIL FROM:<me@domain.com>\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("RCPT TO:<foo@bar>\r\n")
            ->andReturn(2);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(2)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->once()
            ->with("DATA\r\n")
            ->andReturn(3);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(3)
            ->andReturn("451 No\r\n");

        $this->_finishBuffer($buf);

        $smtp->start();
        try {
            $smtp->send($message);
            $this->fail('A bad response was given so exception is expected');
        } catch (Exception $e) {
        }
    }

    public function testStopSendsQuitCommand()
    {
        /* -- RFC 2821, 4.1.1.10.

        This command specifies that the receiver MUST send an OK reply, and
        then close the transmission channel.

        The receiver MUST NOT intentionally close the transmission channel
        until it receives and replies to a QUIT command (even if there was an
        error).  The sender MUST NOT intentionally close the transmission
        channel until it sends a QUIT command and SHOULD wait until it
        receives the reply (even if there was an error response to a previous
        command).  If the connection is closed prematurely due to violations
        of the above or system or network failure, the server MUST cancel any
        pending transaction, but not undo any previously completed
        transaction, and generally MUST act as if the command or transaction
        in progress had received a temporary error (i.e., a 4yz response).

        The QUIT command may be issued at any time.

        Syntax:
            "QUIT" CRLF
        */

        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();
        $buf->shouldReceive('initialize')
            ->once();
        $buf->shouldReceive('write')
            ->once()
            ->with("QUIT\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->once()
            ->with(1)
            ->andReturn("221 Bye\r\n");
        $buf->shouldReceive('terminate')
            ->once();

        $this->_finishBuffer($buf);

        $this->assertFalse($smtp->isStarted());
        $smtp->start();
        $this->assertTrue($smtp->isStarted());
        $smtp->stop();
        $this->assertFalse($smtp->isStarted());
    }

    public function testBufferCanBeFetched()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $ref = $smtp->getBuffer();
        $this->assertEquals($buf, $ref);
    }

    public function testBufferCanBeWrittenToUsingExecuteCommand()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with("FOO\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with(1)
            ->andReturn("250 OK\r\n");

        $res = $smtp->executeCommand("FOO\r\n");
        $this->assertEquals("250 OK\r\n", $res);
    }

    public function testResponseCodesAreValidated()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with("FOO\r\n")
            ->andReturn(1);
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with(1)
            ->andReturn("551 Not ok\r\n");

        try {
            $smtp->executeCommand("FOO\r\n", array(250, 251));
            $this->fail('A 250 or 251 response was needed but 551 was returned.');
        } catch (Exception $e) {
        }
    }

    public function testFailedRecipientsCanBeCollectedByReference()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();

        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null));
        $message->shouldReceive('getBcc')
                ->zeroOrMoreTimes()
                ->andReturn(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));
        $message->shouldReceive('setBcc')
                ->atLeast()->once()
                ->with(array());
        $message->shouldReceive('setBcc')
                ->once()
                ->with(array('zip@button' => 'Zip Button'));
        $message->shouldReceive('setBcc')
                ->once()
                ->with(array('test@domain' => 'Test user'));
        $message->shouldReceive('setBcc')
                ->atLeast()->once()
                ->with(array(
                    'zip@button' => 'Zip Button',
                    'test@domain' => 'Test user',
                ));

        $buf->shouldReceive('write')->once()->with("MAIL FROM:<me@domain.com>\r\n")->andReturn(1);
        $buf->shouldReceive('readLine')->once()->with(1)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("RCPT TO:<foo@bar>\r\n")->andReturn(2);
        $buf->shouldReceive('readLine')->once()->with(2)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("DATA\r\n")->andReturn(3);
        $buf->shouldReceive('readLine')->once()->with(3)->andReturn("354 OK\r\n");
        $buf->shouldReceive('write')->once()->with("\r\n.\r\n")->andReturn(4);
        $buf->shouldReceive('readLine')->once()->with(4)->andReturn("250 OK\r\n");

        $buf->shouldReceive('write')->once()->with("MAIL FROM:<me@domain.com>\r\n")->andReturn(5);
        $buf->shouldReceive('readLine')->once()->with(5)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("RCPT TO:<zip@button>\r\n")->andReturn(6);
        $buf->shouldReceive('readLine')->once()->with(6)->andReturn("500 Bad\r\n");
        $buf->shouldReceive('write')->once()->with("RSET\r\n")->andReturn(7);
        $buf->shouldReceive('readLine')->once()->with(7)->andReturn("250 OK\r\n");

        $buf->shouldReceive('write')->once()->with("MAIL FROM:<me@domain.com>\r\n")->andReturn(9);
        $buf->shouldReceive('readLine')->once()->with(9)->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')->once()->with("RCPT TO:<test@domain>\r\n")->andReturn(10);
        $buf->shouldReceive('readLine')->once()->with(10)->andReturn("500 Bad\r\n");
        $buf->shouldReceive('write')->once()->with("RSET\r\n")->andReturn(11);
        $buf->shouldReceive('readLine')->once()->with(11)->andReturn("250 OK\r\n");

        $this->_finishBuffer($buf);
        $smtp->start();
        $this->assertEquals(1, $smtp->send($message, $failures));
        $this->assertEquals(array('zip@button', 'test@domain'), $failures,
            '%s: Failures should be caught in an array'
            );
    }

    public function testSendingRegeneratesMessageId()
    {
        $buf = $this->_getBuffer();
        $smtp = $this->_getTransport($buf);
        $message = $this->_createMessage();
        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(array('me@domain.com' => 'Me'));
        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null));
        $message->shouldReceive('generateId')
                ->once();

        $this->_finishBuffer($buf);
        $smtp->start();
        $smtp->send($message);
    }

    protected function _getBuffer()
    {
        return $this->getMockery('Swift_Transport_IoBuffer')->shouldIgnoreMissing();
    }

    protected function _createMessage()
    {
        return $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
    }

    protected function _finishBuffer($buf)
    {
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with(0)
            ->andReturn('220 server.com foo'."\r\n");
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with('~^(EH|HE)LO .*?\r\n$~D')
            ->andReturn($x = uniqid());
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with($x)
            ->andReturn('250 ServerName'."\r\n");
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with('~^MAIL FROM:<.*?>\r\n$~D')
            ->andReturn($x = uniqid());
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with($x)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with('~^RCPT TO:<.*?>\r\n$~D')
            ->andReturn($x = uniqid());
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with($x)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with("DATA\r\n")
            ->andReturn($x = uniqid());
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with($x)
            ->andReturn("354 OK\r\n");
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with("\r\n.\r\n")
            ->andReturn($x = uniqid());
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with($x)
            ->andReturn("250 OK\r\n");
        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->with("RSET\r\n")
            ->andReturn($x = uniqid());
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->with($x)
            ->andReturn("250 OK\r\n");

        $buf->shouldReceive('write')
            ->zeroOrMoreTimes()
            ->andReturn(false);
        $buf->shouldReceive('readLine')
            ->zeroOrMoreTimes()
            ->andReturn(false);
    }
}
