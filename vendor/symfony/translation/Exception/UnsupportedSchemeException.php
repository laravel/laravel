<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Exception;

use Symfony\Component\Translation\Bridge;
use Symfony\Component\Translation\Provider\Dsn;

class UnsupportedSchemeException extends LogicException
{
    private const SCHEME_TO_PACKAGE_MAP = [
        'crowdin' => [
            'class' => Bridge\Crowdin\CrowdinProviderFactory::class,
            'package' => 'symfony/crowdin-translation-provider',
        ],
        'loco' => [
            'class' => Bridge\Loco\LocoProviderFactory::class,
            'package' => 'symfony/loco-translation-provider',
        ],
        'lokalise' => [
            'class' => Bridge\Lokalise\LokaliseProviderFactory::class,
            'package' => 'symfony/lokalise-translation-provider',
        ],
        'phrase' => [
            'class' => Bridge\Phrase\PhraseProviderFactory::class,
            'package' => 'symfony/phrase-translation-provider',
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
            parent::__construct(\sprintf('Unable to synchronize translations via "%s" as the provider is not installed. Try running "composer require %s".', $provider, $package['package']));

            return;
        }

        $message = \sprintf('The "%s" scheme is not supported', $dsn->getScheme());
        if ($name && $supported) {
            $message .= \sprintf('; supported schemes for translation provider "%s" are: "%s"', $name, implode('", "', $supported));
        }

        parent::__construct($message.'.');
    }
}
