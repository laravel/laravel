<?php

namespace Illuminate\Database\Console;

use Illuminate\Console\Concerns\FindsAvailableModels;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelInspector;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\suggest;

#[AsCommand(name: 'model:show')]
class ShowModelCommand extends DatabaseInspectionCommand implements PromptsForMissingInput
{
    use FindsAvailableModels;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'model:show {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show information about an Eloquent model';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'model:show {model : The model to show}
                {--database= : The database connection to use}
                {--json : Output the model as JSON}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ModelInspector $modelInspector)
    {
        try {
            $info = $modelInspector->inspect(
                $this->argument('model'),
                $this->option('database')
            );
        } catch (BindingResolutionException $e) {
            $this->components->error($e->getMessage());

            return 1;
        }

        $this->display(
            $info['class'],
            $info['database'],
            $info['table'],
            $info['policy'],
            $info['attributes'],
            $info['relations'],
            $info['events'],
            $info['observers']
        );

        return 0;
    }

    /**
     * Render the model information.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $class
     * @param  string  $database
     * @param  string  $table
     * @param  class-string|null  $policy
     * @param  \Illuminate\Support\Collection  $attributes
     * @param  \Illuminate\Support\Collection  $relations
     * @param  \Illuminate\Support\Collection  $events
     * @param  \Illuminate\Support\Collection  $observers
     * @return void
     */
    protected function display($class, $database, $table, $policy, $attributes, $relations, $events, $observers)
    {
        $this->option('json')
            ? $this->displayJson($class, $database, $table, $policy, $attributes, $relations, $events, $observers)
            : $this->displayCli($class, $database, $table, $policy, $attributes, $relations, $events, $observers);
    }

    /**
     * Render the model information as JSON.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $class
     * @param  string  $database
     * @param  string  $table
     * @param  class-string|null  $policy
     * @param  \Illuminate\Support\Collection  $attributes
     * @param  \Illuminate\Support\Collection  $relations
     * @param  \Illuminate\Support\Collection  $events
     * @param  \Illuminate\Support\Collection  $observers
     * @return void
     */
    protected function displayJson($class, $database, $table, $policy, $attributes, $relations, $events, $observers)
    {
        $this->output->writeln(
            (new Collection([
                'class' => $class,
                'database' => $database,
                'table' => $table,
                'policy' => $policy,
                'attributes' => $attributes,
                'relations' => $relations,
                'events' => $events,
                'observers' => $observers,
            ]))->toJson()
        );
    }

    /**
     * Render the model information for the CLI.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $class
     * @param  string  $database
     * @param  string  $table
     * @param  class-string|null  $policy
     * @param  \Illuminate\Support\Collection  $attributes
     * @param  \Illuminate\Support\Collection  $relations
     * @param  \Illuminate\Support\Collection  $events
     * @param  \Illuminate\Support\Collection  $observers
     * @return void
     */
    protected function displayCli($class, $database, $table, $policy, $attributes, $relations, $events, $observers)
    {
        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>'.$class.'</>');
        $this->components->twoColumnDetail('Database', $database);
        $this->components->twoColumnDetail('Table', $table);

        if ($policy) {
            $this->components->twoColumnDetail('Policy', $policy);
        }

        $this->newLine();

        $this->components->twoColumnDetail(
            '<fg=green;options=bold>Attributes</>',
            'type <fg=gray>/</> <fg=yellow;options=bold>cast</>',
        );

        foreach ($attributes as $attribute) {
            $first = trim(sprintf(
                '%s %s',
                $attribute['name'],
                (new Collection(['increments', 'unique', 'nullable', 'fillable', 'hidden', 'appended']))
                    ->filter(fn ($property) => $attribute[$property])
                    ->map(fn ($property) => sprintf('<fg=gray>%s</>', $property))
                    ->implode('<fg=gray>,</> ')
            ));

            $second = (new Collection([
                $attribute['type'],
                $attribute['cast'] ? '<fg=yellow;options=bold>'.$attribute['cast'].'</>' : null,
            ]))->filter()->implode(' <fg=gray>/</> ');

            $this->components->twoColumnDetail($first, $second);

            if ($attribute['default'] !== null) {
                $this->components->bulletList(
                    [sprintf('default: %s', $attribute['default'])],
                    OutputInterface::VERBOSITY_VERBOSE
                );
            }
        }

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>Relations</>');

        foreach ($relations as $relation) {
            $this->components->twoColumnDetail(
                sprintf('%s <fg=gray>%s</>', $relation['name'], $relation['type']),
                $relation['related']
            );
        }

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>Events</>');

        if ($events->count()) {
            foreach ($events as $event) {
                $this->components->twoColumnDetail(
                    sprintf('%s', $event['event']),
                    sprintf('%s', $event['class']),
                );
            }
        }

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>Observers</>');

        if ($observers->count()) {
            foreach ($observers as $observer) {
                $this->components->twoColumnDetail(
                    sprintf('%s', $observer['event']),
                    implode(', ', $observer['observer'])
                );
            }
        }

        $this->newLine();
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, \Closure(): string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'model' => fn (): string => suggest('Which model would you like to show?', $this->findAvailableModels()),
        ];
    }
}
