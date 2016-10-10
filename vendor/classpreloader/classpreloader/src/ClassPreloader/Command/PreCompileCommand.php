<?php

namespace ClassPreloader\Command;

use ClassPreloader\Config;
use ClassPreloader\Parser\DirVisitor;
use ClassPreloader\Parser\NodeTraverser;
use ClassPreloader\Parser\FileVisitor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class PreCompileCommand extends Command
{
    protected $input;
    protected $output;
    protected $printer;
    protected $traverser;
    protected $parser;

    public function __construct()
    {
        parent::__construct();
        $this->printer = new \PHPParser_PrettyPrinter_Zend();
        $this->parser = new \PHPParser_Parser(new \PHPParser_Lexer());
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('compile')
            ->setDescription('Compiles classes into a single file')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'CSV of filenames to load, or the path to a PHP script that returns an array of file names')
            ->addOption('output', null, InputOption::VALUE_REQUIRED)
            ->addOption('fix_dir', null, InputOption::VALUE_REQUIRED, 'Convert __DIR__ constants to the original directory of a file', 1)
            ->addOption('fix_file', null, InputOption::VALUE_REQUIRED, 'Convert __FILE__ constants to the original path of a file', 1)
            ->addOption('strip_comments', null, InputOption::VALUE_REQUIRED, 'Set to 1 to strip comments from each source file', 0)
            ->setHelp(<<<EOF
The <info>%command.name%</info> command iterates over each script, normalizes
the file to be wrapped in namespaces, and combines each file into a single PHP
file.
EOF
        );
    }

    /**
     * Get the node traverser used by the command
     *
     * @return NodeTraverser
     */
    protected function getTraverser()
    {
        if (!$this->traverser) {
            $this->traverser = new NodeTraverser();
            if ($this->input->getOption('fix_dir')) {
                $this->traverser->addVisitor(new DirVisitor());
            }
            if ($this->input->getOption('fix_file')) {
                $this->traverser->addVisitor(new FileVisitor());
            }
        }

        return $this->traverser;
    }

    /**
     * Get a pretty printed string of code from a file while applying visitors
     *
     * @param string $file Name of the file to get code from
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getCode($file)
    {
        if (!is_readable($file)) {
            throw new \RuntimeException("Cannot open {$file} for reading");
        }

        if ($this->input->getOption('strip_comments')) {
            $content = php_strip_whitespace($file);
        } else {
            $content = file_get_contents($file);
        }

        $stmts = $this->getTraverser()
            ->traverseFile($this->parser->parse($content), $file);
        $pretty = $this->printer->prettyPrint($stmts);

        // Remove the open PHP tag
        if (substr($pretty, 6) == "<?php\n") {
            $pretty = substr($pretty, 7);
        }

        // Add a wrapping namespace if needed
        if (false === strpos($pretty, 'namespace ')) {
            $pretty = "namespace {\n" . $pretty . "\n}\n";
        }

        return $pretty;
    }

    /**
     * Validate the command options
     */
    protected function validateCommand()
    {
        if (!$this->input->getOption('output')) {
            throw new \InvalidArgumentException('An output option is required');
        }

        if (!$this->input->getOption('config')) {
            throw new \InvalidArgumentException('A config option is required');
        }
    }

    /**
     * Get a list of files in order
     *
     * @param mixed $config Configuration option
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getFileList($config)
    {
        $this->output->writeln('> Loading configuration file');
        $filesystem = new Filesystem();

        if (strpos($config, ',')) {
            return array_filter(explode(',', $config));
        }

        // Ensure absolute paths are resolved
        if (!$filesystem->isAbsolutePath($config)) {
            $config = getcwd() . '/' . $config;
        }

        // Ensure that the config file exists
        if (!file_exists($config)) {
            throw new \InvalidArgumentException(sprintf('Configuration file "%s" does not exist.', $config));
        }

        $result = require $config;

        if ($result instanceof Config) {
            foreach ($result->getVisitors() as $visitor) {
                $this->getTraverser()->addVisitor($visitor);
            }

            return $result;
        } elseif (is_array($result)) {
            return $result;
        }

        throw new \InvalidArgumentException(
            'Config must return an array of filenames or a Config object'
        );
    }

    /**
     * Prepare the output file and directory
     *
     * @param string $outputFile The full path to the output file
     *
     * @throws \RuntimeException
     */
    protected function prepareOutput($outputFile)
    {
        $dir = dirname($outputFile);
        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
            throw new \RuntimeException('Unable to create directory ' . $dir);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->validateCommand();
        $outputFile = $this->input->getOption('output');
        $config = $this->input->getOption('config');
        $files = $this->getFileList($config);
        $output->writeLn('- Found ' . count($files) . ' files');

        // Make sure that the output dir can be used or create it
        $this->prepareOutput($outputFile);

        if (!$handle = fopen($input->getOption('output'), 'w')) {
            throw new \RuntimeException(
                "Unable to open {$outputFile} for writing"
            );
        }

        // Write the first line of the output
        fwrite($handle, "<?php\n");
        $output->writeln('> Compiling classes');
        foreach ($files as $file) {
            $this->output->writeln('- Writing ' . $file);
            fwrite($handle, $this->getCode($file) . "\n");
        }
        fclose($handle);

        $output->writeln("> Compiled loader written to {$outputFile}");
        $output->writeln('- ' . (round(filesize($outputFile) / 1024)) . ' kb');
    }
}
