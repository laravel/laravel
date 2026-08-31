<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport\Smtp;

use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
final class EsmtpTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        if (!\in_array($dsn->getScheme(), $this->getSupportedSchemes(), true)) {
            throw new UnsupportedSchemeException($dsn, 'smtp', $this->getSupportedSchemes());
        }

        $autoTls = '' === $dsn->getOption('auto_tls') || filter_var($dsn->getOption('auto_tls', true), \FILTER_VALIDATE_BOOL);
        $tls = 'smtps' === $dsn->getScheme() ? true : ($autoTls ? null : false);
        $port = $dsn->getPort(0);
        $host = $dsn->getHost();

        $transport = new EsmtpTransport($host, $port, $tls, $this->dispatcher, $this->logger);
        $transport->setAutoTls($autoTls);
        $transport->setRequireTls($dsn->getBooleanOption('require_tls'));

        /** @var SocketStream $stream */
        $stream = $transport->getStream();
        if ('' !== $sourceIp = $dsn->getOption('source_ip', '')) {
            $stream->setSourceIp($sourceIp);
        }
        $streamOptions = $stream->getStreamOptions();

        if ('' !== $dsn->getOption('verify_peer') && !filter_var($dsn->getOption('verify_peer', true), \FILTER_VALIDATE_BOOL)) {
            $streamOptions['ssl']['verify_peer'] = false;
            $streamOptions['ssl']['verify_peer_name'] = false;
        }

        if (null !== $peerFingerprint = $dsn->getOption('peer_fingerprint')) {
            $streamOptions['ssl']['peer_fingerprint'] = $peerFingerprint;
        }

        $stream->setStreamOptions($streamOptions);

        if ($user = $dsn->getUser()) {
            $transport->setUsername($user);
        }

        if ($password = $dsn->getPassword()) {
            $transport->setPassword($password);
        }

        if (null !== ($localDomain = $dsn->getOption('local_domain'))) {
            $transport->setLocalDomain($localDomain);
        }

        if (null !== ($maxPerSecond = $dsn->getOption('max_per_second'))) {
            $transport->setMaxPerSecond((float) $maxPerSecond);
        }

        if (null !== ($restartThreshold = $dsn->getOption('restart_threshold'))) {
            $transport->setRestartThreshold((int) $restartThreshold, (int) $dsn->getOption('restart_threshold_sleep', 0));
        }

        if (null !== ($pingThreshold = $dsn->getOption('ping_threshold'))) {
            $transport->setPingThreshold((int) $pingThreshold);
        }

        return $transport;
    }

    protected function getSupportedSchemes(): array
    {
        return ['smtp', 'smtps'];
    }
}
