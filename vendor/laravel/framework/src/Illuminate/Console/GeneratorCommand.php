<?php

namespace Illuminate\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\Concerns\FindsAvailableModels;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

abstract class GeneratorCommand extends Command implements PromptsForMissingInput
{
    use FindsAvailableModels;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * Reserved names that cannot be used for generation.
     *
     * @var string[]
     */
    protected $reservedNames = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'enum',
        'eval',
        'exit',
        'extends',
        'false',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'match',
        'namespace',
        'new',
        'or',
        'parent',
        'print',
        'private',
        'protected',
        'public',
        'readonly',
        'require',
        'require_once',
        'return',
        'self',
        'static',
        'switch',
        'throw',
        'trait',
        'true',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
        '__CLASS__',
        '__DIR__',
        '__FILE__',
        '__FUNCTION__',
        '__LINE__',
        '__METHOD__',
        '__NAMESPACE__',
        '__TRAIT__',
    ];

    /**
     * Create a new generator command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
            $this->addTestOptions();
        }

        $this->files = $files;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub();

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->components->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

            return false;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
             ! $this->option('force')) &&
             $this->alreadyExists($this->getNameInput())) {
            $this->components->error($this->type.' already exists.');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $info = $this->type;

        if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
            $this->handleTestCreation($path);
        }

        if (windows_os()) {
            $path = str_replace('/', '\\', $path);
        }

        $this->components->info(sprintf('%s [%s] created successfully.', $info, $path));
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Qualify the given model class base name.
     *
     * @return class-string
     */
    protected function qualifyModel(string $model)
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace.'Models\\'.$model
            : $rootNamespace.$model;
    }

    /**
     * Get a list of possible model names.
     *
     * @return array<int, string>
     *
     * @deprecated 12.38.0 Use `findAvailableModels()` method instead.
     */
    protected function possibleModels()
    {
        return $this->findAvailableModels();
    }

    /**
     * Get a list of possible event names.
     *
     * @return array<int, string>
     */
    protected function possibleEvents()
    {
        $eventPath = app_path('Events');

        if (! is_dir($eventPath)) {
            return [];
        }

        return (new Collection(Finder::create()->files()->depth(0)->in($eventPath)))
            ->map(fn ($file) => $file->getBasename('.php'))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
                $stub
            );
        }

        return $this;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }

    /**
     * Alphabetically sorts the imports for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function sortImports($stub)
    {
        if (preg_match('/(?P<imports>(?:^use [^;{]+;$\n?)+)/m', $stub, $match)) {
            $imports = explode("\n", trim($match['imports']));

            sort($imports);

            return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
        }

        return $stub;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name'));

        if (Str::endsWith($name, '.php')) {
            return Str::substr($name, 0, -4);
        }

        return $name;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->laravel->getNamespace();
    }

    /**
     * Get the model for the default guard's user provider.
     *
     * @return string|null
     */
    protected function userProviderModel()
    {
        $config = $this->laravel['config'];

        $provider = $config->get('auth.guards.'.$config->get('auth.defaults.guard').'.provider');

        return $config->get("auth.providers.{$provider}.model");
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isReservedName($name)
    {
        return in_array(
            strtolower($name),
            (new Collection($this->reservedNames))
                ->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Get the first view directory path from the application configuration.
     *
     * @param  string  $path
     * @return string
     */
    protected function viewPath($path = '')
    {
        $views = $this->laravel['config']['view.paths'][0] ?? resource_path('views');

        return $views.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the console command arguments.
     *
     * @return (InputArgument|array{
     *    0: non-empty-string,
     *    1?: InputArgument::REQUIRED|InputArgument::OPTIONAL|InputArgument::IS_ARRAY,
     *    2?: string,
     *    3?: mixed,
     *    4?: list<string|Suggestion>|\Closure(CompletionInput, CompletionSuggestions): list<string|Suggestion>
     * })[]
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the '.strtolower($this->type)],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string|array{string, string}|\Closure(): (array<int, string>|string|int|bool)>
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                'What should the '.strtolower($this->type).' be named?',
                match ($this->type) {
                    'Cast' => 'E.g. Json',
                    'Channel' => 'E.g. OrderChannel',
                    'Console command' => 'E.g. SendEmails',
                    'Component' => 'E.g. Alert',
                    'Controller' => 'E.g. UserController',
                    'Event' => 'E.g. PodcastProcessed',
                    'Exception' => 'E.g. InvalidOrderException',
                    'Factory' => 'E.g. PostFactory',
                    'Job' => 'E.g. ProcessPodcast',
                    'Listener' => 'E.g. SendPodcastNotification',
                    'Mailable' => 'E.g. OrderShipped',
                    'Middleware' => 'E.g. EnsureTokenIsValid',
                    'Model' => 'E.g. Flight',
                    'Notification' => 'E.g. InvoicePaid',
                    'Observer' => 'E.g. UserObserver',
                    'Policy' => 'E.g. PostPolicy',
                    'Provider' => 'E.g. ElasticServiceProvider',
                    'Request' => 'E.g. StorePodcastRequest',
                    'Resource' => 'E.g. UserResource',
                    'Rule' => 'E.g. Uppercase',
                    'Scope' => 'E.g. TrendingScope',
                    'Seeder' => 'E.g. UserSeeder',
                    'Test' => 'E.g. UserTest',
                    default => '',
                },
            ],
        ];
    }
}
