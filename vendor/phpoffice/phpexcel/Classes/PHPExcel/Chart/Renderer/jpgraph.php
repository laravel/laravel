<?php

require_once(PHPExcel_Settings::getChartRendererPath().'/jpgraph.php');

/**
 * PHPExcel_Chart_Renderer_jpgraph
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
 * @category    PHPExcel
 * @package        PHPExcel_Chart_Renderer
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license        http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version        ##VERSION##, ##DATE##
 */
class PHPExcel_Chart_Renderer_jpgraph
{
    private static $width    = 640;

    private static $height    = 480;

    private static $colourSet = array(
        'mediumpurple1',    'palegreen3',     'gold1',          'cadetblue1',
        'darkmagenta',      'coral',          'dodgerblue3',    'eggplant',
        'mediumblue',       'magenta',        'sandybrown',     'cyan',
        'firebrick1',       'forestgreen',    'deeppink4',      'darkolivegreen',
        'goldenrod2'
    );

    private static $markSet = array(
        'diamond'  => MARK_DIAMOND,
        'square'   => MARK_SQUARE,
        'triangle' => MARK_UTRIANGLE,
        'x'        => MARK_X,
        'star'     => MARK_STAR,
        'dot'      => MARK_FILLEDCIRCLE,
        'dash'     => MARK_DTRIANGLE,
        'circle'   => MARK_CIRCLE,
        'plus'     => MARK_CROSS
    );


    private $chart;

    private $graph;

    private static $plotColour = 0;

    private static $plotMark = 0;


    private function formatPointMarker($seriesPlot, $markerID)
    {
        $plotMarkKeys = array_keys(self::$markSet);
        if (is_null($markerID)) {
            //    Use default plot marker (next marker in the series)
            self::$plotMark %= count(self::$markSet);
            $seriesPlot->mark->SetType(self::$markSet[$plotMarkKeys[self::$plotMark++]]);
        } elseif ($markerID !== 'none') {
            //    Use specified plot marker (if it exists)
            if (isset(self::$markSet[$markerID])) {
                $seriesPlot->mark->SetType(self::$markSet[$markerID]);
            } else {
                //    If the specified plot marker doesn't exist, use default plot marker (next marker in the series)
                self::$plotMark %= count(self::$markSet);
                $seriesPlot->mark->SetType(self::$markSet[$plotMarkKeys[self::$plotMark++]]);
            }
        } else {
            //    Hide plot marker
            $seriesPlot->mark->Hide();
        }
        $seriesPlot->mark->SetColor(self::$colourSet[self::$plotColour]);
        $seriesPlot->mark->SetFillColor(self::$colourSet[self::$plotColour]);
        $seriesPlot->SetColor(self::$colourSet[self::$plotColour++]);

        return $seriesPlot;
    }


    private function formatDataSetLabels($groupID, $datasetLabels, $labelCount, $rotation = '')
    {
        $datasetLabelFormatCode = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getFormatCode();
        if (!is_null($datasetLabelFormatCode)) {
            //    Retrieve any label formatting code
            $datasetLabelFormatCode = stripslashes($datasetLabelFormatCode);
        }

        $testCurrentIndex = 0;
        foreach ($datasetLabels as $i => $datasetLabel) {
            if (is_array($datasetLabel)) {
                if ($rotation == 'bar') {
                    $datasetLabels[$i] = implode(" ", $datasetLabel);
                } else {
                    $datasetLabel = array_reverse($datasetLabel);
                    $datasetLabels[$i] = implode("\n", $datasetLabel);
                }
            } else {
                //    Format labels according to any formatting code
                if (!is_null($datasetLabelFormatCode)) {
                    $datasetLabels[$i] = PHPExcel_Style_NumberFormat::toFormattedString($datasetLabel, $datasetLabelFormatCode);
                }
            }
            ++$testCurrentIndex;
        }

        return $datasetLabels;
    }


    private function percentageSumCalculation($groupID, $seriesCount)
    {
        //    Adjust our values to a percentage value across all series in the group
        for ($i = 0; $i < $seriesCount; ++$i) {
            if ($i == 0) {
                $sumValues = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            } else {
                $nextValues = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
                foreach ($nextValues as $k => $value) {
                    if (isset($sumValues[$k])) {
                        $sumValues[$k] += $value;
                    } else {
                        $sumValues[$k] = $value;
                    }
                }
            }
        }

        return $sumValues;
    }


