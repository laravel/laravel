<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Exception;

use Symfony\Component\Mailer\Bridge;
use Symfony\Component\Mailer\Transport\Dsn;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
class UnsupportedSchemeException extends LogicException
{
    private const SCHEME_TO_PACKAGE_MAP = [
        'ahasend' => [
            'class' => Bridge\AhaSend\Transport\AhaSendTransportFactory::class,
            'package' => 'symfony/aha-send-mailer',
        ],
        'azure' => [
            'class' => Bridge\Azure\Transport\AzureTransportFactory::class,
            'package' => 'symfony/azure-mailer',
        ],
        'brevo' => [
            'class' => Bridge\Brevo\Transport\BrevoTransportFactory::class,
            'package' => 'symfony/brevo-mailer',
        ],
        'gmail' => [
            'class' => Bridge\Google\Transport\GmailTransportFactory::class,
            'package' => 'symfony/google-mailer',
        ],
        'infobip' => [
            'class' => Bridge\Infobip\Transport\InfobipTransportFactory::class,
            'package' => 'symfony/infobip-mailer',
        ],
        'mailersend' => [
            'class' => Bridge\MailerSend\Transport\MailerSendTransportFactory::class,
            'package' => 'symfony/mailersend-mailer',
        ],
        'mailgun' => [
            'class' => Bridge\Mailgun\Transport\MailgunTransportFactory::class,
            'package' => 'symfony/mailgun-mailer',
        ],
        'mailjet' => [
            'class' => Bridge\Mailjet\Transport\MailjetTransportFactory::class,
            'package' => 'symfony/mailjet-mailer',
        ],
        'mailomat' => [
            'class' => Bridge\Mailomat\Transport\MailomatTransportFactory::class,
            'package' => 'symfony/mailomat-mailer',
        ],
        'mailpace' => [
            'class' => Bridge\MailPace\Transport\MailPaceTransportFactory::class,
            'package' => 'symfony/mail-pace-mailer',
        ],
        'mandrill' => [
            'class' => Bridge\Mailchimp\Transport\MandrillTransportFactory::class,
            'package' => 'symfony/mailchimp-mailer',
        ],
        'postal' => [
            'class' => Bridge\Postal\Transport\PostalTransportFactory::class,
            'package' => 'symfony/postal-mailer',
        ],
        'postmark' => [
            'class' => Bridge\Postmark\Transport\PostmarkTransportFactory::class,
            'package' => 'symfony/postmark-mailer',
        ],
        'mailtrap' => [
            'class' => Bridge\Mailtrap\Transport\MailtrapTransportFactory::class,
            'package' => 'symfony/mailtrap-mailer',
        ],
        'microsoftgraph' => [
            'class' => Bridge\MicrosoftGraph\Transport\MicrosoftGraphTransportFactory::class,
            'package' => 'symfony/microsoft-graph-mailer',
        ],
        'resend' => [
            'class' => Bridge\Resend\Transport\ResendTransportFactory::class,
            'package' => 'symfony/resend-mailer',
        ],
        'scaleway' => [
            'class' => Bridge\Scaleway\Transport\ScalewayTransportFactory::class,
            'package' => 'symfony/scaleway-mailer',
        ],
        'sendgrid' => [
            'class' => Bridge\Sendgrid\Transport\SendgridTransportFactory::class,
            'package' => 'symfony/sendgrid-mailer',
        ],
        'ses' => [
            'class' => Bridge\Amazon\Transport\SesTransportFactory::class,
            'package' => 'symfony/amazon-mailer',
        ],
        'sweego' => [
            'class' => Bridge\Sweego\Transport\SweegoTransportFactory::class,
            'package' => 'symfony/sweego-mailer',
        ],
    ];

    public function __construct(Dsn $dsn, ?string $name = null, array $supported = [])
    {
        $provider = $dsn->getScheme();
        if (false !== $pos = strpos($provider, '+')) {
            $provider = substr($provider, 0, $pos);
        }
        $package = self::SCHEME_TO_PACKAGE_MAP[$provider] ?? null;
        if ($package && !class_exists($package['class'])) {
            parent::__construct(\sprintf('Unable to send emails via "%s" as the bridge is not installed. Try running "composer require %s".', $provider, $package['package']));

            return;
        }

        $message = \sprintf('The "%s" scheme is not supported', $dsn->getScheme());
        if ($name && $supported) {
            $message .= \sprintf('; supported schemes for mailer "%s" are: "%s"', $name, implode('", "', $supported));
        }

        parent::__construct($message.'.');
    }
}
