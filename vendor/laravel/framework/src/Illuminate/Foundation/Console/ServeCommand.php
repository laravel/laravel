<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;
use function Termwind\terminal;

#[AsCommand(name: 'serve')]
class ServeCommand extends Command
{
    use InteractsWithTime;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server';

    /**
     * The number of PHP CLI server workers.
     *
     * @var int<2, max>|false
     */
    protected $phpServerWorkers = 1;

    /**
     * The current port offset.
     *
     * @var int
     */
    protected $portOffset = 0;

    /**
     * The list of lines that are pending to be output.
     *
     * @var string
     */
    protected $outputBuffer = '';

    /**
     * The list of requests being handled and their start time.
     *
     * @var array<int, \Illuminate\Support\Carbon>
     */
    protected $requestsPool;

    /**
     * Indicates if the "Server running on..." output message has been displayed.
     *
     * @var bool
     */
    protected $serverRunningHasBeenDisplayed = false;

    /**
     * The environment variables that should be passed from host machine to the PHP server process.
     *
     * @var string[]
     */
    public static $passthroughVariables = [
        'APP_ENV',
        'HERD_PHP_81_INI_SCAN_DIR',
        'HERD_PHP_82_INI_SCAN_DIR',
        'HERD_PHP_83_INI_SCAN_DIR',
        'HERD_PHP_84_INI_SCAN_DIR',
        'HERD_PHP_85_INI_SCAN_DIR',
        'IGNITION_LOCAL_SITES_PATH',
        'LARAVEL_SAIL',
        'PATH',
        'PHP_IDE_CONFIG',
        'SYSTEMROOT',
        'XDEBUG_CONFIG',
        'XDEBUG_MODE',
        'XDEBUG_SESSION',
    ];

    /** {@inheritdoc} */
    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->phpServerWorkers = transform((int) env('PHP_CLI_SERVER_WORKERS', 1), function (int $workers) {
            if ($workers < 2) {
                return false;
            }

            if ($workers > 1 &&
                ! $this->option('no-reload') &&
                ! (int) env('LARAVEL_SAIL', 0)) {
                $this->components->warn('Unable to respect the `PHP_CLI_SERVER_WORKERS` environment variable without the `--no-reload` flag. Only creating a single server.');

                return false;
            }

            return $workers;
        });

