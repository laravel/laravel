<?php

error_reporting(E_ALL | E_STRICT);
ini_set('short_open_tag', false);

if ('cli' !== php_sapi_name()) {
    die('This script is designed for running on the command line.');
}

function showHelp($error) {
    die($error . "\n\n" .
<<<OUTPUT
This script has to be called with the following signature:

    php run.php [--no-progress] testType pathToTestFiles

The test type can be either "Symfony" or "PHP".

The following options are available:

    --no-progress    Disables showing which file is currently tested.
OUTPUT
    );
}

$options = array();
$arguments = array();

// remove script name from argv
array_shift($argv);

foreach ($argv as $arg) {
    if ('-' === $arg[0]) {
        $options[] = $arg;
    } else {
        $arguments[] = $arg;
    }
}

if (count($arguments) !== 2) {
    showHelp('Too little arguments passed!');
}

$SHOW_PROGRESS = true;
if (count($options) > 0) {
    if (count($options) === 1 && $options[0] === '--no-progress') {
        $SHOW_PROGRESS = false;
    } else {
        showHelp('Invalid option passed!');
    }
}

$TEST_TYPE = $arguments[0];
$DIR       = $arguments[1];

if ('Symfony' === $TEST_TYPE) {
    function filter_func($path) {
        return preg_match('~\.php(?:\.cache)?$~', $path) && false === strpos($path, 'skeleton');
    };
} elseif ('PHP' === $TEST_TYPE) {
    function filter_func($path) {
        return preg_match('~\.phpt$~', $path);
    };
} else {
    showHelp('Test type must be either "Symfony" or "PHP"!');
}

require_once dirname(__FILE__) . '/../lib/PHPParser/Autoloader.php';
PHPParser_Autoloader::register();

$parser        = new PHPParser_Parser(new PHPParser_Lexer_Emulative);
$prettyPrinter = new PHPParser_PrettyPrinter_Default;
$nodeDumper    = new PHPParser_NodeDumper;

$parseFail = $ppFail = $compareFail = $count = 0;

$readTime = $parseTime = $ppTime = $reparseTime = $compareTime = 0;
$totalStartTime = microtime(true);

foreach (new RecursiveIteratorIterator(
             new RecursiveDirectoryIterator($DIR),
             RecursiveIteratorIterator::LEAVES_ONLY)
         as $file) {
    if (!filter_func($file)) {
        continue;
    }

    $startTime = microtime(true);
    $code = file_get_contents($file);
    $readTime += microtime(true) - $startTime;

    if ('PHP' === $TEST_TYPE) {
        if (preg_match('~(?:
# skeleton files
  ext.gmp.tests.001
| ext.skeleton.tests.001
# multibyte encoded files
| ext.mbstring.tests.zend_multibyte-01
| Zend.tests.multibyte.multibyte_encoding_001
| Zend.tests.multibyte.multibyte_encoding_004
| Zend.tests.multibyte.multibyte_encoding_005
# token_get_all bug (https://bugs.php.net/bug.php?id=60097)
| Zend.tests.bug47516
# pretty print difference due to INF vs 1e1000
| ext.standard.tests.general_functions.bug27678
| tests.lang.bug24640
)\.phpt$~x', $file)) {
            continue;
        }

        if (!preg_match('~--FILE--\s*(.*?)--[A-Z]+--~s', $code, $matches)) {
            continue;
        }
        if (preg_match('~--EXPECT(?:F|REGEX)?--\s*(?:Parse|Fatal) error~', $code)) {
            continue;
        }

        $code = $matches[1];
    }

    set_time_limit(10);

    ++$count;

    if ($SHOW_PROGRESS) {
        echo substr(str_pad('Testing file ' . $count . ': ' . substr($file, strlen($DIR)), 79), 0, 79), "\r";
    }

    try {
        $startTime = microtime(true);
        $stmts = $parser->parse($code);
        $parseTime += microtime(true) - $startTime;

        $startTime = microtime(true);
        $code = '<?php' . "\n" . $prettyPrinter->prettyPrint($stmts);
        $ppTime += microtime(true) - $startTime;

        try {
            $startTime = microtime(true);
            $ppStmts = $parser->parse($code);
            $reparseTime += microtime(true) - $startTime;

            $startTime = microtime(true);
            $same = $nodeDumper->dump($stmts) == $nodeDumper->dump($ppStmts);
            $compareTime += microtime(true) - $startTime;

            if (!$same) {
                echo $file, ":\n    Result of initial parse and parse after pretty print differ\n";

                ++$compareFail;
            }
        } catch (PHPParser_Error $e) {
            echo $file, ":\n    Parse of pretty print failed with message: {$e->getMessage()}\n";

            ++$ppFail;
        }
    } catch (PHPParser_Error $e) {
        echo $file, ":\n    Parse failed with message: {$e->getMessage()}\n";

        ++$parseFail;
    }
}

if (0 === $parseFail && 0 === $ppFail && 0 === $compareFail) {
    echo "\n\n", 'All tests passed.', "\n";
} else {
    echo "\n\n", '==========', "\n\n", 'There were: ', "\n";
    if (0 !== $parseFail) {
        echo '    ', $parseFail,   ' parse failures.',        "\n";
    }
    if (0 !== $ppFail) {
        echo '    ', $ppFail,      ' pretty print failures.', "\n";
    }
    if (0 !== $compareFail) {
        echo '    ', $compareFail, ' compare failures.',      "\n";
    }
}

echo "\n",
     'Tested files:         ', $count,        "\n",
     "\n",
     'Reading files took:   ', $readTime,    "\n",
     'Parsing took:         ', $parseTime,   "\n",
     'Pretty printing took: ', $ppTime,      "\n",
     'Reparsing took:       ', $reparseTime, "\n",
     'Comparing took:       ', $compareTime, "\n",
     "\n",
     'Total time:           ', microtime(true) - $totalStartTime, "\n",
     'Maximum memory usage: ', memory_get_peak_usage(true), "\n";