SimpleTest
==========
You probably got this package from...
http://simpletest.sourceforge.net/projects/simpletest/

If there is no licence agreement with this package please download
a version from the location above. You must read and accept that
licence to use this software. The file is titled simply LICENSE.

What is it? It's a framework for unit testing, web site testing and
mock objects for PHP 5.0.5+.

If you have used JUnit, you will find this PHP unit testing version very
similar. Also included is a mock objects and server stubs generator.
The stubs can have return values set for different arguments, can have
sequences set also by arguments and can return items by reference.
The mocks inherit all of this functionality and can also have
expectations set, again in sequences and for different arguments.

A web tester similar in concept to JWebUnit is also included. There is no
JavaScript or tables support, but forms, authentication, cookies and
frames are handled.

You can see a release schedule at http://simpletest.org/en/overview.html
which is also copied to the documentation folder with this release.
A full PHPDocumenter API documentation exists at
http://simpletest.org/api/.

The user interface is minimal
in the extreme, but a lot of information flows from the test suite.
After version 1.0 we will release a better web UI, but we are leaving XUL
and GTk versions to volunteers as everybody has their own opinion
on a good GUI, and we don't want to discourage development by shipping
one with the toolkit. You can download an Eclipse plug-in separately.

You are looking at a second full release. The unit tests for SimpleTest
itself can be run here...

simpletest/test/unit_tests.php

And tests involving live network connections as well are here...

simpletest/test/all_tests.php

The full tests will typically overrun the 8Mb limit often allowed
to a PHP process. A workaround is to run the tests on the command
with a custom php.ini file if you do not have access to your server
version.

You will have to edit the all_tests.php file if you are accesssing
the internet through a proxy server. See the comments in all_tests.php
for instructions.

The full tests read some test data from the LastCraft site. If the site
is down or has been modified for a later version then you will get
spurious errors. A unit_tests.php failure on the other hand would be
very serious. As far as we know we haven't yet managed to check in any
unit test failures, so please correct us if you find one.

Even if all of the tests run please verify that your existing test suites
also function as expected. If they don't see the file...

HELP_MY_TESTS_DONT_WORK_ANYMORE

This contains information on interface changes. It also points out
deprecated interfaces, so you should read this even if all of
your current tests appear to run.

There is a documentation folder which contains the core reference information
in English and French, although this information is fairly basic.
You can find a tutorial on...

http://simpletest.org/en/first_test_tutorial.html

...to get you started and this material will eventually become included
with the project documentation. A French translation exists at...

http://simpletest.org/fr/first_test_tutorial.html

If you download and use, and possibly even extend this tool, please let us
know. Any feedback, even bad, is always welcome and we will work to get
your suggestions into the next release. Ideally please send your
comments to...

simpletest-support@lists.sourceforge.net

...so that others can read them too. We usually try to respond within 48
hours.

There is no change log except at Sourceforge. You can visit the
release notes to see the completed TODO list after each cycle and also the
status of any bugs, but if the bug is recent then it will be fixed in SVN only.
The SVN check-ins always have all the tests passing and so SVN snapshots should
be pretty usable, although the code may not look so good internally.

Oh, yes. It is called "Simple" because it should be simple to
use. We intend to add a complete set of tools for a test first
and "test as you code" type of development. "Simple" does not
mean "Lite" in this context.

Thanks to everyone who has sent comments and offered suggestions. They
really are invaluable, but sadly you are too many to mention in full.
Thanks to all on the advanced PHP forum on SitePoint, especially Harry
Fuecks. Early adopters are always an inspiration.

Marcus Baker, Jason Sweat, Travis Swicegood, Perrick Penet and Edward Z. Yang.
-- 
marcus@lastcraft.com