    private function percentageAdjustValues($dataValues, $sumValues)
    {
        foreach ($dataValues as $k => $dataValue) {
            $dataValues[$k] = $dataValue / $sumValues[$k] * 100;
        }

        return $dataValues;
    }


    private function getCaption($captionElement)
    {
        //    Read any caption
        $caption = (!is_null($captionElement)) ? $captionElement->getCaption() : null;
        //    Test if we have a title caption to display
        if (!is_null($caption)) {
            //    If we do, it could be a plain string or an array
            if (is_array($caption)) {
                //    Implode an array to a plain string
                $caption = implode('', $caption);
            }
        }
        return $caption;
    }


    private function renderTitle()
    {
        $title = $this->getCaption($this->chart->getTitle());
        if (!is_null($title)) {
            $this->graph->title->Set($title);
        }
    }


    private function renderLegend()
    {
        $legend = $this->chart->getLegend();
        if (!is_null($legend)) {
            $legendPosition = $legend->getPosition();
            $legendOverlay = $legend->getOverlay();
            switch ($legendPosition) {
                case 'r':
                    $this->graph->legend->SetPos(0.01, 0.5, 'right', 'center');    //    right
                    $this->graph->legend->SetColumns(1);
                    break;
                case 'l':
                    $this->graph->legend->SetPos(0.01, 0.5, 'left', 'center');    //    left
                    $this->graph->legend->SetColumns(1);
                    break;
                case 't':
                    $this->graph->legend->SetPos(0.5, 0.01, 'center', 'top');    //    top
                    break;
                case 'b':
                    $this->graph->legend->SetPos(0.5, 0.99, 'center', 'bottom');    //    bottom
                    break;
                default:
                    $this->graph->legend->SetPos(0.01, 0.01, 'right', 'top');    //    top-right
                    $this->graph->legend->SetColumns(1);
                    break;
            }
        } else {
            $this->graph->legend->Hide();
        }
    }


    private function renderCartesianPlotArea($type = 'textlin')
    {
        $this->graph = new Graph(self::$width, self::$height);
        $this->graph->SetScale($type);

        $this->renderTitle();

        //    Rotate for bar rather than column chart
        $rotation = $this->chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotDirection();
        $reverse = ($rotation == 'bar') ? true : false;

        $xAxisLabel = $this->chart->getXAxisLabel();
        if (!is_null($xAxisLabel)) {
            $title = $this->getCaption($xAxisLabel);
            if (!is_null($title)) {
                $this->graph->xaxis->SetTitle($title, 'center');
                $this->graph->xaxis->title->SetMargin(35);
                if ($reverse) {
                    $this->graph->xaxis->title->SetAngle(90);
                    $this->graph->xaxis->title->SetMargin(90);
                }
            }
        }

        $yAxisLabel = $this->chart->getYAxisLabel();
        if (!is_null($yAxisLabel)) {
            $title = $this->getCaption($yAxisLabel);
            if (!is_null($title)) {
                $this->graph->yaxis->SetTitle($title, 'center');
                if ($reverse) {
                    $this->graph->yaxis->title->SetAngle(0);
                    $this->graph->yaxis->title->SetMargin(-55);
                }
            }
        }
    }


    private function renderPiePlotArea($doughnut = false)
    {
        $this->graph = new PieGraph(self::$width, self::$height);

        $this->renderTitle();
    }


    private function renderRadarPlotArea()
    {
        $this->graph = new RadarGraph(self::$width, self::$height);
        $this->graph->SetScale('lin');

        $this->renderTitle();
    }


