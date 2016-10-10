#!/usr/bin/env php -q
<?php
/**
* Generates a package.xml file for simpletest
*/
/*---------------------------------------------------------------------------*/
// Modify this - path the the source code - no trailing slash
$packagedir = '/home/username/simpletest';
/*---------------------------------------------------------------------------*/
// Modify this - the version / state of the package
$version = '0.9.4';
$state = 'beta'; // alpha / beta / stable
/*---------------------------------------------------------------------------*/
// Modify the release notes. Try to keep under 80 chars width
$releaseNotes = <<<EOD
This is the final version of the PHP unit and web testing tool before the
stable release 1.0 version. It features many improvements to the HTML form
parsing and exposure of the underlying web browser. There are also numerous
minor improvements and bug fixes.
EOD;
/*---------------------------------------------------------------------------*/
// Modify short description. Try to keep under 80 chars width
$shortDesc = <<<EOD
Unit testing, mock objects and web testing framework for PHP.
EOD;
/*---------------------------------------------------------------------------*/
// Modify long description. Try to keep under 80 chars width
$longDesc = <<<EOD
The heart of SimpleTest is a testing framework built around test case classes.
These are written as extensions of base test case classes, each extended with
methods that actually contain test code. Top level test scripts then invoke
the run()  methods on every one of these test cases in order. Each test
method is written to invoke various assertions that the developer expects to
be true such as assertEqual(). If the expectation is correct, then a
successful result is dispatched to the observing test reporter, but any
failure triggers an alert and a description of the mismatch.

These tools are designed for the developer. Tests are written in the PHP
language itself more or less as the application itself is built. The advantage
of using PHP itself as the testing language is that there are no new languages
to learn, testing can start straight away, and the developer can test any part
of the code. Basically, all parts that can be accessed by the application code
can also be accessed by the test code if they are in the same language. 
EOD;
/*---------------------------------------------------------------------------*/
// Modify the maintainers are required
$maintainers = array (
	array ('handle'=>'lastcraft','role'=>'lead','name'=>'Marcus Baker', 'email'=>'marcus@lastcraft.com'),
	array ('handle'=>'jsweat','role'=>'helper','name'=>'Jason Sweat', 'email'=>'jsweat_php@yahoo.com'),
	array ('handle'=>'hfuecks','role'=>'helper','name'=>'Harry Fuecks', 'email'=>'hfuecks@phppatterns.com'),
);
/*---------------------------------------------------------------------------*/

/**
* Code starts here
*/
require_once('PEAR/PackageFileManager.php');
$PPFM = new PEAR_PackageFileManager;

if (version_compare(phpversion(), '4.3.0', '<') ||
    php_sapi_name() == 'cgi') {
    define('STDOUT', fopen('php://stdout', 'w'));
    define('STDERR', fopen('php://stderr', 'w'));
    register_shutdown_function(
        create_function('', 'fclose(STDOUT); fclose(STDERR); return true;'));
}

/**
* A giant array to configure the PackageFileManager. For the "roles" see
* http://pear.php.net/manual/en/developers.packagedef.php
*/
$options = array(
	'baseinstalldir' => 'simpletest',
	'version' => $version,
	'packagedirectory' => $packagedir,
	'outputdirectory' => $packagedir,
	'pathtopackagefile' => $packagedir, 
	'state' => $state,
	'summary' => $shortDesc,
	'description' => $longDesc,
	'filelistgenerator' => 'file',
	'notes' => $releaseNotes,
	'package' => 'SimpleTest',
	'license' => 'The Open Group Test Suite License',

	'dir_roles' => array(
		'docs' => 'doc',
		'test' => 'test',
		'extensions' => 'php',
		//'tutorials' => 'doc',
		//'tutorials/SimpleTest' => 'doc',
		//'ui' => 'php',
		//'ui/css' => 'data',
		//'ui/img' => 'data',
		//'ui/js' => 'data',
		//'ui/js/tests' => 'test',
		),
	'exceptions' =>
		array(
			'HELP_MY_TESTS_DONT_WORK_ANYMORE' => 'doc',
			'LICENSE' => 'doc',
			'README' => 'doc',
			'TODO' => 'doc',
			'VERSION' => 'doc',
		),
	'ignore' => 
		array(
			"$packagedir/packages",
			"$packagedir/ui",
			),
	);

$status = $PPFM->setOptions($options);

if (PEAR::isError($status)) {
    fwrite (STDERR,$status->getMessage());
    exit;
}

foreach ( $maintainers as $maintainer ) {
	$PPFM->addMaintainer(
		$maintainer['handle'],
		$maintainer['role'],
		$maintainer['name'],
		$maintainer['email'] );
}

// Adds a dependency of PHP 4.2.3+
$status = $PPFM->addDependency('php', '4.2.3', 'ge', 'php');
if (PEAR::isError($status)) {
    fwrite (STDERR,$status->getMessage());
    exit;
}

// hack (apparently)
$PPFM->addRole('tpl', 'php');
$PPFM->addRole('png', 'php');
$PPFM->addRole('gif', 'php');
$PPFM->addRole('jpg', 'php');
$PPFM->addRole('css', 'php');
$PPFM->addRole('js', 'php');
$PPFM->addRole('ini', 'php');
$PPFM->addRole('inc', 'php');
$PPFM->addRole('afm', 'php');
$PPFM->addRole('pkg', 'doc');
$PPFM->addRole('cls', 'doc');
$PPFM->addRole('proc', 'doc');
$PPFM->addRole('sh', 'script');

ob_start();
$status = $PPFM->writePackageFile(false);
$output = ob_get_contents();
ob_end_clean();

// Hacks to handle PPFM output
$start = strpos ($output,"<?xml");
if ( $start != 0 ) {
	$errors = substr($output,0,($start-1));
	$output = substr($output,$start);
	$errors = explode("\n",$errors);
	foreach ( $errors as $error ) {
		fwrite (STDERR,$error."\n");
	}
}
fwrite(STDOUT,$output);

if (PEAR::isError($status)) {
	fwrite (STDERR,$status->getMessage());
}
?>