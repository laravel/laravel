**************************************************************************************
* PHPExcel
*
* Copyright (c) 2006 - 2011 PHPExcel
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*
* @copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
* @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
* @version    ##VERSION##, ##DATE##
**************************************************************************************

Requirements
------------

The following requirements should be met prior to using PHPExcel:
* PHP version 5.2.0 or higher
* PHP extension php_zip enabled *)
* PHP extension php_xml enabled
* PHP extension php_gd2 enabled (if not compiled in)

*) php_zip is only needed by PHPExcel_Reader_Excel2007, PHPExcel_Writer_Excel2007,
   PHPExcel_Reader_OOCalc. In other words, if you need PHPExcel to handle .xlsx or .ods
   files you will need the zip extension, but otherwise not.



Installation instructions
-------------------------

Installation is quite easy: copy the contents of the Classes folder to any location
in your application required.

Example:

If your web root folder is /var/www/ you may want to create a subfolder called
/var/www/Classes/ and copy the files into that folder so you end up with files:

/var/www/Classes/PHPExcel.php
/var/www/Classes/PHPExcel/Calculation.php
/var/www/Classes/PHPExcel/Cell.php
...



Getting started
---------------

A good way to get started is to run some of the tests included in the download.
Copy the "Examples" folder next to your "Classes" folder from above so you end up with:

/var/www/Examples/01simple.php
/var/www/Examples/02types.php
...

Start running the test by pointing your browser to the test scripts:

http://example.com/Examples/01simple.php
http://example.com/Examples/02types.php
...

Note: It may be necessary to modify the include/require statements at the beginning of
each of the test scripts if your "Classes" folder from above is named differently.