    private function renderPlotLine($groupID, $filled = false, $combination = false, $dimensions = '2d')
    {
        $grouping = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();

        $labelCount = count($this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
        if ($labelCount > 0) {
            $datasetLabels = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
            $datasetLabels = $this->formatDataSetLabels($groupID, $datasetLabels, $labelCount);
            $this->graph->xaxis->SetTickLabels($datasetLabels);
        }

        $seriesCount = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();
        if ($grouping == 'percentStacked') {
            $sumValues = $this->percentageSumCalculation($groupID, $seriesCount);
        }

        //    Loop through each data series in turn
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValues = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            $marker = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();

            if ($grouping == 'percentStacked') {
                $dataValues = $this->percentageAdjustValues($dataValues, $sumValues);
            }

            //    Fill in any missing values in the $dataValues array
            $testCurrentIndex = 0;
            foreach ($dataValues as $k => $dataValue) {
                while ($k != $testCurrentIndex) {
                    $dataValues[$testCurrentIndex] = null;
                    ++$testCurrentIndex;
                }
                ++$testCurrentIndex;
            }

            $seriesPlot = new LinePlot($dataValues);
            if ($combination) {
                $seriesPlot->SetBarCenter();
            }

            if ($filled) {
                $seriesPlot->SetFilled(true);
                $seriesPlot->SetColor('black');
                $seriesPlot->SetFillColor(self::$colourSet[self::$plotColour++]);
            } else {
                //    Set the appropriate plot marker
                $this->formatPointMarker($seriesPlot, $marker);
            }
            $dataLabel = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
            $seriesPlot->SetLegend($dataLabel);

            $seriesPlots[] = $seriesPlot;
        }

        if ($grouping == 'standard') {
            $groupPlot = $seriesPlots;
        } else {
            $groupPlot = new AccLinePlot($seriesPlots);
        }
        $this->graph->Add($groupPlot);
    }


    private function renderPlotBar($groupID, $dimensions = '2d')
    {
        $rotation = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotDirection();
        //    Rotate for bar rather than column chart
        if (($groupID == 0) && ($rotation == 'bar')) {
            $this->graph->Set90AndMargin();
        }
        $grouping = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();

        $labelCount = count($this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
        if ($labelCount > 0) {
            $datasetLabels = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
            $datasetLabels = $this->formatDataSetLabels($groupID, $datasetLabels, $labelCount, $rotation);
            //    Rotate for bar rather than column chart
            if ($rotation == 'bar') {
                $datasetLabels = array_reverse($datasetLabels);
                $this->graph->yaxis->SetPos('max');
                $this->graph->yaxis->SetLabelAlign('center', 'top');
                $this->graph->yaxis->SetLabelSide(SIDE_RIGHT);
            }
            $this->graph->xaxis->SetTickLabels($datasetLabels);
        }


        $seriesCount = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();
        if ($grouping == 'percentStacked') {
            $sumValues = $this->percentageSumCalculation($groupID, $seriesCount);
        }

        //    Loop through each data series in turn
        for ($j = 0; $j < $seriesCount; ++$j) {
            $dataValues = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($j)->getDataValues();
            if ($grouping == 'percentStacked') {
                $dataValues = $this->percentageAdjustValues($dataValues, $sumValues);
            }

            //    Fill in any missing values in the $dataValues array
            $testCurrentIndex = 0;
            foreach ($dataValues as $k => $dataValue) {
                while ($k != $testCurrentIndex) {
                    $dataValues[$testCurrentIndex] = null;
                    ++$testCurrentIndex;
                }
                ++$testCurrentIndex;
            }

            //    Reverse the $dataValues order for bar rather than column chart
            if ($rotation == 'bar') {
                $dataValues = array_reverse($dataValues);
            }
            $seriesPlot = new BarPlot($dataValues);
            $seriesPlot->SetColor('black');
            $seriesPlot->SetFillColor(self::$colourSet[self::$plotColour++]);
            if ($dimensions == '3d') {
                $seriesPlot->SetShadow();
            }
            if (!$this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($j)) {
                $dataLabel = '';
            } else {
                $dataLabel = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($j)->getDataValue();
            }
            $seriesPlot->SetLegend($dataLabel);

            $seriesPlots[] = $seriesPlot;
        }
        //    Reverse the plot order for bar rather than column chart
        if (($rotation == 'bar') && (!($grouping == 'percentStacked'))) {
            $seriesPlots = array_reverse($seriesPlots);
        }

        if ($grouping == 'clustered') {
            $groupPlot = new GroupBarPlot($seriesPlots);
        } elseif ($grouping == 'standard') {
            $groupPlot = new GroupBarPlot($seriesPlots);
        } else {
            $groupPlot = new AccBarPlot($seriesPlots);
            if ($dimensions == '3d') {
                $groupPlot->SetShadow();
            }
        }

        $this->graph->Add($groupPlot);
    }


    private function renderPlotScatter($groupID, $bubble)
    {
        $grouping = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
        $scatterStyle = $bubbleSize = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();

        $seriesCount = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();

        //    Loop through each data series in turn
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValuesY = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
            $dataValuesX = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();

            foreach ($dataValuesY as $k => $dataValueY) {
                $dataValuesY[$k] = $k;
            }

            $seriesPlot = new ScatterPlot($dataValuesX, $dataValuesY);
            if ($scatterStyle == 'lineMarker') {
                $seriesPlot->SetLinkPoints();
                $seriesPlot->link->SetColor(self::$colourSet[self::$plotColour]);
            } elseif ($scatterStyle == 'smoothMarker') {
                $spline = new Spline($dataValuesY, $dataValuesX);
                list($splineDataY, $splineDataX) = $spline->Get(count($dataValuesX) * self::$width / 20);
                $lplot = new LinePlot($splineDataX, $splineDataY);
                $lplot->SetColor(self::$colourSet[self::$plotColour]);

                $this->graph->Add($lplot);
            }

            if ($bubble) {
                $this->formatPointMarker($seriesPlot, 'dot');
                $seriesPlot->mark->SetColor('black');
                $seriesPlot->mark->SetSize($bubbleSize);
            } else {
                $marker = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();
                $this->formatPointMarker($seriesPlot, $marker);
            }
            $dataLabel = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
            $seriesPlot->SetLegend($dataLabel);

            $this->graph->Add($seriesPlot);
        }
    }


