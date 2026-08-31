<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\ExternalLink;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class ExternalLinkExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $applyOptions = [
            ExternalLinkProcessor::APPLY_NONE,
            ExternalLinkProcessor::APPLY_ALL,
            ExternalLinkProcessor::APPLY_INTERNAL,
            ExternalLinkProcessor::APPLY_EXTERNAL,
        ];

        $builder->addSchema('external_link', Expect::structure([
            'internal_hosts' => Expect::type('string|string[]'),
            'open_in_new_window' => Expect::bool(false),
            'html_class' => Expect::string()->default(''),
            'nofollow' => Expect::anyOf(...$applyOptions)->default(ExternalLinkProcessor::APPLY_NONE),
            'noopener' => Expect::anyOf(...$applyOptions)->default(ExternalLinkProcessor::APPLY_EXTERNAL),
            'noreferrer' => Expect::anyOf(...$applyOptions)->default(ExternalLinkProcessor::APPLY_EXTERNAL),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, new ExternalLinkProcessor($environment->getConfiguration()), -50);
    }
}
