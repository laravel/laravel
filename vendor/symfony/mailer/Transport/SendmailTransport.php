<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\AbstractStream;
use Symfony\Component\Mailer\Transport\Smtp\Stream\ProcessStream;
use Symfony\Component\Mime\RawMessage;

/**
 * SendmailTransport for sending mail through a Sendmail/Postfix (etc..) binary.
 *
 * Transport can be instantiated through SendmailTransportFactory or NativeTransportFactory:
 *
 * - SendmailTransportFactory to use most common sendmail path and recommended options
 * - NativeTransportFactory when configuration is set via php.ini
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Chris Corbyn
 */
class SendmailTransport extends AbstractTransport
{
    private string $command = '/usr/sbin/sendmail -bs';
    private ProcessStream $stream;
    private ?SmtpTransport $transport = null;

    /**
     * Constructor.
     *
     * Supported modes are -bs and -t, with any additional flags desired.
     *
     * The recommended mode is "-bs" since it is interactive and failure notifications are hence possible.
     * Note that the -t mode does not support error reporting and does not support Bcc properly (the Bcc headers are not removed).
     *
     * If using -t mode, you are strongly advised to include -oi or -i in the flags (like /usr/sbin/sendmail -oi -t)
     *
     * -f<sender> flag will be appended automatically if one is not present.
     */
    public function __construct(?string $command = null, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null)
    {
        parent::__construct($dispatcher, $logger);

        if (null !== $command) {
            if (!str_contains($command, ' -bs') && !str_contains($command, ' -t')) {
                throw new \InvalidArgumentException(\sprintf('Unsupported sendmail command flags "%s"; must be one of "-bs" or "-t" but can include additional flags.', $command));
            }

            $this->command = $command;
        }

        $this->stream = new ProcessStream();
        if (str_contains($this->command, ' -bs')) {
            $this->stream->setCommand($this->command);
            $this->stream->setInteractive(true);
            $this->transport = new SmtpTransport($this->stream, $dispatcher, $logger);
        }
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if ($this->transport) {
            return $this->transport->send($message, $envelope);
        }

        return parent::send($message, $envelope);
    }

    public function __toString(): string
    {
        if ($this->transport) {
            return (string) $this->transport;
        }

        return 'smtp://sendmail';
    }

    protected function doSend(SentMessage $message): void
    {
        $this->getLogger()->debug(\sprintf('Email transport "%s" starting', __CLASS__));

        $command = $this->command;

        if ($recipients = $message->getEnvelope()->getRecipients()) {
            $command = str_replace(' -t', '', $command);
        }

        if (!str_contains($command, ' -f')) {
            $command .= ' -f'.escapeshellarg($message->getEnvelope()->getSender()->getEncodedAddress());
        }

        $chunks = AbstractStream::replace("\r\n", "\n", $message->toIterable());

        if (!str_contains($command, ' -i') && !str_contains($command, ' -oi')) {
            $chunks = AbstractStream::replace("\n.", "\n..", $chunks);
        }

        if ($recipients) {
            $command .= ' --';
        }
        foreach ($recipients as $recipient) {
            $command .= ' '.escapeshellarg($recipient->getEncodedAddress());
        }

        $this->stream->setCommand($command);
        $this->stream->initialize();
        foreach ($chunks as $chunk) {
            $this->stream->write($chunk, false);
        }
        $this->stream->flush();
        $this->stream->terminate();

        $this->getLogger()->debug(\sprintf('Email transport "%s" stopped', __CLASS__));
    }
}
