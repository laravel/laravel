<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Closure;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Utils;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

/**
 * SymfonyMailerHandler uses Symfony's Mailer component to send the emails
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class SymfonyMailerHandler extends MailHandler
{
    protected MailerInterface|TransportInterface $mailer;
    /** @var Email|Closure(string, LogRecord[]): Email */
    private Email|Closure $emailTemplate;

    /**
     * @phpstan-param Email|Closure(string, LogRecord[]): Email $email
     *
     * @param MailerInterface|TransportInterface $mailer The mailer to use
     * @param Closure|Email                      $email  An email template, the subject/body will be replaced
     */
    public function __construct($mailer, Email|Closure $email, int|string|Level $level = Level::Error, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->mailer = $mailer;
        $this->emailTemplate = $email;
    }

    /**
     * {@inheritDoc}
     */
    protected function send(string $content, array $records): void
    {
        $this->mailer->send($this->buildMessage($content, $records));
    }

    /**
     * Gets the formatter for the Swift_Message subject.
     *
     * @param string|null $format The format of the subject
     */
    protected function getSubjectFormatter(?string $format): FormatterInterface
    {
        return new LineFormatter($format);
    }

    /**
     * Creates instance of Email to be sent
     *
     * @param string      $content formatted email body to be sent
     * @param LogRecord[] $records Log records that formed the content
     */
    protected function buildMessage(string $content, array $records): Email
    {
        $message = null;
        if ($this->emailTemplate instanceof Email) {
            $message = clone $this->emailTemplate;
        } elseif (\is_callable($this->emailTemplate)) {
            $message = ($this->emailTemplate)($content, $records);
        }

        if (!$message instanceof Email) {
            $record = reset($records);

            throw new \InvalidArgumentException('Could not resolve message as instance of Email or a callable returning it' . ($record instanceof LogRecord ? Utils::getRecordMessageForException($record) : ''));
        }

        if (\count($records) > 0) {
            $subjectFormatter = $this->getSubjectFormatter($message->getSubject());
            $message->subject($subjectFormatter->format($this->getHighestRecord($records)));
        }

        if ($this->isHtmlBody($content)) {
            if (null !== ($charset = $message->getHtmlCharset())) {
                $message->html($content, $charset);
            } else {
                $message->html($content);
            }
        } else {
            if (null !== ($charset = $message->getTextCharset())) {
                $message->text($content, $charset);
            } else {
                $message->text($content);
            }
        }

        return $message->date(new \DateTimeImmutable());
    }
}
