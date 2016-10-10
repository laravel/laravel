<?php

/**
 * PHPExcel_Writer_Excel2007_Chart
 *
 * Copyright (c) 2006 - 2015 PHPExcel
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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Writer_Excel2007_Chart extends PHPExcel_Writer_Excel2007_WriterPart
{
    protected $calculateCellValues;

    /**
     * Write charts to XML format
     *
     * @param  PHPExcel_Chart $pChart
     *
     * @return  string            XML Output
     * @throws  PHPExcel_Writer_Exception
     */
    public function writeChart(PHPExcel_Chart $pChart = null, $calculateCellValues = true)
    {
        $this->calculateCellValues = $calculateCellValues;

        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
        }
        //    Ensure that data series values are up-to-date before we save
        if ($this->calculateCellValues) {
            $pChart->refresh();
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // c:chartSpace
        $objWriter->startElement('c:chartSpace');
        $objWriter->writeAttribute('xmlns:c', 'http://schemas.openxmlformats.org/drawingml/2006/chart');
        $objWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        $objWriter->startElement('c:date1904');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();
        $objWriter->startElement('c:lang');
        $objWriter->writeAttribute('val', "en-GB");
        $objWriter->endElement();
        $objWriter->startElement('c:roundedCorners');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $this->writeAlternateContent($objWriter);

        $objWriter->startElement('c:chart');

        $this->writeTitle($pChart->getTitle(), $objWriter);

        $objWriter->startElement('c:autoTitleDeleted');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $this->writePlotArea($pChart->getPlotArea(), $pChart->getXAxisLabel(), $pChart->getYAxisLabel(), $objWriter, $pChart->getWorksheet(), $pChart->getChartAxisX(), $pChart->getChartAxisY(), $pChart->getMajorGridlines(), $pChart->getMinorGridlines());

        $this->writeLegend($pChart->getLegend(), $objWriter);

        $objWriter->startElement('c:plotVisOnly');
        $objWriter->writeAttribute('val', 1);
        $objWriter->endElement();

        $objWriter->startElement('c:dispBlanksAs');
        $objWriter->writeAttribute('val', "gap");
        $objWriter->endElement();

        $objWriter->startElement('c:showDLblsOverMax');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->endElement();

        $this->writePrintSettings($objWriter);

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write Chart Title
     *
     * @param  PHPExcel_Chart_Title $title
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeTitle(PHPExcel_Chart_Title $title = null, $objWriter)
    {
        if (is_null($title)) {
            return;
        }

        $objWriter->startElement('c:title');
        $objWriter->startElement('c:tx');
        $objWriter->startElement('c:rich');

        $objWriter->startElement('a:bodyPr');
        $objWriter->endElement();

        $objWriter->startElement('a:lstStyle');
        $objWriter->endElement();

        $objWriter->startElement('a:p');

        $caption = $title->getCaption();
        if ((is_array($caption)) && (count($caption) > 0)) {
            $caption = $caption[0];
        }
        $this->getParentWriter()->getWriterPart('stringtable')->writeRichTextForCharts($objWriter, $caption, 'a');

        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        $this->writeLayout($title->getLayout(), $objWriter);

        $objWriter->startElement('c:overlay');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Chart Legend
     *
     * @param  PHPExcel_Chart_Legend $legend
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeLegend(PHPExcel_Chart_Legend $legend = null, $objWriter)
    {
        if (is_null($legend)) {
            return;
        }

        $objWriter->startElement('c:legend');

        $objWriter->startElement('c:legendPos');
        $objWriter->writeAttribute('val', $legend->getPosition());
        $objWriter->endElement();

        $this->writeLayout($legend->getLayout(), $objWriter);

        $objWriter->startElement('c:overlay');
        $objWriter->writeAttribute('val', ($legend->getOverlay()) ? '1' : '0');
        $objWriter->endElement();

        $objWriter->startElement('c:txPr');
        $objWriter->startElement('a:bodyPr');
        $objWriter->endElement();

        $objWriter->startElement('a:lstStyle');
        $objWriter->endElement();

        $objWriter->startElement('a:p');
        $objWriter->startElement('a:pPr');
        $objWriter->writeAttribute('rtl', 0);

        $objWriter->startElement('a:defRPr');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('a:endParaRPr');
        $objWriter->writeAttribute('lang', "en-US");
        $objWriter->endElement();

        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Chart Plot Area
     *
     * @param  PHPExcel_Chart_PlotArea $plotArea
     * @param  PHPExcel_Chart_Title $xAxisLabel
     * @param  PHPExcel_Chart_Title $yAxisLabel
     * @param  PHPExcel_Chart_Axis $xAxis
     * @param  PHPExcel_Chart_Axis $yAxis
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writePlotArea(PHPExcel_Chart_PlotArea $plotArea, PHPExcel_Chart_Title $xAxisLabel = null, PHPExcel_Chart_Title $yAxisLabel = null, $objWriter, PHPExcel_Worksheet $pSheet, PHPExcel_Chart_Axis $xAxis, PHPExcel_Chart_Axis $yAxis, PHPExcel_Chart_GridLines $majorGridlines, PHPExcel_Chart_GridLines $minorGridlines)
    {
        if (is_null($plotArea)) {
            return;
        }

        $id1 = $id2 = 0;
        $this->_seriesIndex = 0;
        $objWriter->startElement('c:plotArea');

        $layout = $plotArea->getLayout();

        $this->writeLayout($layout, $objWriter);

        $chartTypes = self::getChartType($plotArea);
        $catIsMultiLevelSeries = $valIsMultiLevelSeries = false;
        $plotGroupingType = '';
        foreach ($chartTypes as $chartType) {
            $objWriter->startElement('c:' . $chartType);

            $groupCount = $plotArea->getPlotGroupCount();
            for ($i = 0; $i < $groupCount; ++$i) {
                $plotGroup = $plotArea->getPlotGroupByIndex($i);
                $groupType = $plotGroup->getPlotType();
                if ($groupType == $chartType) {
                    $plotStyle = $plotGroup->getPlotStyle();
                    if ($groupType === PHPExcel_Chart_DataSeries::TYPE_RADARCHART) {
                        $objWriter->startElement('c:radarStyle');
                        $objWriter->writeAttribute('val', $plotStyle);
                        $objWriter->endElement();
                    } elseif ($groupType === PHPExcel_Chart_DataSeries::TYPE_SCATTERCHART) {
                        $objWriter->startElement('c:scatterStyle');
                        $objWriter->writeAttribute('val', $plotStyle);
                        $objWriter->endElement();
                    }

                    $this->writePlotGroup($plotGroup, $chartType, $objWriter, $catIsMultiLevelSeries, $valIsMultiLevelSeries, $plotGroupingType, $pSheet);
                }
            }

            $this->writeDataLabels($objWriter, $layout);

            if ($chartType === PHPExcel_Chart_DataSeries::TYPE_LINECHART) {
                //    Line only, Line3D can't be smoothed

                $objWriter->startElement('c:smooth');
                $objWriter->writeAttribute('val', (integer) $plotGroup->getSmoothLine());
                $objWriter->endElement();
            } elseif (($chartType === PHPExcel_Chart_DataSeries::TYPE_BARCHART) ||($chartType === PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D)) {
                $objWriter->startElement('c:gapWidth');
                $objWriter->writeAttribute('val', 150);
                $objWriter->endElement();

                if ($plotGroupingType == 'percentStacked' || $plotGroupingType == 'stacked') {
                    $objWriter->startElement('c:overlap');
                    $objWriter->writeAttribute('val', 100);
                    $objWriter->endElement();
                }
            } elseif ($chartType === PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART) {
                $objWriter->startElement('c:bubbleScale');
                $objWriter->writeAttribute('val', 25);
                $objWriter->endElement();

                $objWriter->startElement('c:showNegBubbles');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();
            } elseif ($chartType === PHPExcel_Chart_DataSeries::TYPE_STOCKCHART) {
                $objWriter->startElement('c:hiLowLines');
                $objWriter->endElement();

                $objWriter->startElement('c:upDownBars');

                $objWriter->startElement('c:gapWidth');
                $objWriter->writeAttribute('val', 300);
                $objWriter->endElement();

                $objWriter->startElement('c:upBars');
                $objWriter->endElement();

                $objWriter->startElement('c:downBars');
                $objWriter->endElement();

                $objWriter->endElement();
            }

            //    Generate 2 unique numbers to use for axId values
            //                    $id1 = $id2 = rand(10000000,99999999);
            //                    do {
            //                        $id2 = rand(10000000,99999999);
            //                    } while ($id1 == $id2);
            $id1 = '75091328';
            $id2 = '75089408';

            if (($chartType !== PHPExcel_Chart_DataSeries::TYPE_PIECHART) && ($chartType !== PHPExcel_Chart_DataSeries::TYPE_PIECHART_3D) && ($chartType !== PHPExcel_Chart_DataSeries::TYPE_DONUTCHART)) {
                $objWriter->startElement('c:axId');
                $objWriter->writeAttribute('val', $id1);
                $objWriter->endElement();
                $objWriter->startElement('c:axId');
                $objWriter->writeAttribute('val', $id2);
                $objWriter->endElement();
            } else {
                $objWriter->startElement('c:firstSliceAng');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();

                if ($chartType === PHPExcel_Chart_DataSeries::TYPE_DONUTCHART) {
                    $objWriter->startElement('c:holeSize');
                    $objWriter->writeAttribute('val', 50);
                    $objWriter->endElement();
                }
            }

            $objWriter->endElement();
        }

        if (($chartType !== PHPExcel_Chart_DataSeries::TYPE_PIECHART) && ($chartType !== PHPExcel_Chart_DataSeries::TYPE_PIECHART_3D) && ($chartType !== PHPExcel_Chart_DataSeries::TYPE_DONUTCHART)) {
            if ($chartType === PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART) {
                $this->writeValueAxis($objWriter, $plotArea, $xAxisLabel, $chartType, $id1, $id2, $catIsMultiLevelSeries, $xAxis, $yAxis, $majorGridlines, $minorGridlines);
            } else {
                $this->writeCategoryAxis($objWriter, $plotArea, $xAxisLabel, $chartType, $id1, $id2, $catIsMultiLevelSeries, $xAxis, $yAxis);
            }

            $this->writeValueAxis($objWriter, $plotArea, $yAxisLabel, $chartType, $id1, $id2, $valIsMultiLevelSeries, $xAxis, $yAxis, $majorGridlines, $minorGridlines);
        }

        $objWriter->endElement();
    }

    /**
     * Write Data Labels
     *
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     * @param  PHPExcel_Chart_Layout $chartLayout Chart layout
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeDataLabels($objWriter, $chartLayout)
    {
        $objWriter->startElement('c:dLbls');

        $objWriter->startElement('c:showLegendKey');
        $showLegendKey = (empty($chartLayout)) ? 0 : $chartLayout->getShowLegendKey();
        $objWriter->writeAttribute('val', ((empty($showLegendKey)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showVal');
        $showVal = (empty($chartLayout)) ? 0 : $chartLayout->getShowVal();
        $objWriter->writeAttribute('val', ((empty($showVal)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showCatName');
        $showCatName = (empty($chartLayout)) ? 0 : $chartLayout->getShowCatName();
        $objWriter->writeAttribute('val', ((empty($showCatName)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showSerName');
        $showSerName = (empty($chartLayout)) ? 0 : $chartLayout->getShowSerName();
        $objWriter->writeAttribute('val', ((empty($showSerName)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showPercent');
        $showPercent = (empty($chartLayout)) ? 0 : $chartLayout->getShowPercent();
        $objWriter->writeAttribute('val', ((empty($showPercent)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showBubbleSize');
        $showBubbleSize = (empty($chartLayout)) ? 0 : $chartLayout->getShowBubbleSize();
        $objWriter->writeAttribute('val', ((empty($showBubbleSize)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showLeaderLines');
        $showLeaderLines = (empty($chartLayout)) ? 1 : $chartLayout->getShowLeaderLines();
        $objWriter->writeAttribute('val', ((empty($showLeaderLines)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Category Axis
     *
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     * @param  PHPExcel_Chart_PlotArea $plotArea
     * @param  PHPExcel_Chart_Title $xAxisLabel
     * @param  string $groupType Chart type
     * @param  string $id1
     * @param  string $id2
     * @param  boolean $isMultiLevelSeries
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeCategoryAxis($objWriter, PHPExcel_Chart_PlotArea $plotArea, $xAxisLabel, $groupType, $id1, $id2, $isMultiLevelSeries, $xAxis, $yAxis)
    {
        $objWriter->startElement('c:catAx');

        if ($id1 > 0) {
            $objWriter->startElement('c:axId');
            $objWriter->writeAttribute('val', $id1);
            $objWriter->endElement();
        }

        $objWriter->startElement('c:scaling');
        $objWriter->startElement('c:orientation');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('orientation'));
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('c:delete');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:axPos');
        $objWriter->writeAttribute('val', "b");
        $objWriter->endElement();

        if (!is_null($xAxisLabel)) {
            $objWriter->startElement('c:title');
            $objWriter->startElement('c:tx');
            $objWriter->startElement('c:rich');

            $objWriter->startElement('a:bodyPr');
            $objWriter->endElement();

            $objWriter->startElement('a:lstStyle');
            $objWriter->endElement();

            $objWriter->startElement('a:p');
            $objWriter->startElement('a:r');

            $caption = $xAxisLabel->getCaption();
            if (is_array($caption)) {
                $caption = $caption[0];
            }
            $objWriter->startElement('a:t');
            //                                        $objWriter->writeAttribute('xml:space', 'preserve');
            $objWriter->writeRawData(PHPExcel_Shared_String::ControlCharacterPHP2OOXML($caption));
            $objWriter->endElement();

            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();

            $layout = $xAxisLabel->getLayout();
            $this->writeLayout($layout, $objWriter);

            $objWriter->startElement('c:overlay');
            $objWriter->writeAttribute('val', 0);
            $objWriter->endElement();

            $objWriter->endElement();
        }

        $objWriter->startElement('c:numFmt');
        $objWriter->writeAttribute('formatCode', $yAxis->getAxisNumberFormat());
        $objWriter->writeAttribute('sourceLinked', $yAxis->getAxisNumberSourceLinked());
        $objWriter->endElement();

        $objWriter->startElement('c:majorTickMark');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('major_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:minorTickMark');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('minor_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:tickLblPos');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('axis_labels'));
        $objWriter->endElement();

        if ($id2 > 0) {
            $objWriter->startElement('c:crossAx');
            $objWriter->writeAttribute('val', $id2);
            $objWriter->endElement();

            $objWriter->startElement('c:crosses');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('horizontal_crosses'));
            $objWriter->endElement();
        }

        $objWriter->startElement('c:auto');
        $objWriter->writeAttribute('val', 1);
        $objWriter->endElement();

        $objWriter->startElement('c:lblAlgn');
        $objWriter->writeAttribute('val', "ctr");
        $objWriter->endElement();

        $objWriter->startElement('c:lblOffset');
        $objWriter->writeAttribute('val', 100);
        $objWriter->endElement();

        if ($isMultiLevelSeries) {
            $objWriter->startElement('c:noMultiLvlLbl');
            $objWriter->writeAttribute('val', 0);
            $objWriter->endElement();
        }
        $objWriter->endElement();
    }

    /**
     * Write Value Axis
     *
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     * @param  PHPExcel_Chart_PlotArea $plotArea
     * @param  PHPExcel_Chart_Title $yAxisLabel
     * @param  string $groupType Chart type
     * @param  string $id1
     * @param  string $id2
     * @param  boolean $isMultiLevelSeries
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeValueAxis($objWriter, PHPExcel_Chart_PlotArea $plotArea, $yAxisLabel, $groupType, $id1, $id2, $isMultiLevelSeries, $xAxis, $yAxis, $majorGridlines, $minorGridlines)
    {
        $objWriter->startElement('c:valAx');

        if ($id2 > 0) {
            $objWriter->startElement('c:axId');
            $objWriter->writeAttribute('val', $id2);
            $objWriter->endElement();
        }

        $objWriter->startElement('c:scaling');
        $objWriter->startElement('c:orientation');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('orientation'));

        if (!is_null($xAxis->getAxisOptionsProperty('maximum'))) {
            $objWriter->startElement('c:max');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('maximum'));
            $objWriter->endElement();
        }

        if (!is_null($xAxis->getAxisOptionsProperty('minimum'))) {
            $objWriter->startElement('c:min');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('minimum'));
            $objWriter->endElement();
        }

        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('c:delete');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:axPos');
        $objWriter->writeAttribute('val', "l");
        $objWriter->endElement();

        $objWriter->startElement('c:majorGridlines');
        $objWriter->startElement('c:spPr');

        if (!is_null($majorGridlines->getLineColorProperty('value'))) {
            $objWriter->startElement('a:ln');
            $objWriter->writeAttribute('w', $majorGridlines->getLineStyleProperty('width'));
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement("a:{$majorGridlines->getLineColorProperty('type')}");
            $objWriter->writeAttribute('val', $majorGridlines->getLineColorProperty('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $majorGridlines->getLineColorProperty('alpha'));
            $objWriter->endElement(); //end alpha
            $objWriter->endElement(); //end srgbClr
            $objWriter->endElement(); //end solidFill

            $objWriter->startElement('a:prstDash');
            $objWriter->writeAttribute('val', $majorGridlines->getLineStyleProperty('dash'));
            $objWriter->endElement();

            if ($majorGridlines->getLineStyleProperty('join') == 'miter') {
                $objWriter->startElement('a:miter');
                $objWriter->writeAttribute('lim', '800000');
                $objWriter->endElement();
            } else {
                $objWriter->startElement('a:bevel');
                $objWriter->endElement();
            }

            if (!is_null($majorGridlines->getLineStyleProperty(array('arrow', 'head', 'type')))) {
                $objWriter->startElement('a:headEnd');
                $objWriter->writeAttribute('type', $majorGridlines->getLineStyleProperty(array('arrow', 'head', 'type')));
                $objWriter->writeAttribute('w', $majorGridlines->getLineStyleArrowParameters('head', 'w'));
                $objWriter->writeAttribute('len', $majorGridlines->getLineStyleArrowParameters('head', 'len'));
                $objWriter->endElement();
            }

            if (!is_null($majorGridlines->getLineStyleProperty(array('arrow', 'end', 'type')))) {
                $objWriter->startElement('a:tailEnd');
                $objWriter->writeAttribute('type', $majorGridlines->getLineStyleProperty(array('arrow', 'end', 'type')));
                $objWriter->writeAttribute('w', $majorGridlines->getLineStyleArrowParameters('end', 'w'));
                $objWriter->writeAttribute('len', $majorGridlines->getLineStyleArrowParameters('end', 'len'));
                $objWriter->endElement();
            }
            $objWriter->endElement(); //end ln
        }
        $objWriter->startElement('a:effectLst');

        if (!is_null($majorGridlines->getGlowSize())) {
            $objWriter->startElement('a:glow');
            $objWriter->writeAttribute('rad', $majorGridlines->getGlowSize());
            $objWriter->startElement("a:{$majorGridlines->getGlowColor('type')}");
            $objWriter->writeAttribute('val', $majorGridlines->getGlowColor('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $majorGridlines->getGlowColor('alpha'));
            $objWriter->endElement(); //end alpha
            $objWriter->endElement(); //end schemeClr
            $objWriter->endElement(); //end glow
        }

        if (!is_null($majorGridlines->getShadowProperty('presets'))) {
            $objWriter->startElement("a:{$majorGridlines->getShadowProperty('effect')}");
            if (!is_null($majorGridlines->getShadowProperty('blur'))) {
                $objWriter->writeAttribute('blurRad', $majorGridlines->getShadowProperty('blur'));
            }
            if (!is_null($majorGridlines->getShadowProperty('distance'))) {
                $objWriter->writeAttribute('dist', $majorGridlines->getShadowProperty('distance'));
            }
            if (!is_null($majorGridlines->getShadowProperty('direction'))) {
                $objWriter->writeAttribute('dir', $majorGridlines->getShadowProperty('direction'));
            }
            if (!is_null($majorGridlines->getShadowProperty('algn'))) {
                $objWriter->writeAttribute('algn', $majorGridlines->getShadowProperty('algn'));
            }
            if (!is_null($majorGridlines->getShadowProperty(array('size', 'sx')))) {
                $objWriter->writeAttribute('sx', $majorGridlines->getShadowProperty(array('size', 'sx')));
            }
            if (!is_null($majorGridlines->getShadowProperty(array('size', 'sy')))) {
                $objWriter->writeAttribute('sy', $majorGridlines->getShadowProperty(array('size', 'sy')));
            }
            if (!is_null($majorGridlines->getShadowProperty(array('size', 'kx')))) {
                $objWriter->writeAttribute('kx', $majorGridlines->getShadowProperty(array('size', 'kx')));
            }
            if (!is_null($majorGridlines->getShadowProperty('rotWithShape'))) {
                $objWriter->writeAttribute('rotWithShape', $majorGridlines->getShadowProperty('rotWithShape'));
            }
            $objWriter->startElement("a:{$majorGridlines->getShadowProperty(array('color', 'type'))}");
            $objWriter->writeAttribute('val', $majorGridlines->getShadowProperty(array('color', 'value')));

            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $majorGridlines->getShadowProperty(array('color', 'alpha')));
            $objWriter->endElement(); //end alpha

            $objWriter->endElement(); //end color:type
            $objWriter->endElement(); //end shadow
        }

        if (!is_null($majorGridlines->getSoftEdgesSize())) {
            $objWriter->startElement('a:softEdge');
            $objWriter->writeAttribute('rad', $majorGridlines->getSoftEdgesSize());
            $objWriter->endElement(); //end softEdge
        }

        $objWriter->endElement(); //end effectLst
        $objWriter->endElement(); //end spPr
        $objWriter->endElement(); //end majorGridLines

        if ($minorGridlines->getObjectState()) {
            $objWriter->startElement('c:minorGridlines');
            $objWriter->startElement('c:spPr');

            if (!is_null($minorGridlines->getLineColorProperty('value'))) {
                $objWriter->startElement('a:ln');
                $objWriter->writeAttribute('w', $minorGridlines->getLineStyleProperty('width'));
                $objWriter->startElement('a:solidFill');
                $objWriter->startElement("a:{$minorGridlines->getLineColorProperty('type')}");
                $objWriter->writeAttribute('val', $minorGridlines->getLineColorProperty('value'));
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', $minorGridlines->getLineColorProperty('alpha'));
                $objWriter->endElement(); //end alpha
                $objWriter->endElement(); //end srgbClr
                $objWriter->endElement(); //end solidFill

                $objWriter->startElement('a:prstDash');
                $objWriter->writeAttribute('val', $minorGridlines->getLineStyleProperty('dash'));
                $objWriter->endElement();

                if ($minorGridlines->getLineStyleProperty('join') == 'miter') {
                    $objWriter->startElement('a:miter');
                    $objWriter->writeAttribute('lim', '800000');
                    $objWriter->endElement();
                } else {
                    $objWriter->startElement('a:bevel');
                    $objWriter->endElement();
                }

                if (!is_null($minorGridlines->getLineStyleProperty(array('arrow', 'head', 'type')))) {
                    $objWriter->startElement('a:headEnd');
                    $objWriter->writeAttribute('type', $minorGridlines->getLineStyleProperty(array('arrow', 'head', 'type')));
                    $objWriter->writeAttribute('w', $minorGridlines->getLineStyleArrowParameters('head', 'w'));
                    $objWriter->writeAttribute('len', $minorGridlines->getLineStyleArrowParameters('head', 'len'));
                    $objWriter->endElement();
                }

                if (!is_null($minorGridlines->getLineStyleProperty(array('arrow', 'end', 'type')))) {
                    $objWriter->startElement('a:tailEnd');
                    $objWriter->writeAttribute('type', $minorGridlines->getLineStyleProperty(array('arrow', 'end', 'type')));
                    $objWriter->writeAttribute('w', $minorGridlines->getLineStyleArrowParameters('end', 'w'));
                    $objWriter->writeAttribute('len', $minorGridlines->getLineStyleArrowParameters('end', 'len'));
                    $objWriter->endElement();
                }
                $objWriter->endElement(); //end ln
            }

            $objWriter->startElement('a:effectLst');

            if (!is_null($minorGridlines->getGlowSize())) {
                $objWriter->startElement('a:glow');
                $objWriter->writeAttribute('rad', $minorGridlines->getGlowSize());
                $objWriter->startElement("a:{$minorGridlines->getGlowColor('type')}");
                $objWriter->writeAttribute('val', $minorGridlines->getGlowColor('value'));
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', $minorGridlines->getGlowColor('alpha'));
                $objWriter->endElement(); //end alpha
                $objWriter->endElement(); //end schemeClr
                $objWriter->endElement(); //end glow
            }

            if (!is_null($minorGridlines->getShadowProperty('presets'))) {
                $objWriter->startElement("a:{$minorGridlines->getShadowProperty('effect')}");
                if (!is_null($minorGridlines->getShadowProperty('blur'))) {
                    $objWriter->writeAttribute('blurRad', $minorGridlines->getShadowProperty('blur'));
                }
                if (!is_null($minorGridlines->getShadowProperty('distance'))) {
                    $objWriter->writeAttribute('dist', $minorGridlines->getShadowProperty('distance'));
                }
                if (!is_null($minorGridlines->getShadowProperty('direction'))) {
                    $objWriter->writeAttribute('dir', $minorGridlines->getShadowProperty('direction'));
                }
                if (!is_null($minorGridlines->getShadowProperty('algn'))) {
                    $objWriter->writeAttribute('algn', $minorGridlines->getShadowProperty('algn'));
                }
                if (!is_null($minorGridlines->getShadowProperty(array('size', 'sx')))) {
                    $objWriter->writeAttribute('sx', $minorGridlines->getShadowProperty(array('size', 'sx')));
                }
                if (!is_null($minorGridlines->getShadowProperty(array('size', 'sy')))) {
                    $objWriter->writeAttribute('sy', $minorGridlines->getShadowProperty(array('size', 'sy')));
                }
                if (!is_null($minorGridlines->getShadowProperty(array('size', 'kx')))) {
                    $objWriter->writeAttribute('kx', $minorGridlines->getShadowProperty(array('size', 'kx')));
                }
                if (!is_null($minorGridlines->getShadowProperty('rotWithShape'))) {
                    $objWriter->writeAttribute('rotWithShape', $minorGridlines->getShadowProperty('rotWithShape'));
                }
                $objWriter->startElement("a:{$minorGridlines->getShadowProperty(array('color', 'type'))}");
                $objWriter->writeAttribute('val', $minorGridlines->getShadowProperty(array('color', 'value')));
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', $minorGridlines->getShadowProperty(array('color', 'alpha')));
                $objWriter->endElement(); //end alpha
                $objWriter->endElement(); //end color:type
                $objWriter->endElement(); //end shadow
            }

            if (!is_null($minorGridlines->getSoftEdgesSize())) {
                $objWriter->startElement('a:softEdge');
                $objWriter->writeAttribute('rad', $minorGridlines->getSoftEdgesSize());
                $objWriter->endElement(); //end softEdge
            }

            $objWriter->endElement(); //end effectLst
            $objWriter->endElement(); //end spPr
            $objWriter->endElement(); //end minorGridLines
        }

        if (!is_null($yAxisLabel)) {
            $objWriter->startElement('c:title');
            $objWriter->startElement('c:tx');
            $objWriter->startElement('c:rich');

            $objWriter->startElement('a:bodyPr');
            $objWriter->endElement();

            $objWriter->startElement('a:lstStyle');
            $objWriter->endElement();

            $objWriter->startElement('a:p');
            $objWriter->startElement('a:r');

            $caption = $yAxisLabel->getCaption();
            if (is_array($caption)) {
                $caption = $caption[0];
            }

            $objWriter->startElement('a:t');
            //                                        $objWriter->writeAttribute('xml:space', 'preserve');
            $objWriter->writeRawData(PHPExcel_Shared_String::ControlCharacterPHP2OOXML($caption));
            $objWriter->endElement();

            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();

            if ($groupType !== PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART) {
                $layout = $yAxisLabel->getLayout();
                $this->writeLayout($layout, $objWriter);
            }

            $objWriter->startElement('c:overlay');
            $objWriter->writeAttribute('val', 0);
            $objWriter->endElement();

            $objWriter->endElement();
        }

        $objWriter->startElement('c:numFmt');
        $objWriter->writeAttribute('formatCode', $xAxis->getAxisNumberFormat());
        $objWriter->writeAttribute('sourceLinked', $xAxis->getAxisNumberSourceLinked());
        $objWriter->endElement();

        $objWriter->startElement('c:majorTickMark');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('major_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:minorTickMark');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('minor_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:tickLblPos');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('axis_labels'));
        $objWriter->endElement();

        $objWriter->startElement('c:spPr');

        if (!is_null($xAxis->getFillProperty('value'))) {
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement("a:" . $xAxis->getFillProperty('type'));
            $objWriter->writeAttribute('val', $xAxis->getFillProperty('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getFillProperty('alpha'));
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
        }

        $objWriter->startElement('a:ln');

        $objWriter->writeAttribute('w', $xAxis->getLineStyleProperty('width'));
        $objWriter->writeAttribute('cap', $xAxis->getLineStyleProperty('cap'));
        $objWriter->writeAttribute('cmpd', $xAxis->getLineStyleProperty('compound'));

        if (!is_null($xAxis->getLineProperty('value'))) {
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement("a:" . $xAxis->getLineProperty('type'));
            $objWriter->writeAttribute('val', $xAxis->getLineProperty('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getLineProperty('alpha'));
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
        }

        $objWriter->startElement('a:prstDash');
        $objWriter->writeAttribute('val', $xAxis->getLineStyleProperty('dash'));
        $objWriter->endElement();

        if ($xAxis->getLineStyleProperty('join') == 'miter') {
            $objWriter->startElement('a:miter');
            $objWriter->writeAttribute('lim', '800000');
            $objWriter->endElement();
        } else {
            $objWriter->startElement('a:bevel');
            $objWriter->endElement();
        }

        if (!is_null($xAxis->getLineStyleProperty(array('arrow', 'head', 'type')))) {
            $objWriter->startElement('a:headEnd');
            $objWriter->writeAttribute('type', $xAxis->getLineStyleProperty(array('arrow', 'head', 'type')));
            $objWriter->writeAttribute('w', $xAxis->getLineStyleArrowWidth('head'));
            $objWriter->writeAttribute('len', $xAxis->getLineStyleArrowLength('head'));
            $objWriter->endElement();
        }

        if (!is_null($xAxis->getLineStyleProperty(array('arrow', 'end', 'type')))) {
            $objWriter->startElement('a:tailEnd');
            $objWriter->writeAttribute('type', $xAxis->getLineStyleProperty(array('arrow', 'end', 'type')));
            $objWriter->writeAttribute('w', $xAxis->getLineStyleArrowWidth('end'));
            $objWriter->writeAttribute('len', $xAxis->getLineStyleArrowLength('end'));
            $objWriter->endElement();
        }

        $objWriter->endElement();

        $objWriter->startElement('a:effectLst');

        if (!is_null($xAxis->getGlowProperty('size'))) {
            $objWriter->startElement('a:glow');
            $objWriter->writeAttribute('rad', $xAxis->getGlowProperty('size'));
            $objWriter->startElement("a:{$xAxis->getGlowProperty(array('color','type'))}");
            $objWriter->writeAttribute('val', $xAxis->getGlowProperty(array('color','value')));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getGlowProperty(array('color','alpha')));
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
        }

        if (!is_null($xAxis->getShadowProperty('presets'))) {
            $objWriter->startElement("a:{$xAxis->getShadowProperty('effect')}");

            if (!is_null($xAxis->getShadowProperty('blur'))) {
                $objWriter->writeAttribute('blurRad', $xAxis->getShadowProperty('blur'));
            }
            if (!is_null($xAxis->getShadowProperty('distance'))) {
                $objWriter->writeAttribute('dist', $xAxis->getShadowProperty('distance'));
            }
            if (!is_null($xAxis->getShadowProperty('direction'))) {
                $objWriter->writeAttribute('dir', $xAxis->getShadowProperty('direction'));
            }
            if (!is_null($xAxis->getShadowProperty('algn'))) {
                $objWriter->writeAttribute('algn', $xAxis->getShadowProperty('algn'));
            }
            if (!is_null($xAxis->getShadowProperty(array('size','sx')))) {
                $objWriter->writeAttribute('sx', $xAxis->getShadowProperty(array('size','sx')));
            }
            if (!is_null($xAxis->getShadowProperty(array('size','sy')))) {
                $objWriter->writeAttribute('sy', $xAxis->getShadowProperty(array('size','sy')));
            }
            if (!is_null($xAxis->getShadowProperty(array('size','kx')))) {
                $objWriter->writeAttribute('kx', $xAxis->getShadowProperty(array('size','kx')));
            }
            if (!is_null($xAxis->getShadowProperty('rotWithShape'))) {
                $objWriter->writeAttribute('rotWithShape', $xAxis->getShadowProperty('rotWithShape'));
            }

            $objWriter->startElement("a:{$xAxis->getShadowProperty(array('color','type'))}");
            $objWriter->writeAttribute('val', $xAxis->getShadowProperty(array('color','value')));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getShadowProperty(array('color','alpha')));
            $objWriter->endElement();
            $objWriter->endElement();

            $objWriter->endElement();
        }

        if (!is_null($xAxis->getSoftEdgesSize())) {
            $objWriter->startElement('a:softEdge');
            $objWriter->writeAttribute('rad', $xAxis->getSoftEdgesSize());
            $objWriter->endElement();
        }

        $objWriter->endElement(); //effectList
        $objWriter->endElement(); //end spPr

        if ($id1 > 0) {
            $objWriter->startElement('c:crossAx');
            $objWriter->writeAttribute('val', $id2);
            $objWriter->endElement();

            if (!is_null($xAxis->getAxisOptionsProperty('horizontal_crosses_value'))) {
                $objWriter->startElement('c:crossesAt');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('horizontal_crosses_value'));
                $objWriter->endElement();
            } else {
                $objWriter->startElement('c:crosses');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('horizontal_crosses'));
                $objWriter->endElement();
            }

            $objWriter->startElement('c:crossBetween');
            $objWriter->writeAttribute('val', "midCat");
            $objWriter->endElement();

            if (!is_null($xAxis->getAxisOptionsProperty('major_unit'))) {
                $objWriter->startElement('c:majorUnit');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('major_unit'));
                $objWriter->endElement();
            }

            if (!is_null($xAxis->getAxisOptionsProperty('minor_unit'))) {
                $objWriter->startElement('c:minorUnit');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('minor_unit'));
                $objWriter->endElement();
            }
        }

        if ($isMultiLevelSeries) {
            if ($groupType !== PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART) {
                $objWriter->startElement('c:noMultiLvlLbl');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();
    }

    /**
     * Get the data series type(s) for a chart plot series
     *
     * @param  PHPExcel_Chart_PlotArea $plotArea
     *
     * @return  string|array
     * @throws  PHPExcel_Writer_Exception
     */
    private static function getChartType($plotArea)
    {
        $groupCount = $plotArea->getPlotGroupCount();

        if ($groupCount == 1) {
            $chartType = array($plotArea->getPlotGroupByIndex(0)->getPlotType());
        } else {
            $chartTypes = array();
            for ($i = 0; $i < $groupCount; ++$i) {
                $chartTypes[] = $plotArea->getPlotGroupByIndex($i)->getPlotType();
            }
            $chartType = array_unique($chartTypes);
            if (count($chartTypes) == 0) {
                throw new PHPExcel_Writer_Exception('Chart is not yet implemented');
            }
        }

        return $chartType;
    }

    /**
     * Write Plot Group (series of related plots)
     *
     * @param  PHPExcel_Chart_DataSeries $plotGroup
     * @param  string $groupType Type of plot for dataseries
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     * @param  boolean &$catIsMultiLevelSeries Is category a multi-series category
     * @param  boolean &$valIsMultiLevelSeries Is value set a multi-series set
     * @param  string &$plotGroupingType Type of grouping for multi-series values
     * @param  PHPExcel_Worksheet $pSheet
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writePlotGroup($plotGroup, $groupType, $objWriter, &$catIsMultiLevelSeries, &$valIsMultiLevelSeries, &$plotGroupingType, PHPExcel_Worksheet $pSheet)
    {
        if (is_null($plotGroup)) {
            return;
        }

        if (($groupType == PHPExcel_Chart_DataSeries::TYPE_BARCHART) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D)) {
            $objWriter->startElement('c:barDir');
            $objWriter->writeAttribute('val', $plotGroup->getPlotDirection());
            $objWriter->endElement();
        }

        if (!is_null($plotGroup->getPlotGrouping())) {
            $plotGroupingType = $plotGroup->getPlotGrouping();
            $objWriter->startElement('c:grouping');
            $objWriter->writeAttribute('val', $plotGroupingType);
            $objWriter->endElement();
        }

        //    Get these details before the loop, because we can use the count to check for varyColors
        $plotSeriesOrder = $plotGroup->getPlotOrder();
        $plotSeriesCount = count($plotSeriesOrder);

        if (($groupType !== PHPExcel_Chart_DataSeries::TYPE_RADARCHART) && ($groupType !== PHPExcel_Chart_DataSeries::TYPE_STOCKCHART)) {
            if ($groupType !== PHPExcel_Chart_DataSeries::TYPE_LINECHART) {
                if (($groupType == PHPExcel_Chart_DataSeries::TYPE_PIECHART) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_PIECHART_3D) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_DONUTCHART) || ($plotSeriesCount > 1)) {
                    $objWriter->startElement('c:varyColors');
                    $objWriter->writeAttribute('val', 1);
                    $objWriter->endElement();
                } else {
                    $objWriter->startElement('c:varyColors');
                    $objWriter->writeAttribute('val', 0);
                    $objWriter->endElement();
                }
            }
        }

        foreach ($plotSeriesOrder as $plotSeriesIdx => $plotSeriesRef) {
            $objWriter->startElement('c:ser');

            $objWriter->startElement('c:idx');
            $objWriter->writeAttribute('val', $this->_seriesIndex + $plotSeriesIdx);
            $objWriter->endElement();

            $objWriter->startElement('c:order');
            $objWriter->writeAttribute('val', $this->_seriesIndex + $plotSeriesRef);
            $objWriter->endElement();

            if (($groupType == PHPExcel_Chart_DataSeries::TYPE_PIECHART) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_PIECHART_3D) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_DONUTCHART)) {
                $objWriter->startElement('c:dPt');
                $objWriter->startElement('c:idx');
                $objWriter->writeAttribute('val', 3);
                $objWriter->endElement();

                $objWriter->startElement('c:bubble3D');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();

                $objWriter->startElement('c:spPr');
                $objWriter->startElement('a:solidFill');
                $objWriter->startElement('a:srgbClr');
                $objWriter->writeAttribute('val', 'FF9900');
                $objWriter->endElement();
                $objWriter->endElement();
                $objWriter->endElement();
                $objWriter->endElement();
            }

            //    Labels
            $plotSeriesLabel = $plotGroup->getPlotLabelByIndex($plotSeriesRef);
            if ($plotSeriesLabel && ($plotSeriesLabel->getPointCount() > 0)) {
                $objWriter->startElement('c:tx');
                $objWriter->startElement('c:strRef');
                $this->writePlotSeriesLabel($plotSeriesLabel, $objWriter);
                $objWriter->endElement();
                $objWriter->endElement();
            }

            //    Formatting for the points
            if (($groupType == PHPExcel_Chart_DataSeries::TYPE_LINECHART) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_STOCKCHART)) {
                $objWriter->startElement('c:spPr');
                $objWriter->startElement('a:ln');
                $objWriter->writeAttribute('w', 12700);
                if ($groupType == PHPExcel_Chart_DataSeries::TYPE_STOCKCHART) {
                    $objWriter->startElement('a:noFill');
                    $objWriter->endElement();
                }
                $objWriter->endElement();
                $objWriter->endElement();
            }

            $plotSeriesValues = $plotGroup->getPlotValuesByIndex($plotSeriesRef);
            if ($plotSeriesValues) {
                $plotSeriesMarker = $plotSeriesValues->getPointMarker();
                if ($plotSeriesMarker) {
                    $objWriter->startElement('c:marker');
                    $objWriter->startElement('c:symbol');
                    $objWriter->writeAttribute('val', $plotSeriesMarker);
                    $objWriter->endElement();

                    if ($plotSeriesMarker !== 'none') {
                        $objWriter->startElement('c:size');
                        $objWriter->writeAttribute('val', 3);
                        $objWriter->endElement();
                    }

                    $objWriter->endElement();
                }
            }

            if (($groupType === PHPExcel_Chart_DataSeries::TYPE_BARCHART) || ($groupType === PHPExcel_Chart_DataSeries::TYPE_BARCHART_3D) || ($groupType === PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART)) {
                $objWriter->startElement('c:invertIfNegative');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();
            }

            //    Category Labels
            $plotSeriesCategory = $plotGroup->getPlotCategoryByIndex($plotSeriesRef);
            if ($plotSeriesCategory && ($plotSeriesCategory->getPointCount() > 0)) {
                $catIsMultiLevelSeries = $catIsMultiLevelSeries || $plotSeriesCategory->isMultiLevelSeries();

                if (($groupType == PHPExcel_Chart_DataSeries::TYPE_PIECHART) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_PIECHART_3D) || ($groupType == PHPExcel_Chart_DataSeries::TYPE_DONUTCHART)) {
                    if (!is_null($plotGroup->getPlotStyle())) {
                        $plotStyle = $plotGroup->getPlotStyle();
                        if ($plotStyle) {
                            $objWriter->startElement('c:explosion');
                            $objWriter->writeAttribute('val', 25);
                            $objWriter->endElement();
                        }
                    }
                }

                if (($groupType === PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART) || ($groupType === PHPExcel_Chart_DataSeries::TYPE_SCATTERCHART)) {
                    $objWriter->startElement('c:xVal');
                } else {
                    $objWriter->startElement('c:cat');
                }

                $this->writePlotSeriesValues($plotSeriesCategory, $objWriter, $groupType, 'str', $pSheet);
                $objWriter->endElement();
            }

            //    Values
            if ($plotSeriesValues) {
                $valIsMultiLevelSeries = $valIsMultiLevelSeries || $plotSeriesValues->isMultiLevelSeries();

                if (($groupType === PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART) || ($groupType === PHPExcel_Chart_DataSeries::TYPE_SCATTERCHART)) {
                    $objWriter->startElement('c:yVal');
                } else {
                    $objWriter->startElement('c:val');
                }

                $this->writePlotSeriesValues($plotSeriesValues, $objWriter, $groupType, 'num', $pSheet);
                $objWriter->endElement();
            }

            if ($groupType === PHPExcel_Chart_DataSeries::TYPE_BUBBLECHART) {
                $this->writeBubbles($plotSeriesValues, $objWriter, $pSheet);
            }

            $objWriter->endElement();
        }

        $this->_seriesIndex += $plotSeriesIdx + 1;
    }

    /**
     * Write Plot Series Label
     *
     * @param  PHPExcel_Chart_DataSeriesValues $plotSeriesLabel
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writePlotSeriesLabel($plotSeriesLabel, $objWriter)
    {
        if (is_null($plotSeriesLabel)) {
            return;
        }

        $objWriter->startElement('c:f');
        $objWriter->writeRawData($plotSeriesLabel->getDataSource());
        $objWriter->endElement();

        $objWriter->startElement('c:strCache');
        $objWriter->startElement('c:ptCount');
        $objWriter->writeAttribute('val', $plotSeriesLabel->getPointCount());
        $objWriter->endElement();

        foreach ($plotSeriesLabel->getDataValues() as $plotLabelKey => $plotLabelValue) {
            $objWriter->startElement('c:pt');
            $objWriter->writeAttribute('idx', $plotLabelKey);

            $objWriter->startElement('c:v');
            $objWriter->writeRawData($plotLabelValue);
            $objWriter->endElement();
            $objWriter->endElement();
        }
        $objWriter->endElement();
    }

    /**
     * Write Plot Series Values
     *
     * @param  PHPExcel_Chart_DataSeriesValues $plotSeriesValues
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     * @param  string $groupType Type of plot for dataseries
     * @param  string $dataType Datatype of series values
     * @param  PHPExcel_Worksheet $pSheet
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writePlotSeriesValues($plotSeriesValues, $objWriter, $groupType, $dataType = 'str', PHPExcel_Worksheet $pSheet)
    {
        if (is_null($plotSeriesValues)) {
            return;
        }

        if ($plotSeriesValues->isMultiLevelSeries()) {
            $levelCount = $plotSeriesValues->multiLevelCount();

            $objWriter->startElement('c:multiLvlStrRef');

            $objWriter->startElement('c:f');
            $objWriter->writeRawData($plotSeriesValues->getDataSource());
            $objWriter->endElement();

            $objWriter->startElement('c:multiLvlStrCache');

            $objWriter->startElement('c:ptCount');
            $objWriter->writeAttribute('val', $plotSeriesValues->getPointCount());
            $objWriter->endElement();

            for ($level = 0; $level < $levelCount; ++$level) {
                $objWriter->startElement('c:lvl');

                foreach ($plotSeriesValues->getDataValues() as $plotSeriesKey => $plotSeriesValue) {
                    if (isset($plotSeriesValue[$level])) {
                        $objWriter->startElement('c:pt');
                        $objWriter->writeAttribute('idx', $plotSeriesKey);

                        $objWriter->startElement('c:v');
                        $objWriter->writeRawData($plotSeriesValue[$level]);
                        $objWriter->endElement();
                        $objWriter->endElement();
                    }
                }

                $objWriter->endElement();
            }

            $objWriter->endElement();

            $objWriter->endElement();
        } else {
            $objWriter->startElement('c:' . $dataType . 'Ref');

            $objWriter->startElement('c:f');
            $objWriter->writeRawData($plotSeriesValues->getDataSource());
            $objWriter->endElement();

            $objWriter->startElement('c:' . $dataType . 'Cache');

            if (($groupType != PHPExcel_Chart_DataSeries::TYPE_PIECHART) && ($groupType != PHPExcel_Chart_DataSeries::TYPE_PIECHART_3D) && ($groupType != PHPExcel_Chart_DataSeries::TYPE_DONUTCHART)) {
                if (($plotSeriesValues->getFormatCode() !== null) && ($plotSeriesValues->getFormatCode() !== '')) {
                    $objWriter->startElement('c:formatCode');
                    $objWriter->writeRawData($plotSeriesValues->getFormatCode());
                    $objWriter->endElement();
                }
            }

            $objWriter->startElement('c:ptCount');
            $objWriter->writeAttribute('val', $plotSeriesValues->getPointCount());
            $objWriter->endElement();

            $dataValues = $plotSeriesValues->getDataValues();
            if (!empty($dataValues)) {
                if (is_array($dataValues)) {
                    foreach ($dataValues as $plotSeriesKey => $plotSeriesValue) {
                        $objWriter->startElement('c:pt');
                        $objWriter->writeAttribute('idx', $plotSeriesKey);

                        $objWriter->startElement('c:v');
                        $objWriter->writeRawData($plotSeriesValue);
                        $objWriter->endElement();
                        $objWriter->endElement();
                    }
                }
            }

            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Write Bubble Chart Details
     *
     * @param  PHPExcel_Chart_DataSeriesValues $plotSeriesValues
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeBubbles($plotSeriesValues, $objWriter, PHPExcel_Worksheet $pSheet)
    {
        if (is_null($plotSeriesValues)) {
            return;
        }

        $objWriter->startElement('c:bubbleSize');
        $objWriter->startElement('c:numLit');

        $objWriter->startElement('c:formatCode');
        $objWriter->writeRawData('General');
        $objWriter->endElement();

        $objWriter->startElement('c:ptCount');
        $objWriter->writeAttribute('val', $plotSeriesValues->getPointCount());
        $objWriter->endElement();

        $dataValues = $plotSeriesValues->getDataValues();
        if (!empty($dataValues)) {
            if (is_array($dataValues)) {
                foreach ($dataValues as $plotSeriesKey => $plotSeriesValue) {
                    $objWriter->startElement('c:pt');
                    $objWriter->writeAttribute('idx', $plotSeriesKey);
                    $objWriter->startElement('c:v');
                    $objWriter->writeRawData(1);
                    $objWriter->endElement();
                    $objWriter->endElement();
                }
            }
        }

        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('c:bubble3D');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();
    }

    /**
     * Write Layout
     *
     * @param  PHPExcel_Chart_Layout $layout
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeLayout(PHPExcel_Chart_Layout $layout = null, $objWriter)
    {
        $objWriter->startElement('c:layout');

        if (!is_null($layout)) {
            $objWriter->startElement('c:manualLayout');

            $layoutTarget = $layout->getLayoutTarget();
            if (!is_null($layoutTarget)) {
                $objWriter->startElement('c:layoutTarget');
                $objWriter->writeAttribute('val', $layoutTarget);
                $objWriter->endElement();
            }

            $xMode = $layout->getXMode();
            if (!is_null($xMode)) {
                $objWriter->startElement('c:xMode');
                $objWriter->writeAttribute('val', $xMode);
                $objWriter->endElement();
            }

            $yMode = $layout->getYMode();
            if (!is_null($yMode)) {
                $objWriter->startElement('c:yMode');
                $objWriter->writeAttribute('val', $yMode);
                $objWriter->endElement();
            }

            $x = $layout->getXPosition();
            if (!is_null($x)) {
                $objWriter->startElement('c:x');
                $objWriter->writeAttribute('val', $x);
                $objWriter->endElement();
            }

            $y = $layout->getYPosition();
            if (!is_null($y)) {
                $objWriter->startElement('c:y');
                $objWriter->writeAttribute('val', $y);
                $objWriter->endElement();
            }

            $w = $layout->getWidth();
            if (!is_null($w)) {
                $objWriter->startElement('c:w');
                $objWriter->writeAttribute('val', $w);
                $objWriter->endElement();
            }

            $h = $layout->getHeight();
            if (!is_null($h)) {
                $objWriter->startElement('c:h');
                $objWriter->writeAttribute('val', $h);
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    /**
     * Write Alternate Content block
     *
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writeAlternateContent($objWriter)
    {
        $objWriter->startElement('mc:AlternateContent');
        $objWriter->writeAttribute('xmlns:mc', 'http://schemas.openxmlformats.org/markup-compatibility/2006');

        $objWriter->startElement('mc:Choice');
        $objWriter->writeAttribute('xmlns:c14', 'http://schemas.microsoft.com/office/drawing/2007/8/2/chart');
        $objWriter->writeAttribute('Requires', 'c14');

        $objWriter->startElement('c14:style');
        $objWriter->writeAttribute('val', '102');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('mc:Fallback');
        $objWriter->startElement('c:style');
        $objWriter->writeAttribute('val', '2');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Printer Settings
     *
     * @param  PHPExcel_Shared_XMLWriter $objWriter XML Writer
     *
     * @throws  PHPExcel_Writer_Exception
     */
    private function writePrintSettings($objWriter)
    {
        $objWriter->startElement('c:printSettings');

        $objWriter->startElement('c:headerFooter');
        $objWriter->endElement();

        $objWriter->startElement('c:pageMargins');
        $objWriter->writeAttribute('footer', 0.3);
        $objWriter->writeAttribute('header', 0.3);
        $objWriter->writeAttribute('r', 0.7);
        $objWriter->writeAttribute('l', 0.7);
        $objWriter->writeAttribute('t', 0.75);
        $objWriter->writeAttribute('b', 0.75);
        $objWriter->endElement();

        $objWriter->startElement('c:pageSetup');
        $objWriter->writeAttribute('orientation', "portrait");
        $objWriter->endElement();

        $objWriter->endElement();
    }
}
