<?php
require_once('PEAR/PackageFileManager2.php');
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagefile = "simpletest";

$options = array(
    'filelistgenerator' => 'svn',
    'simpleoutput'      => true,
    'baseinstalldir'    => 'simpletest',
    'packagedirectory'  => dirname(__FILE__) . '/../',
    'clearcontents'     => true,
    'ignore'            => array('TODO.xml', 'VERSION', 'docs/', 'tutorials/', 'packages/', '.svn'),
    'dir_roles'         => array(
     	'test'    => 'test'
    )
);

$packagexml = PEAR_PackageFileManager2::importOptions($packagefile, $options);
$packagexml->setPackageType('php');
$packagexml->setPackage('simpletest');
$packagexml->setSummary('PHP Unit Tester');
$packagexml->setDescription("Unit testing, mock objects and web testing framework for PHP");

// update this! do we have a default channel server?
$packagexml->setChannel('pear.php.net');
$packagexml->setUri('http://os.coretxt.net.nz/simpletest-1.1');

$notes = file_get_contents(dirname(__FILE__).'/../README');
$packagexml->setNotes($notes);

$packagexml->setPhpDep('5.0.5');
$packagexml->setPearinstallerDep('1.4.0');
$packagexml->addPackageDepWithChannel('required', 'PEAR', 'pear.php.net', '1.4.0');
$packagexml->addMaintainer('lead', 'lastcraft', 'Marcus Baker', 'marcus@lastcraft.com');
$packagexml->setLicense('LGPL', 'http://www.gnu.org/licenses/lgpl-2.1.html');

preg_match("/([0-9\.]+)([a-z]+)/", file_get_contents(dirname(__FILE__).'/../VERSION'), $version);
$packagexml->setAPIVersion($version[1]);
$packagexml->setReleaseVersion($version[1]);
$packagexml->setReleaseStability($version[2]);
$packagexml->setAPIStability($version[2]);

$packagexml->addRelease();
$packagexml->generateContents();


if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $packagexml->writePackageFile();
} else {
    $packagexml->debugPackageFile();
}

?>