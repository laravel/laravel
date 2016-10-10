<?php

require_once 'Swift/Tests/SwiftUnitTestCase.php';
require_once 'swift_required.php';

/**
 * Smoke test helper class.
 * @package Swift
 * @subpackage Tests
 * @author Chris Corbyn
 */
class Swift_Tests_SwiftSmokeTestCase extends Swift_Tests_SwiftUnitTestCase
{
    public function skip()
    {
        $this->skipUnless(SWIFT_SMOKE_TRANSPORT_TYPE,
            '%s: Smoke tests are skipped if tests/smoke.conf.php is not edited'
            );
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
                throw new Exception('Undefined transport [' . SWIFT_SMOKE_TRANSPORT_TYPE . ']');
        }

        return new Swift_Mailer($transport);
    }

    protected function _visualCheck($url)
    {
        $this->dump('{image @ ' . $url . '}');
    }
}
