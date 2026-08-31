<?php declare(strict_types=1);
use PHPUnit\Event\Facade;
use PHPUnit\Runner\CodeCoverage;
use PHPUnit\Runner\ErrorHandler;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;
use PHPUnit\TextUI\Configuration\CodeCoverageFilterRegistry;
use PHPUnit\TextUI\Configuration\PhpHandler;
use PHPUnit\TextUI\Configuration\SourceMapper;
use PHPUnit\TestRunner\TestResult\PassedTests;

// php://stdout does not obey output buffering. Any output would break
// unserialization of child process results in the parent process.
if (!defined('STDOUT')) {
    define('STDOUT', fopen('php://temp', 'w+b'));
    define('STDERR', fopen('php://stderr', 'wb'));
}

{iniSettings}
ini_set('display_errors', 'stderr');
set_include_path('{include_path}');

$composerAutoload = {composerAutoload};
$phar             = {phar};

ob_start();

if ($composerAutoload) {
    require_once $composerAutoload;

    define('PHPUNIT_COMPOSER_INSTALL', $composerAutoload);
} else if ($phar) {
    require $phar;
}

function __phpunit_run_isolated_test()
{
    $dispatcher = Facade::instance()->initForIsolation(
        PHPUnit\Event\Telemetry\HRTime::fromSecondsAndNanoseconds(
            {offsetSeconds},
            {offsetNanoseconds}
        ),
    );

    require_once '{filename}';

    $configuration = ConfigurationRegistry::get();

    if ({collectCodeCoverageInformation}) {
        CodeCoverage::instance()->init($configuration, CodeCoverageFilterRegistry::instance(), true);
    }

    $deprecationTriggers = [
        'functions' => [],
        'methods'   => [],
    ];

    foreach ($configuration->source()->deprecationTriggers()['functions'] as $function) {
        $deprecationTriggers['functions'][] = $function;
    }

    foreach ($configuration->source()->deprecationTriggers()['methods'] as $method) {
        [$className, $methodName] = explode('::', $method);

        $deprecationTriggers['methods'][] = [
            'className'  => $className,
            'methodName' => $methodName,
        ];
    }

    ErrorHandler::instance()->useDeprecationTriggers($deprecationTriggers);

    $test = new {className}('{methodName}');

    $test->setData('{dataName}', unserialize('{data}'));
    $test->setDependencyInput(unserialize('{dependencyInput}'));
    $test->setInIsolation(true);

    ob_end_clean();

    $test->run();

    $output = '';

    if (!$test->expectsOutput()) {
        $output = $test->output();
    }

    ini_set('xdebug.scream', '0');

    // Not every STDOUT target stream is rewindable
    $hasRewound = @rewind(STDOUT);

    if ($hasRewound && $stdout = @stream_get_contents(STDOUT)) {
        $output         = $stdout . $output;
        $streamMetaData = stream_get_meta_data(STDOUT);

        if (!empty($streamMetaData['stream_type']) && 'STDIO' === $streamMetaData['stream_type']) {
            @ftruncate(STDOUT, 0);
            @rewind(STDOUT);
        }
    }

    file_put_contents(
        '{processResultFile}',
        serialize(
            [
                'testResult'    => $test->result(),
                'codeCoverage'  => {collectCodeCoverageInformation} ? CodeCoverage::instance()->codeCoverage() : null,
                'numAssertions' => $test->numberOfAssertionsPerformed(),
                'output'        => $output,
                'events'        => $dispatcher->flush(),
                'passedTests'   => PassedTests::instance()
            ]
        )
    );
}

function __phpunit_error_handler($errno, $errstr, $errfile, $errline)
{
   return true;
}

set_error_handler('__phpunit_error_handler');

{constants}
{included_files}
{globals}

restore_error_handler();

ConfigurationRegistry::loadFrom('{serializedConfiguration}');

if ('{sourceMapFile}' !== '') {
    SourceMapper::loadFrom('{sourceMapFile}', ConfigurationRegistry::get()->source());
}

(new PhpHandler)->handle(ConfigurationRegistry::get()->php());

if ('{bootstrap}' !== '') {
    require_once '{bootstrap}';
}

__phpunit_run_isolated_test();
