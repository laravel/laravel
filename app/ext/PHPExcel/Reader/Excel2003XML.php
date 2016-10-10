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
 * PHPExcel_Reader_Excel2003XML
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel2003XML extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
	/**
	 * Formats
	 *
	 * @var array
	 */
	private $_styles = array();

	/**
	 * Character set used in the file
	 *
	 * @var string
	 */
	private $_charSet = 'UTF-8';


	/**
	 * Create a new PHPExcel_Reader_Excel2003XML
	 */
	public function __construct() {
		$this->_readFilter 	= new PHPExcel_Reader_DefaultReadFilter();
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

		//	Office					xmlns:o="urn:schemas-microsoft-com:office:office"
		//	Excel					xmlns:x="urn:schemas-microsoft-com:office:excel"
		//	XML Spreadsheet			xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
		//	Spreadsheet component	xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"
		//	XML schema 				xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882"
		//	XML data type			xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
		//	MS-persist recordset	xmlns:rs="urn:schemas-microsoft-com:rowset"
		//	Rowset					xmlns:z="#RowsetSchema"
		//

		$signature = array(
				'<?xml version="1.0"',
				'<?mso-application progid="Excel.Sheet"?>'
			);

		// Open file
		$this->_openFile($pFilename);
		$fileHandle = $this->_fileHandle;
		
		// Read sample data (first 2 KB will do)
		$data = fread($fileHandle, 2048);
		fclose($fileHandle);

		$valid = true;
		foreach($signature as $match) {
			// every part of the signature must be present
			if (strpos($data, $match) === false) {
				$valid = false;
				break;
			}
		}

		//	Retrieve charset encoding
		if(preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/um',$data,$matches)) {
			$this->_charSet = strtoupper($matches[1]);
		}
//		echo 'Character Set is ',$this->_charSet,'<br />';

		return $valid;
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
		if (!$this->canRead($pFilename)) {
			throw new PHPExcel_Reader_Exception($pFilename . " is an Invalid Spreadsheet file.");
		}

		$worksheetNames = array();

		$xml = simplexml_load_file($pFilename, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
		$namespaces = $xml->getNamespaces(true);

		$xml_ss = $xml->children($namespaces['ss']);
		foreach($xml_ss->Worksheet as $worksheet) {
			$worksheet_ss = $worksheet->attributes($namespaces['ss']);
			$worksheetNames[] = self::_convertStringEncoding((string) $worksheet_ss['Name'],$this->_charSet);
		}

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

		$xml = simplexml_load_file($pFilename, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
		$namespaces = $xml->getNamespaces(true);

		$worksheetID = 1;
		$xml_ss = $xml->children($namespaces['ss']);
		foreach($xml_ss->Worksheet as $worksheet) {
			$worksheet_ss = $worksheet->attributes($namespaces['ss']);

			$tmpInfo = array();
			$tmpInfo['worksheetName'] = '';
			$tmpInfo['lastColumnLetter'] = 'A';
			$tmpInfo['lastColumnIndex'] = 0;
			$tmpInfo['totalRows'] = 0;
			$tmpInfo['totalColumns'] = 0;

			if (isset($worksheet_ss['Name'])) {
				$tmpInfo['worksheetName'] = (string) $worksheet_ss['Name'];
			} else {
				$tmpInfo['worksheetName'] = "Worksheet_{$worksheetID}";
			}

			if (isset($worksheet->Table->Row)) {
				$rowIndex = 0;

				foreach($worksheet->Table->Row as $rowData) {
					$columnIndex = 0;
					$rowHasData = false;

					foreach($rowData->Cell as $cell) {
						if (isset($cell->Data)) {
							$tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
							$rowHasData = true;
						}

						++$columnIndex;
					}

					++$rowIndex;

					if ($rowHasData) {
						$tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
					}
				}
			}

			$tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
			$tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;

			$worksheetInfo[] = $tmpInfo;
			++$worksheetID;
		}

		return $worksheetInfo;
	}


    /**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @return 	PHPExcel
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function load($pFilename)
	{
		// Create new PHPExcel
		$objPHPExcel = new PHPExcel();

		// Load into this instance
		return $this->loadIntoExisting($pFilename, $objPHPExcel);
	}


	private static function identifyFixedStyleValue($styleList,&$styleAttributeValue) {
		$styleAttributeValue = strtolower($styleAttributeValue);
		foreach($styleList as $style) {
			if ($styleAttributeValue == strtolower($style)) {
				$styleAttributeValue = $style;
				return true;
			}
		}
		return false;
	}


 	/**
 	 * pixel units to excel width units(units of 1/256th of a character width)
 	 * @param pxs
 	 * @return
 	 */
 	private static function _pixel2WidthUnits($pxs) {
		$UNIT_OFFSET_MAP = array(0, 36, 73, 109, 146, 182, 219);

		$widthUnits = 256 * ($pxs / 7);
		$widthUnits += $UNIT_OFFSET_MAP[($pxs % 7)];
		return $widthUnits;
	}


	/**
	 * excel width units(units of 1/256th of a character width) to pixel units
	 * @param widthUnits
	 * @return
	 */
	private static function _widthUnits2Pixel($widthUnits) {
		$pixels = ($widthUnits / 256) * 7;
		$offsetWidthUnits = $widthUnits % 256;
		$pixels += round($offsetWidthUnits / (256 / 7));
		return $pixels;
	}


	private static function _hex2str($hex) {
		return chr(hexdec($hex[1]));
	}


	/**
	 * Loads PHPExcel from file into PHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	PHPExcel	$objPHPExcel
	 * @return 	PHPExcel
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
	{
		$fromFormats	= array('\-',	'\ ');
		$toFormats		= array('-',	' ');

		$underlineStyles = array (
				PHPExcel_Style_Font::UNDERLINE_NONE,
				PHPExcel_Style_Font::UNDERLINE_DOUBLE,
				PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING,
				PHPExcel_Style_Font::UNDERLINE_SINGLE,
				PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING
			);
		$verticalAlignmentStyles = array (
				PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
				PHPExcel_Style_Alignment::VERTICAL_TOP,
				PHPExcel_Style_Alignment::VERTICAL_CENTER,
				PHPExcel_Style_Alignment::VERTICAL_JUSTIFY
			);
		$horizontalAlignmentStyles = array (
				PHPExcel_Style_Alignment::HORIZONTAL_GENERAL,
				PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
				PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY
			);

		$timezoneObj = new DateTimeZone('Europe/London');
		$GMT = new DateTimeZone('UTC');


		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		if (!$this->canRead($pFilename)) {
			throw new PHPExcel_Reader_Exception($pFilename . " is an Invalid Spreadsheet file.");
		}

		$xml = simplexml_load_file($pFilename, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
		$namespaces = $xml->getNamespaces(true);

		$docProps = $objPHPExcel->getProperties();
		if (isset($xml->DocumentProperties[0])) {
			foreach($xml->DocumentProperties[0] as $propertyName => $propertyValue) {
				switch ($propertyName) {
					case 'Title' :
							$docProps->setTitle(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'Subject' :
							$docProps->setSubject(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'Author' :
							$docProps->setCreator(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'Created' :
							$creationDate = strtotime($propertyValue);
							$docProps->setCreated($creationDate);
							break;
					case 'LastAuthor' :
							$docProps->setLastModifiedBy(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'LastSaved' :
							$lastSaveDate = strtotime($propertyValue);
							$docProps->setModified($lastSaveDate);
							break;
					case 'Company' :
							$docProps->setCompany(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'Category' :
							$docProps->setCategory(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'Manager' :
							$docProps->setManager(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'Keywords' :
							$docProps->setKeywords(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
					case 'Description' :
							$docProps->setDescription(self::_convertStringEncoding($propertyValue,$this->_charSet));
							break;
				}
			}
		}
		if (isset($xml->CustomDocumentProperties)) {
			foreach($xml->CustomDocumentProperties[0] as $propertyName => $propertyValue) {
				$propertyAttributes = $propertyValue->attributes($namespaces['dt']);
				$propertyName = preg_replace_callback('/_x([0-9a-z]{4})_/','PHPExcel_Reader_Excel2003XML::_hex2str',$propertyName);
				$propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_UNKNOWN;
				switch((string) $propertyAttributes) {
					case 'string' :
						$propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
						$propertyValue = trim($propertyValue);
						break;
					case 'boolean' :
						$propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_BOOLEAN;
						$propertyValue = (bool) $propertyValue;
						break;
					case 'integer' :
						$propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_INTEGER;
						$propertyValue = intval($propertyValue);
						break;
					case 'float' :
						$propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_FLOAT;
						$propertyValue = floatval($propertyValue);
						break;
					case 'dateTime.tz' :
						$propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_DATE;
						$propertyValue = strtotime(trim($propertyValue));
						break;
				}
				$docProps->setCustomProperty($propertyName,$propertyValue,$propertyType);
			}
		}

		foreach($xml->Styles[0] as $style) {
			$style_ss = $style->attributes($namespaces['ss']);
			$styleID = (string) $style_ss['ID'];
//			echo 'Style ID = '.$styleID.'<br />';
			if ($styleID == 'Default') {
				$this->_styles['Default'] = array();
			} else {
				$this->_styles[$styleID] = $this->_styles['Default'];
			}
			foreach ($style as $styleType => $styleData) {
				$styleAttributes = $styleData->attributes($namespaces['ss']);
//				echo $styleType.'<br />';
				switch ($styleType) {
					case 'Alignment' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								$styleAttributeValue = (string) $styleAttributeValue;
								switch ($styleAttributeKey) {
									case 'Vertical' :
											if (self::identifyFixedStyleValue($verticalAlignmentStyles,$styleAttributeValue)) {
												$this->_styles[$styleID]['alignment']['vertical'] = $styleAttributeValue;
											}
											break;
									case 'Horizontal' :
											if (self::identifyFixedStyleValue($horizontalAlignmentStyles,$styleAttributeValue)) {
												$this->_styles[$styleID]['alignment']['horizontal'] = $styleAttributeValue;
											}
											break;
									case 'WrapText' :
											$this->_styles[$styleID]['alignment']['wrap'] = true;
											break;
								}
							}
							break;
					case 'Borders' :
							foreach($styleData->Border as $borderStyle) {
								$borderAttributes = $borderStyle->attributes($namespaces['ss']);
								$thisBorder = array();
								foreach($borderAttributes as $borderStyleKey => $borderStyleValue) {
//									echo $borderStyleKey.' = '.$borderStyleValue.'<br />';
									switch ($borderStyleKey) {
										case 'LineStyle' :
												$thisBorder['style'] = PHPExcel_Style_Border::BORDER_MEDIUM;
//												$thisBorder['style'] = $borderStyleValue;
												break;
										case 'Weight' :
//												$thisBorder['style'] = $borderStyleValue;
												break;
										case 'Position' :
												$borderPosition = strtolower($borderStyleValue);
												break;
										case 'Color' :
												$borderColour = substr($borderStyleValue,1);
												$thisBorder['color']['rgb'] = $borderColour;
												break;
									}
								}
								if (!empty($thisBorder)) {
									if (($borderPosition == 'left') || ($borderPosition == 'right') || ($borderPosition == 'top') || ($borderPosition == 'bottom')) {
										$this->_styles[$styleID]['borders'][$borderPosition] = $thisBorder;
									}
								}
							}
							break;
					case 'Font' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								$styleAttributeValue = (string) $styleAttributeValue;
								switch ($styleAttributeKey) {
									case 'FontName' :
											$this->_styles[$styleID]['font']['name'] = $styleAttributeValue;
											break;
									case 'Size' :
											$this->_styles[$styleID]['font']['size'] = $styleAttributeValue;
											break;
									case 'Color' :
											$this->_styles[$styleID]['font']['color']['rgb'] = substr($styleAttributeValue,1);
											break;
									case 'Bold' :
											$this->_styles[$styleID]['font']['bold'] = true;
											break;
									case 'Italic' :
											$this->_styles[$styleID]['font']['italic'] = true;
											break;
									case 'Underline' :
											if (self::identifyFixedStyleValue($underlineStyles,$styleAttributeValue)) {
												$this->_styles[$styleID]['font']['underline'] = $styleAttributeValue;
											}
											break;
								}
							}
							break;
					case 'Interior' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								switch ($styleAttributeKey) {
									case 'Color' :
											$this->_styles[$styleID]['fill']['color']['rgb'] = substr($styleAttributeValue,1);
											break;
								}
							}
							break;
					case 'NumberFormat' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								$styleAttributeValue = str_replace($fromFormats,$toFormats,$styleAttributeValue);
								switch ($styleAttributeValue) {
									case 'Short Date' :
											$styleAttributeValue = 'dd/mm/yyyy';
											break;
								}
								if ($styleAttributeValue > '') {
									$this->_styles[$styleID]['numberformat']['code'] = $styleAttributeValue;
								}
							}
							break;
					case 'Protection' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
							}
							break;
				}
			}
//			print_r($this->_styles[$styleID]);
//			echo '<hr />';
		}
//		echo '<hr />';

		$worksheetID = 0;
		$xml_ss = $xml->children($namespaces['ss']);

		foreach($xml_ss->Worksheet as $worksheet) {
			$worksheet_ss = $worksheet->attributes($namespaces['ss']);

			if ((isset($this->_loadSheetsOnly)) && (isset($worksheet_ss['Name'])) &&
				(!in_array($worksheet_ss['Name'], $this->_loadSheetsOnly))) {
				continue;
			}

//			echo '<h3>Worksheet: ',$worksheet_ss['Name'],'<h3>';
//
			// Create new Worksheet
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($worksheetID);
			if (isset($worksheet_ss['Name'])) {
				$worksheetName = self::_convertStringEncoding((string) $worksheet_ss['Name'],$this->_charSet);
				//	Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
				//		formula cells... during the load, all formulae should be correct, and we're simply bringing
				//		the worksheet name in line with the formula, not the reverse
				$objPHPExcel->getActiveSheet()->setTitle($worksheetName,false);
			}

			$columnID = 'A';
			if (isset($worksheet->Table->Column)) {
				foreach($worksheet->Table->Column as $columnData) {
					$columnData_ss = $columnData->attributes($namespaces['ss']);
					if (isset($columnData_ss['Index'])) {
						$columnID = PHPExcel_Cell::stringFromColumnIndex($columnData_ss['Index']-1);
					}
					if (isset($columnData_ss['Width'])) {
						$columnWidth = $columnData_ss['Width'];
//						echo '<b>Setting column width for '.$columnID.' to '.$columnWidth.'</b><br />';
						$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setWidth($columnWidth / 5.4);
					}
					++$columnID;
				}
			}

			$rowID = 1;
			if (isset($worksheet->Table->Row)) {
				foreach($worksheet->Table->Row as $rowData) {
					$rowHasData = false;
					$row_ss = $rowData->attributes($namespaces['ss']);
					if (isset($row_ss['Index'])) {
						$rowID = (integer) $row_ss['Index'];
					}
//					echo '<b>Row '.$rowID.'</b><br />';

					$columnID = 'A';
					foreach($rowData->Cell as $cell) {

						$cell_ss = $cell->attributes($namespaces['ss']);
						if (isset($cell_ss['Index'])) {
							$columnID = PHPExcel_Cell::stringFromColumnIndex($cell_ss['Index']-1);
						}
						$cellRange = $columnID.$rowID;

						if ($this->getReadFilter() !== NULL) {
							if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
								continue;
							}
						}

						if ((isset($cell_ss['MergeAcross'])) || (isset($cell_ss['MergeDown']))) {
							$columnTo = $columnID;
							if (isset($cell_ss['MergeAcross'])) {
								$columnTo = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cell_ss['MergeAcross'] -1);
							}
							$rowTo = $rowID;
							if (isset($cell_ss['MergeDown'])) {
								$rowTo = $rowTo + $cell_ss['MergeDown'];
							}
							$cellRange .= ':'.$columnTo.$rowTo;
							$objPHPExcel->getActiveSheet()->mergeCells($cellRange);
						}

						$cellIsSet = $hasCalculatedValue = false;
						$cellDataFormula = '';
						if (isset($cell_ss['Formula'])) {
							$cellDataFormula = $cell_ss['Formula'];
							// added this as a check for array formulas
							if (isset($cell_ss['ArrayRange'])) {
								$cellDataCSEFormula = $cell_ss['ArrayRange'];
//								echo "found an array formula at ".$columnID.$rowID."<br />";
							}
							$hasCalculatedValue = true;
						}
						if (isset($cell->Data)) {
							$cellValue = $cellData = $cell->Data;
							$type = PHPExcel_Cell_DataType::TYPE_NULL;
							$cellData_ss = $cellData->attributes($namespaces['ss']);
							if (isset($cellData_ss['Type'])) {
								$cellDataType = $cellData_ss['Type'];
								switch ($cellDataType) {
									/*
									const TYPE_STRING		= 's';
									const TYPE_FORMULA		= 'f';
									const TYPE_NUMERIC		= 'n';
									const TYPE_BOOL			= 'b';
								    const TYPE_NULL			= 'null';
								    const TYPE_INLINE		= 'inlineStr';
									const TYPE_ERROR		= 'e';
									*/
									case 'String' :
											$cellValue = self::_convertStringEncoding($cellValue,$this->_charSet);
											$type = PHPExcel_Cell_DataType::TYPE_STRING;
											break;
									case 'Number' :
											$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
											$cellValue = (float) $cellValue;
											if (floor($cellValue) == $cellValue) {
												$cellValue = (integer) $cellValue;
											}
											break;
									case 'Boolean' :
											$type = PHPExcel_Cell_DataType::TYPE_BOOL;
											$cellValue = ($cellValue != 0);
											break;
									case 'DateTime' :
											$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
											$cellValue = PHPExcel_Shared_Date::PHPToExcel(strtotime($cellValue));
											break;
									case 'Error' :
											$type = PHPExcel_Cell_DataType::TYPE_ERROR;
											break;
								}
							}

							if ($hasCalculatedValue) {
//								echo 'FORMULA<br />';
								$type = PHPExcel_Cell_DataType::TYPE_FORMULA;
								$columnNumber = PHPExcel_Cell::columnIndexFromString($columnID);
								if (substr($cellDataFormula,0,3) == 'of:') {
									$cellDataFormula = substr($cellDataFormula,3);
//									echo 'Before: ',$cellDataFormula,'<br />';
									$temp = explode('"',$cellDataFormula);
									$key = false;
									foreach($temp as &$value) {
										//	Only replace in alternate array entries (i.e. non-quoted blocks)
										if ($key = !$key) {
											$value = str_replace(array('[.','.',']'),'',$value);
										}
									}
								} else {
									//	Convert R1C1 style references to A1 style references (but only when not quoted)
//									echo 'Before: ',$cellDataFormula,'<br />';
									$temp = explode('"',$cellDataFormula);
									$key = false;
									foreach($temp as &$value) {
										//	Only replace in alternate array entries (i.e. non-quoted blocks)
										if ($key = !$key) {
											preg_match_all('/(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))/',$value, $cellReferences,PREG_SET_ORDER+PREG_OFFSET_CAPTURE);
											//	Reverse the matches array, otherwise all our offsets will become incorrect if we modify our way
											//		through the formula from left to right. Reversing means that we work right to left.through
											//		the formula
											$cellReferences = array_reverse($cellReferences);
											//	Loop through each R1C1 style reference in turn, converting it to its A1 style equivalent,
											//		then modify the formula to use that new reference
											foreach($cellReferences as $cellReference) {
												$rowReference = $cellReference[2][0];
												//	Empty R reference is the current row
												if ($rowReference == '') $rowReference = $rowID;
												//	Bracketed R references are relative to the current row
												if ($rowReference{0} == '[') $rowReference = $rowID + trim($rowReference,'[]');
												$columnReference = $cellReference[4][0];
												//	Empty C reference is the current column
												if ($columnReference == '') $columnReference = $columnNumber;
												//	Bracketed C references are relative to the current column
												if ($columnReference{0} == '[') $columnReference = $columnNumber + trim($columnReference,'[]');
												$A1CellReference = PHPExcel_Cell::stringFromColumnIndex($columnReference-1).$rowReference;
													$value = substr_replace($value,$A1CellReference,$cellReference[0][1],strlen($cellReference[0][0]));
											}
										}
									}
								}
								unset($value);
								//	Then rebuild the formula string
								$cellDataFormula = implode('"',$temp);
//								echo 'After: ',$cellDataFormula,'<br />';
							}

