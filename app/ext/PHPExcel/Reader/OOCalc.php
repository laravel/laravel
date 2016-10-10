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
 * PHPExcel_Reader_OOCalc
 *
 * @category	PHPExcel
 * @package		PHPExcel_Reader
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_OOCalc extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
	/**
	 * Formats
	 *
	 * @var array
	 */
	private $_styles = array();


	/**
	 * Create a new PHPExcel_Reader_OOCalc
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
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

        $zipClass = PHPExcel_Settings::getZipClass();

		// Check if zip class exists
//		if (!class_exists($zipClass, FALSE)) {
//			throw new PHPExcel_Reader_Exception($zipClass . " library is not enabled");
//		}

        $mimeType = 'UNKNOWN';
		// Load file
		$zip = new $zipClass;
		if ($zip->open($pFilename) === true) {
			// check if it is an OOXML archive
			$stat = $zip->statName('mimetype');
			if ($stat && ($stat['size'] <= 255)) {
				$mimeType = $zip->getFromName($stat['name']);
			} elseif($stat = $zip->statName('META-INF/manifest.xml')) {
		        $xml = simplexml_load_string($zip->getFromName('META-INF/manifest.xml'), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
		        $namespacesContent = $xml->getNamespaces(true);
				if (isset($namespacesContent['manifest'])) {
			        $manifest = $xml->children($namespacesContent['manifest']);
				    foreach($manifest as $manifestDataSet) {
					    $manifestAttributes = $manifestDataSet->attributes($namespacesContent['manifest']);
				        if ($manifestAttributes->{'full-path'} == '/') {
				            $mimeType = (string) $manifestAttributes->{'media-type'};
				            break;
				    	}
				    }
				}
			}

			$zip->close();

			return ($mimeType === 'application/vnd.oasis.opendocument.spreadsheet');
		}

		return FALSE;
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

        $zipClass = PHPExcel_Settings::getZipClass();

		$zip = new $zipClass;
		if (!$zip->open($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! Error opening file.");
		}

		$worksheetNames = array();

		$xml = new XMLReader();
		$res = $xml->open('zip://'.realpath($pFilename).'#content.xml', null, PHPExcel_Settings::getLibXmlLoaderOptions());
		$xml->setParserProperty(2,true);

		//	Step into the first level of content of the XML
		$xml->read();
		while ($xml->read()) {
			//	Quickly jump through to the office:body node
			while ($xml->name !== 'office:body') {
				if ($xml->isEmptyElement)
					$xml->read();
				else
					$xml->next();
			}
			//	Now read each node until we find our first table:table node
			while ($xml->read()) {
				if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
					//	Loop through each table:table node reading the table:name attribute for each worksheet name
					do {
						$worksheetNames[] = $xml->getAttribute('table:name');
						$xml->next();
					} while ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT);
				}
			}
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

        $zipClass = PHPExcel_Settings::getZipClass();

		$zip = new $zipClass;
		if (!$zip->open($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! Error opening file.");
		}

		$xml = new XMLReader();
		$res = $xml->open('zip://'.realpath($pFilename).'#content.xml', null, PHPExcel_Settings::getLibXmlLoaderOptions());
		$xml->setParserProperty(2,true);

		//	Step into the first level of content of the XML
		$xml->read();
		while ($xml->read()) {
			//	Quickly jump through to the office:body node
			while ($xml->name !== 'office:body') {
				if ($xml->isEmptyElement)
					$xml->read();
				else
					$xml->next();
			}
				//	Now read each node until we find our first table:table node
			while ($xml->read()) {
				if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
					$worksheetNames[] = $xml->getAttribute('table:name');

					$tmpInfo = array(
						'worksheetName' => $xml->getAttribute('table:name'),
						'lastColumnLetter' => 'A',
						'lastColumnIndex' => 0,
						'totalRows' => 0,
						'totalColumns' => 0,
					);

					//	Loop through each child node of the table:table element reading
					$currCells = 0;
					do {
						$xml->read();
						if ($xml->name == 'table:table-row' && $xml->nodeType == XMLReader::ELEMENT) {
							$rowspan = $xml->getAttribute('table:number-rows-repeated');
							$rowspan = empty($rowspan) ? 1 : $rowspan;
							$tmpInfo['totalRows'] += $rowspan;
							$tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'],$currCells);
							$currCells = 0;
							//	Step into the row
							$xml->read();
							do {
								if ($xml->name == 'table:table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
									if (!$xml->isEmptyElement) {
										$currCells++;
										$xml->next();
									} else {
										$xml->read();
									}
								} elseif ($xml->name == 'table:covered-table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
									$mergeSize = $xml->getAttribute('table:number-columns-repeated');
									$currCells += $mergeSize;
									$xml->read();
								}
							} while ($xml->name != 'table:table-row');
						}
					} while ($xml->name != 'table:table');

					$tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'],$currCells);
					$tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
					$tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
					$worksheetInfo[] = $tmpInfo;
				}
			}

//				foreach($workbookData->table as $worksheetDataSet) {
//					$worksheetData = $worksheetDataSet->children($namespacesContent['table']);
//					$worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);
//
//					$rowIndex = 0;
//					foreach ($worksheetData as $key => $rowData) {
//						switch ($key) {
//							case 'table-row' :
//								$rowDataTableAttributes = $rowData->attributes($namespacesContent['table']);
//								$rowRepeats = (isset($rowDataTableAttributes['number-rows-repeated'])) ?
//										$rowDataTableAttributes['number-rows-repeated'] : 1;
//								$columnIndex = 0;
//
//								foreach ($rowData as $key => $cellData) {
//									$cellDataTableAttributes = $cellData->attributes($namespacesContent['table']);
//									$colRepeats = (isset($cellDataTableAttributes['number-columns-repeated'])) ?
//										$cellDataTableAttributes['number-columns-repeated'] : 1;
//									$cellDataOfficeAttributes = $cellData->attributes($namespacesContent['office']);
//									if (isset($cellDataOfficeAttributes['value-type'])) {
//										$tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex + $colRepeats - 1);
//										$tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex + $rowRepeats);
//									}
//									$columnIndex += $colRepeats;
//								}
//								$rowIndex += $rowRepeats;
//								break;
//						}
//					}
//
//					$tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
//					$tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;
//
//				}
//			}
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
	 * Loads PHPExcel from file into PHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	PHPExcel	$objPHPExcel
	 * @return 	PHPExcel
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$timezoneObj = new DateTimeZone('Europe/London');
		$GMT = new DateTimeZone('UTC');

        $zipClass = PHPExcel_Settings::getZipClass();

		$zip = new $zipClass;
		if (!$zip->open($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! Error opening file.");
		}

//		echo '<h1>Meta Information</h1>';
		$xml = simplexml_load_string($zip->getFromName("meta.xml"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
		$namespacesMeta = $xml->getNamespaces(true);
//		echo '<pre>';
//		print_r($namespacesMeta);
//		echo '</pre><hr />';

		$docProps = $objPHPExcel->getProperties();
		$officeProperty = $xml->children($namespacesMeta['office']);
		foreach($officeProperty as $officePropertyData) {
			$officePropertyDC = array();
			if (isset($namespacesMeta['dc'])) {
				$officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
			}
			foreach($officePropertyDC as $propertyName => $propertyValue) {
				$propertyValue = (string) $propertyValue;
				switch ($propertyName) {
					case 'title' :
							$docProps->setTitle($propertyValue);
							break;
					case 'subject' :
							$docProps->setSubject($propertyValue);
							break;
					case 'creator' :
							$docProps->setCreator($propertyValue);
							$docProps->setLastModifiedBy($propertyValue);
							break;
					case 'date' :
							$creationDate = strtotime($propertyValue);
							$docProps->setCreated($creationDate);
							$docProps->setModified($creationDate);
							break;
					case 'description' :
							$docProps->setDescription($propertyValue);
							break;
				}
			}
			$officePropertyMeta = array();
			if (isset($namespacesMeta['dc'])) {
				$officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
			}
			foreach($officePropertyMeta as $propertyName => $propertyValue) {
				$propertyValueAttributes = $propertyValue->attributes($namespacesMeta['meta']);
				$propertyValue = (string) $propertyValue;
				switch ($propertyName) {
					case 'initial-creator' :
							$docProps->setCreator($propertyValue);
							break;
					case 'keyword' :
							$docProps->setKeywords($propertyValue);
							break;
					case 'creation-date' :
							$creationDate = strtotime($propertyValue);
							$docProps->setCreated($creationDate);
							break;
					case 'user-defined' :
							$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
							foreach ($propertyValueAttributes as $key => $value) {
								if ($key == 'name') {
									$propertyValueName = (string) $value;
								} elseif($key == 'value-type') {
									switch ($value) {
										case 'date'	:
											$propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue,'date');
											$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_DATE;
											break;
										case 'boolean'	:
											$propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue,'bool');
											$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_BOOLEAN;
											break;
										case 'float'	:
											$propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue,'r4');
											$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_FLOAT;
											break;
										default :
											$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
									}
								}
							}
							$docProps->setCustomProperty($propertyValueName,$propertyValue,$propertyValueType);
							break;
				}
			}
		}


//		echo '<h1>Workbook Content</h1>';
		$xml = simplexml_load_string($zip->getFromName("content.xml"), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
		$namespacesContent = $xml->getNamespaces(true);
//		echo '<pre>';
//		print_r($namespacesContent);
//		echo '</pre><hr />';

		$workbook = $xml->children($namespacesContent['office']);
		foreach($workbook->body->spreadsheet as $workbookData) {
			$workbookData = $workbookData->children($namespacesContent['table']);
			$worksheetID = 0;
			foreach($workbookData->table as $worksheetDataSet) {
				$worksheetData = $worksheetDataSet->children($namespacesContent['table']);
//				print_r($worksheetData);
//				echo '<br />';
				$worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);
//				print_r($worksheetDataAttributes);
//				echo '<br />';
				if ((isset($this->_loadSheetsOnly)) && (isset($worksheetDataAttributes['name'])) &&
					(!in_array($worksheetDataAttributes['name'], $this->_loadSheetsOnly))) {
					continue;
				}

//				echo '<h2>Worksheet '.$worksheetDataAttributes['name'].'</h2>';
				// Create new Worksheet
				$objPHPExcel->createSheet();
				$objPHPExcel->setActiveSheetIndex($worksheetID);
				if (isset($worksheetDataAttributes['name'])) {
					$worksheetName = (string) $worksheetDataAttributes['name'];
					//	Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
					//		formula cells... during the load, all formulae should be correct, and we're simply
					//		bringing the worksheet name in line with the formula, not the reverse
					$objPHPExcel->getActiveSheet()->setTitle($worksheetName,false);
				}

				$rowID = 1;
				foreach($worksheetData as $key => $rowData) {
//					echo '<b>'.$key.'</b><br />';
					switch ($key) {
						case 'table-header-rows':
							foreach ($rowData as $key=>$cellData) {
								$rowData = $cellData;
								break;
							}
						case 'table-row' :
							$rowDataTableAttributes = $rowData->attributes($namespacesContent['table']);
							$rowRepeats = (isset($rowDataTableAttributes['number-rows-repeated'])) ?
									$rowDataTableAttributes['number-rows-repeated'] : 1;
							$columnID = 'A';
							foreach($rowData as $key => $cellData) {
								if ($this->getReadFilter() !== NULL) {
									if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
										continue;
									}
								}

//								echo '<b>'.$columnID.$rowID.'</b><br />';
								$cellDataText = (isset($namespacesContent['text'])) ?
									$cellData->children($namespacesContent['text']) :
									'';
								$cellDataOffice = $cellData->children($namespacesContent['office']);
								$cellDataOfficeAttributes = $cellData->attributes($namespacesContent['office']);
								$cellDataTableAttributes = $cellData->attributes($namespacesContent['table']);

//								echo 'Office Attributes: ';
//								print_r($cellDataOfficeAttributes);
//								echo '<br />Table Attributes: ';
//								print_r($cellDataTableAttributes);
//								echo '<br />Cell Data Text';
//								print_r($cellDataText);
//								echo '<br />';
//
								$type = $formatting = $hyperlink = null;
								$hasCalculatedValue = false;
								$cellDataFormula = '';
								if (isset($cellDataTableAttributes['formula'])) {
									$cellDataFormula = $cellDataTableAttributes['formula'];
									$hasCalculatedValue = true;
								}

								if (isset($cellDataOffice->annotation)) {
//									echo 'Cell has comment<br />';
									$annotationText = $cellDataOffice->annotation->children($namespacesContent['text']);
									$textArray = array();
									foreach($annotationText as $t) {
										foreach($t->span as $text) {
											$textArray[] = (string)$text;
										}
									}
									$text = implode("\n",$textArray);
//									echo $text,'<br />';
									$objPHPExcel->getActiveSheet()->getComment( $columnID.$rowID )
//																	->setAuthor( $author )
																	->setText($this->_parseRichText($text) );
								}

									if (isset($cellDataText->p)) {
									// Consolidate if there are multiple p records (maybe with spans as well)
									$dataArray = array();
									// Text can have multiple text:p and within those, multiple text:span.
									// text:p newlines, but text:span does not.
									// Also, here we assume there is no text data is span fields are specified, since
									// we have no way of knowing proper positioning anyway.
									foreach ($cellDataText->p as $pData) {
										if (isset($pData->span)) {
											// span sections do not newline, so we just create one large string here
											$spanSection = "";
											foreach ($pData->span as $spanData) {
												$spanSection .= $spanData;
											}
											array_push($dataArray, $spanSection);
										} else {
											array_push($dataArray, $pData);
										}
									}
									$allCellDataText = implode($dataArray, "\n");

//									echo 'Value Type is '.$cellDataOfficeAttributes['value-type'].'<br />';
									switch ($cellDataOfficeAttributes['value-type']) {
 										case 'string' :
												$type = PHPExcel_Cell_DataType::TYPE_STRING;
												$dataValue = $allCellDataText;
												if (isset($dataValue->a)) {
													$dataValue = $dataValue->a;
													$cellXLinkAttributes = $dataValue->attributes($namespacesContent['xlink']);
													$hyperlink = $cellXLinkAttributes['href'];
												}
												break;
										case 'boolean' :
												$type = PHPExcel_Cell_DataType::TYPE_BOOL;
												$dataValue = ($allCellDataText == 'TRUE') ? True : False;
												break;
										case 'percentage' :
												$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
												$dataValue = (float) $cellDataOfficeAttributes['value'];
												if (floor($dataValue) == $dataValue) {
													$dataValue = (integer) $dataValue;
												}
												$formatting = PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
												break;
										case 'currency' :
												$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
												$dataValue = (float) $cellDataOfficeAttributes['value'];
												if (floor($dataValue) == $dataValue) {
													$dataValue = (integer) $dataValue;
												}
												$formatting = PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
												break;
										case 'float' :
												$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
												$dataValue = (float) $cellDataOfficeAttributes['value'];
												if (floor($dataValue) == $dataValue) {
													if ($dataValue == (integer) $dataValue)
														$dataValue = (integer) $dataValue;
													else
														$dataValue = (float) $dataValue;
												}
												break;
										case 'date' :
												$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
											    $dateObj = new DateTime($cellDataOfficeAttributes['date-value'], $GMT);
												$dateObj->setTimeZone($timezoneObj);
												list($year,$month,$day,$hour,$minute,$second) = explode(' ',$dateObj->format('Y m d H i s'));
												$dataValue = PHPExcel_Shared_Date::FormattedPHPToExcel($year,$month,$day,$hour,$minute,$second);
												if ($dataValue != floor($dataValue)) {
													$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15.' '.PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
												} else {
													$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15;
												}
												break;
										case 'time' :
												$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
												$dataValue = PHPExcel_Shared_Date::PHPToExcel(strtotime('01-01-1970 '.implode(':',sscanf($cellDataOfficeAttributes['time-value'],'PT%dH%dM%dS'))));
												$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
												break;
									}
//									echo 'Data value is '.$dataValue.'<br />';
//									if ($hyperlink !== NULL) {
//										echo 'Hyperlink is '.$hyperlink.'<br />';
//									}
								} else {
									$type = PHPExcel_Cell_DataType::TYPE_NULL;
									$dataValue = NULL;
								}

								if ($hasCalculatedValue) {
									$type = PHPExcel_Cell_DataType::TYPE_FORMULA;
//									echo 'Formula: ', $cellDataFormula, PHP_EOL;
									$cellDataFormula = substr($cellDataFormula,strpos($cellDataFormula,':=')+1);
									$temp = explode('"',$cellDataFormula);
									$tKey = false;
									foreach($temp as &$value) {
										//	Only replace in alternate array entries (i.e. non-quoted blocks)
										if ($tKey = !$tKey) {
											$value = preg_replace('/\[([^\.]+)\.([^\.]+):\.([^\.]+)\]/Ui','$1!$2:$3',$value);    //  Cell range reference in another sheet
											$value = preg_replace('/\[([^\.]+)\.([^\.]+)\]/Ui','$1!$2',$value);       //  Cell reference in another sheet
											$value = preg_replace('/\[\.([^\.]+):\.([^\.]+)\]/Ui','$1:$2',$value);    //  Cell range reference
											$value = preg_replace('/\[\.([^\.]+)\]/Ui','$1',$value);                  //  Simple cell reference
											$value = PHPExcel_Calculation::_translateSeparator(';',',',$value,$inBraces);
										}
									}
									unset($value);
									//	Then rebuild the formula string
									$cellDataFormula = implode('"',$temp);
//									echo 'Adjusted Formula: ', $cellDataFormula, PHP_EOL;
								}

								$colRepeats = (isset($cellDataTableAttributes['number-columns-repeated'])) ?
									$cellDataTableAttributes['number-columns-repeated'] : 1;
								if ($type !== NULL) {
									for ($i = 0; $i < $colRepeats; ++$i) {
										if ($i > 0) {
											++$columnID;
										}
										if ($type !== PHPExcel_Cell_DataType::TYPE_NULL) {
											for ($rowAdjust = 0; $rowAdjust < $rowRepeats; ++$rowAdjust) {
												$rID = $rowID + $rowAdjust;
												$objPHPExcel->getActiveSheet()->getCell($columnID.$rID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $dataValue),$type);
												if ($hasCalculatedValue) {
//													echo 'Forumla result is '.$dataValue.'<br />';
													$objPHPExcel->getActiveSheet()->getCell($columnID.$rID)->setCalculatedValue($dataValue);
												}
												if ($formatting !== NULL) {
													$objPHPExcel->getActiveSheet()->getStyle($columnID.$rID)->getNumberFormat()->setFormatCode($formatting);
												} else {
													$objPHPExcel->getActiveSheet()->getStyle($columnID.$rID)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
												}
												if ($hyperlink !== NULL) {
													$objPHPExcel->getActiveSheet()->getCell($columnID.$rID)->getHyperlink()->setUrl($hyperlink);
												}
											}
										}
									}
								}

								//	Merged cells
								if ((isset($cellDataTableAttributes['number-columns-spanned'])) || (isset($cellDataTableAttributes['number-rows-spanned']))) {
									if (($type !== PHPExcel_Cell_DataType::TYPE_NULL) || (!$this->_readDataOnly)) {
										$columnTo = $columnID;
										if (isset($cellDataTableAttributes['number-columns-spanned'])) {
											$columnTo = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cellDataTableAttributes['number-columns-spanned'] -2);
										}
										$rowTo = $rowID;
										if (isset($cellDataTableAttributes['number-rows-spanned'])) {
											$rowTo = $rowTo + $cellDataTableAttributes['number-rows-spanned'] - 1;
										}
										$cellRange = $columnID.$rowID.':'.$columnTo.$rowTo;
										$objPHPExcel->getActiveSheet()->mergeCells($cellRange);
									}
								}

								++$columnID;
							}
							$rowID += $rowRepeats;
							break;
					}
				}
				++$worksheetID;
			}
		}

		// Return
		return $objPHPExcel;
	}


	private function _parseRichText($is = '') {
		$value = new PHPExcel_RichText();

		$value->createText($is);

		return $value;
	}

}
