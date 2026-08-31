<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;

/**
 * Dump error pages to plain HTML files that can be directly served by a web server.
 *
 * @author Loïck Piera <pyrech@gmail.com>
 */
#[AsCommand(
    name: 'error:dump',
    description: 'Dump error pages to plain HTML files that can be directly served by a web server',
)]
final class ErrorDumpCommand extends Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly ErrorRendererInterface $errorRenderer,
        private readonly ?EntrypointLookupInterface $entrypointLookup = null,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Path where to dump the error pages in')
            ->addArgument('status-codes', InputArgument::IS_ARRAY, 'Status codes to dump error pages for, all of them by default')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force directory removal before dumping new error pages')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');

        $io = new SymfonyStyle($input, $output);
        $io->title('Dumping error pages');

        $this->dump($io, $path, $input->getArgument('status-codes'), (bool) $input->getOption('force'));
        $io->success(\sprintf('Error pages have been dumped in "%s".', $path));

        return Command::SUCCESS;
    }

    private function dump(SymfonyStyle $io, string $path, array $statusCodes, bool $force = false): void
    {
        if (!$statusCodes) {
            $statusCodes = array_filter(array_keys(Response::$statusTexts), static fn ($statusCode) => $statusCode >= 400);
        }

        if ($force || ($this->filesystem->exists($path) && $io->confirm(\sprintf('The "%s" directory already exists. Do you want to remove it before dumping the error pages?', $path), false))) {
            $this->filesystem->remove($path);
        }

        foreach ($statusCodes as $statusCode) {
            // Avoid assets to be included only on the first dumped page
            $this->entrypointLookup?->reset();

            $this->filesystem->dumpFile($path.\DIRECTORY_SEPARATOR.$statusCode.'.html', $this->errorRenderer->render(new HttpException((int) $statusCode))->getAsString());
        }
    }
}