//							echo 'Cell '.$columnID.$rowID.' is a '.$type.' with a value of '.(($hasCalculatedValue) ? $cellDataFormula : $cellValue).'<br />';
//
							$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $cellValue),$type);
							if ($hasCalculatedValue) {
//								echo 'Formula result is '.$cellValue.'<br />';
								$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setCalculatedValue($cellValue);
							}
							$cellIsSet = $rowHasData = true;
						}

						if (isset($cell->Comment)) {
//							echo '<b>comment found</b><br />';
							$commentAttributes = $cell->Comment->attributes($namespaces['ss']);
							$author = 'unknown';
							if (isset($commentAttributes->Author)) {
								$author = (string)$commentAttributes->Author;
//								echo 'Author: ',$author,'<br />';
							}
							$node = $cell->Comment->Data->asXML();
//							$annotation = str_replace('html:','',substr($node,49,-10));
//							echo $annotation,'<br />';
							$annotation = strip_tags($node);
//							echo 'Annotation: ',$annotation,'<br />';
							$objPHPExcel->getActiveSheet()->getComment( $columnID.$rowID )
															->setAuthor(self::_convertStringEncoding($author ,$this->_charSet))
															->setText($this->_parseRichText($annotation) );
						}

						if (($cellIsSet) && (isset($cell_ss['StyleID']))) {
							$style = (string) $cell_ss['StyleID'];
//							echo 'Cell style for '.$columnID.$rowID.' is '.$style.'<br />';
							if ((isset($this->_styles[$style])) && (!empty($this->_styles[$style]))) {
//								echo 'Cell '.$columnID.$rowID.'<br />';
//								print_r($this->_styles[$style]);
//								echo '<br />';
								if (!$objPHPExcel->getActiveSheet()->cellExists($columnID.$rowID)) {
									$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setValue(NULL);
								}
								$objPHPExcel->getActiveSheet()->getStyle($cellRange)->applyFromArray($this->_styles[$style]);
							}
						}
						++$columnID;
					}

					if ($rowHasData) {
						if (isset($row_ss['StyleID'])) {
							$rowStyle = $row_ss['StyleID'];
						}
						if (isset($row_ss['Height'])) {
							$rowHeight = $row_ss['Height'];
//							echo '<b>Setting row height to '.$rowHeight.'</b><br />';
							$objPHPExcel->getActiveSheet()->getRowDimension($rowID)->setRowHeight($rowHeight);
						}
					}

					++$rowID;
				}
			}
			++$worksheetID;
		}

		// Return
		return $objPHPExcel;
	}


	private static function _convertStringEncoding($string,$charset) {
		if ($charset != 'UTF-8') {
			return PHPExcel_Shared_String::ConvertEncoding($string,'UTF-8',$charset);
		}
		return $string;
	}


	private function _parseRichText($is = '') {
		$value = new PHPExcel_RichText();

		$value->createText(self::_convertStringEncoding($is,$this->_charSet));

		return $value;
	}

}
