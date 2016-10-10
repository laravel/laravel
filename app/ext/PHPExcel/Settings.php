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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Settings
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.8.0, 2014-03-02
 */

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


class PHPExcel_Settings
{
    /**    constants */
    /**    Available Zip library classes */
    const PCLZIP        = 'PHPExcel_Shared_ZipArchive';
    const ZIPARCHIVE    = 'ZipArchive';

    /**    Optional Chart Rendering libraries */
    const CHART_RENDERER_JPGRAPH    = 'jpgraph';

    /**    Optional PDF Rendering libraries */
    const PDF_RENDERER_TCPDF		= 'tcPDF';
    const PDF_RENDERER_DOMPDF		= 'DomPDF';
    const PDF_RENDERER_MPDF 		= 'mPDF';


    private static $_chartRenderers = array(
        self::CHART_RENDERER_JPGRAPH,
    );

    private static $_pdfRenderers = array(
        self::PDF_RENDERER_TCPDF,
        self::PDF_RENDERER_DOMPDF,
        self::PDF_RENDERER_MPDF,
    );


    /**
     * Name of the class used for Zip file management
     *	e.g.
     *		ZipArchive
     *
     * @var string
     */
    private static $_zipClass    = self::ZIPARCHIVE;


    /**
     * Name of the external Library used for rendering charts
     *	e.g.
     *		jpgraph
     *
     * @var string
     */
    private static $_chartRendererName = NULL;

    /**
     * Directory Path to the external Library used for rendering charts
     *
     * @var string
     */
    private static $_chartRendererPath = NULL;


    /**
     * Name of the external Library used for rendering PDF files
     *	e.g.
     * 		mPDF
     *
     * @var string
     */
    private static $_pdfRendererName = NULL;

    /**
     * Directory Path to the external Library used for rendering PDF files
     *
     * @var string
     */
    private static $_pdfRendererPath = NULL;

    /**
     * Default options for libxml loader
     *
     * @var int
     */
    private static $_libXmlLoaderOptions = null;

    /**
     * Set the Zip handler Class that PHPExcel should use for Zip file management (PCLZip or ZipArchive)
     *
     * @param string $zipClass	The Zip handler class that PHPExcel should use for Zip file management
     * 	 e.g. PHPExcel_Settings::PCLZip or PHPExcel_Settings::ZipArchive
     * @return	boolean	Success or failure
     */
    public static function setZipClass($zipClass)
    {
        if (($zipClass === self::PCLZIP) ||
            ($zipClass === self::ZIPARCHIVE)) {
            self::$_zipClass = $zipClass;
            return TRUE;
        }
        return FALSE;
    } // function setZipClass()


    /**
     * Return the name of the Zip handler Class that PHPExcel is configured to use (PCLZip or ZipArchive)
     *	or Zip file management
     *
     * @return string Name of the Zip handler Class that PHPExcel is configured to use
     *	for Zip file management
     *	e.g. PHPExcel_Settings::PCLZip or PHPExcel_Settings::ZipArchive
     */
    public static function getZipClass()
    {
        return self::$_zipClass;
    } // function getZipClass()


    /**
     * Return the name of the method that is currently configured for cell cacheing
     *
     * @return string Name of the cacheing method
     */
    public static function getCacheStorageMethod()
    {
        return PHPExcel_CachedObjectStorageFactory::getCacheStorageMethod();
    } // function getCacheStorageMethod()


    /**
     * Return the name of the class that is currently being used for cell cacheing
     *
     * @return string Name of the class currently being used for cacheing
     */
    public static function getCacheStorageClass()
    {
        return PHPExcel_CachedObjectStorageFactory::getCacheStorageClass();
    } // function getCacheStorageClass()


    /**
     * Set the method that should be used for cell cacheing
     *
     * @param string $method Name of the cacheing method
     * @param array $arguments Optional configuration arguments for the cacheing method
     * @return boolean Success or failure
     */
    public static function setCacheStorageMethod(
    	$method = PHPExcel_CachedObjectStorageFactory::cache_in_memory,
      $arguments = array()
    )
    {
        return PHPExcel_CachedObjectStorageFactory::initialize($method, $arguments);
    } // function setCacheStorageMethod()


    /**
     * Set the locale code to use for formula translations and any special formatting
     *
     * @param string $locale The locale code to use (e.g. "fr" or "pt_br" or "en_uk")
     * @return boolean Success or failure
     */
    public static function setLocale($locale='en_us')
    {
        return PHPExcel_Calculation::getInstance()->setLocale($locale);
    } // function setLocale()


