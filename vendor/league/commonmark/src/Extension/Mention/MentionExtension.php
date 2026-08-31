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

namespace League\CommonMark\Extension\Mention;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;
use League\Config\ConfigurationBuilderInterface;
use League\Config\Exception\InvalidConfigurationException;
use Nette\Schema\Expect;

final class MentionExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $isAValidPartialRegex = static function (string $regex): bool {
            $regex = '/' . $regex . '/i';

            return @\preg_match($regex, '') !== false;
        };

        $builder->addSchema('mentions', Expect::arrayOf(
            Expect::structure([
                'prefix' => Expect::string()->required(),
                'pattern' => Expect::string()->assert($isAValidPartialRegex, 'Pattern must not include starting/ending delimiters (like "/")')->required(),
                'generator' => Expect::anyOf(
                    Expect::type(MentionGeneratorInterface::class),
                    Expect::string(),
                    Expect::type('callable')
                )->required(),
            ])
        ));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $mentions = $environment->getConfiguration()->get('mentions');
        foreach ($mentions as $name => $mention) {
            if ($mention['generator'] instanceof MentionGeneratorInterface) {
                $environment->addInlineParser(new MentionParser($name, $mention['prefix'], $mention['pattern'], $mention['generator']));
            } elseif (\is_string($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithStringTemplate($name, $mention['prefix'], $mention['pattern'], $mention['generator']));
            } elseif (\is_callable($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithCallback($name, $mention['prefix'], $mention['pattern'], $mention['generator']));
            } else {
                throw new InvalidConfigurationException(\sprintf('The "generator" provided for the "%s" MentionParser configuration must be a string template, callable, or an object that implements %s.', $name, MentionGeneratorInterface::class));
            }
        }
    }
}
