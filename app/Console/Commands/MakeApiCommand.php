<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeApiCommand extends Command
{
    protected $signature = 'make:api
        {name : The API resource name}
        {--controller : Generate an API controller}
        {--service : Generate a service class}
        {--request : Generate a form request validator}
        {--resource : Generate an API resource}
        {--test : Generate a feature test}
        {--dto : Generate a DTO scaffold}
        {--all : Generate the standard API stack}
        {--routes : Register an API resource route}
        {--api-version=v1 : API route version prefix}
        {--force : Overwrite existing files}';

    protected $description = 'Generate a standardized API stack for an API-only Laravel application.';

    private const STANDARD_COMPONENTS = [
        'controller',
        'service',
        'request',
        'resource',
        'test',
    ];

    private const COMPONENTS = [
        ...self::STANDARD_COMPONENTS,
        'dto',
    ];

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $components = $this->selectedComponents();

        if ($components === []) {
            $this->components->warn('No components selected. Use --all or choose one or more component flags.');

            return self::FAILURE;
        }

        $context = $this->buildContext((string) $this->argument('name'));
        $context = $this->withComponentAvailability($context, $components);
        $created = 0;

        foreach ($components as $component) {
            $created += $this->generateComponent($component, $context) ? 1 : 0;
        }

        if ($this->option('routes')) {
            $this->registerRoute($context);
        }

        $this->components->info("API scaffold for [{$context['class']}] complete. {$created} file(s) generated.");

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $context
     * @param  array<int, string>  $components
     * @return array<string, string|bool>
     */
    private function withComponentAvailability(array $context, array $components): array
    {
        foreach (self::COMPONENTS as $component) {
            $context['has'.Str::studly($component)] = in_array($component, $components, true)
                || $this->files->exists($context["{$component}Path"]);
        }

        return $context;
    }

    /**
     * @return array<int, string>
     */
    private function selectedComponents(): array
    {
        $components = $this->option('all')
            ? self::STANDARD_COMPONENTS
            : collect(self::COMPONENTS)
                ->filter(fn (string $component): bool => (bool) $this->option($component))
                ->values()
                ->all();

        if ($this->option('dto') && ! in_array('dto', $components, true)) {
            $components[] = 'dto';
        }

        return $components;
    }

    /**
     * @return array<string, string>
     */
    private function buildContext(string $name): array
    {
        $segments = collect(preg_split('/[\/\\\\]+/', trim($name, " \t\n\r\0\x0B/\\") ?: $name))
            ->filter()
            ->map(fn (string $segment): string => Str::studly($segment))
            ->values();

        $class = $segments->pop() ?: Str::studly($name);
        $subNamespace = $segments->implode('\\');
        $subPath = $segments->implode(DIRECTORY_SEPARATOR);
        $namespaceSuffix = $subNamespace === '' ? '' : '\\'.$subNamespace;
        $pathSuffix = $subPath === '' ? '' : DIRECTORY_SEPARATOR.$subPath;
        $routeName = Str::kebab(Str::pluralStudly($class));
        $version = trim((string) $this->option('api-version'), '/');

        return [
            'class' => $class,
            'model' => $class,
            'modelVariable' => Str::camel($class),
            'modelPluralVariable' => Str::camel(Str::pluralStudly($class)),
            'route' => $routeName,
            'routeParameter' => Str::camel($class),
            'version' => $version,
            'versionPrefix' => $version === '' ? '' : $version.'/',
            'controller' => "{$class}Controller",
            'controllerNamespace' => "App\\Http\\Controllers\\Api{$namespaceSuffix}",
            'controllerPath' => app_path("Http/Controllers/Api{$pathSuffix}/{$class}Controller.php"),
            'service' => "{$class}Service",
            'serviceNamespace' => "App\\Services{$namespaceSuffix}",
            'servicePath' => app_path("Services{$pathSuffix}/{$class}Service.php"),
            'request' => "{$class}Request",
            'requestNamespace' => "App\\Http\\Requests{$namespaceSuffix}",
            'requestPath' => app_path("Http/Requests{$pathSuffix}/{$class}Request.php"),
            'resource' => "{$class}Resource",
            'resourceNamespace' => "App\\Http\\Resources{$namespaceSuffix}",
            'resourcePath' => app_path("Http/Resources{$pathSuffix}/{$class}Resource.php"),
            'test' => "{$class}ApiTest",
            'testNamespace' => "Tests\\Feature{$namespaceSuffix}",
            'testPath' => base_path("tests/Feature{$pathSuffix}/{$class}ApiTest.php"),
            'dto' => "{$class}Data",
            'dtoNamespace' => "App\\DTOs{$namespaceSuffix}",
            'dtoPath' => app_path("DTOs{$pathSuffix}/{$class}Data.php"),
            'namespaceSuffix' => $namespaceSuffix,
        ];
    }

    /**
     * @param  array<string, string>  $context
     */
    private function generateComponent(string $component, array $context): bool
    {
        $stub = base_path("stubs/api/{$component}.stub");
        $path = $context["{$component}Path"];

        if (! $this->files->exists($stub)) {
            $this->components->error("Stub [{$stub}] does not exist.");

            return false;
        }

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->components->warn("{$context[$component]} already exists. Use --force to overwrite.");

            return false;
        }

        $this->ensureDirectoryExists(dirname($path));

        $this->files->put($path, $this->replaceStubPlaceholders(
            $this->files->get($stub),
            $component,
            $context,
        ));

        $this->components->info("Created {$component}: {$path}");

        return true;
    }

    /**
     * @param  array<string, string>  $context
     */
    private function replaceStubPlaceholders(string $stub, string $component, array $context): string
    {
        $replacements = [
            '{{ class }}' => $context[$component],
            '{{ namespace }}' => $context["{$component}Namespace"],
            '{{ rootNamespace }}' => app()->getNamespace(),
            '{{ model }}' => $context['model'],
            '{{ modelVariable }}' => $context['modelVariable'],
            '{{ modelPluralVariable }}' => $context['modelPluralVariable'],
            '{{ request }}' => $context['request'],
            '{{ requestNamespace }}' => $context['requestNamespace'],
            '{{ resource }}' => $context['resource'],
            '{{ resourceNamespace }}' => $context['resourceNamespace'],
            '{{ service }}' => $context['service'],
            '{{ serviceNamespace }}' => $context['serviceNamespace'],
            '{{ controller }}' => $context['controller'],
            '{{ controllerNamespace }}' => $context['controllerNamespace'],
            '{{ route }}' => $context['route'],
            '{{ routeParameter }}' => $context['routeParameter'],
            '{{ version }}' => $context['version'],
            '{{ versionPrefix }}' => $context['versionPrefix'],
            '{{ dto }}' => $context['dto'],
            '{{ dtoNamespace }}' => $context['dtoNamespace'],
            '{{ controllerImports }}' => $this->controllerImports($context),
            '{{ controllerConstructor }}' => $this->controllerConstructor($context),
            '{{ controllerMethods }}' => $this->controllerMethods($context),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerImports(array $context): string
    {
        $imports = [
            'use App\Http\Controllers\Controller;',
            "use App\Models\\{$context['model']};",
        ];

        if ($context['hasRequest']) {
            $imports[] = "use {$context['requestNamespace']}\\{$context['request']};";
        } else {
            $imports[] = 'use Illuminate\Http\Request;';
        }

        if ($context['hasResource']) {
            $imports[] = "use {$context['resourceNamespace']}\\{$context['resource']};";
            $imports[] = 'use Illuminate\Http\Resources\Json\AnonymousResourceCollection;';
        }

        if ($context['hasService']) {
            $imports[] = "use {$context['serviceNamespace']}\\{$context['service']};";
        }

        $imports[] = 'use Illuminate\Http\JsonResponse;';

        if ($context['hasService']) {
            $imports[] = 'use Illuminate\Http\Response;';
        }

        sort($imports);

        return implode("\n", $imports);
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerConstructor(array $context): string
    {
        if (! $context['hasService']) {
            return '';
        }

        return <<<PHP
            public function __construct(private readonly {$context['service']} \${$context['modelVariable']}Service)
            {
            }

        PHP."\n";
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerMethods(array $context): string
    {
        return implode("\n\n", [
            $this->controllerIndexMethod($context),
            $this->controllerStoreMethod($context),
            $this->controllerShowMethod($context),
            $this->controllerUpdateMethod($context),
            $this->controllerDestroyMethod($context),
        ]);
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerIndexMethod(array $context): string
    {
        if ($context['hasService'] && $context['hasResource']) {
            return <<<PHP
                public function index(): AnonymousResourceCollection
                {
                    return {$context['resource']}::collection(\$this->{$context['modelVariable']}Service->list());
                }
            PHP;
        }

        if ($context['hasService']) {
            return <<<PHP
                public function index(): JsonResponse
                {
                    return response()->json([
                        'data' => \$this->{$context['modelVariable']}Service->list(),
                    ]);
                }
            PHP;
        }

        return <<<PHP
            public function index(): JsonResponse
            {
                return response()->json([
                    'message' => '{$context['service']} is required for this action.',
                ], 501);
            }
        PHP;
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerStoreMethod(array $context): string
    {
        $requestClass = $context['hasRequest'] ? $context['request'] : 'Request';
        $payload = $context['hasRequest'] ? '$request->validated()' : '$request->all()';

        if ($context['hasService'] && $context['hasResource']) {
            return <<<PHP
                public function store({$requestClass} \$request): {$context['resource']}
                {
                    \${$context['modelVariable']} = \$this->{$context['modelVariable']}Service->create({$payload});

                    return new {$context['resource']}(\${$context['modelVariable']});
                }
            PHP;
        }

        if ($context['hasService']) {
            return <<<PHP
                public function store({$requestClass} \$request): JsonResponse
                {
                    \${$context['modelVariable']} = \$this->{$context['modelVariable']}Service->create({$payload});

                    return response()->json([
                        'data' => \${$context['modelVariable']},
                    ], 201);
                }
            PHP;
        }

        return <<<PHP
            public function store({$requestClass} \$request): JsonResponse
            {
                return response()->json([
                    'message' => '{$context['service']} is required for this action.',
                ], 501);
            }
        PHP;
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerShowMethod(array $context): string
    {
        if ($context['hasService'] && $context['hasResource']) {
            return <<<PHP
                public function show({$context['model']} \${$context['modelVariable']}): {$context['resource']}
                {
                    return new {$context['resource']}(\$this->{$context['modelVariable']}Service->find(\${$context['modelVariable']}));
                }
            PHP;
        }

        if ($context['hasResource']) {
            return <<<PHP
                public function show({$context['model']} \${$context['modelVariable']}): {$context['resource']}
                {
                    return new {$context['resource']}(\${$context['modelVariable']});
                }
            PHP;
        }

        if ($context['hasService']) {
            return <<<PHP
                public function show({$context['model']} \${$context['modelVariable']}): JsonResponse
                {
                    return response()->json([
                        'data' => \$this->{$context['modelVariable']}Service->find(\${$context['modelVariable']}),
                    ]);
                }
            PHP;
        }

        return <<<PHP
            public function show({$context['model']} \${$context['modelVariable']}): JsonResponse
            {
                return response()->json([
                    'data' => \${$context['modelVariable']},
                ]);
            }
        PHP;
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerUpdateMethod(array $context): string
    {
        $requestClass = $context['hasRequest'] ? $context['request'] : 'Request';
        $payload = $context['hasRequest'] ? '$request->validated()' : '$request->all()';

        if ($context['hasService'] && $context['hasResource']) {
            return <<<PHP
                public function update({$requestClass} \$request, {$context['model']} \${$context['modelVariable']}): {$context['resource']}
                {
                    \${$context['modelVariable']} = \$this->{$context['modelVariable']}Service->update(\${$context['modelVariable']}, {$payload});

                    return new {$context['resource']}(\${$context['modelVariable']});
                }
            PHP;
        }

        if ($context['hasService']) {
            return <<<PHP
                public function update({$requestClass} \$request, {$context['model']} \${$context['modelVariable']}): JsonResponse
                {
                    \${$context['modelVariable']} = \$this->{$context['modelVariable']}Service->update(\${$context['modelVariable']}, {$payload});

                    return response()->json([
                        'data' => \${$context['modelVariable']},
                    ]);
                }
            PHP;
        }

        return <<<PHP
            public function update({$requestClass} \$request, {$context['model']} \${$context['modelVariable']}): JsonResponse
            {
                return response()->json([
                    'message' => '{$context['service']} is required for this action.',
                ], 501);
            }
        PHP;
    }

    /**
     * @param  array<string, string|bool>  $context
     */
    private function controllerDestroyMethod(array $context): string
    {
        if ($context['hasService']) {
            return <<<PHP
                public function destroy({$context['model']} \${$context['modelVariable']}): Response
                {
                    \$this->{$context['modelVariable']}Service->delete(\${$context['modelVariable']});

                    return response()->noContent();
                }
            PHP;
        }

        return <<<PHP
            public function destroy({$context['model']} \${$context['modelVariable']}): JsonResponse
            {
                return response()->json([
                    'message' => '{$context['service']} is required for this action.',
                ], 501);
            }
        PHP;
    }

    /**
     * @param  array<string, string>  $context
     */
    private function registerRoute(array $context): void
    {
        $routesPath = base_path('routes/api.php');
        $controllerClass = "{$context['controllerNamespace']}\\{$context['controller']}";
        $routeStatement = "Route::apiResource('{$context['versionPrefix']}{$context['route']}', {$context['controller']}::class);";

        $contents = $this->files->exists($routesPath) ? $this->files->get($routesPath) : "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n";

        if (! Str::contains($contents, "use {$controllerClass};")) {
            $contents = $this->insertUseStatement($contents, "use {$controllerClass};");
        }

        if (Str::contains($contents, $routeStatement)) {
            $this->components->warn("Route for [{$context['versionPrefix']}{$context['route']}] already exists.");
        } else {
            $contents = rtrim($contents)."\n\n{$routeStatement}\n";
            $this->components->info("Registered route: {$routeStatement}");
        }

        $this->files->put($routesPath, $contents);
    }

    private function insertUseStatement(string $contents, string $useStatement): string
    {
        if (preg_match_all('/^use\s+[^;]+;$/m', $contents, $matches, PREG_OFFSET_CAPTURE) > 0) {
            $lastUse = end($matches[0]);
            $insertAt = $lastUse[1] + strlen($lastUse[0]);

            return substr($contents, 0, $insertAt)."\n{$useStatement}".substr($contents, $insertAt);
        }

        return preg_replace('/^<\?php\s*/', "<?php\n\n{$useStatement}\n", $contents, 1) ?? $contents;
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
}
