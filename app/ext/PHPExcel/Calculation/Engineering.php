<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.8.0, 2014-03-02
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/** EULER */
define('EULER', 2.71828182845904523536);


/**
 * PHPExcel_Calculation_Engineering
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Engineering {

	/**
	 * Details of the Units of measure that can be used in CONVERTUOM()
	 *
	 * @var mixed[]
	 */
	private static $_conversionUnits = array( 'g'		=> array(	'Group'	=> 'Mass',			'Unit Name'	=> 'Gram',						'AllowPrefix'	=> True		),
											  'sg'		=> array(	'Group'	=> 'Mass',			'Unit Name'	=> 'Slug',						'AllowPrefix'	=> False	),
											  'lbm'		=> array(	'Group'	=> 'Mass',			'Unit Name'	=> 'Pound mass (avoirdupois)',	'AllowPrefix'	=> False	),
											  'u'		=> array(	'Group'	=> 'Mass',			'Unit Name'	=> 'U (atomic mass unit)',		'AllowPrefix'	=> True		),
											  'ozm'		=> array(	'Group'	=> 'Mass',			'Unit Name'	=> 'Ounce mass (avoirdupois)',	'AllowPrefix'	=> False	),
											  'm'		=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Meter',						'AllowPrefix'	=> True		),
											  'mi'		=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Statute mile',				'AllowPrefix'	=> False	),
											  'Nmi'		=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Nautical mile',				'AllowPrefix'	=> False	),
											  'in'		=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Inch',						'AllowPrefix'	=> False	),
											  'ft'		=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Foot',						'AllowPrefix'	=> False	),
											  'yd'		=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Yard',						'AllowPrefix'	=> False	),
											  'ang'		=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Angstrom',					'AllowPrefix'	=> True		),
											  'Pica'	=> array(	'Group'	=> 'Distance',		'Unit Name'	=> 'Pica (1/72 in)',			'AllowPrefix'	=> False	),
											  'yr'		=> array(	'Group'	=> 'Time',			'Unit Name'	=> 'Year',						'AllowPrefix'	=> False	),
											  'day'		=> array(	'Group'	=> 'Time',			'Unit Name'	=> 'Day',						'AllowPrefix'	=> False	),
											  'hr'		=> array(	'Group'	=> 'Time',			'Unit Name'	=> 'Hour',						'AllowPrefix'	=> False	),
											  'mn'		=> array(	'Group'	=> 'Time',			'Unit Name'	=> 'Minute',					'AllowPrefix'	=> False	),
											  'sec'		=> array(	'Group'	=> 'Time',			'Unit Name'	=> 'Second',					'AllowPrefix'	=> True		),
											  'Pa'		=> array(	'Group'	=> 'Pressure',		'Unit Name'	=> 'Pascal',					'AllowPrefix'	=> True		),
											  'p'		=> array(	'Group'	=> 'Pressure',		'Unit Name'	=> 'Pascal',					'AllowPrefix'	=> True		),
											  'atm'		=> array(	'Group'	=> 'Pressure',		'Unit Name'	=> 'Atmosphere',				'AllowPrefix'	=> True		),
											  'at'		=> array(	'Group'	=> 'Pressure',		'Unit Name'	=> 'Atmosphere',				'AllowPrefix'	=> True		),
											  'mmHg'	=> array(	'Group'	=> 'Pressure',		'Unit Name'	=> 'mm of Mercury',				'AllowPrefix'	=> True		),
											  'N'		=> array(	'Group'	=> 'Force',			'Unit Name'	=> 'Newton',					'AllowPrefix'	=> True		),
											  'dyn'		=> array(	'Group'	=> 'Force',			'Unit Name'	=> 'Dyne',						'AllowPrefix'	=> True		),
											  'dy'		=> array(	'Group'	=> 'Force',			'Unit Name'	=> 'Dyne',						'AllowPrefix'	=> True		),
											  'lbf'		=> array(	'Group'	=> 'Force',			'Unit Name'	=> 'Pound force',				'AllowPrefix'	=> False	),
											  'J'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Joule',						'AllowPrefix'	=> True		),
											  'e'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Erg',						'AllowPrefix'	=> True		),
											  'c'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Thermodynamic calorie',		'AllowPrefix'	=> True		),
											  'cal'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'IT calorie',				'AllowPrefix'	=> True		),
											  'eV'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Electron volt',				'AllowPrefix'	=> True		),
											  'ev'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Electron volt',				'AllowPrefix'	=> True		),
											  'HPh'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Horsepower-hour',			'AllowPrefix'	=> False	),
											  'hh'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Horsepower-hour',			'AllowPrefix'	=> False	),
											  'Wh'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Watt-hour',					'AllowPrefix'	=> True		),
											  'wh'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Watt-hour',					'AllowPrefix'	=> True		),
											  'flb'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'Foot-pound',				'AllowPrefix'	=> False	),
											  'BTU'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'BTU',						'AllowPrefix'	=> False	),
											  'btu'		=> array(	'Group'	=> 'Energy',		'Unit Name'	=> 'BTU',						'AllowPrefix'	=> False	),
											  'HP'		=> array(	'Group'	=> 'Power',			'Unit Name'	=> 'Horsepower',				'AllowPrefix'	=> False	),
											  'h'		=> array(	'Group'	=> 'Power',			'Unit Name'	=> 'Horsepower',				'AllowPrefix'	=> False	),
											  'W'		=> array(	'Group'	=> 'Power',			'Unit Name'	=> 'Watt',						'AllowPrefix'	=> True		),
											  'w'		=> array(	'Group'	=> 'Power',			'Unit Name'	=> 'Watt',						'AllowPrefix'	=> True		),
											  'T'		=> array(	'Group'	=> 'Magnetism',		'Unit Name'	=> 'Tesla',						'AllowPrefix'	=> True		),
											  'ga'		=> array(	'Group'	=> 'Magnetism',		'Unit Name'	=> 'Gauss',						'AllowPrefix'	=> True		),
											  'C'		=> array(	'Group'	=> 'Temperature',	'Unit Name'	=> 'Celsius',					'AllowPrefix'	=> False	),
											  'cel'		=> array(	'Group'	=> 'Temperature',	'Unit Name'	=> 'Celsius',					'AllowPrefix'	=> False	),
											  'F'		=> array(	'Group'	=> 'Temperature',	'Unit Name'	=> 'Fahrenheit',				'AllowPrefix'	=> False	),
											  'fah'		=> array(	'Group'	=> 'Temperature',	'Unit Name'	=> 'Fahrenheit',				'AllowPrefix'	=> False	),
											  'K'		=> array(	'Group'	=> 'Temperature',	'Unit Name'	=> 'Kelvin',					'AllowPrefix'	=> False	),
											  'kel'		=> array(	'Group'	=> 'Temperature',	'Unit Name'	=> 'Kelvin',					'AllowPrefix'	=> False	),
											  'tsp'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Teaspoon',					'AllowPrefix'	=> False	),
											  'tbs'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Tablespoon',				'AllowPrefix'	=> False	),
											  'oz'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Fluid Ounce',				'AllowPrefix'	=> False	),
											  'cup'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Cup',						'AllowPrefix'	=> False	),
											  'pt'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'U.S. Pint',					'AllowPrefix'	=> False	),
											  'us_pt'	=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'U.S. Pint',					'AllowPrefix'	=> False	),
											  'uk_pt'	=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'U.K. Pint',					'AllowPrefix'	=> False	),
											  'qt'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Quart',						'AllowPrefix'	=> False	),
											  'gal'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Gallon',					'AllowPrefix'	=> False	),
											  'l'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Litre',						'AllowPrefix'	=> True		),
											  'lt'		=> array(	'Group'	=> 'Liquid',		'Unit Name'	=> 'Litre',						'AllowPrefix'	=> True		)
											);

	/**
	 * Details of the Multiplier prefixes that can be used with Units of Measure in CONVERTUOM()
	 *
	 * @var mixed[]
	 */
	private static $_conversionMultipliers = array(	'Y'	=> array(	'multiplier'	=> 1E24,	'name'	=> 'yotta'	),
													'Z'	=> array(	'multiplier'	=> 1E21,	'name'	=> 'zetta'	),
													'E'	=> array(	'multiplier'	=> 1E18,	'name'	=> 'exa'	),
													'P'	=> array(	'multiplier'	=> 1E15,	'name'	=> 'peta'	),
													'T'	=> array(	'multiplier'	=> 1E12,	'name'	=> 'tera'	),
													'G'	=> array(	'multiplier'	=> 1E9,		'name'	=> 'giga'	),
													'M'	=> array(	'multiplier'	=> 1E6,		'name'	=> 'mega'	),
													'k'	=> array(	'multiplier'	=> 1E3,		'name'	=> 'kilo'	),
													'h'	=> array(	'multiplier'	=> 1E2,		'name'	=> 'hecto'	),
													'e'	=> array(	'multiplier'	=> 1E1,		'name'	=> 'deka'	),
													'd'	=> array(	'multiplier'	=> 1E-1,	'name'	=> 'deci'	),
													'c'	=> array(	'multiplier'	=> 1E-2,	'name'	=> 'centi'	),
													'm'	=> array(	'multiplier'	=> 1E-3,	'name'	=> 'milli'	),
													'u'	=> array(	'multiplier'	=> 1E-6,	'name'	=> 'micro'	),
													'n'	=> array(	'multiplier'	=> 1E-9,	'name'	=> 'nano'	),
													'p'	=> array(	'multiplier'	=> 1E-12,	'name'	=> 'pico'	),
													'f'	=> array(	'multiplier'	=> 1E-15,	'name'	=> 'femto'	),
													'a'	=> array(	'multiplier'	=> 1E-18,	'name'	=> 'atto'	),
													'z'	=> array(	'multiplier'	=> 1E-21,	'name'	=> 'zepto'	),
													'y'	=> array(	'multiplier'	=> 1E-24,	'name'	=> 'yocto'	)
												 );

	/**
	 * Details of the Units of measure conversion factors, organised by group
	 *
	 * @var mixed[]
	 */
	private static $_unitConversions = array(	'Mass'		=> array(	'g'		=> array(	'g'		=> 1.0,
																							'sg'	=> 6.85220500053478E-05,
																							'lbm'	=> 2.20462291469134E-03,
																							'u'		=> 6.02217000000000E+23,
																							'ozm'	=> 3.52739718003627E-02
																						),
																		'sg'	=> array(	'g'		=> 1.45938424189287E+04,
																							'sg'	=> 1.0,
																							'lbm'	=> 3.21739194101647E+01,
																							'u'		=> 8.78866000000000E+27,
																							'ozm'	=> 5.14782785944229E+02
																						),
																		'lbm'	=> array(	'g'		=> 4.5359230974881148E+02,
																							'sg'	=> 3.10810749306493E-02,
																							'lbm'	=> 1.0,
																							'u'		=> 2.73161000000000E+26,
																							'ozm'	=> 1.60000023429410E+01
																						),
																		'u'		=> array(	'g'		=> 1.66053100460465E-24,
																							'sg'	=> 1.13782988532950E-28,
																							'lbm'	=> 3.66084470330684E-27,
																							'u'		=> 1.0,
																							'ozm'	=> 5.85735238300524E-26
																						),
																		'ozm'	=> array(	'g'		=> 2.83495152079732E+01,
																							'sg'	=> 1.94256689870811E-03,
																							'lbm'	=> 6.24999908478882E-02,
																							'u'		=> 1.70725600000000E+25,
																							'ozm'	=> 1.0
																						)
																	),
												'Distance'	=> array(	'm'		=> array(	'm'		=> 1.0,
																							'mi'	=> 6.21371192237334E-04,
																							'Nmi'	=> 5.39956803455724E-04,
																							'in'	=> 3.93700787401575E+01,
																							'ft'	=> 3.28083989501312E+00,
																							'yd'	=> 1.09361329797891E+00,
																							'ang'	=> 1.00000000000000E+10,
																							'Pica'	=> 2.83464566929116E+03
																						),
																		'mi'	=> array(	'm'		=> 1.60934400000000E+03,
																							'mi'	=> 1.0,
																							'Nmi'	=> 8.68976241900648E-01,
																							'in'	=> 6.33600000000000E+04,
																							'ft'	=> 5.28000000000000E+03,
																							'yd'	=> 1.76000000000000E+03,
																							'ang'	=> 1.60934400000000E+13,
																							'Pica'	=> 4.56191999999971E+06
																						),
																		'Nmi'	=> array(	'm'		=> 1.85200000000000E+03,
																							'mi'	=> 1.15077944802354E+00,
																							'Nmi'	=> 1.0,
																							'in'	=> 7.29133858267717E+04,
																							'ft'	=> 6.07611548556430E+03,
																							'yd'	=> 2.02537182785694E+03,
																							'ang'	=> 1.85200000000000E+13,
																							'Pica'	=> 5.24976377952723E+06
																						),
																		'in'	=> array(	'm'		=> 2.54000000000000E-02,
																							'mi'	=> 1.57828282828283E-05,
																							'Nmi'	=> 1.37149028077754E-05,
																							'in'	=> 1.0,
																							'ft'	=> 8.33333333333333E-02,
																							'yd'	=> 2.77777777686643E-02,
																							'ang'	=> 2.54000000000000E+08,
																							'Pica'	=> 7.19999999999955E+01
																						),
																		'ft'	=> array(	'm'		=> 3.04800000000000E-01,
																							'mi'	=> 1.89393939393939E-04,
																							'Nmi'	=> 1.64578833693305E-04,
																							'in'	=> 1.20000000000000E+01,
																							'ft'	=> 1.0,
																							'yd'	=> 3.33333333223972E-01,
																							'ang'	=> 3.04800000000000E+09,
																							'Pica'	=> 8.63999999999946E+02
																						),
																		'yd'	=> array(	'm'		=> 9.14400000300000E-01,
																							'mi'	=> 5.68181818368230E-04,
																							'Nmi'	=> 4.93736501241901E-04,
																							'in'	=> 3.60000000118110E+01,
																							'ft'	=> 3.00000000000000E+00,
																							'yd'	=> 1.0,
																							'ang'	=> 9.14400000300000E+09,
																							'Pica'	=> 2.59200000085023E+03
																						),
																		'ang'	=> array(	'm'		=> 1.00000000000000E-10,
																							'mi'	=> 6.21371192237334E-14,
																							'Nmi'	=> 5.39956803455724E-14,
																							'in'	=> 3.93700787401575E-09,
																							'ft'	=> 3.28083989501312E-10,
																							'yd'	=> 1.09361329797891E-10,
																							'ang'	=> 1.0,
																							'Pica'	=> 2.83464566929116E-07
																						),
																		'Pica'	=> array(	'm'		=> 3.52777777777800E-04,
																							'mi'	=> 2.19205948372629E-07,
																							'Nmi'	=> 1.90484761219114E-07,
																							'in'	=> 1.38888888888898E-02,
																							'ft'	=> 1.15740740740748E-03,
																							'yd'	=> 3.85802469009251E-04,
																							'ang'	=> 3.52777777777800E+06,
																							'Pica'	=> 1.0
																						)
																	),
												'Time'		=> array(	'yr'	=> array(	'yr'		=> 1.0,
																							'day'		=> 365.25,
																							'hr'		=> 8766.0,
																							'mn'		=> 525960.0,
																							'sec'		=> 31557600.0
																						),
																		'day'	=> array(	'yr'		=> 2.73785078713210E-03,
																							'day'		=> 1.0,
																							'hr'		=> 24.0,
																							'mn'		=> 1440.0,
																							'sec'		=> 86400.0
																						),
																		'hr'	=> array(	'yr'		=> 1.14077116130504E-04,
																							'day'		=> 4.16666666666667E-02,
																							'hr'		=> 1.0,
																							'mn'		=> 60.0,
																							'sec'		=> 3600.0
																						),
																		'mn'	=> array(	'yr'		=> 1.90128526884174E-06,
																							'day'		=> 6.94444444444444E-04,
																							'hr'		=> 1.66666666666667E-02,
																							'mn'		=> 1.0,
																							'sec'		=> 60.0
																						),
																		'sec'	=> array(	'yr'		=> 3.16880878140289E-08,
																							'day'		=> 1.15740740740741E-05,
																							'hr'		=> 2.77777777777778E-04,
																							'mn'		=> 1.66666666666667E-02,
																							'sec'		=> 1.0
																						)
																	),
												'Pressure'	=> array(	'Pa'	=> array(	'Pa'		=> 1.0,
																							'p'			=> 1.0,
																							'atm'		=> 9.86923299998193E-06,
																							'at'		=> 9.86923299998193E-06,
																							'mmHg'		=> 7.50061707998627E-03
																						),
																		'p'		=> array(	'Pa'		=> 1.0,
																							'p'			=> 1.0,
																							'atm'		=> 9.86923299998193E-06,
																							'at'		=> 9.86923299998193E-06,
																							'mmHg'		=> 7.50061707998627E-03
																						),
																		'atm'	=> array(	'Pa'		=> 1.01324996583000E+05,
																							'p'			=> 1.01324996583000E+05,
																							'atm'		=> 1.0,
																							'at'		=> 1.0,
																							'mmHg'		=> 760.0
																						),
																		'at'	=> array(	'Pa'		=> 1.01324996583000E+05,
																							'p'			=> 1.01324996583000E+05,
																							'atm'		=> 1.0,
																							'at'		=> 1.0,
																							'mmHg'		=> 760.0
																						),
																		'mmHg'	=> array(	'Pa'		=> 1.33322363925000E+02,
																							'p'			=> 1.33322363925000E+02,
																							'atm'		=> 1.31578947368421E-03,
																							'at'		=> 1.31578947368421E-03,
																							'mmHg'		=> 1.0
																						)
																	),
												'Force'		=> array(	'N'		=> array(	'N'			=> 1.0,
																							'dyn'		=> 1.0E+5,
																							'dy'		=> 1.0E+5,
																							'lbf'		=> 2.24808923655339E-01
																						),
																		'dyn'	=> array(	'N'			=> 1.0E-5,
																							'dyn'		=> 1.0,
																							'dy'		=> 1.0,
																							'lbf'		=> 2.24808923655339E-06
																						),
																		'dy'	=> array(	'N'			=> 1.0E-5,
																							'dyn'		=> 1.0,
																							'dy'		=> 1.0,
																							'lbf'		=> 2.24808923655339E-06
																						),
																		'lbf'	=> array(	'N'			=> 4.448222,
																							'dyn'		=> 4.448222E+5,
																							'dy'		=> 4.448222E+5,
																							'lbf'		=> 1.0
																						)
																	),
												'Energy'	=> array(	'J'		=> array(	'J'			=> 1.0,
																							'e'			=> 9.99999519343231E+06,
																							'c'			=> 2.39006249473467E-01,
																							'cal'		=> 2.38846190642017E-01,
																							'eV'		=> 6.24145700000000E+18,
																							'ev'		=> 6.24145700000000E+18,
																							'HPh'		=> 3.72506430801000E-07,
																							'hh'		=> 3.72506430801000E-07,
																							'Wh'		=> 2.77777916238711E-04,
																							'wh'		=> 2.77777916238711E-04,
																							'flb'		=> 2.37304222192651E+01,
																							'BTU'		=> 9.47815067349015E-04,
																							'btu'		=> 9.47815067349015E-04
																						),
																		'e'		=> array(	'J'			=> 1.00000048065700E-07,
																							'e'			=> 1.0,
																							'c'			=> 2.39006364353494E-08,
																							'cal'		=> 2.38846305445111E-08,
																							'eV'		=> 6.24146000000000E+11,
																							'ev'		=> 6.24146000000000E+11,
																							'HPh'		=> 3.72506609848824E-14,
																							'hh'		=> 3.72506609848824E-14,
																							'Wh'		=> 2.77778049754611E-11,
																							'wh'		=> 2.77778049754611E-11,
																							'flb'		=> 2.37304336254586E-06,
																							'BTU'		=> 9.47815522922962E-11,
																							'btu'		=> 9.47815522922962E-11
																						),
																		'c'		=> array(	'J'			=> 4.18399101363672E+00,
																							'e'			=> 4.18398900257312E+07,
																							'c'			=> 1.0,
																							'cal'		=> 9.99330315287563E-01,
																							'eV'		=> 2.61142000000000E+19,
																							'ev'		=> 2.61142000000000E+19,
																							'HPh'		=> 1.55856355899327E-06,
																							'hh'		=> 1.55856355899327E-06,
																							'Wh'		=> 1.16222030532950E-03,
																							'wh'		=> 1.16222030532950E-03,
																							'flb'		=> 9.92878733152102E+01,
																							'BTU'		=> 3.96564972437776E-03,
																							'btu'		=> 3.96564972437776E-03
																						),
																		'cal'	=> array(	'J'			=> 4.18679484613929E+00,
																							'e'			=> 4.18679283372801E+07,
																							'c'			=> 1.00067013349059E+00,
																							'cal'		=> 1.0,
																							'eV'		=> 2.61317000000000E+19,
																							'ev'		=> 2.61317000000000E+19,
																							'HPh'		=> 1.55960800463137E-06,
																							'hh'		=> 1.55960800463137E-06,
																							'Wh'		=> 1.16299914807955E-03,
																							'wh'		=> 1.16299914807955E-03,
																							'flb'		=> 9.93544094443283E+01,
																							'BTU'		=> 3.96830723907002E-03,
																							'btu'		=> 3.96830723907002E-03
																						),
																		'eV'	=> array(	'J'			=> 1.60219000146921E-19,
																							'e'			=> 1.60218923136574E-12,
																							'c'			=> 3.82933423195043E-20,
																							'cal'		=> 3.82676978535648E-20,
																							'eV'		=> 1.0,
																							'ev'		=> 1.0,
																							'HPh'		=> 5.96826078912344E-26,
																							'hh'		=> 5.96826078912344E-26,
																							'Wh'		=> 4.45053000026614E-23,
																							'wh'		=> 4.45053000026614E-23,
																							'flb'		=> 3.80206452103492E-18,
																							'BTU'		=> 1.51857982414846E-22,
																							'btu'		=> 1.51857982414846E-22
																						),
																		'ev'	=> array(	'J'			=> 1.60219000146921E-19,
																							'e'			=> 1.60218923136574E-12,
																							'c'			=> 3.82933423195043E-20,
																							'cal'		=> 3.82676978535648E-20,
																							'eV'		=> 1.0,
																							'ev'		=> 1.0,
																							'HPh'		=> 5.96826078912344E-26,
																							'hh'		=> 5.96826078912344E-26,
																							'Wh'		=> 4.45053000026614E-23,
																							'wh'		=> 4.45053000026614E-23,
																							'flb'		=> 3.80206452103492E-18,
																							'BTU'		=> 1.51857982414846E-22,
																							'btu'		=> 1.51857982414846E-22
																						),
																		'HPh'	=> array(	'J'			=> 2.68451741316170E+06,
																							'e'			=> 2.68451612283024E+13,
																							'c'			=> 6.41616438565991E+05,
																							'cal'		=> 6.41186757845835E+05,
																							'eV'		=> 1.67553000000000E+25,
																							'ev'		=> 1.67553000000000E+25,
																							'HPh'		=> 1.0,
																							'hh'		=> 1.0,
																							'Wh'		=> 7.45699653134593E+02,
																							'wh'		=> 7.45699653134593E+02,
																							'flb'		=> 6.37047316692964E+07,
																							'BTU'		=> 2.54442605275546E+03,
																							'btu'		=> 2.54442605275546E+03
																						),
																		'hh'	=> array(	'J'			=> 2.68451741316170E+06,
																							'e'			=> 2.68451612283024E+13,
																							'c'			=> 6.41616438565991E+05,
																							'cal'		=> 6.41186757845835E+05,
																							'eV'		=> 1.67553000000000E+25,
																							'ev'		=> 1.67553000000000E+25,
																							'HPh'		=> 1.0,
																							'hh'		=> 1.0,
																							'Wh'		=> 7.45699653134593E+02,
																							'wh'		=> 7.45699653134593E+02,
																							'flb'		=> 6.37047316692964E+07,
																							'BTU'		=> 2.54442605275546E+03,
																							'btu'		=> 2.54442605275546E+03
																						),
																		'Wh'	=> array(	'J'			=> 3.59999820554720E+03,
																							'e'			=> 3.59999647518369E+10,
																							'c'			=> 8.60422069219046E+02,
																							'cal'		=> 8.59845857713046E+02,
																							'eV'		=> 2.24692340000000E+22,
																							'ev'		=> 2.24692340000000E+22,
																							'HPh'		=> 1.34102248243839E-03,
																							'hh'		=> 1.34102248243839E-03,
																							'Wh'		=> 1.0,
																							'wh'		=> 1.0,
																							'flb'		=> 8.54294774062316E+04,
																							'BTU'		=> 3.41213254164705E+00,
																							'btu'		=> 3.41213254164705E+00
																						),
																		'wh'	=> array(	'J'			=> 3.59999820554720E+03,
																							'e'			=> 3.59999647518369E+10,
																							'c'			=> 8.60422069219046E+02,
																							'cal'		=> 8.59845857713046E+02,
																							'eV'		=> 2.24692340000000E+22,
																							'ev'		=> 2.24692340000000E+22,
																							'HPh'		=> 1.34102248243839E-03,
																							'hh'		=> 1.34102248243839E-03,
																							'Wh'		=> 1.0,
																							'wh'		=> 1.0,
																							'flb'		=> 8.54294774062316E+04,
																							'BTU'		=> 3.41213254164705E+00,
																							'btu'		=> 3.41213254164705E+00
																						),
																		'flb'	=> array(	'J'			=> 4.21400003236424E-02,
																							'e'			=> 4.21399800687660E+05,
																							'c'			=> 1.00717234301644E-02,
																							'cal'		=> 1.00649785509554E-02,
																							'eV'		=> 2.63015000000000E+17,
																							'ev'		=> 2.63015000000000E+17,
																							'HPh'		=> 1.56974211145130E-08,
																							'hh'		=> 1.56974211145130E-08,
																							'Wh'		=> 1.17055614802000E-05,
																							'wh'		=> 1.17055614802000E-05,
																							'flb'		=> 1.0,
																							'BTU'		=> 3.99409272448406E-05,
																							'btu'		=> 3.99409272448406E-05
																						),
																		'BTU'	=> array(	'J'			=> 1.05505813786749E+03,
																							'e'			=> 1.05505763074665E+10,
																							'c'			=> 2.52165488508168E+02,
																							'cal'		=> 2.51996617135510E+02,
																							'eV'		=> 6.58510000000000E+21,
																							'ev'		=> 6.58510000000000E+21,
																							'HPh'		=> 3.93015941224568E-04,
																							'hh'		=> 3.93015941224568E-04,
																							'Wh'		=> 2.93071851047526E-01,
																							'wh'		=> 2.93071851047526E-01,
																							'flb'		=> 2.50369750774671E+04,
																							'BTU'		=> 1.0,
																							'btu'		=> 1.0,
																						),
																		'btu'	=> array(	'J'			=> 1.05505813786749E+03,
																							'e'			=> 1.05505763074665E+10,
																							'c'			=> 2.52165488508168E+02,
																							'cal'		=> 2.51996617135510E+02,
																							'eV'		=> 6.58510000000000E+21,
																							'ev'		=> 6.58510000000000E+21,
																							'HPh'		=> 3.93015941224568E-04,
																							'hh'		=> 3.93015941224568E-04,
																							'Wh'		=> 2.93071851047526E-01,
																							'wh'		=> 2.93071851047526E-01,
																							'flb'		=> 2.50369750774671E+04,
																							'BTU'		=> 1.0,
																							'btu'		=> 1.0,
																						)
																	),
												'Power'		=> array(	'HP'	=> array(	'HP'		=> 1.0,
																							'h'			=> 1.0,
																							'W'			=> 7.45701000000000E+02,
																							'w'			=> 7.45701000000000E+02
																						),
																		'h'		=> array(	'HP'		=> 1.0,
																							'h'			=> 1.0,
																							'W'			=> 7.45701000000000E+02,
																							'w'			=> 7.45701000000000E+02
																						),
																		'W'		=> array(	'HP'		=> 1.34102006031908E-03,
																							'h'			=> 1.34102006031908E-03,
																							'W'			=> 1.0,
																							'w'			=> 1.0
																						),
																		'w'		=> array(	'HP'		=> 1.34102006031908E-03,
																							'h'			=> 1.34102006031908E-03,
																							'W'			=> 1.0,
																							'w'			=> 1.0
																						)
																	),
												'Magnetism'	=> array(	'T'		=> array(	'T'			=> 1.0,
																							'ga'		=> 10000.0
																						),
																		'ga'	=> array(	'T'			=> 0.0001,
																							'ga'		=> 1.0
																						)
																	),
												'Liquid'	=> array(	'tsp'	=> array(	'tsp'		=> 1.0,
																							'tbs'		=> 3.33333333333333E-01,
																							'oz'		=> 1.66666666666667E-01,
																							'cup'		=> 2.08333333333333E-02,
																							'pt'		=> 1.04166666666667E-02,
																							'us_pt'		=> 1.04166666666667E-02,
																							'uk_pt'		=> 8.67558516821960E-03,
																							'qt'		=> 5.20833333333333E-03,
																							'gal'		=> 1.30208333333333E-03,
																							'l'			=> 4.92999408400710E-03,
																							'lt'		=> 4.92999408400710E-03
																						),
																		'tbs'	=> array(	'tsp'		=> 3.00000000000000E+00,
																							'tbs'		=> 1.0,
																							'oz'		=> 5.00000000000000E-01,
																							'cup'		=> 6.25000000000000E-02,
																							'pt'		=> 3.12500000000000E-02,
																							'us_pt'		=> 3.12500000000000E-02,
																							'uk_pt'		=> 2.60267555046588E-02,
																							'qt'		=> 1.56250000000000E-02,
																							'gal'		=> 3.90625000000000E-03,
																							'l'			=> 1.47899822520213E-02,
																							'lt'		=> 1.47899822520213E-02
																						),
																		'oz'	=> array(	'tsp'		=> 6.00000000000000E+00,
																							'tbs'		=> 2.00000000000000E+00,
																							'oz'		=> 1.0,
																							'cup'		=> 1.25000000000000E-01,
																							'pt'		=> 6.25000000000000E-02,
																							'us_pt'		=> 6.25000000000000E-02,
																							'uk_pt'		=> 5.20535110093176E-02,
																							'qt'		=> 3.12500000000000E-02,
																							'gal'		=> 7.81250000000000E-03,
																							'l'			=> 2.95799645040426E-02,
																							'lt'		=> 2.95799645040426E-02
																						),
																		'cup'	=> array(	'tsp'		=> 4.80000000000000E+01,
																							'tbs'		=> 1.60000000000000E+01,
																							'oz'		=> 8.00000000000000E+00,
																							'cup'		=> 1.0,
																							'pt'		=> 5.00000000000000E-01,
																							'us_pt'		=> 5.00000000000000E-01,
																							'uk_pt'		=> 4.16428088074541E-01,
																							'qt'		=> 2.50000000000000E-01,
																							'gal'		=> 6.25000000000000E-02,
																							'l'			=> 2.36639716032341E-01,
																							'lt'		=> 2.36639716032341E-01
																						),
																		'pt'	=> array(	'tsp'		=> 9.60000000000000E+01,
																							'tbs'		=> 3.20000000000000E+01,
																							'oz'		=> 1.60000000000000E+01,
																							'cup'		=> 2.00000000000000E+00,
																							'pt'		=> 1.0,
																							'us_pt'		=> 1.0,
																							'uk_pt'		=> 8.32856176149081E-01,
																							'qt'		=> 5.00000000000000E-01,
																							'gal'		=> 1.25000000000000E-01,
																							'l'			=> 4.73279432064682E-01,
																							'lt'		=> 4.73279432064682E-01
																						),
																		'us_pt'	=> array(	'tsp'		=> 9.60000000000000E+01,
																							'tbs'		=> 3.20000000000000E+01,
																							'oz'		=> 1.60000000000000E+01,
																							'cup'		=> 2.00000000000000E+00,
																							'pt'		=> 1.0,
																							'us_pt'		=> 1.0,
																							'uk_pt'		=> 8.32856176149081E-01,
																							'qt'		=> 5.00000000000000E-01,
																							'gal'		=> 1.25000000000000E-01,
																							'l'			=> 4.73279432064682E-01,
																							'lt'		=> 4.73279432064682E-01
																						),
																		'uk_pt'	=> array(	'tsp'		=> 1.15266000000000E+02,
																							'tbs'		=> 3.84220000000000E+01,
																							'oz'		=> 1.92110000000000E+01,
																							'cup'		=> 2.40137500000000E+00,
																							'pt'		=> 1.20068750000000E+00,
																							'us_pt'		=> 1.20068750000000E+00,
																							'uk_pt'		=> 1.0,
																							'qt'		=> 6.00343750000000E-01,
																							'gal'		=> 1.50085937500000E-01,
																							'l'			=> 5.68260698087162E-01,
																							'lt'		=> 5.68260698087162E-01
																						),
																		'qt'	=> array(	'tsp'		=> 1.92000000000000E+02,
																							'tbs'		=> 6.40000000000000E+01,
																							'oz'		=> 3.20000000000000E+01,
																							'cup'		=> 4.00000000000000E+00,
																							'pt'		=> 2.00000000000000E+00,
																							'us_pt'		=> 2.00000000000000E+00,
																							'uk_pt'		=> 1.66571235229816E+00,
																							'qt'		=> 1.0,
																							'gal'		=> 2.50000000000000E-01,
																							'l'			=> 9.46558864129363E-01,
																							'lt'		=> 9.46558864129363E-01
																						),
																		'gal'	=> array(	'tsp'		=> 7.68000000000000E+02,
																							'tbs'		=> 2.56000000000000E+02,
																							'oz'		=> 1.28000000000000E+02,
																							'cup'		=> 1.60000000000000E+01,
																							'pt'		=> 8.00000000000000E+00,
																							'us_pt'		=> 8.00000000000000E+00,
																							'uk_pt'		=> 6.66284940919265E+00,
																							'qt'		=> 4.00000000000000E+00,
																							'gal'		=> 1.0,
																							'l'			=> 3.78623545651745E+00,
																							'lt'		=> 3.78623545651745E+00
																						),
																		'l'		=> array(	'tsp'		=> 2.02840000000000E+02,
																							'tbs'		=> 6.76133333333333E+01,
																							'oz'		=> 3.38066666666667E+01,
																							'cup'		=> 4.22583333333333E+00,
																							'pt'		=> 2.11291666666667E+00,
																							'us_pt'		=> 2.11291666666667E+00,
																							'uk_pt'		=> 1.75975569552166E+00,
																							'qt'		=> 1.05645833333333E+00,
																							'gal'		=> 2.64114583333333E-01,
																							'l'			=> 1.0,
																							'lt'		=> 1.0
																						),
																		'lt'	=> array(	'tsp'		=> 2.02840000000000E+02,
																							'tbs'		=> 6.76133333333333E+01,
																							'oz'		=> 3.38066666666667E+01,
																							'cup'		=> 4.22583333333333E+00,
																							'pt'		=> 2.11291666666667E+00,
																							'us_pt'		=> 2.11291666666667E+00,
																							'uk_pt'		=> 1.75975569552166E+00,
																							'qt'		=> 1.05645833333333E+00,
																							'gal'		=> 2.64114583333333E-01,
																							'l'			=> 1.0,
																							'lt'		=> 1.0
																						)
																	)
											);


	/**
	 * _parseComplex
	 *
	 * Parses a complex number into its real and imaginary parts, and an I or J suffix
	 *
	 * @param	string		$complexNumber	The complex number
	 * @return	string[]	Indexed on "real", "imaginary" and "suffix"
	 */
	public static function _parseComplex($complexNumber) {
		$workString = (string) $complexNumber;

		$realNumber = $imaginary = 0;
		//	Extract the suffix, if there is one
		$suffix = substr($workString,-1);
		if (!is_numeric($suffix)) {
			$workString = substr($workString,0,-1);
		} else {
			$suffix = '';
		}

		//	Split the input into its Real and Imaginary components
		$leadingSign = 0;
		if (strlen($workString) > 0) {
			$leadingSign = (($workString{0} == '+') || ($workString{0} == '-')) ? 1 : 0;
		}
		$power = '';
		$realNumber = strtok($workString, '+-');
		if (strtoupper(substr($realNumber,-1)) == 'E') {
			$power = strtok('+-');
			++$leadingSign;
		}

		$realNumber = substr($workString,0,strlen($realNumber)+strlen($power)+$leadingSign);

		if ($suffix != '') {
			$imaginary = substr($workString,strlen($realNumber));

			if (($imaginary == '') && (($realNumber == '') || ($realNumber == '+') || ($realNumber == '-'))) {
				$imaginary = $realNumber.'1';
				$realNumber = '0';
			} else if ($imaginary == '') {
				$imaginary = $realNumber;
				$realNumber = '0';
			} elseif (($imaginary == '+') || ($imaginary == '-')) {
				$imaginary .= '1';
			}
		}

		return array( 'real'		=> $realNumber,
					  'imaginary'	=> $imaginary,
					  'suffix'		=> $suffix
					);
	}	//	function _parseComplex()


	/**
	 * Cleans the leading characters in a complex number string
	 *
	 * @param	string		$complexNumber	The complex number to clean
	 * @return	string		The "cleaned" complex number
	 */
	private static function _cleanComplex($complexNumber) {
		if ($complexNumber{0} == '+') $complexNumber = substr($complexNumber,1);
		if ($complexNumber{0} == '0') $complexNumber = substr($complexNumber,1);
		if ($complexNumber{0} == '.') $complexNumber = '0'.$complexNumber;
		if ($complexNumber{0} == '+') $complexNumber = substr($complexNumber,1);
		return $complexNumber;
	}

	/**
	 * Formats a number base string value with leading zeroes
	 *
	 * @param	string		$xVal		The "number" to pad
	 * @param	integer		$places		The length that we want to pad this value
	 * @return	string		The padded "number"
	 */
	private static function _nbrConversionFormat($xVal, $places) {
		if (!is_null($places)) {
			if (strlen($xVal) <= $places) {
				return substr(str_pad($xVal, $places, '0', STR_PAD_LEFT), -10);
			} else {
				return PHPExcel_Calculation_Functions::NaN();
			}
		}

		return substr($xVal, -10);
	}	//	function _nbrConversionFormat()

	/**
	 *	BESSELI
	 *
	 *	Returns the modified Bessel function In(x), which is equivalent to the Bessel function evaluated
	 *		for purely imaginary arguments
	 *
	 *	Excel Function:
	 *		BESSELI(x,ord)
	 *
	 *	@access	public
	 *	@category Engineering Functions
	 *	@param	float		$x		The value at which to evaluate the function.
	 *								If x is nonnumeric, BESSELI returns the #VALUE! error value.
	 *	@param	integer		$ord	The order of the Bessel function.
	 *								If ord is not an integer, it is truncated.
	 *								If $ord is nonnumeric, BESSELI returns the #VALUE! error value.
	 *								If $ord < 0, BESSELI returns the #NUM! error value.
	 *	@return	float
	 *
	 */
	public static function BESSELI($x, $ord) {
		$x	= (is_null($x))	? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$ord	= (is_null($ord))	? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($ord);

		if ((is_numeric($x)) && (is_numeric($ord))) {
			$ord	= floor($ord);
			if ($ord < 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			if (abs($x) <= 30) {
				$fResult = $fTerm = pow($x / 2, $ord) / PHPExcel_Calculation_MathTrig::FACT($ord);
				$ordK = 1;
				$fSqrX = ($x * $x) / 4;
				do {
					$fTerm *= $fSqrX;
					$fTerm /= ($ordK * ($ordK + $ord));
					$fResult += $fTerm;
				} while ((abs($fTerm) > 1e-12) && (++$ordK < 100));
			} else {
				$f_2_PI = 2 * M_PI;

				$fXAbs = abs($x);
				$fResult = exp($fXAbs) / sqrt($f_2_PI * $fXAbs);
				if (($ord & 1) && ($x < 0)) {
					$fResult = -$fResult;
				}
			}
			return (is_nan($fResult)) ? PHPExcel_Calculation_Functions::NaN() : $fResult;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function BESSELI()


	/**
	 *	BESSELJ
	 *
	 *	Returns the Bessel function
	 *
	 *	Excel Function:
	 *		BESSELJ(x,ord)
	 *
	 *	@access	public
	 *	@category Engineering Functions
	 *	@param	float		$x		The value at which to evaluate the function.
	 *								If x is nonnumeric, BESSELJ returns the #VALUE! error value.
	 *	@param	integer		$ord	The order of the Bessel function. If n is not an integer, it is truncated.
	 *								If $ord is nonnumeric, BESSELJ returns the #VALUE! error value.
	 *								If $ord < 0, BESSELJ returns the #NUM! error value.
	 *	@return	float
	 *
	 */
	public static function BESSELJ($x, $ord) {
		$x	= (is_null($x))	? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$ord	= (is_null($ord))	? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($ord);

		if ((is_numeric($x)) && (is_numeric($ord))) {
			$ord	= floor($ord);
			if ($ord < 0) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			$fResult = 0;
			if (abs($x) <= 30) {
				$fResult = $fTerm = pow($x / 2, $ord) / PHPExcel_Calculation_MathTrig::FACT($ord);
				$ordK = 1;
				$fSqrX = ($x * $x) / -4;
				do {
					$fTerm *= $fSqrX;
					$fTerm /= ($ordK * ($ordK + $ord));
					$fResult += $fTerm;
				} while ((abs($fTerm) > 1e-12) && (++$ordK < 100));
			} else {
				$f_PI_DIV_2 = M_PI / 2;
				$f_PI_DIV_4 = M_PI / 4;

				$fXAbs = abs($x);
				$fResult = sqrt(M_2DIVPI / $fXAbs) * cos($fXAbs - $ord * $f_PI_DIV_2 - $f_PI_DIV_4);
				if (($ord & 1) && ($x < 0)) {
					$fResult = -$fResult;
				}
			}
			return (is_nan($fResult)) ? PHPExcel_Calculation_Functions::NaN() : $fResult;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function BESSELJ()


	private static function _Besselk0($fNum) {
		if ($fNum <= 2) {
			$fNum2 = $fNum * 0.5;
			$y = ($fNum2 * $fNum2);
			$fRet = -log($fNum2) * self::BESSELI($fNum, 0) +
					(-0.57721566 + $y * (0.42278420 + $y * (0.23069756 + $y * (0.3488590e-1 + $y * (0.262698e-2 + $y *
					(0.10750e-3 + $y * 0.74e-5))))));
		} else {
			$y = 2 / $fNum;
			$fRet = exp(-$fNum) / sqrt($fNum) *
					(1.25331414 + $y * (-0.7832358e-1 + $y * (0.2189568e-1 + $y * (-0.1062446e-1 + $y *
					(0.587872e-2 + $y * (-0.251540e-2 + $y * 0.53208e-3))))));
		}
		return $fRet;
	}	//	function _Besselk0()


	private static function _Besselk1($fNum) {
		if ($fNum <= 2) {
			$fNum2 = $fNum * 0.5;
			$y = ($fNum2 * $fNum2);
			$fRet = log($fNum2) * self::BESSELI($fNum, 1) +
					(1 + $y * (0.15443144 + $y * (-0.67278579 + $y * (-0.18156897 + $y * (-0.1919402e-1 + $y *
					(-0.110404e-2 + $y * (-0.4686e-4))))))) / $fNum;
		} else {
			$y = 2 / $fNum;
			$fRet = exp(-$fNum) / sqrt($fNum) *
					(1.25331414 + $y * (0.23498619 + $y * (-0.3655620e-1 + $y * (0.1504268e-1 + $y * (-0.780353e-2 + $y *
					(0.325614e-2 + $y * (-0.68245e-3)))))));
		}
		return $fRet;
	}	//	function _Besselk1()


	/**
	 *	BESSELK
	 *
	 *	Returns the modified Bessel function Kn(x), which is equivalent to the Bessel functions evaluated
	 *		for purely imaginary arguments.
	 *
	 *	Excel Function:
	 *		BESSELK(x,ord)
	 *
	 *	@access	public
	 *	@category Engineering Functions
	 *	@param	float		$x		The value at which to evaluate the function.
	 *								If x is nonnumeric, BESSELK returns the #VALUE! error value.
	 *	@param	integer		$ord	The order of the Bessel function. If n is not an integer, it is truncated.
	 *								If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
	 *								If $ord < 0, BESSELK returns the #NUM! error value.
	 *	@return	float
	 *
	 */
	public static function BESSELK($x, $ord) {
		$x		= (is_null($x))		? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$ord	= (is_null($ord))	? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($ord);

		if ((is_numeric($x)) && (is_numeric($ord))) {
			if (($ord < 0) || ($x == 0.0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			switch(floor($ord)) {
				case 0 :	return self::_Besselk0($x);
							break;
				case 1 :	return self::_Besselk1($x);
							break;
				default :	$fTox	= 2 / $x;
							$fBkm	= self::_Besselk0($x);
							$fBk	= self::_Besselk1($x);
							for ($n = 1; $n < $ord; ++$n) {
								$fBkp	= $fBkm + $n * $fTox * $fBk;
								$fBkm	= $fBk;
								$fBk	= $fBkp;
							}
			}
			return (is_nan($fBk)) ? PHPExcel_Calculation_Functions::NaN() : $fBk;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function BESSELK()


	private static function _Bessely0($fNum) {
		if ($fNum < 8.0) {
			$y = ($fNum * $fNum);
			$f1 = -2957821389.0 + $y * (7062834065.0 + $y * (-512359803.6 + $y * (10879881.29 + $y * (-86327.92757 + $y * 228.4622733))));
			$f2 = 40076544269.0 + $y * (745249964.8 + $y * (7189466.438 + $y * (47447.26470 + $y * (226.1030244 + $y))));
			$fRet = $f1 / $f2 + 0.636619772 * self::BESSELJ($fNum, 0) * log($fNum);
		} else {
			$z = 8.0 / $fNum;
			$y = ($z * $z);
			$xx = $fNum - 0.785398164;
			$f1 = 1 + $y * (-0.1098628627e-2 + $y * (0.2734510407e-4 + $y * (-0.2073370639e-5 + $y * 0.2093887211e-6)));
			$f2 = -0.1562499995e-1 + $y * (0.1430488765e-3 + $y * (-0.6911147651e-5 + $y * (0.7621095161e-6 + $y * (-0.934945152e-7))));
			$fRet = sqrt(0.636619772 / $fNum) * (sin($xx) * $f1 + $z * cos($xx) * $f2);
		}
		return $fRet;
	}	//	function _Bessely0()


	private static function _Bessely1($fNum) {
		if ($fNum < 8.0) {
			$y = ($fNum * $fNum);
			$f1 = $fNum * (-0.4900604943e13 + $y * (0.1275274390e13 + $y * (-0.5153438139e11 + $y * (0.7349264551e9 + $y *
				(-0.4237922726e7 + $y * 0.8511937935e4)))));
			$f2 = 0.2499580570e14 + $y * (0.4244419664e12 + $y * (0.3733650367e10 + $y * (0.2245904002e8 + $y *
				(0.1020426050e6 + $y * (0.3549632885e3 + $y)))));
			$fRet = $f1 / $f2 + 0.636619772 * ( self::BESSELJ($fNum, 1) * log($fNum) - 1 / $fNum);
		} else {
			$fRet = sqrt(0.636619772 / $fNum) * sin($fNum - 2.356194491);
		}
		return $fRet;
	}	//	function _Bessely1()


	/**
	 *	BESSELY
	 *
	 *	Returns the Bessel function, which is also called the Weber function or the Neumann function.
	 *
	 *	Excel Function:
	 *		BESSELY(x,ord)
	 *
	 *	@access	public
	 *	@category Engineering Functions
	 *	@param	float		$x		The value at which to evaluate the function.
	 *								If x is nonnumeric, BESSELK returns the #VALUE! error value.
	 *	@param	integer		$ord	The order of the Bessel function. If n is not an integer, it is truncated.
	 *								If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
	 *								If $ord < 0, BESSELK returns the #NUM! error value.
	 *
	 *	@return	float
	 */
	public static function BESSELY($x, $ord) {
		$x		= (is_null($x))		? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$ord	= (is_null($ord))	? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($ord);

		if ((is_numeric($x)) && (is_numeric($ord))) {
			if (($ord < 0) || ($x == 0.0)) {
				return PHPExcel_Calculation_Functions::NaN();
			}

			switch(floor($ord)) {
				case 0 :	return self::_Bessely0($x);
							break;
				case 1 :	return self::_Bessely1($x);
							break;
				default:	$fTox	= 2 / $x;
							$fBym	= self::_Bessely0($x);
							$fBy	= self::_Bessely1($x);
							for ($n = 1; $n < $ord; ++$n) {
								$fByp	= $n * $fTox * $fBy - $fBym;
								$fBym	= $fBy;
								$fBy	= $fByp;
							}
			}
			return (is_nan($fBy)) ? PHPExcel_Calculation_Functions::NaN() : $fBy;
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function BESSELY()


	/**
	 * BINTODEC
	 *
	 * Return a binary value as decimal.
	 *
	 * Excel Function:
	 *		BIN2DEC(x)
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The binary number (as a string) that you want to convert. The number
	 *								cannot contain more than 10 characters (10 bits). The most significant
	 *								bit of number is the sign bit. The remaining 9 bits are magnitude bits.
	 *								Negative numbers are represented using two's-complement notation.
	 *								If number is not a valid binary number, or if number contains more than
	 *								10 characters (10 bits), BIN2DEC returns the #NUM! error value.
	 * @return	string
	 */
	public static function BINTODEC($x) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);

		if (is_bool($x)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$x = (int) $x;
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
			$x = floor($x);
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[01]/',$x,$out)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if (strlen($x) > 10) {
			return PHPExcel_Calculation_Functions::NaN();
		} elseif (strlen($x) == 10) {
			//	Two's Complement
			$x = substr($x,-9);
			return '-'.(512-bindec($x));
		}
		return bindec($x);
	}	//	function BINTODEC()


	/**
	 * BINTOHEX
	 *
	 * Return a binary value as hex.
	 *
	 * Excel Function:
	 *		BIN2HEX(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The binary number (as a string) that you want to convert. The number
	 *								cannot contain more than 10 characters (10 bits). The most significant
	 *								bit of number is the sign bit. The remaining 9 bits are magnitude bits.
	 *								Negative numbers are represented using two's-complement notation.
	 *								If number is not a valid binary number, or if number contains more than
	 *								10 characters (10 bits), BIN2HEX returns the #NUM! error value.
	 * @param	integer		$places	The number of characters to use. If places is omitted, BIN2HEX uses the
	 *								minimum number of characters necessary. Places is useful for padding the
	 *								return value with leading 0s (zeros).
	 *								If places is not an integer, it is truncated.
	 *								If places is nonnumeric, BIN2HEX returns the #VALUE! error value.
	 *								If places is negative, BIN2HEX returns the #NUM! error value.
	 * @return	string
	 */
	public static function BINTOHEX($x, $places=NULL) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$x = (int) $x;
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
			$x = floor($x);
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[01]/',$x,$out)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if (strlen($x) > 10) {
			return PHPExcel_Calculation_Functions::NaN();
		} elseif (strlen($x) == 10) {
			//	Two's Complement
			return str_repeat('F',8).substr(strtoupper(dechex(bindec(substr($x,-9)))),-2);
		}
		$hexVal = (string) strtoupper(dechex(bindec($x)));

		return self::_nbrConversionFormat($hexVal,$places);
	}	//	function BINTOHEX()


	/**
	 * BINTOOCT
	 *
	 * Return a binary value as octal.
	 *
	 * Excel Function:
	 *		BIN2OCT(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The binary number (as a string) that you want to convert. The number
	 *								cannot contain more than 10 characters (10 bits). The most significant
	 *								bit of number is the sign bit. The remaining 9 bits are magnitude bits.
	 *								Negative numbers are represented using two's-complement notation.
	 *								If number is not a valid binary number, or if number contains more than
	 *								10 characters (10 bits), BIN2OCT returns the #NUM! error value.
	 * @param	integer		$places	The number of characters to use. If places is omitted, BIN2OCT uses the
	 *								minimum number of characters necessary. Places is useful for padding the
	 *								return value with leading 0s (zeros).
	 *								If places is not an integer, it is truncated.
	 *								If places is nonnumeric, BIN2OCT returns the #VALUE! error value.
	 *								If places is negative, BIN2OCT returns the #NUM! error value.
	 * @return	string
	 */
	public static function BINTOOCT($x, $places=NULL) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$x = (int) $x;
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
			$x = floor($x);
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[01]/',$x,$out)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if (strlen($x) > 10) {
			return PHPExcel_Calculation_Functions::NaN();
		} elseif (strlen($x) == 10) {
			//	Two's Complement
			return str_repeat('7',7).substr(strtoupper(decoct(bindec(substr($x,-9)))),-3);
		}
		$octVal = (string) decoct(bindec($x));

		return self::_nbrConversionFormat($octVal,$places);
	}	//	function BINTOOCT()


	/**
	 * DECTOBIN
	 *
	 * Return a decimal value as binary.
	 *
	 * Excel Function:
	 *		DEC2BIN(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The decimal integer you want to convert. If number is negative,
	 *								valid place values are ignored and DEC2BIN returns a 10-character
	 *								(10-bit) binary number in which the most significant bit is the sign
	 *								bit. The remaining 9 bits are magnitude bits. Negative numbers are
	 *								represented using two's-complement notation.
	 *								If number < -512 or if number > 511, DEC2BIN returns the #NUM! error
	 *								value.
	 *								If number is nonnumeric, DEC2BIN returns the #VALUE! error value.
	 *								If DEC2BIN requires more than places characters, it returns the #NUM!
	 *								error value.
	 * @param	integer		$places	The number of characters to use. If places is omitted, DEC2BIN uses
	 *								the minimum number of characters necessary. Places is useful for
	 *								padding the return value with leading 0s (zeros).
	 *								If places is not an integer, it is truncated.
	 *								If places is nonnumeric, DEC2BIN returns the #VALUE! error value.
	 *								If places is zero or negative, DEC2BIN returns the #NUM! error value.
	 * @return	string
	 */
	public static function DECTOBIN($x, $places=NULL) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$x = (int) $x;
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[-0123456789.]/',$x,$out)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) floor($x);
		$r = decbin($x);
		if (strlen($r) == 32) {
			//	Two's Complement
			$r = substr($r,-10);
		} elseif (strlen($r) > 11) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		return self::_nbrConversionFormat($r,$places);
	}	//	function DECTOBIN()


	/**
	 * DECTOHEX
	 *
	 * Return a decimal value as hex.
	 *
	 * Excel Function:
	 *		DEC2HEX(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The decimal integer you want to convert. If number is negative,
	 *								places is ignored and DEC2HEX returns a 10-character (40-bit)
	 *								hexadecimal number in which the most significant bit is the sign
	 *								bit. The remaining 39 bits are magnitude bits. Negative numbers
	 *								are represented using two's-complement notation.
	 *								If number < -549,755,813,888 or if number > 549,755,813,887,
	 *								DEC2HEX returns the #NUM! error value.
	 *								If number is nonnumeric, DEC2HEX returns the #VALUE! error value.
	 *								If DEC2HEX requires more than places characters, it returns the
	 *								#NUM! error value.
	 * @param	integer		$places	The number of characters to use. If places is omitted, DEC2HEX uses
	 *								the minimum number of characters necessary. Places is useful for
	 *								padding the return value with leading 0s (zeros).
	 *								If places is not an integer, it is truncated.
	 *								If places is nonnumeric, DEC2HEX returns the #VALUE! error value.
	 *								If places is zero or negative, DEC2HEX returns the #NUM! error value.
	 * @return	string
	 */
	public static function DECTOHEX($x, $places=null) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$x = (int) $x;
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[-0123456789.]/',$x,$out)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) floor($x);
		$r = strtoupper(dechex($x));
		if (strlen($r) == 8) {
			//	Two's Complement
			$r = 'FF'.$r;
		}

		return self::_nbrConversionFormat($r,$places);
	}	//	function DECTOHEX()


	/**
	 * DECTOOCT
	 *
	 * Return an decimal value as octal.
	 *
	 * Excel Function:
	 *		DEC2OCT(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The decimal integer you want to convert. If number is negative,
	 *								places is ignored and DEC2OCT returns a 10-character (30-bit)
	 *								octal number in which the most significant bit is the sign bit.
	 *								The remaining 29 bits are magnitude bits. Negative numbers are
	 *								represented using two's-complement notation.
	 *								If number < -536,870,912 or if number > 536,870,911, DEC2OCT
	 *								returns the #NUM! error value.
	 *								If number is nonnumeric, DEC2OCT returns the #VALUE! error value.
	 *								If DEC2OCT requires more than places characters, it returns the
	 *								#NUM! error value.
	 * @param	integer		$places	The number of characters to use. If places is omitted, DEC2OCT uses
	 *								the minimum number of characters necessary. Places is useful for
	 *								padding the return value with leading 0s (zeros).
	 *								If places is not an integer, it is truncated.
	 *								If places is nonnumeric, DEC2OCT returns the #VALUE! error value.
	 *								If places is zero or negative, DEC2OCT returns the #NUM! error value.
	 * @return	string
	 */
	public static function DECTOOCT($x, $places=null) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$x = (int) $x;
			} else {
				return PHPExcel_Calculation_Functions::VALUE();
			}
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[-0123456789.]/',$x,$out)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) floor($x);
		$r = decoct($x);
		if (strlen($r) == 11) {
			//	Two's Complement
			$r = substr($r,-10);
		}

		return self::_nbrConversionFormat($r,$places);
	}	//	function DECTOOCT()


	/**
	 * HEXTOBIN
	 *
	 * Return a hex value as binary.
	 *
	 * Excel Function:
	 *		HEX2BIN(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x			the hexadecimal number you want to convert. Number cannot
	 *									contain more than 10 characters. The most significant bit of
	 *									number is the sign bit (40th bit from the right). The remaining
	 *									9 bits are magnitude bits. Negative numbers are represented
	 *									using two's-complement notation.
	 *									If number is negative, HEX2BIN ignores places and returns a
	 *									10-character binary number.
	 *									If number is negative, it cannot be less than FFFFFFFE00, and
	 *									if number is positive, it cannot be greater than 1FF.
	 *									If number is not a valid hexadecimal number, HEX2BIN returns
	 *									the #NUM! error value.
	 *									If HEX2BIN requires more than places characters, it returns
	 *									the #NUM! error value.
	 * @param	integer		$places		The number of characters to use. If places is omitted,
	 *									HEX2BIN uses the minimum number of characters necessary. Places
	 *									is useful for padding the return value with leading 0s (zeros).
	 *									If places is not an integer, it is truncated.
	 *									If places is nonnumeric, HEX2BIN returns the #VALUE! error value.
	 *									If places is negative, HEX2BIN returns the #NUM! error value.
	 * @return	string
	 */
	public static function HEXTOBIN($x, $places=null) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/',strtoupper($x),$out)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$binVal = decbin(hexdec($x));

		return substr(self::_nbrConversionFormat($binVal,$places),-10);
	}	//	function HEXTOBIN()


	/**
	 * HEXTODEC
	 *
	 * Return a hex value as decimal.
	 *
	 * Excel Function:
	 *		HEX2DEC(x)
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The hexadecimal number you want to convert. This number cannot
	 *								contain more than 10 characters (40 bits). The most significant
	 *								bit of number is the sign bit. The remaining 39 bits are magnitude
	 *								bits. Negative numbers are represented using two's-complement
	 *								notation.
	 *								If number is not a valid hexadecimal number, HEX2DEC returns the
	 *								#NUM! error value.
	 * @return	string
	 */
	public static function HEXTODEC($x) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);

		if (is_bool($x)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/',strtoupper($x),$out)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		return hexdec($x);
	}	//	function HEXTODEC()


	/**
	 * HEXTOOCT
	 *
	 * Return a hex value as octal.
	 *
	 * Excel Function:
	 *		HEX2OCT(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x			The hexadecimal number you want to convert. Number cannot
	 *									contain more than 10 characters. The most significant bit of
	 *									number is the sign bit. The remaining 39 bits are magnitude
	 *									bits. Negative numbers are represented using two's-complement
	 *									notation.
	 *									If number is negative, HEX2OCT ignores places and returns a
	 *									10-character octal number.
	 *									If number is negative, it cannot be less than FFE0000000, and
	 *									if number is positive, it cannot be greater than 1FFFFFFF.
	 *									If number is not a valid hexadecimal number, HEX2OCT returns
	 *									the #NUM! error value.
	 *									If HEX2OCT requires more than places characters, it returns
	 *									the #NUM! error value.
	 * @param	integer		$places		The number of characters to use. If places is omitted, HEX2OCT
	 *									uses the minimum number of characters necessary. Places is
	 *									useful for padding the return value with leading 0s (zeros).
	 *									If places is not an integer, it is truncated.
	 *									If places is nonnumeric, HEX2OCT returns the #VALUE! error
	 *									value.
	 *									If places is negative, HEX2OCT returns the #NUM! error value.
	 * @return	string
	 */
	public static function HEXTOOCT($x, $places=null) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) $x;
		if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/',strtoupper($x),$out)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$octVal = decoct(hexdec($x));

		return self::_nbrConversionFormat($octVal,$places);
	}	//	function HEXTOOCT()


	/**
	 * OCTTOBIN
	 *
	 * Return an octal value as binary.
	 *
	 * Excel Function:
	 *		OCT2BIN(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x			The octal number you want to convert. Number may not
	 *									contain more than 10 characters. The most significant
	 *									bit of number is the sign bit. The remaining 29 bits
	 *									are magnitude bits. Negative numbers are represented
	 *									using two's-complement notation.
	 *									If number is negative, OCT2BIN ignores places and returns
	 *									a 10-character binary number.
	 *									If number is negative, it cannot be less than 7777777000,
	 *									and if number is positive, it cannot be greater than 777.
	 *									If number is not a valid octal number, OCT2BIN returns
	 *									the #NUM! error value.
	 *									If OCT2BIN requires more than places characters, it
	 *									returns the #NUM! error value.
	 * @param	integer		$places		The number of characters to use. If places is omitted,
	 *									OCT2BIN uses the minimum number of characters necessary.
	 *									Places is useful for padding the return value with
	 *									leading 0s (zeros).
	 *									If places is not an integer, it is truncated.
	 *									If places is nonnumeric, OCT2BIN returns the #VALUE!
	 *									error value.
	 *									If places is negative, OCT2BIN returns the #NUM! error
	 *									value.
	 * @return	string
	 */
	public static function OCTTOBIN($x, $places=null) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) $x;
		if (preg_match_all('/[01234567]/',$x,$out) != strlen($x)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$r = decbin(octdec($x));

		return self::_nbrConversionFormat($r,$places);
	}	//	function OCTTOBIN()


	/**
	 * OCTTODEC
	 *
	 * Return an octal value as decimal.
	 *
	 * Excel Function:
	 *		OCT2DEC(x)
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x		The octal number you want to convert. Number may not contain
	 *								more than 10 octal characters (30 bits). The most significant
	 *								bit of number is the sign bit. The remaining 29 bits are
	 *								magnitude bits. Negative numbers are represented using
	 *								two's-complement notation.
	 *								If number is not a valid octal number, OCT2DEC returns the
	 *								#NUM! error value.
	 * @return	string
	 */
	public static function OCTTODEC($x) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);

		if (is_bool($x)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) $x;
		if (preg_match_all('/[01234567]/',$x,$out) != strlen($x)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		return octdec($x);
	}	//	function OCTTODEC()


	/**
	 * OCTTOHEX
	 *
	 * Return an octal value as hex.
	 *
	 * Excel Function:
	 *		OCT2HEX(x[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$x			The octal number you want to convert. Number may not contain
	 *									more than 10 octal characters (30 bits). The most significant
	 *									bit of number is the sign bit. The remaining 29 bits are
	 *									magnitude bits. Negative numbers are represented using
	 *									two's-complement notation.
	 *									If number is negative, OCT2HEX ignores places and returns a
	 *									10-character hexadecimal number.
	 *									If number is not a valid octal number, OCT2HEX returns the
	 *									#NUM! error value.
	 *									If OCT2HEX requires more than places characters, it returns
	 *									the #NUM! error value.
	 * @param	integer		$places		The number of characters to use. If places is omitted, OCT2HEX
	 *									uses the minimum number of characters necessary. Places is useful
	 *									for padding the return value with leading 0s (zeros).
	 *									If places is not an integer, it is truncated.
	 *									If places is nonnumeric, OCT2HEX returns the #VALUE! error value.
	 *									If places is negative, OCT2HEX returns the #NUM! error value.
	 * @return	string
	 */
	public static function OCTTOHEX($x, $places=null) {
		$x	= PHPExcel_Calculation_Functions::flattenSingleValue($x);
		$places	= PHPExcel_Calculation_Functions::flattenSingleValue($places);

		if (is_bool($x)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$x = (string) $x;
		if (preg_match_all('/[01234567]/',$x,$out) != strlen($x)) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		$hexVal = strtoupper(dechex(octdec($x)));

		return self::_nbrConversionFormat($hexVal,$places);
	}	//	function OCTTOHEX()


	/**
	 * COMPLEX
	 *
	 * Converts real and imaginary coefficients into a complex number of the form x + yi or x + yj.
	 *
	 * Excel Function:
	 *		COMPLEX(realNumber,imaginary[,places])
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	float		$realNumber		The real coefficient of the complex number.
	 * @param	float		$imaginary		The imaginary coefficient of the complex number.
	 * @param	string		$suffix			The suffix for the imaginary component of the complex number.
	 *										If omitted, the suffix is assumed to be "i".
	 * @return	string
	 */
	public static function COMPLEX($realNumber=0.0, $imaginary=0.0, $suffix='i') {
		$realNumber	= (is_null($realNumber))	? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($realNumber);
		$imaginary	= (is_null($imaginary))		? 0.0 :	PHPExcel_Calculation_Functions::flattenSingleValue($imaginary);
		$suffix		= (is_null($suffix))		? 'i' :	PHPExcel_Calculation_Functions::flattenSingleValue($suffix);

		if (((is_numeric($realNumber)) && (is_numeric($imaginary))) &&
			(($suffix == 'i') || ($suffix == 'j') || ($suffix == ''))) {
			$realNumber	= (float) $realNumber;
			$imaginary	= (float) $imaginary;

			if ($suffix == '') $suffix = 'i';
			if ($realNumber == 0.0) {
				if ($imaginary == 0.0) {
					return (string) '0';
				} elseif ($imaginary == 1.0) {
					return (string) $suffix;
				} elseif ($imaginary == -1.0) {
					return (string) '-'.$suffix;
				}
				return (string) $imaginary.$suffix;
			} elseif ($imaginary == 0.0) {
				return (string) $realNumber;
			} elseif ($imaginary == 1.0) {
				return (string) $realNumber.'+'.$suffix;
			} elseif ($imaginary == -1.0) {
				return (string) $realNumber.'-'.$suffix;
			}
			if ($imaginary > 0) { $imaginary = (string) '+'.$imaginary; }
			return (string) $realNumber.$imaginary.$suffix;
		}

		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function COMPLEX()


	/**
	 * IMAGINARY
	 *
	 * Returns the imaginary coefficient of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMAGINARY(complexNumber)
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$complexNumber	The complex number for which you want the imaginary
	 * 										coefficient.
	 * @return	float
	 */
	public static function IMAGINARY($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);
		return $parsedComplex['imaginary'];
	}	//	function IMAGINARY()


	/**
	 * IMREAL
	 *
	 * Returns the real coefficient of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMREAL(complexNumber)
	 *
	 * @access	public
	 * @category Engineering Functions
	 * @param	string		$complexNumber	The complex number for which you want the real coefficient.
	 * @return	float
	 */
	public static function IMREAL($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);
		return $parsedComplex['real'];
	}	//	function IMREAL()


	/**
	 * IMABS
	 *
	 * Returns the absolute value (modulus) of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMABS(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the absolute value.
	 * @return	float
	 */
	public static function IMABS($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		return sqrt(($parsedComplex['real'] * $parsedComplex['real']) + ($parsedComplex['imaginary'] * $parsedComplex['imaginary']));
	}	//	function IMABS()


	/**
	 * IMARGUMENT
	 *
	 * Returns the argument theta of a complex number, i.e. the angle in radians from the real
	 * axis to the representation of the number in polar coordinates.
	 *
	 * Excel Function:
	 *		IMARGUMENT(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the argument theta.
	 * @return	float
	 */
	public static function IMARGUMENT($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if ($parsedComplex['real'] == 0.0) {
			if ($parsedComplex['imaginary'] == 0.0) {
				return 0.0;
			} elseif($parsedComplex['imaginary'] < 0.0) {
				return M_PI / -2;
			} else {
				return M_PI / 2;
			}
		} elseif ($parsedComplex['real'] > 0.0) {
			return atan($parsedComplex['imaginary'] / $parsedComplex['real']);
		} elseif ($parsedComplex['imaginary'] < 0.0) {
			return 0 - (M_PI - atan(abs($parsedComplex['imaginary']) / abs($parsedComplex['real'])));
		} else {
			return M_PI - atan($parsedComplex['imaginary'] / abs($parsedComplex['real']));
		}
	}	//	function IMARGUMENT()


	/**
	 * IMCONJUGATE
	 *
	 * Returns the complex conjugate of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMCONJUGATE(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the conjugate.
	 * @return	string
	 */
	public static function IMCONJUGATE($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if ($parsedComplex['imaginary'] == 0.0) {
			return $parsedComplex['real'];
		} else {
			return self::_cleanComplex( self::COMPLEX( $parsedComplex['real'],
													   0 - $parsedComplex['imaginary'],
													   $parsedComplex['suffix']
													 )
									  );
		}
	}	//	function IMCONJUGATE()


	/**
	 * IMCOS
	 *
	 * Returns the cosine of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMCOS(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the cosine.
	 * @return	string|float
	 */
	public static function IMCOS($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if ($parsedComplex['imaginary'] == 0.0) {
			return cos($parsedComplex['real']);
		} else {
			return self::IMCONJUGATE(self::COMPLEX(cos($parsedComplex['real']) * cosh($parsedComplex['imaginary']),sin($parsedComplex['real']) * sinh($parsedComplex['imaginary']),$parsedComplex['suffix']));
		}
	}	//	function IMCOS()


	/**
	 * IMSIN
	 *
	 * Returns the sine of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMSIN(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the sine.
	 * @return	string|float
	 */
	public static function IMSIN($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if ($parsedComplex['imaginary'] == 0.0) {
			return sin($parsedComplex['real']);
		} else {
			return self::COMPLEX(sin($parsedComplex['real']) * cosh($parsedComplex['imaginary']),cos($parsedComplex['real']) * sinh($parsedComplex['imaginary']),$parsedComplex['suffix']);
		}
	}	//	function IMSIN()


	/**
	 * IMSQRT
	 *
	 * Returns the square root of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMSQRT(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the square root.
	 * @return	string
	 */
	public static function IMSQRT($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		$theta = self::IMARGUMENT($complexNumber);
		$d1 = cos($theta / 2);
		$d2 = sin($theta / 2);
		$r = sqrt(sqrt(($parsedComplex['real'] * $parsedComplex['real']) + ($parsedComplex['imaginary'] * $parsedComplex['imaginary'])));

		if ($parsedComplex['suffix'] == '') {
			return self::COMPLEX($d1 * $r,$d2 * $r);
		} else {
			return self::COMPLEX($d1 * $r,$d2 * $r,$parsedComplex['suffix']);
		}
	}	//	function IMSQRT()


	/**
	 * IMLN
	 *
	 * Returns the natural logarithm of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMLN(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the natural logarithm.
	 * @return	string
	 */
	public static function IMLN($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if (($parsedComplex['real'] == 0.0) && ($parsedComplex['imaginary'] == 0.0)) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		$logR = log(sqrt(($parsedComplex['real'] * $parsedComplex['real']) + ($parsedComplex['imaginary'] * $parsedComplex['imaginary'])));
		$t = self::IMARGUMENT($complexNumber);

		if ($parsedComplex['suffix'] == '') {
			return self::COMPLEX($logR,$t);
		} else {
			return self::COMPLEX($logR,$t,$parsedComplex['suffix']);
		}
	}	//	function IMLN()


	/**
	 * IMLOG10
	 *
	 * Returns the common logarithm (base 10) of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMLOG10(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the common logarithm.
	 * @return	string
	 */
	public static function IMLOG10($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if (($parsedComplex['real'] == 0.0) && ($parsedComplex['imaginary'] == 0.0)) {
			return PHPExcel_Calculation_Functions::NaN();
		} elseif (($parsedComplex['real'] > 0.0) && ($parsedComplex['imaginary'] == 0.0)) {
			return log10($parsedComplex['real']);
		}

		return self::IMPRODUCT(log10(EULER),self::IMLN($complexNumber));
	}	//	function IMLOG10()


	/**
	 * IMLOG2
	 *
	 * Returns the base-2 logarithm of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMLOG2(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the base-2 logarithm.
	 * @return	string
	 */
	public static function IMLOG2($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if (($parsedComplex['real'] == 0.0) && ($parsedComplex['imaginary'] == 0.0)) {
			return PHPExcel_Calculation_Functions::NaN();
		} elseif (($parsedComplex['real'] > 0.0) && ($parsedComplex['imaginary'] == 0.0)) {
			return log($parsedComplex['real'],2);
		}

		return self::IMPRODUCT(log(EULER,2),self::IMLN($complexNumber));
	}	//	function IMLOG2()


	/**
	 * IMEXP
	 *
	 * Returns the exponential of a complex number in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMEXP(complexNumber)
	 *
	 * @param	string		$complexNumber	The complex number for which you want the exponential.
	 * @return	string
	 */
	public static function IMEXP($complexNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);

		$parsedComplex = self::_parseComplex($complexNumber);

		if (($parsedComplex['real'] == 0.0) && ($parsedComplex['imaginary'] == 0.0)) {
			return '1';
		}

		$e = exp($parsedComplex['real']);
		$eX = $e * cos($parsedComplex['imaginary']);
		$eY = $e * sin($parsedComplex['imaginary']);

		if ($parsedComplex['suffix'] == '') {
			return self::COMPLEX($eX,$eY);
		} else {
			return self::COMPLEX($eX,$eY,$parsedComplex['suffix']);
		}
	}	//	function IMEXP()


	/**
	 * IMPOWER
	 *
	 * Returns a complex number in x + yi or x + yj text format raised to a power.
	 *
	 * Excel Function:
	 *		IMPOWER(complexNumber,realNumber)
	 *
	 * @param	string		$complexNumber	The complex number you want to raise to a power.
	 * @param	float		$realNumber		The power to which you want to raise the complex number.
	 * @return	string
	 */
	public static function IMPOWER($complexNumber,$realNumber) {
		$complexNumber	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
		$realNumber		= PHPExcel_Calculation_Functions::flattenSingleValue($realNumber);

		if (!is_numeric($realNumber)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		$parsedComplex = self::_parseComplex($complexNumber);

		$r = sqrt(($parsedComplex['real'] * $parsedComplex['real']) + ($parsedComplex['imaginary'] * $parsedComplex['imaginary']));
		$rPower = pow($r,$realNumber);
		$theta = self::IMARGUMENT($complexNumber) * $realNumber;
		if ($theta == 0) {
			return 1;
		} elseif ($parsedComplex['imaginary'] == 0.0) {
			return self::COMPLEX($rPower * cos($theta),$rPower * sin($theta),$parsedComplex['suffix']);
		} else {
			return self::COMPLEX($rPower * cos($theta),$rPower * sin($theta),$parsedComplex['suffix']);
		}
	}	//	function IMPOWER()


	/**
	 * IMDIV
	 *
	 * Returns the quotient of two complex numbers in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMDIV(complexDividend,complexDivisor)
	 *
	 * @param	string		$complexDividend	The complex numerator or dividend.
	 * @param	string		$complexDivisor		The complex denominator or divisor.
	 * @return	string
	 */
	public static function IMDIV($complexDividend,$complexDivisor) {
		$complexDividend	= PHPExcel_Calculation_Functions::flattenSingleValue($complexDividend);
		$complexDivisor	= PHPExcel_Calculation_Functions::flattenSingleValue($complexDivisor);

		$parsedComplexDividend = self::_parseComplex($complexDividend);
		$parsedComplexDivisor = self::_parseComplex($complexDivisor);

		if (($parsedComplexDividend['suffix'] != '') && ($parsedComplexDivisor['suffix'] != '') &&
			($parsedComplexDividend['suffix'] != $parsedComplexDivisor['suffix'])) {
			return PHPExcel_Calculation_Functions::NaN();
		}
		if (($parsedComplexDividend['suffix'] != '') && ($parsedComplexDivisor['suffix'] == '')) {
			$parsedComplexDivisor['suffix'] = $parsedComplexDividend['suffix'];
		}

		$d1 = ($parsedComplexDividend['real'] * $parsedComplexDivisor['real']) + ($parsedComplexDividend['imaginary'] * $parsedComplexDivisor['imaginary']);
		$d2 = ($parsedComplexDividend['imaginary'] * $parsedComplexDivisor['real']) - ($parsedComplexDividend['real'] * $parsedComplexDivisor['imaginary']);
		$d3 = ($parsedComplexDivisor['real'] * $parsedComplexDivisor['real']) + ($parsedComplexDivisor['imaginary'] * $parsedComplexDivisor['imaginary']);

		$r = $d1/$d3;
		$i = $d2/$d3;

		if ($i > 0.0) {
			return self::_cleanComplex($r.'+'.$i.$parsedComplexDivisor['suffix']);
		} elseif ($i < 0.0) {
			return self::_cleanComplex($r.$i.$parsedComplexDivisor['suffix']);
		} else {
			return $r;
		}
	}	//	function IMDIV()


	/**
	 * IMSUB
	 *
	 * Returns the difference of two complex numbers in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMSUB(complexNumber1,complexNumber2)
	 *
	 * @param	string		$complexNumber1		The complex number from which to subtract complexNumber2.
	 * @param	string		$complexNumber2		The complex number to subtract from complexNumber1.
	 * @return	string
	 */
	public static function IMSUB($complexNumber1,$complexNumber2) {
		$complexNumber1	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber1);
		$complexNumber2	= PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber2);

		$parsedComplex1 = self::_parseComplex($complexNumber1);
		$parsedComplex2 = self::_parseComplex($complexNumber2);

		if ((($parsedComplex1['suffix'] != '') && ($parsedComplex2['suffix'] != '')) &&
			($parsedComplex1['suffix'] != $parsedComplex2['suffix'])) {
			return PHPExcel_Calculation_Functions::NaN();
		} elseif (($parsedComplex1['suffix'] == '') && ($parsedComplex2['suffix'] != '')) {
			$parsedComplex1['suffix'] = $parsedComplex2['suffix'];
		}

		$d1 = $parsedComplex1['real'] - $parsedComplex2['real'];
		$d2 = $parsedComplex1['imaginary'] - $parsedComplex2['imaginary'];

		return self::COMPLEX($d1,$d2,$parsedComplex1['suffix']);
	}	//	function IMSUB()


	/**
	 * IMSUM
	 *
	 * Returns the sum of two or more complex numbers in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMSUM(complexNumber[,complexNumber[,...]])
	 *
	 * @param	string		$complexNumber,...	Series of complex numbers to add
	 * @return	string
	 */
	public static function IMSUM() {
		// Return value
		$returnValue = self::_parseComplex('0');
		$activeSuffix = '';

		// Loop through the arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			$parsedComplex = self::_parseComplex($arg);

			if ($activeSuffix == '') {
				$activeSuffix = $parsedComplex['suffix'];
			} elseif (($parsedComplex['suffix'] != '') && ($activeSuffix != $parsedComplex['suffix'])) {
				return PHPExcel_Calculation_Functions::VALUE();
			}

			$returnValue['real'] += $parsedComplex['real'];
			$returnValue['imaginary'] += $parsedComplex['imaginary'];
		}

		if ($returnValue['imaginary'] == 0.0) { $activeSuffix = ''; }
		return self::COMPLEX($returnValue['real'],$returnValue['imaginary'],$activeSuffix);
	}	//	function IMSUM()


	/**
	 * IMPRODUCT
	 *
	 * Returns the product of two or more complex numbers in x + yi or x + yj text format.
	 *
	 * Excel Function:
	 *		IMPRODUCT(complexNumber[,complexNumber[,...]])
	 *
	 * @param	string		$complexNumber,...	Series of complex numbers to multiply
	 * @return	string
	 */
	public static function IMPRODUCT() {
		// Return value
		$returnValue = self::_parseComplex('1');
		$activeSuffix = '';

		// Loop through the arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			$parsedComplex = self::_parseComplex($arg);

			$workValue = $returnValue;
			if (($parsedComplex['suffix'] != '') && ($activeSuffix == '')) {
				$activeSuffix = $parsedComplex['suffix'];
			} elseif (($parsedComplex['suffix'] != '') && ($activeSuffix != $parsedComplex['suffix'])) {
				return PHPExcel_Calculation_Functions::NaN();
			}
			$returnValue['real'] = ($workValue['real'] * $parsedComplex['real']) - ($workValue['imaginary'] * $parsedComplex['imaginary']);
			$returnValue['imaginary'] = ($workValue['real'] * $parsedComplex['imaginary']) + ($workValue['imaginary'] * $parsedComplex['real']);
		}

		if ($returnValue['imaginary'] == 0.0) { $activeSuffix = ''; }
		return self::COMPLEX($returnValue['real'],$returnValue['imaginary'],$activeSuffix);
	}	//	function IMPRODUCT()


	/**
	 *	DELTA
	 *
	 *	Tests whether two values are equal. Returns 1 if number1 = number2; returns 0 otherwise.
	 *	Use this function to filter a set of values. For example, by summing several DELTA
	 *	functions you calculate the count of equal pairs. This function is also known as the
	 *	Kronecker Delta function.
	 *
	 *	Excel Function:
	 *		DELTA(a[,b])
	 *
	 *	@param	float		$a	The first number.
	 *	@param	float		$b	The second number. If omitted, b is assumed to be zero.
	 *	@return	int
	 */
	public static function DELTA($a, $b=0) {
		$a	= PHPExcel_Calculation_Functions::flattenSingleValue($a);
		$b	= PHPExcel_Calculation_Functions::flattenSingleValue($b);

		return (int) ($a == $b);
	}	//	function DELTA()


	/**
	 *	GESTEP
	 *
	 *	Excel Function:
	 *		GESTEP(number[,step])
	 *
	 *	Returns 1 if number >= step; returns 0 (zero) otherwise
	 *	Use this function to filter a set of values. For example, by summing several GESTEP
	 *	functions you calculate the count of values that exceed a threshold.
	 *
	 *	@param	float		$number		The value to test against step.
	 *	@param	float		$step		The threshold value.
	 *									If you omit a value for step, GESTEP uses zero.
	 *	@return	int
	 */
	public static function GESTEP($number, $step=0) {
		$number	= PHPExcel_Calculation_Functions::flattenSingleValue($number);
		$step	= PHPExcel_Calculation_Functions::flattenSingleValue($step);

		return (int) ($number >= $step);
	}	//	function GESTEP()


	//
	//	Private method to calculate the erf value
	//
	private static $_two_sqrtpi = 1.128379167095512574;

	public static function _erfVal($x) {
		if (abs($x) > 2.2) {
			return 1 - self::_erfcVal($x);
		}
		$sum = $term = $x;
		$xsqr = ($x * $x);
		$j = 1;
		do {
			$term *= $xsqr / $j;
			$sum -= $term / (2 * $j + 1);
			++$j;
			$term *= $xsqr / $j;
			$sum += $term / (2 * $j + 1);
			++$j;
			if ($sum == 0.0) {
				break;
			}
		} while (abs($term / $sum) > PRECISION);
		return self::$_two_sqrtpi * $sum;
	}	//	function _erfVal()


	/**
	 *	ERF
	 *
	 *	Returns the error function integrated between the lower and upper bound arguments.
	 *
	 *	Note: In Excel 2007 or earlier, if you input a negative value for the upper or lower bound arguments,
	 *			the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
	 *			improved, so that it can now calculate the function for both positive and negative ranges.
	 *			PHPExcel follows Excel 2010 behaviour, and accepts nagative arguments.
	 *
	 *	Excel Function:
	 *		ERF(lower[,upper])
	 *
	 *	@param	float		$lower	lower bound for integrating ERF
	 *	@param	float		$upper	upper bound for integrating ERF.
	 *								If omitted, ERF integrates between zero and lower_limit
	 *	@return	float
	 */
	public static function ERF($lower, $upper = NULL) {
		$lower	= PHPExcel_Calculation_Functions::flattenSingleValue($lower);
		$upper	= PHPExcel_Calculation_Functions::flattenSingleValue($upper);

		if (is_numeric($lower)) {
			if (is_null($upper)) {
				return self::_erfVal($lower);
			}
			if (is_numeric($upper)) {
				return self::_erfVal($upper) - self::_erfVal($lower);
			}
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function ERF()


	//
	//	Private method to calculate the erfc value
	//
	private static $_one_sqrtpi = 0.564189583547756287;

	private static function _erfcVal($x) {
		if (abs($x) < 2.2) {
			return 1 - self::_erfVal($x);
		}
		if ($x < 0) {
			return 2 - self::ERFC(-$x);
		}
		$a = $n = 1;
		$b = $c = $x;
		$d = ($x * $x) + 0.5;
		$q1 = $q2 = $b / $d;
		$t = 0;
		do {
			$t = $a * $n + $b * $x;
			$a = $b;
			$b = $t;
			$t = $c * $n + $d * $x;
			$c = $d;
			$d = $t;
			$n += 0.5;
			$q1 = $q2;
			$q2 = $b / $d;
		} while ((abs($q1 - $q2) / $q2) > PRECISION);
		return self::$_one_sqrtpi * exp(-$x * $x) * $q2;
	}	//	function _erfcVal()


	/**
	 *	ERFC
	 *
	 *	Returns the complementary ERF function integrated between x and infinity
	 *
	 *	Note: In Excel 2007 or earlier, if you input a negative value for the lower bound argument,
	 *		the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
	 *		improved, so that it can now calculate the function for both positive and negative x values.
	 *			PHPExcel follows Excel 2010 behaviour, and accepts nagative arguments.
	 *
	 *	Excel Function:
	 *		ERFC(x)
	 *
	 *	@param	float	$x	The lower bound for integrating ERFC
	 *	@return	float
	 */
	public static function ERFC($x) {
		$x = PHPExcel_Calculation_Functions::flattenSingleValue($x);

		if (is_numeric($x)) {
			return self::_erfcVal($x);
		}
		return PHPExcel_Calculation_Functions::VALUE();
	}	//	function ERFC()


	/**
	 *	getConversionGroups
	 *	Returns a list of the different conversion groups for UOM conversions
	 *
	 *	@return	array
	 */
	public static function getConversionGroups() {
		$conversionGroups = array();
		foreach(self::$_conversionUnits as $conversionUnit) {
			$conversionGroups[] = $conversionUnit['Group'];
		}
		return array_merge(array_unique($conversionGroups));
	}	//	function getConversionGroups()


	/**
	 *	getConversionGroupUnits
	 *	Returns an array of units of measure, for a specified conversion group, or for all groups
	 *
	 *	@param	string	$group	The group whose units of measure you want to retrieve
	 *	@return	array
	 */
	public static function getConversionGroupUnits($group = NULL) {
		$conversionGroups = array();
		foreach(self::$_conversionUnits as $conversionUnit => $conversionGroup) {
			if ((is_null($group)) || ($conversionGroup['Group'] == $group)) {
				$conversionGroups[$conversionGroup['Group']][] = $conversionUnit;
			}
		}
		return $conversionGroups;
	}	//	function getConversionGroupUnits()


	/**
	 *	getConversionGroupUnitDetails
	 *
	 *	@param	string	$group	The group whose units of measure you want to retrieve
	 *	@return	array
	 */
	public static function getConversionGroupUnitDetails($group = NULL) {
		$conversionGroups = array();
		foreach(self::$_conversionUnits as $conversionUnit => $conversionGroup) {
			if ((is_null($group)) || ($conversionGroup['Group'] == $group)) {
				$conversionGroups[$conversionGroup['Group']][] = array(	'unit'			=> $conversionUnit,
																		'description'	=> $conversionGroup['Unit Name']
																	  );
			}
		}
		return $conversionGroups;
	}	//	function getConversionGroupUnitDetails()


	/**
	 *	getConversionMultipliers
	 *	Returns an array of the Multiplier prefixes that can be used with Units of Measure in CONVERTUOM()
	 *
	 *	@return	array of mixed
	 */
	public static function getConversionMultipliers() {
		return self::$_conversionMultipliers;
	}	//	function getConversionGroups()


	/**
	 *	CONVERTUOM
	 *
	 *	Converts a number from one measurement system to another.
	 *	For example, CONVERT can translate a table of distances in miles to a table of distances
	 *	in kilometers.
	 *
	 *	Excel Function:
	 *		CONVERT(value,fromUOM,toUOM)
	 *
	 *	@param	float		$value		The value in fromUOM to convert.
	 *	@param	string		$fromUOM	The units for value.
	 *	@param	string		$toUOM		The units for the result.
	 *
	 *	@return	float
	 */
	public static function CONVERTUOM($value, $fromUOM, $toUOM) {
		$value		= PHPExcel_Calculation_Functions::flattenSingleValue($value);
		$fromUOM	= PHPExcel_Calculation_Functions::flattenSingleValue($fromUOM);
		$toUOM		= PHPExcel_Calculation_Functions::flattenSingleValue($toUOM);

		if (!is_numeric($value)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$fromMultiplier = 1.0;
		if (isset(self::$_conversionUnits[$fromUOM])) {
			$unitGroup1 = self::$_conversionUnits[$fromUOM]['Group'];
		} else {
			$fromMultiplier = substr($fromUOM,0,1);
			$fromUOM = substr($fromUOM,1);
			if (isset(self::$_conversionMultipliers[$fromMultiplier])) {
				$fromMultiplier = self::$_conversionMultipliers[$fromMultiplier]['multiplier'];
			} else {
				return PHPExcel_Calculation_Functions::NA();
			}
			if ((isset(self::$_conversionUnits[$fromUOM])) && (self::$_conversionUnits[$fromUOM]['AllowPrefix'])) {
				$unitGroup1 = self::$_conversionUnits[$fromUOM]['Group'];
			} else {
				return PHPExcel_Calculation_Functions::NA();
			}
		}
		$value *= $fromMultiplier;

		$toMultiplier = 1.0;
		if (isset(self::$_conversionUnits[$toUOM])) {
			$unitGroup2 = self::$_conversionUnits[$toUOM]['Group'];
		} else {
			$toMultiplier = substr($toUOM,0,1);
			$toUOM = substr($toUOM,1);
			if (isset(self::$_conversionMultipliers[$toMultiplier])) {
				$toMultiplier = self::$_conversionMultipliers[$toMultiplier]['multiplier'];
			} else {
				return PHPExcel_Calculation_Functions::NA();
			}
			if ((isset(self::$_conversionUnits[$toUOM])) && (self::$_conversionUnits[$toUOM]['AllowPrefix'])) {
				$unitGroup2 = self::$_conversionUnits[$toUOM]['Group'];
			} else {
				return PHPExcel_Calculation_Functions::NA();
			}
		}
		if ($unitGroup1 != $unitGroup2) {
			return PHPExcel_Calculation_Functions::NA();
		}

		if (($fromUOM == $toUOM) && ($fromMultiplier == $toMultiplier)) {
			//	We've already factored $fromMultiplier into the value, so we need
			//		to reverse it again
			return $value / $fromMultiplier;
		} elseif ($unitGroup1 == 'Temperature') {
			if (($fromUOM == 'F') || ($fromUOM == 'fah')) {
				if (($toUOM == 'F') || ($toUOM == 'fah')) {
					return $value;
				} else {
					$value = (($value - 32) / 1.8);
					if (($toUOM == 'K') || ($toUOM == 'kel')) {
						$value += 273.15;
					}
					return $value;
				}
			} elseif ((($fromUOM == 'K') || ($fromUOM == 'kel')) &&
					  (($toUOM == 'K') || ($toUOM == 'kel'))) {
						return $value;
			} elseif ((($fromUOM == 'C') || ($fromUOM == 'cel')) &&
					  (($toUOM == 'C') || ($toUOM == 'cel'))) {
					return $value;
			}
			if (($toUOM == 'F') || ($toUOM == 'fah')) {
				if (($fromUOM == 'K') || ($fromUOM == 'kel')) {
					$value -= 273.15;
				}
				return ($value * 1.8) + 32;
			}
			if (($toUOM == 'C') || ($toUOM == 'cel')) {
				return $value - 273.15;
			}
			return $value + 273.15;
		}
		return ($value * self::$_unitConversions[$unitGroup1][$fromUOM][$toUOM]) / $toMultiplier;
	}	//	function CONVERTUOM()

}	//	class PHPExcel_Calculation_Engineering
