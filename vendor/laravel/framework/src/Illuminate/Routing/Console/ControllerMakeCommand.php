<?php

namespace Illuminate\Routing\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;

#[AsCommand(name: 'make:controller')]
class ControllerMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = null;

        if ($type = $this->option('type')) {
            $stub = "/stubs/controller.{$type}.stub";
        } elseif ($this->option('parent')) {
            $stub = $this->option('singleton')
                ? '/stubs/controller.nested.singleton.stub'
                : '/stubs/controller.nested.stub';
        } elseif ($this->option('model')) {
            $stub = '/stubs/controller.model.stub';
        } elseif ($this->option('invokable')) {
            $stub = '/stubs/controller.invokable.stub';
        } elseif ($this->option('singleton')) {
            $stub = '/stubs/controller.singleton.stub';
        } elseif ($this->option('resource')) {
            $stub = '/stubs/controller.stub';
        }

        if ($this->option('api') && is_null($stub)) {
            $stub = '/stubs/controller.api.stub';
        } elseif ($this->option('api') && ! is_null($stub) && ! $this->option('invokable')) {
            $stub = str_replace('.stub', '.api.stub', $stub);
        }

        $stub ??= '/stubs/controller.plain.stub';

        return $this->resolveStubPath($stub);
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
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in the base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $rootNamespace = $this->rootNamespace();
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('parent')) {
            $replace = $this->buildParentReplacements();
        }

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        if ($this->option('creatable')) {
            $replace['abort(404);'] = '//';
        }

        $baseControllerExists = file_exists($this->getPath("{$rootNamespace}Http\Controllers\Controller"));

        if ($baseControllerExists) {
            $replace["use {$controllerNamespace}\Controller;\n"] = '';
        } else {
            $replace[' extends Controller'] = '';
            $replace["use {$rootNamespace}Http\Controllers\Controller;\n"] = '';
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the replacements for a parent controller.
     *
     * @return array
     */
    protected function buildParentReplacements()
    {
        $parentModelClass = $this->parseModel($this->option('parent'));

        if (! class_exists($parentModelClass) &&
            confirm("A {$parentModelClass} model does not exist. Do you want to generate it?", default: true)) {
            $this->call('make:model', ['name' => $parentModelClass]);
        }

        return [
            'ParentDummyFullModelClass' => $parentModelClass,
            '{{ namespacedParentModel }}' => $parentModelClass,
            '{{namespacedParentModel}}' => $parentModelClass,
            'ParentDummyModelClass' => class_basename($parentModelClass),
            '{{ parentModel }}' => class_basename($parentModelClass),
            '{{parentModel}}' => class_basename($parentModelClass),
            'ParentDummyModelVariable' => lcfirst(class_basename($parentModelClass)),
            '{{ parentModelVariable }}' => lcfirst(class_basename($parentModelClass)),
            '{{parentModelVariable}}' => lcfirst(class_basename($parentModelClass)),
        ];
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass) && confirm("A {$modelClass} model does not exist. Do you want to generate it?", default: true)) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        $replace = $this->buildFormRequestReplacements($replace, $modelClass);

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('/[^A-Za-z0-9_\/\\\\]/', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @param  string  $modelClass
     * @return array
     */
    protected function buildFormRequestReplacements(array $replace, $modelClass)
    {
        [$namespace, $storeRequestClass, $updateRequestClass] = [
            'Illuminate\\Http', 'Request', 'Request',
        ];

        if ($this->option('requests')) {
            $namespace = 'App\\Http\\Requests';

            [$storeRequestClass, $updateRequestClass] = $this->generateFormRequests(
                $modelClass, $storeRequestClass, $updateRequestClass
            );
        }

        $namespacedRequests = $namespace.'\\'.$storeRequestClass.';';

        if ($storeRequestClass !== $updateRequestClass) {
            $namespacedRequests .= PHP_EOL.'use '.$namespace.'\\'.$updateRequestClass.';';
        }

        return array_merge($replace, [
            '{{ storeRequest }}' => $storeRequestClass,
            '{{storeRequest}}' => $storeRequestClass,
            '{{ updateRequest }}' => $updateRequestClass,
            '{{updateRequest}}' => $updateRequestClass,
            '{{ namespacedStoreRequest }}' => $namespace.'\\'.$storeRequestClass,
            '{{namespacedStoreRequest}}' => $namespace.'\\'.$storeRequestClass,
            '{{ namespacedUpdateRequest }}' => $namespace.'\\'.$updateRequestClass,
            '{{namespacedUpdateRequest}}' => $namespace.'\\'.$updateRequestClass,
            '{{ namespacedRequests }}' => $namespacedRequests,
            '{{namespacedRequests}}' => $namespacedRequests,
        ]);
    }

    /**
     * Generate the form requests for the given model and classes.
     *
     * @param  string  $modelClass
     * @param  string  $storeRequestClass
     * @param  string  $updateRequestClass
     * @return array
     */
    protected function generateFormRequests($modelClass, $storeRequestClass, $updateRequestClass)
    {
        $storeRequestClass = 'Store'.class_basename($modelClass).'Request';

        $this->call('make:request', [
            'name' => $storeRequestClass,
        ]);

        $updateRequestClass = 'Update'.class_basename($modelClass).'Request';

        $this->call('make:request', [
            'name' => $updateRequestClass,
        ]);

        return [$storeRequestClass, $updateRequestClass];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['api', null, InputOption::VALUE_NONE, 'Exclude the create and edit methods from the controller'],
            ['type', null, InputOption::VALUE_REQUIRED, 'Manually specify the controller stub file to use'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the controller already exists'],
            ['invokable', 'i', InputOption::VALUE_NONE, 'Generate a single method, invokable controller class'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model'],
            ['parent', 'p', InputOption::VALUE_OPTIONAL, 'Generate a nested resource controller class'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller class'],
            ['requests', 'R', InputOption::VALUE_NONE, 'Generate FormRequest classes for store and update'],
            ['singleton', 's', InputOption::VALUE_NONE, 'Generate a singleton resource controller class'],
            ['creatable', null, InputOption::VALUE_NONE, 'Indicate that a singleton resource should be creatable'],
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

        $type = select('Which type of controller would you like?', [
            'empty' => 'Empty',
            'resource' => 'Resource',
            'singleton' => 'Singleton',
            'api' => 'API',
            'invokable' => 'Invokable',
        ]);

        if ($type !== 'empty') {
            $input->setOption($type, true);
        }

        if (in_array($type, ['api', 'resource', 'singleton'])) {
            $model = suggest(
                "What model is this $type controller for? (Optional)",
                $this->findAvailableModels()
            );

            if ($model) {
                $input->setOption('model', $model);
            }
        }
    }
}