    private function renderPlotRadar($groupID)
    {
        $radarStyle = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();

        $seriesCount = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();

        //    Loop through each data series in turn
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValuesY = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
            $dataValuesX = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            $marker = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();

            $dataValues = array();
            foreach ($dataValuesY as $k => $dataValueY) {
                $dataValues[$k] = implode(' ', array_reverse($dataValueY));
            }
            $tmp = array_shift($dataValues);
            $dataValues[] = $tmp;
            $tmp = array_shift($dataValuesX);
            $dataValuesX[] = $tmp;

            $this->graph->SetTitles(array_reverse($dataValues));

            $seriesPlot = new RadarPlot(array_reverse($dataValuesX));

            $dataLabel = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
            $seriesPlot->SetColor(self::$colourSet[self::$plotColour++]);
            if ($radarStyle == 'filled') {
                $seriesPlot->SetFillColor(self::$colourSet[self::$plotColour]);
            }
            $this->formatPointMarker($seriesPlot, $marker);
            $seriesPlot->SetLegend($dataLabel);

            $this->graph->Add($seriesPlot);
        }
    }


    private function renderPlotContour($groupID)
    {
        $contourStyle = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();

        $seriesCount = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();

        $dataValues = array();
        //    Loop through each data series in turn
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValuesY = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
            $dataValuesX = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();

            $dataValues[$i] = $dataValuesX;
        }
        $seriesPlot = new ContourPlot($dataValues);

        $this->graph->Add($seriesPlot);
    }


    private function renderPlotStock($groupID)
    {
        $seriesCount = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $plotOrder = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotOrder();

        $dataValues = array();
        //    Loop through each data series in turn and build the plot arrays
        foreach ($plotOrder as $i => $v) {
            $dataValuesX = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($v)->getDataValues();
            foreach ($dataValuesX as $j => $dataValueX) {
                $dataValues[$plotOrder[$i]][$j] = $dataValueX;
            }
        }
        if (empty($dataValues)) {
            return;
        }

        $dataValuesPlot = array();
        // Flatten the plot arrays to a single dimensional array to work with jpgraph
        for ($j = 0; $j < count($dataValues[0]); ++$j) {
            for ($i = 0; $i < $seriesCount; ++$i) {
                $dataValuesPlot[] = $dataValues[$i][$j];
            }
        }

        // Set the x-axis labels
        $labelCount = count($this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
        if ($labelCount > 0) {
            $datasetLabels = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
            $datasetLabels = $this->formatDataSetLabels($groupID, $datasetLabels, $labelCount);
            $this->graph->xaxis->SetTickLabels($datasetLabels);
        }

        $seriesPlot = new StockPlot($dataValuesPlot);
        $seriesPlot->SetWidth(20);

        $this->graph->Add($seriesPlot);
    }


    private function renderAreaChart($groupCount, $dimensions = '2d')
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_line.php');

        $this->renderCartesianPlotArea();

        for ($i = 0; $i < $groupCount; ++$i) {
            $this->renderPlotLine($i, true, false, $dimensions);
        }
    }


    private function renderLineChart($groupCount, $dimensions = '2d')
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_line.php');

        $this->renderCartesianPlotArea();

        for ($i = 0; $i < $groupCount; ++$i) {
            $this->renderPlotLine($i, false, false, $dimensions);
        }
    }


    private function renderBarChart($groupCount, $dimensions = '2d')
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_bar.php');

        $this->renderCartesianPlotArea();

        for ($i = 0; $i < $groupCount; ++$i) {
            $this->renderPlotBar($i, $dimensions);
        }
    }


    private function renderScatterChart($groupCount)
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_scatter.php');
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_regstat.php');
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_line.php');

        $this->renderCartesianPlotArea('linlin');

        for ($i = 0; $i < $groupCount; ++$i) {
            $this->renderPlotScatter($i, false);
        }
    }


    private function renderBubbleChart($groupCount)
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_scatter.php');

        $this->renderCartesianPlotArea('linlin');

        for ($i = 0; $i < $groupCount; ++$i) {
            $this->renderPlotScatter($i, true);
        }
    }


    private function renderPieChart($groupCount, $dimensions = '2d', $doughnut = false, $multiplePlots = false)
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_pie.php');
        if ($dimensions == '3d') {
            require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_pie3d.php');
        }

        $this->renderPiePlotArea($doughnut);

        $iLimit = ($multiplePlots) ? $groupCount : 1;
        for ($groupID = 0; $groupID < $iLimit; ++$groupID) {
            $grouping = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
            $exploded = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();
            if ($groupID == 0) {
                $labelCount = count($this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
                if ($labelCount > 0) {
                    $datasetLabels = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
                    $datasetLabels = $this->formatDataSetLabels($groupID, $datasetLabels, $labelCount);
                }
            }

            $seriesCount = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
            $seriesPlots = array();
            //    For pie charts, we only display the first series: doughnut charts generally display all series
            $jLimit = ($multiplePlots) ? $seriesCount : 1;
            //    Loop through each data series in turn
            for ($j = 0; $j < $jLimit; ++$j) {
                $dataValues = $this->chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($j)->getDataValues();

                //    Fill in any missing values in the $dataValues array
                $testCurrentIndex = 0;
                foreach ($dataValues as $k => $dataValue) {
                    while ($k != $testCurrentIndex) {
                        $dataValues[$testCurrentIndex] = null;
                        ++$testCurrentIndex;
                    }
                    ++$testCurrentIndex;
                }

                if ($dimensions == '3d') {
                    $seriesPlot = new PiePlot3D($dataValues);
                } else {
                    if ($doughnut) {
                        $seriesPlot = new PiePlotC($dataValues);
                    } else {
                        $seriesPlot = new PiePlot($dataValues);
                    }
                }

                if ($multiplePlots) {
                    $seriesPlot->SetSize(($jLimit-$j) / ($jLimit * 4));
                }

                if ($doughnut) {
                    $seriesPlot->SetMidColor('white');
                }

                $seriesPlot->SetColor(self::$colourSet[self::$plotColour++]);
                if (count($datasetLabels) > 0) {
                    $seriesPlot->SetLabels(array_fill(0, count($datasetLabels), ''));
                }
                if ($dimensions != '3d') {
                    $seriesPlot->SetGuideLines(false);
                }
                if ($j == 0) {
                    if ($exploded) {
                        $seriesPlot->ExplodeAll();
                    }
                    $seriesPlot->SetLegends($datasetLabels);
                }

                $this->graph->Add($seriesPlot);
            }
        }
    }


    private function renderRadarChart($groupCount)
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_radar.php');

        $this->renderRadarPlotArea();

        for ($groupID = 0; $groupID < $groupCount; ++$groupID) {
            $this->renderPlotRadar($groupID);
        }
    }


    private function renderStockChart($groupCount)
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_stock.php');

        $this->renderCartesianPlotArea('intint');

        for ($groupID = 0; $groupID < $groupCount; ++$groupID) {
            $this->renderPlotStock($groupID);
        }
    }


    private function renderContourChart($groupCount, $dimensions)
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_contour.php');

        $this->renderCartesianPlotArea('intint');

        for ($i = 0; $i < $groupCount; ++$i) {
            $this->renderPlotContour($i);
        }
    }


    private function renderCombinationChart($groupCount, $dimensions, $outputDestination)
    {
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_line.php');
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_bar.php');
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_scatter.php');
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_regstat.php');
        require_once(PHPExcel_Settings::getChartRendererPath().'jpgraph_line.php');

        $this->renderCartesianPlotArea();

        for ($i = 0; $i < $groupCount; ++$i) {
            $dimensions = null;
            $chartType = $this->chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
            switch ($chartType) {
                case 'area3DChart':
                    $dimensions = '3d';
                    // no break
                case 'areaChart':
                    $this->renderPlotLine($i, true, true, $dimensions);
                    break;
                case 'bar3DChart':
                    $dimensions = '3d';
                    // no break
                case 'barChart':
                    $this->renderPlotBar($i, $dimensions);
                    break;
                case 'line3DChart':
                    $dimensions = '3d';
                    // no break
                case 'lineChart':
                    $this->renderPlotLine($i, false, true, $dimensions);
                    break;
                case 'scatterChart':
                    $this->renderPlotScatter($i, false);
                    break;
                case 'bubbleChart':
                    $this->renderPlotScatter($i, true);
                    break;
                default:
                    $this->graph = null;
                    return false;
            }
        }

        $this->renderLegend();

        $this->graph->Stroke($outputDestination);
        return true;
    }


    public function render($outputDestination)
    {
        self::$plotColour = 0;

        $groupCount = $this->chart->getPlotArea()->getPlotGroupCount();

        $dimensions = null;
        if ($groupCount == 1) {
            $chartType = $this->chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotType();
        } else {
            $chartTypes = array();
            for ($i = 0; $i < $groupCount; ++$i) {
                $chartTypes[] = $this->chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
            }
            $chartTypes = array_unique($chartTypes);
            if (count($chartTypes) == 1) {
                $chartType = array_pop($chartTypes);
            } elseif (count($chartTypes) == 0) {
                echo 'Chart is not yet implemented<br />';
                return false;
            } else {
                return $this->renderCombinationChart($groupCount, $dimensions, $outputDestination);
            }
        }

        switch ($chartType) {
            case 'area3DChart':
                $dimensions = '3d';
                // no break
            case 'areaChart':
                $this->renderAreaChart($groupCount, $dimensions);
                break;
            case 'bar3DChart':
                $dimensions = '3d';
                // no break
            case 'barChart':
                $this->renderBarChart($groupCount, $dimensions);
                break;
            case 'line3DChart':
                $dimensions = '3d';
                // no break
            case 'lineChart':
                $this->renderLineChart($groupCount, $dimensions);
                break;
            case 'pie3DChart':
                $dimensions = '3d';
                // no break
            case 'pieChart':
                $this->renderPieChart($groupCount, $dimensions, false, false);
                break;
            case 'doughnut3DChart':
                $dimensions = '3d';
                // no break
            case 'doughnutChart':
                $this->renderPieChart($groupCount, $dimensions, true, true);
                break;
            case 'scatterChart':
                $this->renderScatterChart($groupCount);
                break;
            case 'bubbleChart':
                $this->renderBubbleChart($groupCount);
                break;
            case 'radarChart':
                $this->renderRadarChart($groupCount);
                break;
            case 'surface3DChart':
                $dimensions = '3d';
                // no break
            case 'surfaceChart':
                $this->renderContourChart($groupCount, $dimensions);
                break;
            case 'stockChart':
                $this->renderStockChart($groupCount, $dimensions);
                break;
            default:
                echo $chartType.' is not yet implemented<br />';
                return false;
        }
        $this->renderLegend();

        $this->graph->Stroke($outputDestination);
        return true;
    }


    /**
     * Create a new PHPExcel_Chart_Renderer_jpgraph
     */
    public function __construct(PHPExcel_Chart $chart)
    {
        $this->graph    = null;
        $this->chart    = $chart;
    }
}
