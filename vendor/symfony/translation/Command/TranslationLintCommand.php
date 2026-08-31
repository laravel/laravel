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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\Exception\ExceptionInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Lint translations files syntax and outputs encountered errors.
 *
 * @author Hugo Alliaume <hugo@alliau.me>
 */
#[AsCommand(name: 'lint:translations', description: 'Lint translations files syntax and outputs encountered errors')]
class TranslationLintCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private TranslatorInterface&TranslatorBagInterface $translator,
        private array $enabledLocales = [],
    ) {
        parent::__construct();
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestOptionValuesFor('locale')) {
            $suggestions->suggestValues($this->enabledLocales);
        }
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputOption('locale', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Specify the locales to lint.', $this->enabledLocales),
            ])
            ->setHelp(<<<'EOF'
                The <info>%command.name%</> command lint translations.

                  <info>php %command.full_name%</>
                EOF
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $locales = $input->getOption('locale');

        /** @var array<string, array<string, array<string, \Throwable>>> $errors */
        $errors = [];
        $domainsByLocales = [];

        foreach ($locales as $locale) {
            $messageCatalogue = $this->translator->getCatalogue($locale);

            foreach ($domainsByLocales[$locale] = $messageCatalogue->getDomains() as $domain) {
                foreach ($messageCatalogue->all($domain) as $id => $translation) {
                    try {
                        $this->translator->trans($id, [], $domain, $messageCatalogue->getLocale());
                    } catch (ExceptionInterface $e) {
                        $errors[$locale][$domain][$id] = $e;
                    }
                }
            }
        }

        if (!$domainsByLocales) {
            $this->io->error('No translation files were found.');

            return Command::SUCCESS;
        }

        $this->io->table(
            ['Locale', 'Domains', 'Valid?'],
            array_map(
                static fn (string $locale, array $domains) => [
                    $locale,
                    implode(', ', $domains),
                    !\array_key_exists($locale, $errors) ? '<info>Yes</>' : '<error>No</>',
                ],
                array_keys($domainsByLocales),
                $domainsByLocales
            ),
        );

        if ($errors) {
            foreach ($errors as $locale => $domains) {
                foreach ($domains as $domain => $domainsErrors) {
                    $this->io->section(\sprintf('Errors for locale "%s" and domain "%s"', $locale, $domain));

                    foreach ($domainsErrors as $id => $error) {
                        $this->io->text(\sprintf('Translation key "%s" is invalid:', $id));
                        $this->io->error($error->getMessage());
                    }
                }
            }

            return Command::FAILURE;
        }

        $this->io->success('All translations are valid.');

        return Command::SUCCESS;
    }
}
