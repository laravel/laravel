HOW TO MAKE A PACKAGE

1. First make sure PEAR_PackageFileManager is installed;
(version 1.2.0 was used)

$ pear install PEAR_PackageFileManager

2. Edit the simpletest/packages/pear_package_create.php file (see comments for what
needs changing).

3. Run the simpletest/packages/pear_package_create.php script, piping the output
to the file you want to create e.g.;

$ ./pear_package_create.php > package.xml

4. Copy the package.xml to the root of Simpletest.

5. From the root of Simpletest type;

$ pear package package.xml

This creates the package zip

6. Install with;

$ pear install SimpleTest-x.x.x.tgz