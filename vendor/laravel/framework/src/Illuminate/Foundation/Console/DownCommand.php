<?php

namespace Illuminate\Foundation\Console;

use App\Http\Middleware\PreventRequestsDuringMaintenance as AppPreventRequestsDuringMaintenance;
use DateTimeInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Events\MaintenanceModeEnabled;
use Illuminate\Foundation\Exceptions\RegisterErrorViewPaths;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'down')]
class DownCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'down {--redirect= : The path that users should be redirected to}
                                 {--render= : The view that should be prerendered for display during maintenance mode}
                                 {--retry= : The number of seconds or the datetime after which the request may be retried}
                                 {--refresh= : The number of seconds after which the browser may refresh}
                                 {--secret= : The secret phrase that may be used to bypass maintenance mode}
                                 {--with-secret : Generate a random secret phrase that may be used to bypass maintenance mode}
                                 {--status=503 : The status code that should be used when returning the maintenance mode response}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into maintenance / demo mode';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $wasAlreadyDown = $this->laravel->maintenanceMode()->active();

            $downFilePayload = $this->getDownFilePayload();

            $this->laravel->maintenanceMode()->activate($downFilePayload);

            file_put_contents(
                storage_path('framework/maintenance.php'),
                file_get_contents(__DIR__.'/stubs/maintenance-mode.stub')
            );

            $this->laravel->get('events')->dispatch(new MaintenanceModeEnabled());

            $this->components->info($wasAlreadyDown
                ? 'Maintenance mode options updated.'
                : 'Application is now in maintenance mode.'
            );

            if ($downFilePayload['secret'] !== null) {
                $this->components->info('You may bypass maintenance mode via ['.config('app.url')."/{$downFilePayload['secret']}].");
            }
        } catch (Exception $e) {
            $this->components->error(sprintf(
                'Failed to enter maintenance mode: %s.',
                $e->getMessage(),
            ));

            return 1;
        }
    }

    /**
     * Get the payload to be placed in the "down" file.
     *
     * @return array
     */
    protected function getDownFilePayload()
    {
        return [
            'except' => $this->excludedPaths(),
            'redirect' => $this->redirectPath(),
            'retry' => $this->getRetryTime(),
            'refresh' => $this->option('refresh'),
            'secret' => $this->getSecret(),
            'status' => (int) ($this->option('status') ?? 503),
            'template' => $this->option('render') ? $this->prerenderView() : null,
        ];
    }

    /**
     * Get the paths that should be excluded from maintenance mode.
     *
     * @return array
     */
    protected function excludedPaths()
    {
        try {
            return $this->laravel->make(AppPreventRequestsDuringMaintenance::class)->getExcludedPaths();
        } catch (Throwable) {
            try {
                return $this->laravel->make(PreventRequestsDuringMaintenance::class)->getExcludedPaths();
            } catch (Throwable) {
                return [];
            }
        }
    }

    /**
     * Get the path that users should be redirected to.
     *
     * @return string
     */
    protected function redirectPath()
    {
        if ($this->option('redirect') && $this->option('redirect') !== '/') {
            return '/'.trim($this->option('redirect'), '/');
        }

        return $this->option('redirect');
    }

    /**
     * Prerender the specified view so that it can be rendered even before loading Composer.
     *
     * @return string
     */
    protected function prerenderView()
    {
        (new RegisterErrorViewPaths)();

        return view($this->option('render'), [
            'retryAfter' => $this->option('retry'),
        ])->render();
    }

    /**
     * Get the number of seconds or date / time the client should wait before retrying their request.
     *
     * @return int|string|null
     */
    protected function getRetryTime()
    {
        $retry = $this->option('retry');

        if (is_numeric($retry) && $retry > 0) {
            return (int) $retry;
        }

        if (is_string($retry) && ! empty($retry)) {
            try {
                $date = Carbon::parse($retry);

                return $date->format(DateTimeInterface::RFC7231);
            } catch (Exception) {
                return null;
            }
        }

        return null;
    }

    /**
     * Get the secret phrase that may be used to bypass maintenance mode.
     *
     * @return string|null
     */
    protected function getSecret()
    {
        return match (true) {
            ! is_null($this->option('secret')) => $this->option('secret'),
            $this->option('with-secret') => Str::random(),
            default => null,
        };
    }
}
