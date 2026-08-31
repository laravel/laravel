<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command;

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard as Printer;
use Psy\Input\CodeArgument;
use Psy\Readline\Readline;
use Psy\Readline\ReadlineAware;
use Psy\Sudo\SudoVisitor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Evaluate PHP code, bypassing visibility restrictions.
 */
class SudoCommand extends Command implements ReadlineAware
{
    private Readline $readline;
    private CodeArgumentParser $parser;
    private NodeTraverser $traverser;
    private Printer $printer;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null)
    {
        $this->parser = new CodeArgumentParser();

        // @todo Pass visitor directly to once we drop support for PHP-Parser 4.x
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new SudoVisitor());

        $this->printer = new Printer();

        parent::__construct($name);
    }

    /**
     * Set the Shell's Readline service.
     *
     * @param Readline $readline
     */
    public function setReadline(Readline $readline)
    {
        $this->readline = $readline;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sudo')
            ->setDefinition([
                new CodeArgument('code', CodeArgument::REQUIRED, 'Code to execute.'),
            ])
            ->setDescription('Evaluate PHP code, bypassing visibility restrictions.')
            ->setHelp(
                <<<'HELP'
Evaluate PHP code, bypassing visibility restrictions.

e.g.
<return>>>> $sekret->whisper("hi")</return>
<return>PHP error:  Call to private method Sekret::whisper() from context '' on line 1</return>

<return>>>> sudo $sekret->whisper("hi")</return>
<return>=> "hi"</return>

<return>>>> $sekret->word</return>
<return>PHP error:  Cannot access private property Sekret::$word on line 1</return>

<return>>>> sudo $sekret->word</return>
<return>=> "hi"</return>

<return>>>> $sekret->word = "please"</return>
<return>PHP error:  Cannot access private property Sekret::$word on line 1</return>

<return>>>> sudo $sekret->word = "please"</return>
<return>=> "please"</return>
HELP
            );
    }

    /**
     * {@inheritdoc}
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $code = $input->getArgument('code');

        // special case for !!
        if ($code === '!!') {
            $history = $this->readline->listHistory();
            if (\count($history) < 2) {
                throw new \InvalidArgumentException('No previous command to replay');
            }
            $code = $history[\count($history) - 2];
        }

        $nodes = $this->traverser->traverse($this->parser->parse($code));

        $sudoCode = $this->printer->prettyPrint($nodes);

        $shell = $this->getShell();
        $shell->addCode($sudoCode, !$shell->hasCode());

        return 0;
    }
}
