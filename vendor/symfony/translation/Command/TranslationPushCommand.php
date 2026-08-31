<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\Provider\FilteringProvider;
use Symfony\Component\Translation\Provider\TranslationProviderCollection;
use Symfony\Component\Translation\Reader\TranslationReaderInterface;
use Symfony\Component\Translation\TranslatorBag;

/**
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 */
#[AsCommand(name: 'translation:push', description: 'Push translations to a given provider.')]
final class TranslationPushCommand extends Command
{
    use TranslationTrait;

    public function __construct(
        private TranslationProviderCollection $providers,
        private TranslationReaderInterface $reader,
        private array $transPaths = [],
        private array $enabledLocales = [],
    ) {
        parent::__construct();
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('provider')) {
            $suggestions->suggestValues($this->providers->keys());

            return;
        }

        if ($input->mustSuggestOptionValuesFor('domains')) {
            $provider = $this->providers->get($input->getArgument('provider'));

            if (method_exists($provider, 'getDomains')) {
                $domains = $provider->getDomains();
                $suggestions->suggestValues($domains);
            }

            return;
        }

        if ($input->mustSuggestOptionValuesFor('locales')) {
            $suggestions->suggestValues($this->enabledLocales);
        }
    }

    protected function configure(): void
    {
        $keys = $this->providers->keys();
        $defaultProvider = 1 === \count($keys) ? $keys[0] : null;

        $this
            ->setDefinition([
                new InputArgument('provider', null !== $defaultProvider ? InputArgument::OPTIONAL : InputArgument::REQUIRED, 'The provider to push translations to.', $defaultProvider),
                new InputOption('force', null, InputOption::VALUE_NONE, 'Override existing translations with local ones (it will delete not synchronized messages).'),
                new InputOption('delete-missing', null, InputOption::VALUE_NONE, 'Delete translations available on provider but not locally.'),
                new InputOption('domains', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Specify the domains to push.'),
                new InputOption('locales', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Specify the locales to push.', $this->enabledLocales),
            ])
            ->setHelp(<<<'EOF'
                The <info>%command.name%</> command pushes translations to the given provider. Only new
                translations are pushed, existing ones are not overwritten.

                You can overwrite existing translations by using the <info>--force</> flag:

                  <info>php %command.full_name% --force provider</>

                You can delete provider translations which are not present locally by using the <info>--delete-missing</> flag:

                  <info>php %command.full_name% --delete-missing provider</>

                Full example:

                  <info>php %command.full_name% provider --force --delete-missing --domains=messages --domains=validators --locales=en</>

                This command pushes all translations associated with the <info>messages</> and <info>validators</> domains for the <info>en</> locale.
                Provider translations for the specified domains and locale are deleted if they're not present locally and overwritten if it's the case.
                Provider translations for others domains and locales are ignored.

                EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $this->providers->get($input->getArgument('provider'));

        if (!$this->enabledLocales) {
            throw new InvalidArgumentException(\sprintf('You must define "framework.enabled_locales" or "framework.translator.providers.%s.locales" config key in order to work with translation providers.', parse_url($provider, \PHP_URL_SCHEME)));
        }

        $io = new SymfonyStyle($input, $output);
        $domains = $input->getOption('domains');
        $locales = $input->getOption('locales');
        $force = $input->getOption('force');
        $deleteMissing = $input->getOption('delete-missing');

        if (!$domains && $provider instanceof FilteringProvider) {
            $domains = $provider->getDomains();
        }

        // Reading local translations must be done after retrieving the domains from the provider
        // in order to manage only translations from configured domains
        $localTranslations = $this->readLocalTranslations($locales, $domains, $this->transPaths);

        if (!$domains) {
            $domains = $this->getDomainsFromTranslatorBag($localTranslations);
        }

        if (!$deleteMissing && $force) {
            $provider->write($localTranslations);

            $io->success(\sprintf('All local translations has been sent to "%s" (for "%s" locale(s), and "%s" domain(s)).', parse_url($provider, \PHP_URL_SCHEME), implode(', ', $locales), implode(', ', $domains)));

            return 0;
        }

        $providerTranslations = $provider->read($domains, $locales);

        if ($deleteMissing) {
            $provider->delete($providerTranslations->diff($localTranslations));

            $io->success(\sprintf('Missing translations on "%s" has been deleted (for "%s" locale(s), and "%s" domain(s)).', parse_url($provider, \PHP_URL_SCHEME), implode(', ', $locales), implode(', ', $domains)));

            // Read provider translations again, after missing translations deletion,
            // to avoid push freshly deleted translations.
            $providerTranslations = $provider->read($domains, $locales);
        }

        $translationsToWrite = $localTranslations->diff($providerTranslations);

        if ($force) {
            $translationsToWrite->addBag($localTranslations->intersect($providerTranslations));
        }

        $provider->write($translationsToWrite);

        $io->success(\sprintf('%s local translations has been sent to "%s" (for "%s" locale(s), and "%s" domain(s)).', $force ? 'All' : 'New', parse_url($provider, \PHP_URL_SCHEME), implode(', ', $locales), implode(', ', $domains)));

        return 0;
    }

    private function getDomainsFromTranslatorBag(TranslatorBag $translatorBag): array
    {
        $domains = [];

        foreach ($translatorBag->getCatalogues() as $catalogue) {
            $domains = array_merge($domains, $catalogue->getDomains());
        }

        return array_values(array_unique($domains));
    }
}
