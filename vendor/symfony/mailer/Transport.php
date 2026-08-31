<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Bridge\AhaSend\Transport\AhaSendTransportFactory;
use Symfony\Component\Mailer\Bridge\Amazon\Transport\SesTransportFactory;
use Symfony\Component\Mailer\Bridge\Azure\Transport\AzureTransportFactory;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailTransportFactory;
use Symfony\Component\Mailer\Bridge\Infobip\Transport\InfobipTransportFactory;
use Symfony\Component\Mailer\Bridge\Mailchimp\Transport\MandrillTransportFactory;
use Symfony\Component\Mailer\Bridge\MailerSend\Transport\MailerSendTransportFactory;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunTransportFactory;
use Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetTransportFactory;
use Symfony\Component\Mailer\Bridge\Mailomat\Transport\MailomatTransportFactory;
use Symfony\Component\Mailer\Bridge\MailPace\Transport\MailPaceTransportFactory;
use Symfony\Component\Mailer\Bridge\Mailtrap\Transport\MailtrapTransportFactory;
use Symfony\Component\Mailer\Bridge\MicrosoftGraph\Transport\MicrosoftGraphTransportFactory;
use Symfony\Component\Mailer\Bridge\Postal\Transport\PostalTransportFactory;
use Symfony\Component\Mailer\Bridge\Postmark\Transport\PostmarkTransportFactory;
use Symfony\Component\Mailer\Bridge\Resend\Transport\ResendTransportFactory;
use Symfony\Component\Mailer\Bridge\Scaleway\Transport\ScalewayTransportFactory;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridTransportFactory;
use Symfony\Component\Mailer\Bridge\Sweego\Transport\SweegoTransportFactory;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\FailoverTransport;
use Symfony\Component\Mailer\Transport\NativeTransportFactory;
use Symfony\Component\Mailer\Transport\NullTransportFactory;
use Symfony\Component\Mailer\Transport\RoundRobinTransport;
use Symfony\Component\Mailer\Transport\SendmailTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Transport\Transports;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
final class Transport
{
    private const FACTORY_CLASSES = [
        AhaSendTransportFactory::class,
        AzureTransportFactory::class,
        BrevoTransportFactory::class,
        GmailTransportFactory::class,
        InfobipTransportFactory::class,
        MailerSendTransportFactory::class,
        MailgunTransportFactory::class,
        MailjetTransportFactory::class,
        MailomatTransportFactory::class,
        MailPaceTransportFactory::class,
        MandrillTransportFactory::class,
        PostalTransportFactory::class,
        PostmarkTransportFactory::class,
        MailtrapTransportFactory::class,
        MicrosoftGraphTransportFactory::class,
        ResendTransportFactory::class,
        ScalewayTransportFactory::class,
        SendgridTransportFactory::class,
        SesTransportFactory::class,
        SweegoTransportFactory::class,
    ];

    public static function fromDsn(#[\SensitiveParameter] string $dsn, ?EventDispatcherInterface $dispatcher = null, ?HttpClientInterface $client = null, ?LoggerInterface $logger = null): TransportInterface
    {
        $factory = new self(iterator_to_array(self::getDefaultFactories($dispatcher, $client, $logger)));

        return $factory->fromString($dsn);
    }

    public static function fromDsns(#[\SensitiveParameter] array $dsns, ?EventDispatcherInterface $dispatcher = null, ?HttpClientInterface $client = null, ?LoggerInterface $logger = null): TransportInterface
    {
        $factory = new self(iterator_to_array(self::getDefaultFactories($dispatcher, $client, $logger)));

        return $factory->fromStrings($dsns);
    }

    /**
     * @param TransportFactoryInterface[] $factories
     */
    public function __construct(
        private iterable $factories,
    ) {
    }

    public function fromStrings(#[\SensitiveParameter] array $dsns): Transports
    {
        $transports = [];
        foreach ($dsns as $name => $dsn) {
            $transports[$name] = $this->fromString($dsn);
        }

        return new Transports($transports);
    }

    public function fromString(#[\SensitiveParameter] string $dsn): TransportInterface
    {
        [$transport, $offset] = $this->parseDsn($dsn);
        if ($offset !== \strlen($dsn)) {
            throw new InvalidArgumentException('The mailer DSN has some garbage at the end.');
        }

        return $transport;
    }

    private function parseDsn(#[\SensitiveParameter] string $dsn, int $offset = 0): array
    {
        static $keywords = [
            'failover' => FailoverTransport::class,
            'roundrobin' => RoundRobinTransport::class,
        ];

        while (true) {
            foreach ($keywords as $name => $class) {
                $name .= '(';
                if ($name === substr($dsn, $offset, \strlen($name))) {
                    $offset += \strlen($name) - 1;
                    preg_match('{\(([^()]|(?R))*\)}A', $dsn, $matches, 0, $offset);
                    if (!isset($matches[0])) {
                        continue;
                    }

                    ++$offset;
                    $args = [];
                    while (true) {
                        [$arg, $offset] = $this->parseDsn($dsn, $offset);
                        $args[] = $arg;
                        if (\strlen($dsn) === $offset) {
                            break;
                        }
                        ++$offset;
                        if (')' === $dsn[$offset - 1]) {
                            break;
                        }
                    }

                    parse_str(substr($dsn, $offset + 1), $query);
                    if ($period = $query['retry_period'] ?? 0) {
                        return [new $class($args, (int) $period), $offset + \strlen('retry_period='.$period) + 1];
                    }

                    return [new $class($args), $offset];
                }
            }

            if (preg_match('{(\w+)\(}A', $dsn, $matches, 0, $offset)) {
                throw new InvalidArgumentException(\sprintf('The "%s" keyword is not valid (valid ones are "%s"), ', $matches[1], implode('", "', array_keys($keywords))));
            }

            if ($pos = strcspn($dsn, ' )', $offset)) {
                return [$this->fromDsnObject(Dsn::fromString(substr($dsn, $offset, $pos))), $offset + $pos];
            }

            return [$this->fromDsnObject(Dsn::fromString(substr($dsn, $offset))), \strlen($dsn)];
        }
    }

    public function fromDsnObject(Dsn $dsn): TransportInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($dsn)) {
                return $factory->create($dsn);
            }
        }

        throw new UnsupportedSchemeException($dsn);
    }

    /**
     * @return \Traversable<int, TransportFactoryInterface>
     */
    public static function getDefaultFactories(?EventDispatcherInterface $dispatcher = null, ?HttpClientInterface $client = null, ?LoggerInterface $logger = null): \Traversable
    {
        foreach (self::FACTORY_CLASSES as $factoryClass) {
            if (class_exists($factoryClass)) {
                yield new $factoryClass($dispatcher, $client, $logger);
            }
        }

        yield new NullTransportFactory($dispatcher, $client, $logger);

        yield new SendmailTransportFactory($dispatcher, $client, $logger);

        yield new EsmtpTransportFactory($dispatcher, $client, $logger);

        yield new NativeTransportFactory($dispatcher, $client, $logger);
    }
}
