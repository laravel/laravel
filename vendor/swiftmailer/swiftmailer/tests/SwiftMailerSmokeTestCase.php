<?php

/**
 * Base test for smoke tests.
 *
 * @author Rouven Weßling
 */
class SwiftMailerSmokeTestCase extends SwiftMailerTestCase
{
    public function setUp()
    {
        if (!defined('SWIFT_SMOKE_TRANSPORT_TYPE')) {
            $this->markTestSkipped(
                'Smoke tests are skipped if tests/smoke.conf.php is not edited'
             );
        }
    }

    protected function _getMailer()
    {
        switch (SWIFT_SMOKE_TRANSPORT_TYPE) {
            case 'smtp':
                $transport = Swift_DependencyContainer::getInstance()->lookup('transport.smtp')
                    ->setHost(SWIFT_SMOKE_SMTP_HOST)
                    ->setPort(SWIFT_SMOKE_SMTP_PORT)
                    ->setUsername(SWIFT_SMOKE_SMTP_USER)
                    ->setPassword(SWIFT_SMOKE_SMTP_PASS)
                    ->setEncryption(SWIFT_SMOKE_SMTP_ENCRYPTION)
                    ;
                break;
            case 'sendmail':
                $transport = Swift_DependencyContainer::getInstance()->lookup('transport.sendmail')
                    ->setCommand(SWIFT_SMOKE_SENDMAIL_COMMAND)
                    ;
                break;
            case 'mail':
            case 'nativemail':
                $transport = Swift_DependencyContainer::getInstance()->lookup('transport.mail');
                break;
            default:
                throw new Exception('Undefined transport ['.SWIFT_SMOKE_TRANSPORT_TYPE.']');
        }

        return new Swift_Mailer($transport);
    }
}