        parent::initialize($input, $output);
    }

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        $environmentFile = $this->option('env')
            ? base_path('.env').'.'.$this->option('env')
            : base_path('.env');

        $hasEnvironment = file_exists($environmentFile);

        $environmentLastModified = $hasEnvironment
            ? filemtime($environmentFile)
            : now()->addDays(30)->getTimestamp();

        $process = $this->startProcess($hasEnvironment);

        while ($process->isRunning()) {
            if ($hasEnvironment) {
                clearstatcache(false, $environmentFile);
            }

            if (! $this->option('no-reload') &&
                $hasEnvironment &&
                filemtime($environmentFile) > $environmentLastModified) {
                $environmentLastModified = filemtime($environmentFile);

                $this->newLine();

                $this->components->info('Environment modified. Restarting server...');

                $process->stop(5);

                $this->serverRunningHasBeenDisplayed = false;

                $process = $this->startProcess($hasEnvironment);
            }

            usleep(500 * 1000);
        }

        $status = $process->getExitCode();

        if ($status && $this->canTryAnotherPort()) {
            $this->portOffset += 1;

            return $this->handle();
        }

        return $status;
    }

    /**
     * Start a new server process.
     *
     * @param  bool  $hasEnvironment
     * @return \Symfony\Component\Process\Process
     */
    protected function startProcess($hasEnvironment)
    {
        $process = new Process($this->serverCommand(), public_path(), (new Collection($_ENV))->mapWithKeys(function ($value, $key) use ($hasEnvironment) {
            if ($this->option('no-reload') || ! $hasEnvironment) {
                return [$key => $value];
            }

            return in_array($key, static::$passthroughVariables) ? [$key => $value] : [$key => false];
        })->merge(['PHP_CLI_SERVER_WORKERS' => $this->phpServerWorkers])->all());

        $this->trap(fn () => [SIGTERM, SIGINT, SIGHUP, SIGUSR1, SIGUSR2, SIGQUIT], function ($signal) use ($process) {
            if ($process->isRunning()) {
                $process->stop(10, $signal);
            }

            exit;
        });

        $process->start($this->handleProcessOutput());

        return $process;
    }

    /**
     * Get the full server command.
     *
     * @return array
     */
    protected function serverCommand()
    {
        $server = file_exists(base_path('server.php'))
            ? base_path('server.php')
            : __DIR__.'/../resources/server.php';

        return [
            php_binary(),
            '-S',
            $this->host().':'.$this->port(),
            $server,
        ];
    }

    /**
     * Get the host for the command.
     *
     * @return string
     */
    protected function host()
    {
        [$host] = $this->getHostAndPort();

        return $host;
    }

    /**
     * Get the port for the command.
     *
     * @return string
     */
    protected function port()
    {
        $port = $this->input->getOption('port');

        if (is_null($port)) {
            [, $port] = $this->getHostAndPort();
        }

        $port = $port ?: 8000;

        return $port + $this->portOffset;
    }

    /**
     * Get the host and port from the host option string.
     *
     * @return array
     */
    protected function getHostAndPort()
    {
        if (preg_match('/(\[.*\]):?([0-9]+)?/', $this->input->getOption('host'), $matches) !== false) {
            return [
                $matches[1] ?? $this->input->getOption('host'),
                $matches[2] ?? null,
            ];
        }

        $hostParts = explode(':', $this->input->getOption('host'));

        return [
            $hostParts[0],
            $hostParts[1] ?? null,
        ];
    }

    /**
     * Check if the command has reached its maximum number of port tries.
     *
     * @return bool
     */
    protected function canTryAnotherPort()
    {
        return is_null($this->input->getOption('port')) &&
            ($this->input->getOption('tries') > $this->portOffset);
    }

    /**
     * Returns a "callable" to handle the process output.
     *
     * @return callable(string, string): void
     */
    protected function handleProcessOutput()
    {
        return function ($type, $buffer) {
            $this->outputBuffer .= $buffer;

            $this->flushOutputBuffer();
        };
    }

    /**
     * Flush the output buffer.
     *
     * @return void
     */
    protected function flushOutputBuffer()
    {
        $lines = (new Stringable($this->outputBuffer))->explode("\n");

        $this->outputBuffer = (string) $lines->pop();

        $lines
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(function ($line) {
                if ((new Stringable($line))->contains('Development Server (http')) {
                    if ($this->serverRunningHasBeenDisplayed === false) {
                        $this->serverRunningHasBeenDisplayed = true;

                        $this->components->info("Server running on [http://{$this->host()}:{$this->port()}].");
                        $this->comment('  <fg=yellow;options=bold>Press Ctrl+C to stop the server</>');

                        $this->newLine();
                    }

                    return;
                }

                if ((new Stringable($line))->contains(' Accepted')) {
                    $requestPort = static::getRequestPortFromLine($line);

                    $this->requestsPool[$requestPort] = [
                        $this->getDateFromLine($line),
                        $this->requestsPool[$requestPort][1] ?? false,
                        microtime(true),
                    ];
                } elseif ((new Stringable($line))->contains([' [200]: GET '])) {
                    $requestPort = static::getRequestPortFromLine($line);

                    $this->requestsPool[$requestPort][1] = trim(explode('[200]: GET', $line)[1]);
                } elseif ((new Stringable($line))->contains('URI:')) {
                    $requestPort = static::getRequestPortFromLine($line);

                    $this->requestsPool[$requestPort][1] = trim(explode('URI: ', $line)[1]);
                } elseif ((new Stringable($line))->contains(' Closing')) {
                    $requestPort = static::getRequestPortFromLine($line);

                    if (empty($this->requestsPool[$requestPort]) || count($this->requestsPool[$requestPort] ?? []) !== 3) {
                        $this->requestsPool[$requestPort] = [
                            $this->getDateFromLine($line),
                            false,
                            microtime(true),
                        ];
                    }

                    [$startDate, $file, $startMicrotime] = $this->requestsPool[$requestPort];

                    $formattedStartedAt = $startDate->format('Y-m-d H:i:s');

                    unset($this->requestsPool[$requestPort]);

                    [$date, $time] = explode(' ', $formattedStartedAt);

                    $this->output->write("  <fg=gray>$date</> $time");

                    $runTime = $this->runTimeForHumans($startMicrotime);

                    if ($file) {
                        $this->output->write($file = " $file");
                    }

                    $dots = max(terminal()->width() - mb_strlen($formattedStartedAt) - mb_strlen($file) - mb_strlen($runTime) - 9, 0);

                    $this->output->write(' '.str_repeat('<fg=gray>.</>', $dots));
                    $this->output->writeln(" <fg=gray>~ {$runTime}</>");
                } elseif ((new Stringable($line))->contains(['Closed without sending a request', 'Failed to poll event'])) {
                    // ...
                } elseif (! empty($line)) {
                    if ((new Stringable($line))->startsWith('[')) {
                        $line = (new Stringable($line))->after('] ');
                    }

                    $this->output->writeln("  <fg=gray>$line</>");
                }
            });
    }

    /**
     * Get the date from the given PHP server output.
     *
     * @param  string  $line
     * @return \Illuminate\Support\Carbon
     */
    protected function getDateFromLine($line)
    {
        $regex = ! windows_os() && is_int($this->phpServerWorkers)
            ? '/^\[\d+]\s\[([a-zA-Z0-9: ]+)\]/'
            : '/^\[([^\]]+)\]/';

        $line = str_replace('  ', ' ', $line);

        preg_match($regex, $line, $matches);

        return Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
    }

    /**
     * Get the request port from the given PHP server output.
     *
     * @param  string  $line
     * @return int
     */
    public static function getRequestPortFromLine($line)
    {
        preg_match('/(\[\w+\s\w+\s\d+\s[\d:]+\s\d{4}\]\s)?:(\d+)\s(?:(?:\w+$)|(?:\[.*))/', $line, $matches);

        if (! isset($matches[2])) {
            throw new \InvalidArgumentException("Failed to extract the request port. Ensure the log line contains a valid port: {$line}");
        }

        return (int) $matches[2];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on', Env::get('SERVER_HOST', '127.0.0.1')],
            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on', Env::get('SERVER_PORT')],
            ['tries', null, InputOption::VALUE_OPTIONAL, 'The max number of ports to attempt to serve from', 10],
            ['no-reload', null, InputOption::VALUE_NONE, 'Do not reload the development server on .env file changes'],
        ];
    }
}
