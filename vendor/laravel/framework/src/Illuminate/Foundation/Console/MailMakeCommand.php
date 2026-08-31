<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;

#[AsCommand(name: 'make:mail')]
class MailMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new email class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Mailable';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return;
        }

        if ($this->option('markdown') !== false) {
            $this->writeMarkdownTemplate();
        }

        if ($this->option('view') !== false) {
            $this->writeView();
        }
    }

    /**
     * Write the Markdown template for the mailable.
     *
     * @return void
     */
    protected function writeMarkdownTemplate()
    {
        $separator = '/';

        if (windows_os()) {
            $separator = '\\';
        }

        $path = $this->viewPath(
            str_replace('.', $separator, $this->getView()).'.blade.php'
        );

        if ($this->files->exists($path)) {
            return $this->components->error(sprintf('%s [%s] already exists.', 'Markdown view', $path));
        }

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put($path, file_get_contents(__DIR__.'/stubs/markdown.stub'));

        $this->components->info(sprintf('%s [%s] created successfully.', 'Markdown view', $path));
    }

    /**
     * Write the Blade template for the mailable.
     *
     * @return void
     */
    protected function writeView()
    {
        $separator = '/';

        if (windows_os()) {
            $separator = '\\';
        }

        $path = $this->viewPath(
            str_replace('.', $separator, $this->getView()).'.blade.php'
        );

        if ($this->files->exists($path)) {
            return $this->components->error(sprintf('%s [%s] already exists.', 'View', $path));
        }

        $this->files->ensureDirectoryExists(dirname($path));

        $stub = str_replace(
            '{{ quote }}',
            Inspiring::quotes()->random(),
            file_get_contents(__DIR__.'/stubs/view.stub')
        );

        $this->files->put($path, $stub);

        $this->components->info(sprintf('%s [%s] created successfully.', 'View', $path));
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $class = str_replace(
            '{{ subject }}',
            Str::headline(str_replace($this->getNamespace($name).'\\', '', $name)),
            parent::buildClass($name)
        );

        if ($this->option('markdown') !== false || $this->option('view') !== false) {
            $class = str_replace(['DummyView', '{{ view }}'], $this->getView(), $class);
        }

        return $class;
    }

    /**
     * Get the view name.
     *
     * @return string
     */
    protected function getView()
    {
        $view = $this->option('markdown') ?: $this->option('view');

        if (! $view) {
            $name = str_replace('\\', '/', $this->argument('name'));

            $view = 'mail.'.(new Collection(explode('/', $name)))
                ->map(fn ($part) => Str::kebab($part))
                ->implode('.');
        }

        return $view;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('markdown') !== false) {
            return $this->resolveStubPath('/stubs/markdown-mail.stub');
        }

        if ($this->option('view') !== false) {
            return $this->resolveStubPath('/stubs/view-mail.stub');
        }

        return $this->resolveStubPath('/stubs/mail.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Mail';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the mailable already exists'],
            ['markdown', 'm', InputOption::VALUE_OPTIONAL, 'Create a new Markdown template for the mailable', false],
            ['view', null, InputOption::VALUE_OPTIONAL, 'Create a new Blade template for the mailable', false],
        ];
    }

    /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->didReceiveOptions($input)) {
            return;
        }

        $type = select('Would you like to create a view?', [
            'markdown' => 'Markdown View',
            'view' => 'Empty View',
            'none' => 'No View',
        ]);

        if ($type !== 'none') {
            $input->setOption($type, null);
        }
    }
}
