<?php

namespace Illuminate\Mail\Transport;

use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

class LogTransport implements Stringable, TransportInterface
{
    /**
     * The Logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Create a new log transport instance.
     *
     * @param  \Psr\Log\LoggerInterface  $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        $string = Str::of($message->toString());

        if ($string->contains('Content-Type: multipart/')) {
            $boundary = $string
                ->after('boundary=')
                ->before("\r\n")
                ->prepend('--')
                ->append("\r\n");

            $string = $string
                ->explode($boundary)
                ->map($this->decodeQuotedPrintableContent(...))
                ->implode($boundary);
        } elseif ($string->contains('Content-Transfer-Encoding: quoted-printable')) {
            $string = $this->decodeQuotedPrintableContent($string);
        }

        $this->logger->debug((string) $string);

        return new SentMessage($message, $envelope ?? Envelope::create($message));
    }

    /**
     * Decode the given quoted printable content.
     *
     * @param  string  $part
     * @return string
     */
    protected function decodeQuotedPrintableContent(string $part)
    {
        if (! str_contains($part, 'Content-Transfer-Encoding: quoted-printable')) {
            return $part;
        }

        [$headers, $content] = explode("\r\n\r\n", $part, 2);

        return implode("\r\n\r\n", [
            $headers,
            quoted_printable_decode($content),
        ]);
    }

    /**
     * Get the logger for the LogTransport instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function logger()
    {
        return $this->logger;
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'log';
    }
}
