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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_Reader_Excel2007
 *
 * @category	PHPExcel
 * @package	PHPExcel_Reader
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel2007 extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
	/**
	 * PHPExcel_ReferenceHelper instance
	 *
	 * @var PHPExcel_ReferenceHelper
	 */
	private $_referenceHelper = NULL;

	/**
	 * PHPExcel_Reader_Excel2007_Theme instance
	 *
	 * @var PHPExcel_Reader_Excel2007_Theme
	 */
	private static $_theme = NULL;


	/**
	 * Create a new PHPExcel_Reader_Excel2007 instance
	 */
	public function __construct() {
		$this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
		$this->_referenceHelper = PHPExcel_ReferenceHelper::getInstance();
	}


	/**
	 * Can the current PHPExcel_Reader_IReader read the file?
	 *
	 * @param 	string 		$pFilename
	 * @return 	boolean
	 * @throws PHPExcel_Reader_Exception
	 */
	public function canRead($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

        $zipClass = PHPExcel_Settings::getZipClass();

		// Check if zip class exists
//		if (!class_exists($zipClass, FALSE)) {
//			throw new PHPExcel_Reader_Exception($zipClass . " library is not enabled");
//		}

		$xl = false;
		// Load file
		$zip = new $zipClass;
		if ($zip->open($pFilename) === true) {
			// check if it is an OOXML archive
			$rels = simplexml_load_string($this->_getFromZipArchive($zip, "_rels/.rels"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
			if ($rels !== false) {
				foreach ($rels->Relationship as $rel) {
					switch ($rel["Type"]) {
						case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument":
							if (basename($rel["Target"]) == 'workbook.xml') {
								$xl = true;
							}
							break;

					}
				}
			}
			$zip->close();
		}

		return $xl;
	}


	/**
	 * Reads names of the worksheets from a file, without parsing the whole file to a PHPExcel object
	 *
	 * @param 	string 		$pFilename
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function listWorksheetNames($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$worksheetNames = array();

        $zipClass = PHPExcel_Settings::getZipClass();

		$zip = new $zipClass;
		$zip->open($pFilename);

		//	The files we're looking at here are small enough that simpleXML is more efficient than XMLReader
		$rels = simplexml_load_string(
		    $this->_getFromZipArchive($zip, "_rels/.rels", 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions())
		); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($rels->Relationship as $rel) {
			switch ($rel["Type"]) {
				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument":
					$xmlWorkbook = simplexml_load_string(
					    $this->_getFromZipArchive($zip, "{$rel['Target']}", 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions())
					);  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");

					if ($xmlWorkbook->sheets) {
						foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
							// Check if sheet should be skipped
							$worksheetNames[] = (string) $eleSheet["name"];
						}
					}
			}
		}

		$zip->close();

		return $worksheetNames;
	}


	/**
	 * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
	 *
	 * @param   string     $pFilename
	 * @throws   PHPExcel_Reader_Exception
	 */
	public function listWorksheetInfo($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$worksheetInfo = array();

        $zipClass = PHPExcel_Settings::getZipClass();

		$zip = new $zipClass;
		$zip->open($pFilename);

		$rels = simplexml_load_string($this->_getFromZipArchive($zip, "_rels/.rels"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($rels->Relationship as $rel) {
			if ($rel["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument") {
				$dir = dirname($rel["Target"]);
				$relsWorkbook = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/_rels/" . basename($rel["Target"]) . ".rels"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());  //~ http://schemas.openxmlformats.org/package/2006/relationships");
				$relsWorkbook->registerXPathNamespace("rel", "http://schemas.openxmlformats.org/package/2006/relationships");

				$worksheets = array();
				foreach ($relsWorkbook->Relationship as $ele) {
					if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet") {
						$worksheets[(string) $ele["Id"]] = $ele["Target"];
					}
				}

				$xmlWorkbook = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
				if ($xmlWorkbook->sheets) {
					$dir = dirname($rel["Target"]);
					foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
						$tmpInfo = array(
							'worksheetName' => (string) $eleSheet["name"],
							'lastColumnLetter' => 'A',
							'lastColumnIndex' => 0,
							'totalRows' => 0,
							'totalColumns' => 0,
						);

						$fileWorksheet = $worksheets[(string) self::array_item($eleSheet->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "id")];

						$xml = new XMLReader();
						$res = $xml->open('zip://'.PHPExcel_Shared_File::realpath($pFilename).'#'."$dir/$fileWorksheet", null, PHPExcel_Settings::getLibXmlLoaderOptions());
						$xml->setParserProperty(2,true);

						$currCells = 0;
						while ($xml->read()) {
							if ($xml->name == 'row' && $xml->nodeType == XMLReader::ELEMENT) {
								$row = $xml->getAttribute('r');
								$tmpInfo['totalRows'] = $row;
								$tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'],$currCells);
								$currCells = 0;
							} elseif ($xml->name == 'c' && $xml->nodeType == XMLReader::ELEMENT) {
								$currCells++;
							}
						}
						$tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'],$currCells);
						$xml->close();

						$tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
						$tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);

						$worksheetInfo[] = $tmpInfo;
					}
				}
			}
		}

		$zip->close();

		return $worksheetInfo;
	}


	private static function _castToBool($c) {
//		echo 'Initial Cast to Boolean', PHP_EOL;
		$value = isset($c->v) ? (string) $c->v : NULL;
		if ($value == '0') {
			return FALSE;
		} elseif ($value == '1') {
			return TRUE;
		} else {
			return (bool)$c->v;
		}
		return $value;
	}	//	function _castToBool()


	private static function _castToError($c) {
//		echo 'Initial Cast to Error', PHP_EOL;
		return isset($c->v) ? (string) $c->v : NULL;
	}	//	function _castToError()


	private static function _castToString($c) {
//		echo 'Initial Cast to String, PHP_EOL;
		return isset($c->v) ? (string) $c->v : NULL;
	}	//	function _castToString()


	private function _castToFormula($c,$r,&$cellDataType,&$value,&$calculatedValue,&$sharedFormulas,$castBaseType) {
//		echo 'Formula', PHP_EOL;
//		echo '$c->f is ', $c->f, PHP_EOL;
		$cellDataType 		= 'f';
		$value 				= "={$c->f}";
		$calculatedValue 	= self::$castBaseType($c);

		// Shared formula?
		if (isset($c->f['t']) && strtolower((string)$c->f['t']) == 'shared') {
//			echo 'SHARED FORMULA', PHP_EOL;
			$instance = (string)$c->f['si'];

//			echo 'Instance ID = ', $instance, PHP_EOL;
//
//			echo 'Shared Formula Array:', PHP_EOL;
//			print_r($sharedFormulas);
			if (!isset($sharedFormulas[(string)$c->f['si']])) {
//				echo 'SETTING NEW SHARED FORMULA', PHP_EOL;
//				echo 'Master is ', $r, PHP_EOL;
//				echo 'Formula is ', $value, PHP_EOL;
				$sharedFormulas[$instance] = array(	'master' => $r,
													'formula' => $value
												  );
//				echo 'New Shared Formula Array:', PHP_EOL;
//				print_r($sharedFormulas);
			} else {
//				echo 'GETTING SHARED FORMULA', PHP_EOL;
//				echo 'Master is ', $sharedFormulas[$instance]['master'], PHP_EOL;
//				echo 'Formula is ', $sharedFormulas[$instance]['formula'], PHP_EOL;
				$master = PHPExcel_Cell::coordinateFromString($sharedFormulas[$instance]['master']);
				$current = PHPExcel_Cell::coordinateFromString($r);

				$difference = array(0, 0);
				$difference[0] = PHPExcel_Cell::columnIndexFromString($current[0]) - PHPExcel_Cell::columnIndexFromString($master[0]);
				$difference[1] = $current[1] - $master[1];

				$value = $this->_referenceHelper->updateFormulaReferences(	$sharedFormulas[$instance]['formula'],
																			'A1',
																			$difference[0],
																			$difference[1]
																		 );
//				echo 'Adjusted Formula is ', $value, PHP_EOL;
			}
		}
	}


	public function _getFromZipArchive($archive, $fileName = '')
	{
		// Root-relative paths
		if (strpos($fileName, '//') !== false)
		{
			$fileName = substr($fileName, strpos($fileName, '//') + 1);
		}
		$fileName = PHPExcel_Shared_File::realpath($fileName);

		// Apache POI fixes
		$contents = $archive->getFromName($fileName);
		if ($contents === false)
		{
			$contents = $archive->getFromName(substr($fileName, 1));
		}

		return $contents;
	}


	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function load($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Initialisations
		$excel = new PHPExcel;
		$excel->removeSheetByIndex(0);
		if (!$this->_readDataOnly) {
			$excel->removeCellStyleXfByIndex(0); // remove the default style
			$excel->removeCellXfByIndex(0); // remove the default style
		}

        $zipClass = PHPExcel_Settings::getZipClass();

		$zip = new $zipClass;
		$zip->open($pFilename);

		//	Read the theme first, because we need the colour scheme when reading the styles
		$wbRels = simplexml_load_string($this->_getFromZipArchive($zip, "xl/_rels/workbook.xml.rels"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($wbRels->Relationship as $rel) {
			switch ($rel["Type"]) {
				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme":
					$themeOrderArray = array('lt1','dk1','lt2','dk2');
					$themeOrderAdditional = count($themeOrderArray);

					$xmlTheme = simplexml_load_string($this->_getFromZipArchive($zip, "xl/{$rel['Target']}"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
					if (is_object($xmlTheme)) {
						$xmlThemeName = $xmlTheme->attributes();
						$xmlTheme = $xmlTheme->children("http://schemas.openxmlformats.org/drawingml/2006/main");
						$themeName = (string)$xmlThemeName['name'];

						$colourScheme = $xmlTheme->themeElements->clrScheme->attributes();
						$colourSchemeName = (string)$colourScheme['name'];
						$colourScheme = $xmlTheme->themeElements->clrScheme->children("http://schemas.openxmlformats.org/drawingml/2006/main");

						$themeColours = array();
						foreach ($colourScheme as $k => $xmlColour) {
							$themePos = array_search($k,$themeOrderArray);
							if ($themePos === false) {
								$themePos = $themeOrderAdditional++;
							}
							if (isset($xmlColour->sysClr)) {
								$xmlColourData = $xmlColour->sysClr->attributes();
								$themeColours[$themePos] = $xmlColourData['lastClr'];
							} elseif (isset($xmlColour->srgbClr)) {
								$xmlColourData = $xmlColour->srgbClr->attributes();
								$themeColours[$themePos] = $xmlColourData['val'];
							}
						}
						self::$_theme = new PHPExcel_Reader_Excel2007_Theme($themeName,$colourSchemeName,$themeColours);
					}
					break;
			}
		}

		$rels = simplexml_load_string($this->_getFromZipArchive($zip, "_rels/.rels"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($rels->Relationship as $rel) {
			switch ($rel["Type"]) {
				case "http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties":
					$xmlCore = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
					if (is_object($xmlCore)) {
						$xmlCore->registerXPathNamespace("dc", "http://purl.org/dc/elements/1.1/");
						$xmlCore->registerXPathNamespace("dcterms", "http://purl.org/dc/terms/");
						$xmlCore->registerXPathNamespace("cp", "http://schemas.openxmlformats.org/package/2006/metadata/core-properties");
						$docProps = $excel->getProperties();
						$docProps->setCreator((string) self::array_item($xmlCore->xpath("dc:creator")));
						$docProps->setLastModifiedBy((string) self::array_item($xmlCore->xpath("cp:lastModifiedBy")));
						$docProps->setCreated(strtotime(self::array_item($xmlCore->xpath("dcterms:created")))); //! respect xsi:type
						$docProps->setModified(strtotime(self::array_item($xmlCore->xpath("dcterms:modified")))); //! respect xsi:type
						$docProps->setTitle((string) self::array_item($xmlCore->xpath("dc:title")));
						$docProps->setDescription((string) self::array_item($xmlCore->xpath("dc:description")));
						$docProps->setSubject((string) self::array_item($xmlCore->xpath("dc:subject")));
						$docProps->setKeywords((string) self::array_item($xmlCore->xpath("cp:keywords")));
						$docProps->setCategory((string) self::array_item($xmlCore->xpath("cp:category")));
					}
				break;

				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties":
					$xmlCore = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
					if (is_object($xmlCore)) {
						$docProps = $excel->getProperties();
						if (isset($xmlCore->Company))
							$docProps->setCompany((string) $xmlCore->Company);
						if (isset($xmlCore->Manager))
							$docProps->setManager((string) $xmlCore->Manager);
					}
				break;

				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/custom-properties":
					$xmlCore = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
					if (is_object($xmlCore)) {
						$docProps = $excel->getProperties();
						foreach ($xmlCore as $xmlProperty) {
							$cellDataOfficeAttributes = $xmlProperty->attributes();
							if (isset($cellDataOfficeAttributes['name'])) {
								$propertyName = (string) $cellDataOfficeAttributes['name'];
								$cellDataOfficeChildren = $xmlProperty->children('http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');
								$attributeType = $cellDataOfficeChildren->getName();
								$attributeValue = (string) $cellDataOfficeChildren->{$attributeType};
								$attributeValue = PHPExcel_DocumentProperties::convertProperty($attributeValue,$attributeType);
								$attributeType = PHPExcel_DocumentProperties::convertPropertyType($attributeType);
								$docProps->setCustomProperty($propertyName,$attributeValue,$attributeType);
							}
						}
					}
				break;
				//Ribbon
				case "http://schemas.microsoft.com/office/2006/relationships/ui/extensibility":
					$customUI = $rel['Target'];
					if(!is_null($customUI)){
						$this->_readRibbon($excel, $customUI, $zip);
					}
				break;
				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument":
					$dir = dirname($rel["Target"]);
					$relsWorkbook = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/_rels/" . basename($rel["Target"]) . ".rels"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());  //~ http://schemas.openxmlformats.org/package/2006/relationships");
					$relsWorkbook->registerXPathNamespace("rel", "http://schemas.openxmlformats.org/package/2006/relationships");

					$sharedStrings = array();
					$xpath = self::array_item($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings']"));
					$xmlStrings = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/$xpath[Target]"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					if (isset($xmlStrings) && isset($xmlStrings->si)) {
						foreach ($xmlStrings->si as $val) {
							if (isset($val->t)) {
								$sharedStrings[] = PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $val->t );
							} elseif (isset($val->r)) {
								$sharedStrings[] = $this->_parseRichText($val);
							}
						}
					}

					$worksheets = array();
                    $macros = $customUI = NULL;
					foreach ($relsWorkbook->Relationship as $ele) {
						switch($ele['Type']){
						case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet": 
							$worksheets[(string) $ele["Id"]] = $ele["Target"];
							break;
						// a vbaProject ? (: some macros)
						case "http://schemas.microsoft.com/office/2006/relationships/vbaProject":
							$macros = $ele["Target"];
							break;
						}
					}

					if(!is_null($macros)){
						$macrosCode = $this->_getFromZipArchive($zip, 'xl/vbaProject.bin');//vbaProject.bin always in 'xl' dir and always named vbaProject.bin
						if($macrosCode !== false){
							$excel->setMacrosCode($macrosCode);
							$excel->setHasMacros(true);
							//short-circuit : not reading vbaProject.bin.rel to get Signature =>allways vbaProjectSignature.bin in 'xl' dir
							$Certificate = $this->_getFromZipArchive($zip, 'xl/vbaProjectSignature.bin');
							if($Certificate !== false)
								$excel->setMacrosCertificate($Certificate);
						}
					}
					$styles 	= array();
					$cellStyles = array();
					$xpath = self::array_item($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles']"));
					$xmlStyles = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/$xpath[Target]"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					$numFmts = null;
					if ($xmlStyles && $xmlStyles->numFmts[0]) {
						$numFmts = $xmlStyles->numFmts[0];
					}
					if (isset($numFmts) && ($numFmts !== NULL)) {
						$numFmts->registerXPathNamespace("sml", "http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					}
					if (!$this->_readDataOnly && $xmlStyles) {
						foreach ($xmlStyles->cellXfs->xf as $xf) {
							$numFmt = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;

							if ($xf["numFmtId"]) {
								if (isset($numFmts)) {
									$tmpNumFmt = self::array_item($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));

									if (isset($tmpNumFmt["formatCode"])) {
										$numFmt = (string) $tmpNumFmt["formatCode"];
									}
								}

								if ((int)$xf["numFmtId"] < 164) {
									$numFmt = PHPExcel_Style_NumberFormat::builtInFormatCode((int)$xf["numFmtId"]);
								}
							}
                            $quotePrefix = false;
							if (isset($xf["quotePrefix"])) {
                                $quotePrefix = (boolean) $xf["quotePrefix"];
                            }
							//$numFmt = str_replace('mm', 'i', $numFmt);
							//$numFmt = str_replace('h', 'H', $numFmt);

							$style = (object) array(
								"numFmt" => $numFmt,
								"font" => $xmlStyles->fonts->font[intval($xf["fontId"])],
								"fill" => $xmlStyles->fills->fill[intval($xf["fillId"])],
								"border" => $xmlStyles->borders->border[intval($xf["borderId"])],
								"alignment" => $xf->alignment,
								"protection" => $xf->protection,
								"quotePrefix" => $quotePrefix,
							);
							$styles[] = $style;

							// add style to cellXf collection
							$objStyle = new PHPExcel_Style;
							self::_readStyle($objStyle, $style);
							$excel->addCellXf($objStyle);
						}

						foreach ($xmlStyles->cellStyleXfs->xf as $xf) {
							$numFmt = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
							if ($numFmts && $xf["numFmtId"]) {
								$tmpNumFmt = self::array_item($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));
								if (isset($tmpNumFmt["formatCode"])) {
									$numFmt = (string) $tmpNumFmt["formatCode"];
								} else if ((int)$xf["numFmtId"] < 165) {
									$numFmt = PHPExcel_Style_NumberFormat::builtInFormatCode((int)$xf["numFmtId"]);
								}
							}

							$cellStyle = (object) array(
								"numFmt" => $numFmt,
								"font" => $xmlStyles->fonts->font[intval($xf["fontId"])],
								"fill" => $xmlStyles->fills->fill[intval($xf["fillId"])],
								"border" => $xmlStyles->borders->border[intval($xf["borderId"])],
								"alignment" => $xf->alignment,
								"protection" => $xf->protection,
								"quotePrefix" => $quotePrefix,
							);
							$cellStyles[] = $cellStyle;

							// add style to cellStyleXf collection
							$objStyle = new PHPExcel_Style;
							self::_readStyle($objStyle, $cellStyle);
							$excel->addCellStyleXf($objStyle);
						}
					}

					$dxfs = array();
					if (!$this->_readDataOnly && $xmlStyles) {
						//	Conditional Styles
						if ($xmlStyles->dxfs) {
							foreach ($xmlStyles->dxfs->dxf as $dxf) {
								$style = new PHPExcel_Style(FALSE, TRUE);
								self::_readStyle($style, $dxf);
								$dxfs[] = $style;
							}
						}
						//	Cell Styles
						if ($xmlStyles->cellStyles) {
							foreach ($xmlStyles->cellStyles->cellStyle as $cellStyle) {
								if (intval($cellStyle['builtinId']) == 0) {
									if (isset($cellStyles[intval($cellStyle['xfId'])])) {
										// Set default style
										$style = new PHPExcel_Style;
										self::_readStyle($style, $cellStyles[intval($cellStyle['xfId'])]);

										// normal style, currently not using it for anything
									}
								}
							}
						}
					}

					$xmlWorkbook = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");

					// Set base date
					if ($xmlWorkbook->workbookPr) {
						PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900);
						if (isset($xmlWorkbook->workbookPr['date1904'])) {
							if (self::boolean((string) $xmlWorkbook->workbookPr['date1904'])) {
								PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_MAC_1904);
							}
						}
					}

					$sheetId = 0; // keep track of new sheet id in final workbook
					$oldSheetId = -1; // keep track of old sheet id in final workbook
					$countSkippedSheets = 0; // keep track of number of skipped sheets
					$mapSheetId = array(); // mapping of sheet ids from old to new


					$charts = $chartDetails = array();

					if ($xmlWorkbook->sheets) {
						foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
							++$oldSheetId;

							// Check if sheet should be skipped
							if (isset($this->_loadSheetsOnly) && !in_array((string) $eleSheet["name"], $this->_loadSheetsOnly)) {
								++$countSkippedSheets;
								$mapSheetId[$oldSheetId] = null;
								continue;
							}

							// Map old sheet id in original workbook to new sheet id.
							// They will differ if loadSheetsOnly() is being used
							$mapSheetId[$oldSheetId] = $oldSheetId - $countSkippedSheets;

							// Load sheet
							$docSheet = $excel->createSheet();
							//	Use false for $updateFormulaCellReferences to prevent adjustment of worksheet
							//		references in formula cells... during the load, all formulae should be correct,
							//		and we're simply bringing the worksheet name in line with the formula, not the
							//		reverse
							$docSheet->setTitle((string) $eleSheet["name"],false);
							$fileWorksheet = $worksheets[(string) self::array_item($eleSheet->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "id")];
							$xmlSheet = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/$fileWorksheet"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");

							$sharedFormulas = array();

							if (isset($eleSheet["state"]) && (string) $eleSheet["state"] != '') {
								$docSheet->setSheetState( (string) $eleSheet["state"] );
							}

							if (isset($xmlSheet->sheetViews) && isset($xmlSheet->sheetViews->sheetView)) {
							    if (isset($xmlSheet->sheetViews->sheetView['zoomScale'])) {
								    $docSheet->getSheetView()->setZoomScale( intval($xmlSheet->sheetViews->sheetView['zoomScale']) );
								}

							    if (isset($xmlSheet->sheetViews->sheetView['zoomScaleNormal'])) {
								    $docSheet->getSheetView()->setZoomScaleNormal( intval($xmlSheet->sheetViews->sheetView['zoomScaleNormal']) );
								}

							    if (isset($xmlSheet->sheetViews->sheetView['view'])) {
								    $docSheet->getSheetView()->setView((string) $xmlSheet->sheetViews->sheetView['view']);
								}

								if (isset($xmlSheet->sheetViews->sheetView['showGridLines'])) {
									$docSheet->setShowGridLines(self::boolean((string)$xmlSheet->sheetViews->sheetView['showGridLines']));
								}

								if (isset($xmlSheet->sheetViews->sheetView['showRowColHeaders'])) {
									$docSheet->setShowRowColHeaders(self::boolean((string)$xmlSheet->sheetViews->sheetView['showRowColHeaders']));
								}

								if (isset($xmlSheet->sheetViews->sheetView['rightToLeft'])) {
									$docSheet->setRightToLeft(self::boolean((string)$xmlSheet->sheetViews->sheetView['rightToLeft']));
								}

								if (isset($xmlSheet->sheetViews->sheetView->pane)) {
								    if (isset($xmlSheet->sheetViews->sheetView->pane['topLeftCell'])) {
								        $docSheet->freezePane( (string)$xmlSheet->sheetViews->sheetView->pane['topLeftCell'] );
								    } else {
								        $xSplit = 0;
								        $ySplit = 0;

								        if (isset($xmlSheet->sheetViews->sheetView->pane['xSplit'])) {
								            $xSplit = 1 + intval($xmlSheet->sheetViews->sheetView->pane['xSplit']);
								        }

								    	if (isset($xmlSheet->sheetViews->sheetView->pane['ySplit'])) {
								            $ySplit = 1 + intval($xmlSheet->sheetViews->sheetView->pane['ySplit']);
								        }

								        $docSheet->freezePaneByColumnAndRow($xSplit, $ySplit);
								    }
								}

								if (isset($xmlSheet->sheetViews->sheetView->selection)) {
									if (isset($xmlSheet->sheetViews->sheetView->selection['sqref'])) {
										$sqref = (string)$xmlSheet->sheetViews->sheetView->selection['sqref'];
										$sqref = explode(' ', $sqref);
										$sqref = $sqref[0];
										$docSheet->setSelectedCells($sqref);
									}
								}

							}

							if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->tabColor)) {
								if (isset($xmlSheet->sheetPr->tabColor['rgb'])) {
									$docSheet->getTabColor()->setARGB( (string)$xmlSheet->sheetPr->tabColor['rgb'] );
								}
							}
							if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr['codeName'])) {
								$docSheet->setCodeName((string) $xmlSheet->sheetPr['codeName']);
							}
							if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->outlinePr)) {
								if (isset($xmlSheet->sheetPr->outlinePr['summaryRight']) &&
									!self::boolean((string) $xmlSheet->sheetPr->outlinePr['summaryRight'])) {
									$docSheet->setShowSummaryRight(FALSE);
								} else {
									$docSheet->setShowSummaryRight(TRUE);
								}

								if (isset($xmlSheet->sheetPr->outlinePr['summaryBelow']) &&
									!self::boolean((string) $xmlSheet->sheetPr->outlinePr['summaryBelow'])) {
									$docSheet->setShowSummaryBelow(FALSE);
								} else {
									$docSheet->setShowSummaryBelow(TRUE);
								}
							}

							if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->pageSetUpPr)) {
								if (isset($xmlSheet->sheetPr->pageSetUpPr['fitToPage']) &&
									!self::boolean((string) $xmlSheet->sheetPr->pageSetUpPr['fitToPage'])) {
									$docSheet->getPageSetup()->setFitToPage(FALSE);
								} else {
									$docSheet->getPageSetup()->setFitToPage(TRUE);
								}
							}

							if (isset($xmlSheet->sheetFormatPr)) {
								if (isset($xmlSheet->sheetFormatPr['customHeight']) &&
									self::boolean((string) $xmlSheet->sheetFormatPr['customHeight']) &&
									isset($xmlSheet->sheetFormatPr['defaultRowHeight'])) {
									$docSheet->getDefaultRowDimension()->setRowHeight( (float)$xmlSheet->sheetFormatPr['defaultRowHeight'] );
								}
								if (isset($xmlSheet->sheetFormatPr['defaultColWidth'])) {
									$docSheet->getDefaultColumnDimension()->setWidth( (float)$xmlSheet->sheetFormatPr['defaultColWidth'] );
								}
								if (isset($xmlSheet->sheetFormatPr['zeroHeight']) &&
									((string)$xmlSheet->sheetFormatPr['zeroHeight'] == '1')) {
									$docSheet->getDefaultRowDimension()->setzeroHeight(true);
								}
							}

							if (isset($xmlSheet->cols) && !$this->_readDataOnly) {
								foreach ($xmlSheet->cols->col as $col) {
                                    for ($i = intval($col["min"]) - 1; $i < intval($col["max"]); ++$i) {
										if ($col["style"] && !$this->_readDataOnly) {
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setXfIndex(intval($col["style"]));
										}
										if (self::boolean($col["bestFit"])) {
											//$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(TRUE);
										}
										if (self::boolean($col["hidden"])) {
                                        // echo PHPExcel_Cell::stringFromColumnIndex($i),': HIDDEN COLUMN',PHP_EOL;
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setVisible(FALSE);
										}
										if (self::boolean($col["collapsed"])) {
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setCollapsed(TRUE);
										}
										if ($col["outlineLevel"] > 0) {
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setOutlineLevel(intval($col["outlineLevel"]));
										}
										$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(floatval($col["width"]));

										if (intval($col["max"]) == 16384) {
											break;
										}
									}
								}
							}

							if (isset($xmlSheet->printOptions) && !$this->_readDataOnly) {
								if (self::boolean((string) $xmlSheet->printOptions['gridLinesSet'])) {
									$docSheet->setShowGridlines(TRUE);
								}

								if (self::boolean((string) $xmlSheet->printOptions['gridLines'])) {
									$docSheet->setPrintGridlines(TRUE);
								}

								if (self::boolean((string) $xmlSheet->printOptions['horizontalCentered'])) {
									$docSheet->getPageSetup()->setHorizontalCentered(TRUE);
								}
								if (self::boolean((string) $xmlSheet->printOptions['verticalCentered'])) {
									$docSheet->getPageSetup()->setVerticalCentered(TRUE);
								}
							}

							if ($xmlSheet && $xmlSheet->sheetData && $xmlSheet->sheetData->row) {
								foreach ($xmlSheet->sheetData->row as $row) {
									if ($row["ht"] && !$this->_readDataOnly) {
										$docSheet->getRowDimension(intval($row["r"]))->setRowHeight(floatval($row["ht"]));
									}
									if (self::boolean($row["hidden"]) && !$this->_readDataOnly) {
										$docSheet->getRowDimension(intval($row["r"]))->setVisible(FALSE);
									}
									if (self::boolean($row["collapsed"])) {
										$docSheet->getRowDimension(intval($row["r"]))->setCollapsed(TRUE);
									}
									if ($row["outlineLevel"] > 0) {
										$docSheet->getRowDimension(intval($row["r"]))->setOutlineLevel(intval($row["outlineLevel"]));
									}
									if ($row["s"] && !$this->_readDataOnly) {
										$docSheet->getRowDimension(intval($row["r"]))->setXfIndex(intval($row["s"]));
									}

									foreach ($row->c as $c) {
										$r 					= (string) $c["r"];
										$cellDataType 		= (string) $c["t"];
										$value				= null;
										$calculatedValue 	= null;

										// Read cell?
										if ($this->getReadFilter() !== NULL) {
											$coordinates = PHPExcel_Cell::coordinateFromString($r);

											if (!$this->getReadFilter()->readCell($coordinates[0], $coordinates[1], $docSheet->getTitle())) {
												continue;
											}
										}

	//									echo 'Reading cell ', $coordinates[0], $coordinates[1], PHP_EOL;
	//									print_r($c);
	//									echo PHP_EOL;
	//									echo 'Cell Data Type is ', $cellDataType, ': ';
	//
										// Read cell!
										switch ($cellDataType) {
											case "s":
	//											echo 'String', PHP_EOL;
												if ((string)$c->v != '') {
													$value = $sharedStrings[intval($c->v)];

													if ($value instanceof PHPExcel_RichText) {
														$value = clone $value;
													}
												} else {
													$value = '';
												}

												break;
											case "b":
	//											echo 'Boolean', PHP_EOL;
												if (!isset($c->f)) {
													$value = self::_castToBool($c);
												} else {
													// Formula
													$this->_castToFormula($c,$r,$cellDataType,$value,$calculatedValue,$sharedFormulas,'_castToBool');
													if (isset($c->f['t'])) {
														$att = array();
														$att = $c->f;
														$docSheet->getCell($r)->setFormulaAttributes($att);
													}
	//												echo '$calculatedValue = ', $calculatedValue, PHP_EOL;
												}
												break;
											case "inlineStr":
	//											echo 'Inline String', PHP_EOL;
												$value = $this->_parseRichText($c->is);

												break;
											case "e":
	//											echo 'Error', PHP_EOL;
												if (!isset($c->f)) {
													$value = self::_castToError($c);
												} else {
													// Formula
													$this->_castToFormula($c,$r,$cellDataType,$value,$calculatedValue,$sharedFormulas,'_castToError');
	//												echo '$calculatedValue = ', $calculatedValue, PHP_EOL;
												}

												break;

											default:
	//											echo 'Default', PHP_EOL;
												if (!isset($c->f)) {
	//												echo 'Not a Formula', PHP_EOL;
													$value = self::_castToString($c);
												} else {
	//												echo 'Treat as Formula', PHP_EOL;
													// Formula
													$this->_castToFormula($c,$r,$cellDataType,$value,$calculatedValue,$sharedFormulas,'_castToString');
	//												echo '$calculatedValue = ', $calculatedValue, PHP_EOL;
												}

												break;
										}
	//									echo 'Value is ', $value, PHP_EOL;

										// Check for numeric values
										if (is_numeric($value) && $cellDataType != 's') {
											if ($value == (int)$value) $value = (int)$value;
											elseif ($value == (float)$value) $value = (float)$value;
											elseif ($value == (double)$value) $value = (double)$value;
										}

										// Rich text?
										if ($value instanceof PHPExcel_RichText && $this->_readDataOnly) {
											$value = $value->getPlainText();
										}

										$cell = $docSheet->getCell($r);
										// Assign value
										if ($cellDataType != '') {
											$cell->setValueExplicit($value, $cellDataType);
										} else {
											$cell->setValue($value);
										}
										if ($calculatedValue !== NULL) {
											$cell->setCalculatedValue($calculatedValue);
										}

										// Style information?
										if ($c["s"] && !$this->_readDataOnly) {
											// no style index means 0, it seems
											$cell->setXfIndex(isset($styles[intval($c["s"])]) ?
												intval($c["s"]) : 0);
										}
									}
								}
							}

							$conditionals = array();
							if (!$this->_readDataOnly && $xmlSheet && $xmlSheet->conditionalFormatting) {
								foreach ($xmlSheet->conditionalFormatting as $conditional) {
									foreach ($conditional->cfRule as $cfRule) {
										if (
											(
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_NONE ||
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_CELLIS ||
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT ||
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_EXPRESSION
											) && isset($dxfs[intval($cfRule["dxfId"])])
										) {
											$conditionals[(string) $conditional["sqref"]][intval($cfRule["priority"])] = $cfRule;
										}
									}
								}

								foreach ($conditionals as $ref => $cfRules) {
									ksort($cfRules);
									$conditionalStyles = array();
									foreach ($cfRules as $cfRule) {
										$objConditional = new PHPExcel_Style_Conditional();
										$objConditional->setConditionType((string)$cfRule["type"]);
										$objConditional->setOperatorType((string)$cfRule["operator"]);

										if ((string)$cfRule["text"] != '') {
											$objConditional->setText((string)$cfRule["text"]);
										}

										if (count($cfRule->formula) > 1) {
											foreach ($cfRule->formula as $formula) {
												$objConditional->addCondition((string)$formula);
											}
										} else {
											$objConditional->addCondition((string)$cfRule->formula);
										}
										$objConditional->setStyle(clone $dxfs[intval($cfRule["dxfId"])]);
										$conditionalStyles[] = $objConditional;
									}

									// Extract all cell references in $ref
									$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($ref);
									foreach ($aReferences as $reference) {
										$docSheet->getStyle($reference)->setConditionalStyles($conditionalStyles);
									}
								}
							}

							$aKeys = array("sheet", "objects", "scenarios", "formatCells", "formatColumns", "formatRows", "insertColumns", "insertRows", "insertHyperlinks", "deleteColumns", "deleteRows", "selectLockedCells", "sort", "autoFilter", "pivotTables", "selectUnlockedCells");
							if (!$this->_readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
								foreach ($aKeys as $key) {
									$method = "set" . ucfirst($key);
									$docSheet->getProtection()->$method(self::boolean((string) $xmlSheet->sheetProtection[$key]));
								}
							}

							if (!$this->_readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
								$docSheet->getProtection()->setPassword((string) $xmlSheet->sheetProtection["password"], TRUE);
								if ($xmlSheet->protectedRanges->protectedRange) {
									foreach ($xmlSheet->protectedRanges->protectedRange as $protectedRange) {
										$docSheet->protectCells((string) $protectedRange["sqref"], (string) $protectedRange["password"], true);
									}
								}
							}

							if ($xmlSheet && $xmlSheet->autoFilter && !$this->_readDataOnly) {
								$autoFilter = $docSheet->getAutoFilter();
								$autoFilter->setRange((string) $xmlSheet->autoFilter["ref"]);
								foreach ($xmlSheet->autoFilter->filterColumn as $filterColumn) {
									$column = $autoFilter->getColumnByOffset((integer) $filterColumn["colId"]);
									//	Check for standard filters
									if ($filterColumn->filters) {
										$column->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER);
										$filters = $filterColumn->filters;
										if ((isset($filters["blank"])) && ($filters["blank"] == 1)) {
											$column->createRule()->setRule(
												NULL,	//	Operator is undefined, but always treated as EQUAL
												''
											)
											->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_FILTER);
										}
										//	Standard filters are always an OR join, so no join rule needs to be set
										//	Entries can be either filter elements
										foreach ($filters->filter as $filterRule) {
											$column->createRule()->setRule(
												NULL,	//	Operator is undefined, but always treated as EQUAL
												(string) $filterRule["val"]
											)
											->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_FILTER);
										}
										//	Or Date Group elements
										foreach ($filters->dateGroupItem as $dateGroupItem) {
											$column->createRule()->setRule(
												NULL,	//	Operator is undefined, but always treated as EQUAL
												array(
													'year' => (string) $dateGroupItem["year"],
													'month' => (string) $dateGroupItem["month"],
													'day' => (string) $dateGroupItem["day"],
													'hour' => (string) $dateGroupItem["hour"],
													'minute' => (string) $dateGroupItem["minute"],
													'second' => (string) $dateGroupItem["second"],
												),
												(string) $dateGroupItem["dateTimeGrouping"]
											)
											->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP);
										}
									}
									//	Check for custom filters
									if ($filterColumn->customFilters) {
										$column->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
										$customFilters = $filterColumn->customFilters;
										//	Custom filters can an AND or an OR join;
										//		and there should only ever be one or two entries
										if ((isset($customFilters["and"])) && ($customFilters["and"] == 1)) {
											$column->setJoin(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND);
										}
										foreach ($customFilters->customFilter as $filterRule) {
											$column->createRule()->setRule(
												(string) $filterRule["operator"],
												(string) $filterRule["val"]
											)
											->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
										}
									}
									//	Check for dynamic filters
									if ($filterColumn->dynamicFilter) {
										$column->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
										//	We should only ever have one dynamic filter
										foreach ($filterColumn->dynamicFilter as $filterRule) {
											$column->createRule()->setRule(
												NULL,	//	Operator is undefined, but always treated as EQUAL
												(string) $filterRule["val"],
												(string) $filterRule["type"]
											)
											->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER);
											if (isset($filterRule["val"])) {
												$column->setAttribute('val',(string) $filterRule["val"]);
											}
											if (isset($filterRule["maxVal"])) {
												$column->setAttribute('maxVal',(string) $filterRule["maxVal"]);
											}
										}
									}
									//	Check for dynamic filters
									if ($filterColumn->top10) {
										$column->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER);
										//	We should only ever have one top10 filter
										foreach ($filterColumn->top10 as $filterRule) {
											$column->createRule()->setRule(
												(((isset($filterRule["percent"])) && ($filterRule["percent"] == 1))
													? PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT
													: PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE
												),
												(string) $filterRule["val"],
												(((isset($filterRule["top"])) && ($filterRule["top"] == 1))
													? PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP
													: PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM
												)
											)
											->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_TOPTENFILTER);
										}
									}
								}
							}

							if ($xmlSheet && $xmlSheet->mergeCells && $xmlSheet->mergeCells->mergeCell && !$this->_readDataOnly) {
								foreach ($xmlSheet->mergeCells->mergeCell as $mergeCell) {
									$mergeRef = (string) $mergeCell["ref"];
									if (strpos($mergeRef,':') !== FALSE) {
										$docSheet->mergeCells((string) $mergeCell["ref"]);
									}
								}
							}

							if ($xmlSheet && $xmlSheet->pageMargins && !$this->_readDataOnly) {
								$docPageMargins = $docSheet->getPageMargins();
								$docPageMargins->setLeft(floatval($xmlSheet->pageMargins["left"]));
								$docPageMargins->setRight(floatval($xmlSheet->pageMargins["right"]));
								$docPageMargins->setTop(floatval($xmlSheet->pageMargins["top"]));
								$docPageMargins->setBottom(floatval($xmlSheet->pageMargins["bottom"]));
								$docPageMargins->setHeader(floatval($xmlSheet->pageMargins["header"]));
								$docPageMargins->setFooter(floatval($xmlSheet->pageMargins["footer"]));
							}

							if ($xmlSheet && $xmlSheet->pageSetup && !$this->_readDataOnly) {
								$docPageSetup = $docSheet->getPageSetup();

								if (isset($xmlSheet->pageSetup["orientation"])) {
									$docPageSetup->setOrientation((string) $xmlSheet->pageSetup["orientation"]);
								}
								if (isset($xmlSheet->pageSetup["paperSize"])) {
									$docPageSetup->setPaperSize(intval($xmlSheet->pageSetup["paperSize"]));
								}
								if (isset($xmlSheet->pageSetup["scale"])) {
									$docPageSetup->setScale(intval($xmlSheet->pageSetup["scale"]), FALSE);
								}
								if (isset($xmlSheet->pageSetup["fitToHeight"]) && intval($xmlSheet->pageSetup["fitToHeight"]) >= 0) {
									$docPageSetup->setFitToHeight(intval($xmlSheet->pageSetup["fitToHeight"]), FALSE);
								}
								if (isset($xmlSheet->pageSetup["fitToWidth"]) && intval($xmlSheet->pageSetup["fitToWidth"]) >= 0) {
									$docPageSetup->setFitToWidth(intval($xmlSheet->pageSetup["fitToWidth"]), FALSE);
								}
								if (isset($xmlSheet->pageSetup["firstPageNumber"]) && isset($xmlSheet->pageSetup["useFirstPageNumber"]) &&
									self::boolean((string) $xmlSheet->pageSetup["useFirstPageNumber"])) {
									$docPageSetup->setFirstPageNumber(intval($xmlSheet->pageSetup["firstPageNumber"]));
								}
							}

							if ($xmlSheet && $xmlSheet->headerFooter && !$this->_readDataOnly) {
								$docHeaderFooter = $docSheet->getHeaderFooter();

								if (isset($xmlSheet->headerFooter["differentOddEven"]) &&
									self::boolean((string)$xmlSheet->headerFooter["differentOddEven"])) {
									$docHeaderFooter->setDifferentOddEven(TRUE);
								} else {
									$docHeaderFooter->setDifferentOddEven(FALSE);
								}
								if (isset($xmlSheet->headerFooter["differentFirst"]) &&
									self::boolean((string)$xmlSheet->headerFooter["differentFirst"])) {
									$docHeaderFooter->setDifferentFirst(TRUE);
								} else {
									$docHeaderFooter->setDifferentFirst(FALSE);
								}
								if (isset($xmlSheet->headerFooter["scaleWithDoc"]) &&
									!self::boolean((string)$xmlSheet->headerFooter["scaleWithDoc"])) {
									$docHeaderFooter->setScaleWithDocument(FALSE);
								} else {
									$docHeaderFooter->setScaleWithDocument(TRUE);
								}
								if (isset($xmlSheet->headerFooter["alignWithMargins"]) &&
									!self::boolean((string)$xmlSheet->headerFooter["alignWithMargins"])) {
									$docHeaderFooter->setAlignWithMargins(FALSE);
								} else {
									$docHeaderFooter->setAlignWithMargins(TRUE);
								}

								$docHeaderFooter->setOddHeader((string) $xmlSheet->headerFooter->oddHeader);
								$docHeaderFooter->setOddFooter((string) $xmlSheet->headerFooter->oddFooter);
								$docHeaderFooter->setEvenHeader((string) $xmlSheet->headerFooter->evenHeader);
								$docHeaderFooter->setEvenFooter((string) $xmlSheet->headerFooter->evenFooter);
								$docHeaderFooter->setFirstHeader((string) $xmlSheet->headerFooter->firstHeader);
								$docHeaderFooter->setFirstFooter((string) $xmlSheet->headerFooter->firstFooter);
							}

							if ($xmlSheet && $xmlSheet->rowBreaks && $xmlSheet->rowBreaks->brk && !$this->_readDataOnly) {
								foreach ($xmlSheet->rowBreaks->brk as $brk) {
									if ($brk["man"]) {
										$docSheet->setBreak("A$brk[id]", PHPExcel_Worksheet::BREAK_ROW);
									}
								}
							}
							if ($xmlSheet && $xmlSheet->colBreaks && $xmlSheet->colBreaks->brk && !$this->_readDataOnly) {
								foreach ($xmlSheet->colBreaks->brk as $brk) {
									if ($brk["man"]) {
										$docSheet->setBreak(PHPExcel_Cell::stringFromColumnIndex((string) $brk["id"]) . "1", PHPExcel_Worksheet::BREAK_COLUMN);
									}
								}
							}

							if ($xmlSheet && $xmlSheet->dataValidations && !$this->_readDataOnly) {
								foreach ($xmlSheet->dataValidations->dataValidation as $dataValidation) {
								    // Uppercase coordinate
							    	$range = strtoupper($dataValidation["sqref"]);
									$rangeSet = explode(' ',$range);
									foreach($rangeSet as $range) {
										$stRange = $docSheet->shrinkRangeToFit($range);

										// Extract all cell references in $range
										$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($stRange);
										foreach ($aReferences as $reference) {
											// Create validation
											$docValidation = $docSheet->getCell($reference)->getDataValidation();
											$docValidation->setType((string) $dataValidation["type"]);
											$docValidation->setErrorStyle((string) $dataValidation["errorStyle"]);
											$docValidation->setOperator((string) $dataValidation["operator"]);
											$docValidation->setAllowBlank($dataValidation["allowBlank"] != 0);
											$docValidation->setShowDropDown($dataValidation["showDropDown"] == 0);
											$docValidation->setShowInputMessage($dataValidation["showInputMessage"] != 0);
											$docValidation->setShowErrorMessage($dataValidation["showErrorMessage"] != 0);
											$docValidation->setErrorTitle((string) $dataValidation["errorTitle"]);
											$docValidation->setError((string) $dataValidation["error"]);
											$docValidation->setPromptTitle((string) $dataValidation["promptTitle"]);
											$docValidation->setPrompt((string) $dataValidation["prompt"]);
											$docValidation->setFormula1((string) $dataValidation->formula1);
											$docValidation->setFormula2((string) $dataValidation->formula2);
										}
									}
								}
							}

							// Add hyperlinks
							$hyperlinks = array();
							if (!$this->_readDataOnly) {
								// Locate hyperlink relations
								if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
									$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") , 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
									foreach ($relsWorksheet->Relationship as $ele) {
										if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink") {
											$hyperlinks[(string)$ele["Id"]] = (string)$ele["Target"];
										}
									}
								}

								// Loop through hyperlinks
								if ($xmlSheet && $xmlSheet->hyperlinks) {
									foreach ($xmlSheet->hyperlinks->hyperlink as $hyperlink) {
										// Link url
										$linkRel = $hyperlink->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');

										foreach (PHPExcel_Cell::extractAllCellReferencesInRange($hyperlink['ref']) as $cellReference) {
											$cell = $docSheet->getCell( $cellReference );
											if (isset($linkRel['id'])) {
												$hyperlinkUrl = $hyperlinks[ (string)$linkRel['id'] ];
												if (isset($hyperlink['location'])) {
													$hyperlinkUrl .= '#' . (string) $hyperlink['location'];
												}
												$cell->getHyperlink()->setUrl($hyperlinkUrl);
											} elseif (isset($hyperlink['location'])) {
												$cell->getHyperlink()->setUrl( 'sheet://' . (string)$hyperlink['location'] );
											}

											// Tooltip
											if (isset($hyperlink['tooltip'])) {
												$cell->getHyperlink()->setTooltip( (string)$hyperlink['tooltip'] );
											}
										}
									}
								}
							}

							// Add comments
							$comments = array();
							$vmlComments = array();
							if (!$this->_readDataOnly) {
								// Locate comment relations
								if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
									$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") , 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
									foreach ($relsWorksheet->Relationship as $ele) {
									    if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments") {
											$comments[(string)$ele["Id"]] = (string)$ele["Target"];
										}
									    if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing") {
											$vmlComments[(string)$ele["Id"]] = (string)$ele["Target"];
										}
									}
								}

								// Loop through comments
								foreach ($comments as $relName => $relPath) {
									// Load comments file
									$relPath = PHPExcel_Shared_File::realpath(dirname("$dir/$fileWorksheet") . "/" . $relPath);
									$commentsFile = simplexml_load_string($this->_getFromZipArchive($zip, $relPath) , 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());

									// Utility variables
									$authors = array();

									// Loop through authors
									foreach ($commentsFile->authors->author as $author) {
										$authors[] = (string)$author;
									}

									// Loop through contents
									foreach ($commentsFile->commentList->comment as $comment) {
										$docSheet->getComment( (string)$comment['ref'] )->setAuthor( $authors[(string)$comment['authorId']] );
										$docSheet->getComment( (string)$comment['ref'] )->setText( $this->_parseRichText($comment->text) );
									}
								}

								// Loop through VML comments
							    foreach ($vmlComments as $relName => $relPath) {
									// Load VML comments file
									$relPath = PHPExcel_Shared_File::realpath(dirname("$dir/$fileWorksheet") . "/" . $relPath);
									$vmlCommentsFile = simplexml_load_string( $this->_getFromZipArchive($zip, $relPath) , 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
									$vmlCommentsFile->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

									$shapes = $vmlCommentsFile->xpath('//v:shape');
									foreach ($shapes as $shape) {
										$shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

										if (isset($shape['style'])) {
	    									$style        = (string)$shape['style'];
	    									$fillColor    = strtoupper( substr( (string)$shape['fillcolor'], 1 ) );
	    									$column       = null;
	    									$row          = null;

	    									$clientData   = $shape->xpath('.//x:ClientData');
	    									if (is_array($clientData) && !empty($clientData)) {
	        									$clientData   = $clientData[0];

	        									if ( isset($clientData['ObjectType']) && (string)$clientData['ObjectType'] == 'Note' ) {
	        									    $temp = $clientData->xpath('.//x:Row');
	        									    if (is_array($temp)) $row = $temp[0];

	        									    $temp = $clientData->xpath('.//x:Column');
	        									    if (is_array($temp)) $column = $temp[0];
	        									}
	    									}

	    									if (($column !== NULL) && ($row !== NULL)) {
	    									    // Set comment properties
	    									    $comment = $docSheet->getCommentByColumnAndRow((string) $column, $row + 1);
	    									    $comment->getFillColor()->setRGB( $fillColor );

	    									    // Parse style
	    									    $styleArray = explode(';', str_replace(' ', '', $style));
	    									    foreach ($styleArray as $stylePair) {
	    									        $stylePair = explode(':', $stylePair);

	    									        if ($stylePair[0] == 'margin-left')     $comment->setMarginLeft($stylePair[1]);
	    									        if ($stylePair[0] == 'margin-top')      $comment->setMarginTop($stylePair[1]);
	    									        if ($stylePair[0] == 'width')           $comment->setWidth($stylePair[1]);
	    									        if ($stylePair[0] == 'height')          $comment->setHeight($stylePair[1]);
	    									        if ($stylePair[0] == 'visibility')      $comment->setVisible( $stylePair[1] == 'visible' );

	    									    }
	    									}
										}
									}
								}

								// Header/footer images
								if ($xmlSheet && $xmlSheet->legacyDrawingHF && !$this->_readDataOnly) {
									if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
										$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") , 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
										$vmlRelationship = '';

										foreach ($relsWorksheet->Relationship as $ele) {
											if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing") {
												$vmlRelationship = self::dir_add("$dir/$fileWorksheet", $ele["Target"]);
											}
										}

										if ($vmlRelationship != '') {
											// Fetch linked images
											$relsVML = simplexml_load_string($this->_getFromZipArchive($zip,  dirname($vmlRelationship) . '/_rels/' . basename($vmlRelationship) . '.rels' ), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
											$drawings = array();
											foreach ($relsVML->Relationship as $ele) {
												if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image") {
													$drawings[(string) $ele["Id"]] = self::dir_add($vmlRelationship, $ele["Target"]);
												}
											}

											// Fetch VML document
											$vmlDrawing = simplexml_load_string($this->_getFromZipArchive($zip, $vmlRelationship), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
											$vmlDrawing->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

											$hfImages = array();

											$shapes = $vmlDrawing->xpath('//v:shape');
											foreach ($shapes as $idx => $shape) {
												$shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');
												$imageData = $shape->xpath('//v:imagedata');
												$imageData = $imageData[$idx];

												$imageData = $imageData->attributes('urn:schemas-microsoft-com:office:office');
												$style = self::toCSSArray( (string)$shape['style'] );

												$hfImages[ (string)$shape['id'] ] = new PHPExcel_Worksheet_HeaderFooterDrawing();
												if (isset($imageData['title'])) {
													$hfImages[ (string)$shape['id'] ]->setName( (string)$imageData['title'] );
												}

												$hfImages[ (string)$shape['id'] ]->setPath("zip://".PHPExcel_Shared_File::realpath($pFilename)."#" . $drawings[(string)$imageData['relid']], false);
												$hfImages[ (string)$shape['id'] ]->setResizeProportional(false);
												$hfImages[ (string)$shape['id'] ]->setWidth($style['width']);
												$hfImages[ (string)$shape['id'] ]->setHeight($style['height']);
												if (isset($style['margin-left'])) {
													$hfImages[ (string)$shape['id'] ]->setOffsetX($style['margin-left']);
												}
												$hfImages[ (string)$shape['id'] ]->setOffsetY($style['margin-top']);
												$hfImages[ (string)$shape['id'] ]->setResizeProportional(true);
											}

											$docSheet->getHeaderFooter()->setImages($hfImages);
										}
									}
								}

							}

                            // TODO: Autoshapes from twoCellAnchors!
							if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
								$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") , 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
								$drawings = array();
								foreach ($relsWorksheet->Relationship as $ele) {
									if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing") {
										$drawings[(string) $ele["Id"]] = self::dir_add("$dir/$fileWorksheet", $ele["Target"]);
									}
								}
								if ($xmlSheet->drawing && !$this->_readDataOnly) {
									foreach ($xmlSheet->drawing as $drawing) {
										$fileDrawing = $drawings[(string) self::array_item($drawing->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "id")];
										$relsDrawing = simplexml_load_string($this->_getFromZipArchive($zip,  dirname($fileDrawing) . "/_rels/" . basename($fileDrawing) . ".rels") , 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions()); //~ http://schemas.openxmlformats.org/package/2006/relationships");
										$images = array();

										if ($relsDrawing && $relsDrawing->Relationship) {
											foreach ($relsDrawing->Relationship as $ele) {
												if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image") {
													$images[(string) $ele["Id"]] = self::dir_add($fileDrawing, $ele["Target"]);
												} elseif ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/chart") {
													if ($this->_includeCharts) {
														$charts[self::dir_add($fileDrawing, $ele["Target"])] = array('id'		=> (string) $ele["Id"],
																													 'sheet'	=> $docSheet->getTitle()
																													);
													}
												}
											}
										}
										$xmlDrawing = simplexml_load_string($this->_getFromZipArchive($zip, $fileDrawing), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions())->children("http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing");

										if ($xmlDrawing->oneCellAnchor) {
											foreach ($xmlDrawing->oneCellAnchor as $oneCellAnchor) {
												if ($oneCellAnchor->pic->blipFill) {
													$blip = $oneCellAnchor->pic->blipFill->children("http://schemas.openxmlformats.org/drawingml/2006/main")->blip;
													$xfrm = $oneCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->xfrm;
													$outerShdw = $oneCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->effectLst->outerShdw;
													$objDrawing = new PHPExcel_Worksheet_Drawing;
													$objDrawing->setName((string) self::array_item($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), "name"));
													$objDrawing->setDescription((string) self::array_item($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), "descr"));
													$objDrawing->setPath("zip://".PHPExcel_Shared_File::realpath($pFilename)."#" . $images[(string) self::array_item($blip->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "embed")], false);
													$objDrawing->setCoordinates(PHPExcel_Cell::stringFromColumnIndex((string) $oneCellAnchor->from->col) . ($oneCellAnchor->from->row + 1));
													$objDrawing->setOffsetX(PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->colOff));
													$objDrawing->setOffsetY(PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->rowOff));
													$objDrawing->setResizeProportional(false);
													$objDrawing->setWidth(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cx")));
													$objDrawing->setHeight(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cy")));
													if ($xfrm) {
														$objDrawing->setRotation(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($xfrm->attributes(), "rot")));
													}
													if ($outerShdw) {
														$shadow = $objDrawing->getShadow();
														$shadow->setVisible(true);
														$shadow->setBlurRadius(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "blurRad")));
														$shadow->setDistance(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "dist")));
														$shadow->setDirection(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($outerShdw->attributes(), "dir")));
														$shadow->setAlignment((string) self::array_item($outerShdw->attributes(), "algn"));
														$shadow->getColor()->setRGB(self::array_item($outerShdw->srgbClr->attributes(), "val"));
														$shadow->setAlpha(self::array_item($outerShdw->srgbClr->alpha->attributes(), "val") / 1000);
													}
													$objDrawing->setWorksheet($docSheet);
												} else {
													//	? Can charts be positioned with a oneCellAnchor ?
													$coordinates	= PHPExcel_Cell::stringFromColumnIndex((string) $oneCellAnchor->from->col) . ($oneCellAnchor->from->row + 1);
													$offsetX		= PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->colOff);
													$offsetY		= PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->rowOff);
													$width			= PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cx"));
													$height			= PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cy"));
												}
											}
										}
										if ($xmlDrawing->twoCellAnchor) {
											foreach ($xmlDrawing->twoCellAnchor as $twoCellAnchor) {
												if ($twoCellAnchor->pic->blipFill) {
													$blip = $twoCellAnchor->pic->blipFill->children("http://schemas.openxmlformats.org/drawingml/2006/main")->blip;
													$xfrm = $twoCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->xfrm;
													$outerShdw = $twoCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->effectLst->outerShdw;
													$objDrawing = new PHPExcel_Worksheet_Drawing;
													$objDrawing->setName((string) self::array_item($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), "name"));
													$objDrawing->setDescription((string) self::array_item($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), "descr"));
													$objDrawing->setPath("zip://".PHPExcel_Shared_File::realpath($pFilename)."#" . $images[(string) self::array_item($blip->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "embed")], false);
													$objDrawing->setCoordinates(PHPExcel_Cell::stringFromColumnIndex((string) $twoCellAnchor->from->col) . ($twoCellAnchor->from->row + 1));
													$objDrawing->setOffsetX(PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->from->colOff));
													$objDrawing->setOffsetY(PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->from->rowOff));
													$objDrawing->setResizeProportional(false);

													$objDrawing->setWidth(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($xfrm->ext->attributes(), "cx")));
													$objDrawing->setHeight(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($xfrm->ext->attributes(), "cy")));

													if ($xfrm) {
														$objDrawing->setRotation(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($xfrm->attributes(), "rot")));
													}
													if ($outerShdw) {
														$shadow = $objDrawing->getShadow();
														$shadow->setVisible(true);
														$shadow->setBlurRadius(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "blurRad")));
														$shadow->setDistance(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "dist")));
														$shadow->setDirection(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($outerShdw->attributes(), "dir")));
														$shadow->setAlignment((string) self::array_item($outerShdw->attributes(), "algn"));
														$shadow->getColor()->setRGB(self::array_item($outerShdw->srgbClr->attributes(), "val"));
														$shadow->setAlpha(self::array_item($outerShdw->srgbClr->alpha->attributes(), "val") / 1000);
													}
													$objDrawing->setWorksheet($docSheet);
												} elseif(($this->_includeCharts) && ($twoCellAnchor->graphicFrame)) {
													$fromCoordinate	= PHPExcel_Cell::stringFromColumnIndex((string) $twoCellAnchor->from->col) . ($twoCellAnchor->from->row + 1);
													$fromOffsetX	= PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->from->colOff);
													$fromOffsetY	= PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->from->rowOff);
													$toCoordinate	= PHPExcel_Cell::stringFromColumnIndex((string) $twoCellAnchor->to->col) . ($twoCellAnchor->to->row + 1);
													$toOffsetX		= PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->to->colOff);
													$toOffsetY		= PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->to->rowOff);
													$graphic		= $twoCellAnchor->graphicFrame->children("http://schemas.openxmlformats.org/drawingml/2006/main")->graphic;
													$chartRef		= $graphic->graphicData->children("http://schemas.openxmlformats.org/drawingml/2006/chart")->chart;
													$thisChart		= (string) $chartRef->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships");

													$chartDetails[$docSheet->getTitle().'!'.$thisChart] =
															array(	'fromCoordinate'	=> $fromCoordinate,
																	'fromOffsetX'		=> $fromOffsetX,
																	'fromOffsetY'		=> $fromOffsetY,
																	'toCoordinate'		=> $toCoordinate,
																	'toOffsetX'			=> $toOffsetX,
																	'toOffsetY'			=> $toOffsetY,
																	'worksheetTitle'	=> $docSheet->getTitle()
																 );
												}
											}
										}

									}
								}
							}

							// Loop through definedNames
							if ($xmlWorkbook->definedNames) {
								foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
									// Extract range
									$extractedRange = (string)$definedName;
									$extractedRange = preg_replace('/\'(\w+)\'\!/', '', $extractedRange);
									if (($spos = strpos($extractedRange,'!')) !== false) {
										$extractedRange = substr($extractedRange,0,$spos).str_replace('$', '', substr($extractedRange,$spos));
									} else {
										$extractedRange = str_replace('$', '', $extractedRange);
									}

									// Valid range?
									if (stripos((string)$definedName, '#REF!') !== FALSE || $extractedRange == '') {
										continue;
									}

									// Some definedNames are only applicable if we are on the same sheet...
									if ((string)$definedName['localSheetId'] != '' && (string)$definedName['localSheetId'] == $sheetId) {
										// Switch on type
										switch ((string)$definedName['name']) {

											case '_xlnm._FilterDatabase':
												if ((string)$definedName['hidden'] !== '1') {
													$docSheet->getAutoFilter()->setRange($extractedRange);
												}
												break;

											case '_xlnm.Print_Titles':
												// Split $extractedRange
												$extractedRange = explode(',', $extractedRange);

												// Set print titles
												foreach ($extractedRange as $range) {
													$matches = array();
													$range = str_replace('$', '', $range);

													// check for repeating columns, e g. 'A:A' or 'A:D'
													if (preg_match('/!?([A-Z]+)\:([A-Z]+)$/', $range, $matches)) {
														$docSheet->getPageSetup()->setColumnsToRepeatAtLeft(array($matches[1], $matches[2]));
													}
													// check for repeating rows, e.g. '1:1' or '1:5'
													elseif (preg_match('/!?(\d+)\:(\d+)$/', $range, $matches)) {
														$docSheet->getPageSetup()->setRowsToRepeatAtTop(array($matches[1], $matches[2]));
													}
												}
												break;

											case '_xlnm.Print_Area':
												$rangeSets = explode(',', $extractedRange);		// FIXME: what if sheetname contains comma?
												$newRangeSets = array();
												foreach($rangeSets as $rangeSet) {
													$range = explode('!', $rangeSet);	// FIXME: what if sheetname contains exclamation mark?
													$rangeSet = isset($range[1]) ? $range[1] : $range[0];
													if (strpos($rangeSet, ':') === FALSE) {
														$rangeSet = $rangeSet . ':' . $rangeSet;
													}
													$newRangeSets[] = str_replace('$', '', $rangeSet);
												}
												$docSheet->getPageSetup()->setPrintArea(implode(',',$newRangeSets));
												break;

											default:
												break;
										}
									}
								}
							}

							// Next sheet id
							++$sheetId;
						}

						// Loop through definedNames
						if ($xmlWorkbook->definedNames) {
							foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
								// Extract range
								$extractedRange = (string)$definedName;
								$extractedRange = preg_replace('/\'(\w+)\'\!/', '', $extractedRange);
								if (($spos = strpos($extractedRange,'!')) !== false) {
									$extractedRange = substr($extractedRange,0,$spos).str_replace('$', '', substr($extractedRange,$spos));
								} else {
									$extractedRange = str_replace('$', '', $extractedRange);
								}

								// Valid range?
								if (stripos((string)$definedName, '#REF!') !== false || $extractedRange == '') {
									continue;
								}

								// Some definedNames are only applicable if we are on the same sheet...
								if ((string)$definedName['localSheetId'] != '') {
									// Local defined name
									// Switch on type
									switch ((string)$definedName['name']) {

										case '_xlnm._FilterDatabase':
										case '_xlnm.Print_Titles':
										case '_xlnm.Print_Area':
											break;

										default:
											if ($mapSheetId[(integer) $definedName['localSheetId']] !== null) {
												$range = explode('!', (string)$definedName);
												if (count($range) == 2) {
													$range[0] = str_replace("''", "'", $range[0]);
													$range[0] = str_replace("'", "", $range[0]);
													if ($worksheet = $docSheet->getParent()->getSheetByName($range[0])) {
														$extractedRange = str_replace('$', '', $range[1]);
														$scope = $docSheet->getParent()->getSheet($mapSheetId[(integer) $definedName['localSheetId']]);
														$excel->addNamedRange( new PHPExcel_NamedRange((string)$definedName['name'], $worksheet, $extractedRange, true, $scope) );
													}
												}
											}
											break;
									}
								} else if (!isset($definedName['localSheetId'])) {
									// "Global" definedNames
									$locatedSheet = null;
									$extractedSheetName = '';
									if (strpos( (string)$definedName, '!' ) !== false) {
										// Extract sheet name
										$extractedSheetName = PHPExcel_Worksheet::extractSheetTitle( (string)$definedName, true );
										$extractedSheetName = $extractedSheetName[0];

										// Locate sheet
										$locatedSheet = $excel->getSheetByName($extractedSheetName);

										// Modify range
										$range = explode('!', $extractedRange);
										$extractedRange = isset($range[1]) ? $range[1] : $range[0];
									}

									if ($locatedSheet !== NULL) {
										$excel->addNamedRange( new PHPExcel_NamedRange((string)$definedName['name'], $locatedSheet, $extractedRange, false) );
									}
								}
							}
						}
					}

					if ((!$this->_readDataOnly) || (!empty($this->_loadSheetsOnly))) {
						// active sheet index
						$activeTab = intval($xmlWorkbook->bookViews->workbookView["activeTab"]); // refers to old sheet index

						// keep active sheet index if sheet is still loaded, else first sheet is set as the active
						if (isset($mapSheetId[$activeTab]) && $mapSheetId[$activeTab] !== null) {
							$excel->setActiveSheetIndex($mapSheetId[$activeTab]);
						} else {
							if ($excel->getSheetCount() == 0) {
								$excel->createSheet();
							}
							$excel->setActiveSheetIndex(0);
						}
					}
				break;
			}

		}


		if (!$this->_readDataOnly) {
			$contentTypes = simplexml_load_string($this->_getFromZipArchive($zip, "[Content_Types].xml"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
			foreach ($contentTypes->Override as $contentType) {
				switch ($contentType["ContentType"]) {
					case "application/vnd.openxmlformats-officedocument.drawingml.chart+xml":
						if ($this->_includeCharts) {
							$chartEntryRef = ltrim($contentType['PartName'],'/');
							$chartElements = simplexml_load_string($this->_getFromZipArchive($zip, $chartEntryRef), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
							$objChart = PHPExcel_Reader_Excel2007_Chart::readChart($chartElements,basename($chartEntryRef,'.xml'));

//							echo 'Chart ',$chartEntryRef,'<br />';
//							var_dump($charts[$chartEntryRef]);
//
							if (isset($charts[$chartEntryRef])) {
								$chartPositionRef = $charts[$chartEntryRef]['sheet'].'!'.$charts[$chartEntryRef]['id'];
//								echo 'Position Ref ',$chartPositionRef,'<br />';
								if (isset($chartDetails[$chartPositionRef])) {
//									var_dump($chartDetails[$chartPositionRef]);

									$excel->getSheetByName($charts[$chartEntryRef]['sheet'])->addChart($objChart);
									$objChart->setWorksheet($excel->getSheetByName($charts[$chartEntryRef]['sheet']));
									$objChart->setTopLeftPosition( $chartDetails[$chartPositionRef]['fromCoordinate'],
																   $chartDetails[$chartPositionRef]['fromOffsetX'],
																   $chartDetails[$chartPositionRef]['fromOffsetY']
																 );
									$objChart->setBottomRightPosition( $chartDetails[$chartPositionRef]['toCoordinate'],
																	   $chartDetails[$chartPositionRef]['toOffsetX'],
																	   $chartDetails[$chartPositionRef]['toOffsetY']
																	 );
								}
							}
						}
				}
			}
		}

		$zip->close();

		return $excel;
	}


	private static function _readColor($color, $background=FALSE) {
		if (isset($color["rgb"])) {
			return (string)$color["rgb"];
		} else if (isset($color["indexed"])) {
			return PHPExcel_Style_Color::indexedColor($color["indexed"]-7,$background)->getARGB();
		} else if (isset($color["theme"])) {
			if (self::$_theme !== NULL) {
				$returnColour = self::$_theme->getColourByIndex((int)$color["theme"]);
				if (isset($color["tint"])) {
					$tintAdjust = (float) $color["tint"];
					$returnColour = PHPExcel_Style_Color::changeBrightness($returnColour, $tintAdjust);
				}
				return 'FF'.$returnColour;
			}
		}

		if ($background) {
			return 'FFFFFFFF';
		}
		return 'FF000000';
	}


	private static function _readStyle($docStyle, $style) {
		// format code
//		if (isset($style->numFmt)) {
//			if (isset($style->numFmt['formatCode'])) {
//				$docStyle->getNumberFormat()->setFormatCode((string) $style->numFmt['formatCode']);
//			} else {
				$docStyle->getNumberFormat()->setFormatCode($style->numFmt);
//			}
//		}

		// font
		if (isset($style->font)) {
			$docStyle->getFont()->setName((string) $style->font->name["val"]);
			$docStyle->getFont()->setSize((string) $style->font->sz["val"]);
			if (isset($style->font->b)) {
				$docStyle->getFont()->setBold(!isset($style->font->b["val"]) || self::boolean((string) $style->font->b["val"]));
			}
			if (isset($style->font->i)) {
				$docStyle->getFont()->setItalic(!isset($style->font->i["val"]) || self::boolean((string) $style->font->i["val"]));
			}
			if (isset($style->font->strike)) {
				$docStyle->getFont()->setStrikethrough(!isset($style->font->strike["val"]) || self::boolean((string) $style->font->strike["val"]));
			}
			$docStyle->getFont()->getColor()->setARGB(self::_readColor($style->font->color));

			if (isset($style->font->u) && !isset($style->font->u["val"])) {
				$docStyle->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			} else if (isset($style->font->u) && isset($style->font->u["val"])) {
				$docStyle->getFont()->setUnderline((string)$style->font->u["val"]);
			}

			if (isset($style->font->vertAlign) && isset($style->font->vertAlign["val"])) {
				$vertAlign = strtolower((string)$style->font->vertAlign["val"]);
				if ($vertAlign == 'superscript') {
					$docStyle->getFont()->setSuperScript(true);
				}
				if ($vertAlign == 'subscript') {
					$docStyle->getFont()->setSubScript(true);
				}
			}
		}

		// fill
		if (isset($style->fill)) {
			if ($style->fill->gradientFill) {
				$gradientFill = $style->fill->gradientFill[0];
				if(!empty($gradientFill["type"])) {
					$docStyle->getFill()->setFillType((string) $gradientFill["type"]);
				}
				$docStyle->getFill()->setRotation(floatval($gradientFill["degree"]));
				$gradientFill->registerXPathNamespace("sml", "http://schemas.openxmlformats.org/spreadsheetml/2006/main");
				$docStyle->getFill()->getStartColor()->setARGB(self::_readColor( self::array_item($gradientFill->xpath("sml:stop[@position=0]"))->color) );
				$docStyle->getFill()->getEndColor()->setARGB(self::_readColor( self::array_item($gradientFill->xpath("sml:stop[@position=1]"))->color) );
			} elseif ($style->fill->patternFill) {
				$patternType = (string)$style->fill->patternFill["patternType"] != '' ? (string)$style->fill->patternFill["patternType"] : 'solid';
				$docStyle->getFill()->setFillType($patternType);
				if ($style->fill->patternFill->fgColor) {
					$docStyle->getFill()->getStartColor()->setARGB(self::_readColor($style->fill->patternFill->fgColor,true));
				} else {
					$docStyle->getFill()->getStartColor()->setARGB('FF000000');
				}
				if ($style->fill->patternFill->bgColor) {
					$docStyle->getFill()->getEndColor()->setARGB(self::_readColor($style->fill->patternFill->bgColor,true));
				}
			}
		}

		// border
		if (isset($style->border)) {
			$diagonalUp = self::boolean((string) $style->border["diagonalUp"]);
			$diagonalDown = self::boolean((string) $style->border["diagonalDown"]);
			if (!$diagonalUp && !$diagonalDown) {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_NONE);
			} elseif ($diagonalUp && !$diagonalDown) {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_UP);
			} elseif (!$diagonalUp && $diagonalDown) {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_DOWN);
			} else {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_BOTH);
			}
			self::_readBorder($docStyle->getBorders()->getLeft(), $style->border->left);
			self::_readBorder($docStyle->getBorders()->getRight(), $style->border->right);
			self::_readBorder($docStyle->getBorders()->getTop(), $style->border->top);
			self::_readBorder($docStyle->getBorders()->getBottom(), $style->border->bottom);
			self::_readBorder($docStyle->getBorders()->getDiagonal(), $style->border->diagonal);
		}

		// alignment
		if (isset($style->alignment)) {
			$docStyle->getAlignment()->setHorizontal((string) $style->alignment["horizontal"]);
			$docStyle->getAlignment()->setVertical((string) $style->alignment["vertical"]);

			$textRotation = 0;
			if ((int)$style->alignment["textRotation"] <= 90) {
				$textRotation = (int)$style->alignment["textRotation"];
			} else if ((int)$style->alignment["textRotation"] > 90) {
				$textRotation = 90 - (int)$style->alignment["textRotation"];
			}

			$docStyle->getAlignment()->setTextRotation(intval($textRotation));
			$docStyle->getAlignment()->setWrapText(self::boolean((string) $style->alignment["wrapText"]));
			$docStyle->getAlignment()->setShrinkToFit(self::boolean((string) $style->alignment["shrinkToFit"]));
			$docStyle->getAlignment()->setIndent( intval((string)$style->alignment["indent"]) > 0 ? intval((string)$style->alignment["indent"]) : 0 );
		}

		// protection
		if (isset($style->protection)) {
			if (isset($style->protection['locked'])) {
				if (self::boolean((string) $style->protection['locked'])) {
					$docStyle->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
				} else {
					$docStyle->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				}
			}

			if (isset($style->protection['hidden'])) {
				if (self::boolean((string) $style->protection['hidden'])) {
					$docStyle->getProtection()->setHidden(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
				} else {
					$docStyle->getProtection()->setHidden(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				}
			}
		}

		// top-level style settings
		if (isset($style->quotePrefix)) {
			$docStyle->setQuotePrefix($style->quotePrefix);
        }
	}


	private static function _readBorder($docBorder, $eleBorder) {
		if (isset($eleBorder["style"])) {
			$docBorder->setBorderStyle((string) $eleBorder["style"]);
		}
		if (isset($eleBorder->color)) {
			$docBorder->getColor()->setARGB(self::_readColor($eleBorder->color));
		}
	}


	private function _parseRichText($is = null) {
		$value = new PHPExcel_RichText();

		if (isset($is->t)) {
			$value->createText( PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $is->t ) );
		} else {
			foreach ($is->r as $run) {
				if (!isset($run->rPr)) {
					$objText = $value->createText( PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $run->t ) );

				} else {
					$objText = $value->createTextRun( PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $run->t ) );

					if (isset($run->rPr->rFont["val"])) {
						$objText->getFont()->setName((string) $run->rPr->rFont["val"]);
					}

					if (isset($run->rPr->sz["val"])) {
						$objText->getFont()->setSize((string) $run->rPr->sz["val"]);
					}

					if (isset($run->rPr->color)) {
						$objText->getFont()->setColor( new PHPExcel_Style_Color( self::_readColor($run->rPr->color) ) );
					}

					if ((isset($run->rPr->b["val"]) && self::boolean((string) $run->rPr->b["val"])) ||
						(isset($run->rPr->b) && !isset($run->rPr->b["val"]))) {
						$objText->getFont()->setBold(TRUE);
					}

					if ((isset($run->rPr->i["val"]) && self::boolean((string) $run->rPr->i["val"])) ||
						(isset($run->rPr->i) && !isset($run->rPr->i["val"]))) {
						$objText->getFont()->setItalic(TRUE);
					}

					if (isset($run->rPr->vertAlign) && isset($run->rPr->vertAlign["val"])) {
						$vertAlign = strtolower((string)$run->rPr->vertAlign["val"]);
						if ($vertAlign == 'superscript') {
							$objText->getFont()->setSuperScript(TRUE);
						}
						if ($vertAlign == 'subscript') {
							$objText->getFont()->setSubScript(TRUE);
						}
					}

					if (isset($run->rPr->u) && !isset($run->rPr->u["val"])) {
						$objText->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					} else if (isset($run->rPr->u) && isset($run->rPr->u["val"])) {
						$objText->getFont()->setUnderline((string)$run->rPr->u["val"]);
					}

					if ((isset($run->rPr->strike["val"]) && self::boolean((string) $run->rPr->strike["val"])) ||
						(isset($run->rPr->strike) && !isset($run->rPr->strike["val"]))) {
						$objText->getFont()->setStrikethrough(TRUE);
					}
				}
			}
		}

		return $value;
	}

	private function _readRibbon($excel, $customUITarget, $zip)
    {
		$baseDir = dirname($customUITarget);
		$nameCustomUI = basename($customUITarget);
        // get the xml file (ribbon)
		$localRibbon = $this->_getFromZipArchive($zip, $customUITarget);
		$customUIImagesNames = array(); 
        $customUIImagesBinaries = array();
        // something like customUI/_rels/customUI.xml.rels
		$pathRels = $baseDir . '/_rels/' . $nameCustomUI . '.rels';
		$dataRels = $this->_getFromZipArchive($zip, $pathRels);
		if ($dataRels) {
            // exists and not empty if the ribbon have some pictures (other than internal MSO)
			$UIRels = simplexml_load_string($dataRels, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
			if ($UIRels) {
				// we need to save id and target to avoid parsing customUI.xml and "guess" if it's a pseudo callback who load the image
				foreach ($UIRels->Relationship as $ele) {
					if ($ele["Type"] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image') {
                        // an image ?
						$customUIImagesNames[(string) $ele['Id']] = (string)$ele['Target'];
						$customUIImagesBinaries[(string)$ele['Target']] = $this->_getFromZipArchive($zip, $baseDir . '/' . (string) $ele['Target']);
					}
				}
			}
		}
		if ($localRibbon) {
			$excel->setRibbonXMLData($customUITarget, $localRibbon);
			if (count($customUIImagesNames) > 0 && count($customUIImagesBinaries) > 0) {
				$excel->setRibbonBinObjects($customUIImagesNames, $customUIImagesBinaries);
			} else {
				$excel->setRibbonBinObjects(NULL);
			}
		} else {
			$excel->setRibbonXMLData(NULL);
			$excel->setRibbonBinObjects(NULL);
		}
	}

	private static function array_item($array, $key = 0) {
		return (isset($array[$key]) ? $array[$key] : null);
	}


	private static function dir_add($base, $add) {
		return preg_replace('~[^/]+/\.\./~', '', dirname($base) . "/$add");
	}


	private static function toCSSArray($style) {
		$style = str_replace(array("\r","\n"), "", $style);

		$temp = explode(';', $style);
		$style = array();
		foreach ($temp as $item) {
			$item = explode(':', $item);

			if (strpos($item[1], 'px') !== false) {
				$item[1] = str_replace('px', '', $item[1]);
			}
			if (strpos($item[1], 'pt') !== false) {
				$item[1] = str_replace('pt', '', $item[1]);
				$item[1] = PHPExcel_Shared_Font::fontSizeToPixels($item[1]);
			}
			if (strpos($item[1], 'in') !== false) {
				$item[1] = str_replace('in', '', $item[1]);
				$item[1] = PHPExcel_Shared_Font::inchSizeToPixels($item[1]);
			}
			if (strpos($item[1], 'cm') !== false) {
				$item[1] = str_replace('cm', '', $item[1]);
				$item[1] = PHPExcel_Shared_Font::centimeterSizeToPixels($item[1]);
			}

			$style[$item[0]] = $item[1];
		}

		return $style;
	}

	private static function boolean($value = NULL)
	{
        if (is_object($value)) {
			$value = (string) $value;
        }
		if (is_numeric($value)) {
			return (bool) $value;
        }
		return ($value === 'true' || $value === 'TRUE');
	}
}
