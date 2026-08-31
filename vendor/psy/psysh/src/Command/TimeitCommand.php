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
use Psy\Command\TimeitCommand\TimeitVisitor;
use Psy\Input\CodeArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TimeitCommand.
 */
class TimeitCommand extends Command
{
    const RESULT_MSG = '<info>Command took %.6f seconds to complete.</info>';
    const AVG_RESULT_MSG = '<info>Command took %.6f seconds on average (%.6f median; %.6f total) to complete.</info>';

    // All times stored as nanoseconds (int on 64-bit, float on 32-bit overflow)
    /** @var int|float|null */
    private static $start = null;
    private static array $times = [];

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
        $this->traverser->addVisitor(new TimeitVisitor());

        $this->printer = new Printer();

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('timeit')
            ->setDefinition([
                new InputOption('num', 'n', InputOption::VALUE_REQUIRED, 'Number of iterations.'),
                new CodeArgument('code', CodeArgument::REQUIRED, 'Code to execute.'),
            ])
            ->setDescription('Profiles with a timer.')
            ->setHelp(
                <<<'HELP'
Time profiling for functions and commands.

e.g.
<return>>>> timeit sleep(1)</return>
<return>>>> timeit -n1000 $closure()</return>
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
        $num = (int) ($input->getOption('num') ?: 1);

        $shell = $this->getShell();

        $instrumentedCode = $this->instrumentCode($code);

        self::$times = [];

        do {
            $_ = $shell->execute($instrumentedCode, true);
            $this->ensureEndMarked();
        } while (\count(self::$times) < $num);

        $shell->writeReturnValue($_);

        $times = self::$times;
        self::$times = [];

        if ($num === 1) {
            // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible (guaranteed by loop: count($times) >= $num)
            $output->writeln(\sprintf(self::RESULT_MSG, $times[0] / 1e+9));
        } else {
            $total = \array_sum($times);
            \rsort($times);
            // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible (guaranteed by loop: count($times) >= $num)
            $median = $times[\intdiv($num, 2)];

            $output->writeln(\sprintf(self::AVG_RESULT_MSG, ($total / $num) / 1e+9, $median / 1e+9, $total / 1e+9));
        }

        return 0;
    }

    /**
     * Internal method for marking the start of timeit execution.
     *
     * A static call to this method will be injected at the start of the timeit
     * input code to instrument the call. We will use the saved start time to
     * more accurately calculate time elapsed during execution.
     */
    public static function markStart()
    {
        self::$start = \hrtime(true);
    }

    /**
     * Internal method for marking the end of timeit execution.
     *
     * A static call to this method is injected by TimeitVisitor at the end
     * of the timeit input code to instrument the call.
     *
     * Note that this accepts an optional $ret parameter, which is used to pass
     * the return value of the last statement back out of timeit. This saves us
     * a bunch of code rewriting shenanigans.
     *
     * @param mixed $ret
     *
     * @return mixed it just passes $ret right back
     */
    public static function markEnd($ret = null)
    {
        self::$times[] = \hrtime(true) - self::$start;
        self::$start = null;

        return $ret;
    }

    /**
     * Ensure that the end of code execution was marked.
     *
     * The end *should* be marked in the instrumented code, but just in case
     * we'll add a fallback here.
     */
    private function ensureEndMarked()
    {
        if (self::$start !== null) {
            self::markEnd();
        }
    }

    /**
     * Instrument code for timeit execution.
     *
     * This inserts `markStart` and `markEnd` calls to ensure that (reasonably)
     * accurate times are recorded for just the code being executed.
     */
    private function instrumentCode(string $code): string
    {
        return $this->printer->prettyPrint($this->traverser->traverse($this->parser->parse($code)));
    }
}