    /**
     * Set details of the external library that PHPExcel should use for rendering charts
     *
     * @param string $libraryName	Internal reference name of the library
     *	e.g. PHPExcel_Settings::CHART_RENDERER_JPGRAPH
     * @param string $libraryBaseDir Directory path to the library's base folder
     *
     * @return	boolean	Success or failure
     */
    public static function setChartRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setChartRendererName($libraryName))
            return FALSE;
        return self::setChartRendererPath($libraryBaseDir);
    } // function setChartRenderer()


    /**
     * Identify to PHPExcel the external library to use for rendering charts
     *
     * @param string $libraryName	Internal reference name of the library
     *	e.g. PHPExcel_Settings::CHART_RENDERER_JPGRAPH
     *
     * @return	boolean	Success or failure
     */
    public static function setChartRendererName($libraryName)
    {
        if (!in_array($libraryName,self::$_chartRenderers)) {
            return FALSE;
        }

        self::$_chartRendererName = $libraryName;

        return TRUE;
    } // function setChartRendererName()


    /**
     * Tell PHPExcel where to find the external library to use for rendering charts
     *
     * @param string $libraryBaseDir	Directory path to the library's base folder
     * @return	boolean	Success or failure
     */
    public static function setChartRendererPath($libraryBaseDir)
    {
        if ((file_exists($libraryBaseDir) === false) || (is_readable($libraryBaseDir) === false)) {
            return FALSE;
        }
        self::$_chartRendererPath = $libraryBaseDir;

        return TRUE;
    } // function setChartRendererPath()


    /**
     * Return the Chart Rendering Library that PHPExcel is currently configured to use (e.g. jpgraph)
     *
     * @return string|NULL Internal reference name of the Chart Rendering Library that PHPExcel is
     *	currently configured to use
     *	e.g. PHPExcel_Settings::CHART_RENDERER_JPGRAPH
     */
    public static function getChartRendererName()
    {
        return self::$_chartRendererName;
    } // function getChartRendererName()


    /**
     * Return the directory path to the Chart Rendering Library that PHPExcel is currently configured to use
     *
     * @return string|NULL Directory Path to the Chart Rendering Library that PHPExcel is
     * 	currently configured to use
     */
    public static function getChartRendererPath()
    {
        return self::$_chartRendererPath;
    } // function getChartRendererPath()


    /**
     * Set details of the external library that PHPExcel should use for rendering PDF files
     *
     * @param string $libraryName Internal reference name of the library
     * 	e.g. PHPExcel_Settings::PDF_RENDERER_TCPDF,
     * 	PHPExcel_Settings::PDF_RENDERER_DOMPDF
     *  or PHPExcel_Settings::PDF_RENDERER_MPDF
     * @param string $libraryBaseDir Directory path to the library's base folder
     *
     * @return boolean Success or failure
     */
    public static function setPdfRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setPdfRendererName($libraryName))
            return FALSE;
        return self::setPdfRendererPath($libraryBaseDir);
    } // function setPdfRenderer()


    /**
     * Identify to PHPExcel the external library to use for rendering PDF files
     *
     * @param string $libraryName Internal reference name of the library
     * 	e.g. PHPExcel_Settings::PDF_RENDERER_TCPDF,
     *	PHPExcel_Settings::PDF_RENDERER_DOMPDF
     * 	or PHPExcel_Settings::PDF_RENDERER_MPDF
     *
     * @return boolean Success or failure
     */
    public static function setPdfRendererName($libraryName)
    {
        if (!in_array($libraryName,self::$_pdfRenderers)) {
            return FALSE;
        }

        self::$_pdfRendererName = $libraryName;

        return TRUE;
    } // function setPdfRendererName()


    /**
     * Tell PHPExcel where to find the external library to use for rendering PDF files
     *
     * @param string $libraryBaseDir Directory path to the library's base folder
     * @return boolean Success or failure
     */
    public static function setPdfRendererPath($libraryBaseDir)
    {
        if ((file_exists($libraryBaseDir) === false) || (is_readable($libraryBaseDir) === false)) {
            return FALSE;
        }
        self::$_pdfRendererPath = $libraryBaseDir;

        return TRUE;
    } // function setPdfRendererPath()


    /**
     * Return the PDF Rendering Library that PHPExcel is currently configured to use (e.g. dompdf)
     *
     * @return string|NULL Internal reference name of the PDF Rendering Library that PHPExcel is
     * 	currently configured to use
     *  e.g. PHPExcel_Settings::PDF_RENDERER_TCPDF,
     *  PHPExcel_Settings::PDF_RENDERER_DOMPDF
     *  or PHPExcel_Settings::PDF_RENDERER_MPDF
     */
    public static function getPdfRendererName()
    {
        return self::$_pdfRendererName;
    } // function getPdfRendererName()

    /**
     * Return the directory path to the PDF Rendering Library that PHPExcel is currently configured to use
     *
     * @return string|NULL Directory Path to the PDF Rendering Library that PHPExcel is
     *		currently configured to use
     */
    public static function getPdfRendererPath()
    {
        return self::$_pdfRendererPath;
    } // function getPdfRendererPath()

    /**
     * Set default options for libxml loader
     *
     * @param int $options Default options for libxml loader
     */
    public static function setLibXmlLoaderOptions($options = null)
    {
        if (is_null($options)) {
            $options = LIBXML_DTDLOAD | LIBXML_DTDATTR;
        }
        @libxml_disable_entity_loader($options == (LIBXML_DTDLOAD | LIBXML_DTDATTR)); 
        self::$_libXmlLoaderOptions = $options;
    } // function setLibXmlLoaderOptions

    /**
     * Get default options for libxml loader.
     * Defaults to LIBXML_DTDLOAD | LIBXML_DTDATTR when not set explicitly.
     *
     * @return int Default options for libxml loader
     */
    public static function getLibXmlLoaderOptions()
    {
        if (is_null(self::$_libXmlLoaderOptions)) {
            self::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR);
        }
        @libxml_disable_entity_loader($options == (LIBXML_DTDLOAD | LIBXML_DTDATTR));
        return self::$_libXmlLoaderOptions;
    } // function getLibXmlLoaderOptions
}
