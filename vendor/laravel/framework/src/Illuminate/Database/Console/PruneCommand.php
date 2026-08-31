<?php

namespace Illuminate\Database\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\ModelPruningFinished;
use Illuminate\Database\Events\ModelPruningStarting;
use Illuminate\Database\Events\ModelsPruned;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'model:prune')]
class PruneCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'model:prune
                                {--model=* : Class names of the models to be pruned}
                                {--except=* : Class names of the models to be excluded from pruning}
                                {--path=* : Absolute path(s) to directories where models are located}
                                {--chunk=1000 : The number of models to retrieve per chunk of models to be deleted}
                                {--pretend : Display the number of prunable records found instead of deleting them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune models that are no longer needed';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function handle(Dispatcher $events)
    {
        $models = $this->models();

        if ($models->isEmpty()) {
            $this->components->info('No prunable models found.');

            return;
        }

        if ($this->option('pretend')) {
            $models->each(function ($model) {
                $this->pretendToPrune($model);
            });

            return;
        }

        $pruning = [];

        $events->listen(ModelsPruned::class, function ($event) use (&$pruning) {
            if (! in_array($event->model, $pruning)) {
                $pruning[] = $event->model;

                $this->newLine();

                $this->components->info(sprintf('Pruning [%s] records.', $event->model));
            }

            $this->components->twoColumnDetail($event->model, "{$event->count} records");
        });

        $events->dispatch(new ModelPruningStarting($models->all()));

        $models->each(function ($model) {
            $this->pruneModel($model);
        });

        $events->dispatch(new ModelPruningFinished($models->all()));

        $events->forget(ModelsPruned::class);
    }

    /**
     * Prune the given model.
     *
     * @param  string  $model
     * @return void
     */
    protected function pruneModel(string $model)
    {
        $instance = new $model;

        $chunkSize = property_exists($instance, 'prunableChunkSize')
            ? $instance->prunableChunkSize
            : $this->option('chunk');

        $total = $model::isPrunable()
            ? $instance->pruneAll($chunkSize)
            : 0;

        if ($total == 0) {
            $this->components->info("No prunable [$model] records found.");
        }
    }

    /**
     * Determine the models that should be pruned.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function models()
    {
        $models = $this->option('model');
        $except = $this->option('except');

        if ($models && $except) {
            throw new InvalidArgumentException('The --models and --except options cannot be combined.');
        }

        if ($models) {
            return (new Collection($models))
                ->filter(static fn (string $model) => class_exists($model))
                ->values();
        }

        return (new Collection(Finder::create()->in($this->getPath())->files()->name('*.php')))
            ->map(function ($model) {
                $namespace = $this->laravel->getNamespace();

                return $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($model->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
                );
            })
            ->when(! empty($except), fn ($models) => $models->reject(fn ($model) => in_array($model, $except)))
            ->filter(fn ($model) => $this->isPrunable($model))
            ->values();
    }

    /**
     * Get the path where models are located.
     *
     * @return string[]|string
     */
    protected function getPath()
    {
        if (! empty($path = $this->option('path'))) {
            return (new Collection($path))
                ->map(fn ($path) => base_path($path))
                ->all();
        }

        return app_path('Models');
    }

    /**
     * Display how many models will be pruned.
     *
     * @param  class-string  $model
     * @return void
     */
    protected function pretendToPrune($model)
    {
        $instance = new $model;

        $count = $instance->prunable()
            ->when($model::isSoftDeletable(), function ($query) {
                $query->withTrashed();
            })->count();

        if ($count === 0) {
            $this->components->info("No prunable [$model] records found.");
        } else {
            $this->components->info("{$count} [{$model}] records will be pruned.");
        }
    }

    /**
     * Determine if the given model is prunable.
     *
     * @param  string  $model
     * @return bool
     */
    protected function isPrunable(string $model)
    {
        return class_exists($model)
            && is_a($model, Model::class, true)
            && ! (new \ReflectionClass($model))->isAbstract()
            && $model::isPrunable();
    }
}
