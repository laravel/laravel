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
 * @category	PHPExcel
 * @package		PHPExcel_Chart_Renderer
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.8.0, 2014-03-02
 */


require_once(PHPExcel_Settings::getChartRendererPath().'/jpgraph.php');


/**
 * PHPExcel_Chart_Renderer_jpgraph
 *
 * @category	PHPExcel
 * @package		PHPExcel_Chart_Renderer
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Chart_Renderer_jpgraph
{
	private static $_width	= 640;

	private static $_height	= 480;

	private static $_colourSet = array( 'mediumpurple1',	'palegreen3',	'gold1',		'cadetblue1',
										'darkmagenta',		'coral',		'dodgerblue3',	'eggplant',
										'mediumblue',		'magenta',		'sandybrown',	'cyan',
										'firebrick1',		'forestgreen',	'deeppink4',	'darkolivegreen',
										'goldenrod2'
									  );

	private static $_markSet = array(	'diamond'	=> MARK_DIAMOND,
										'square'	=> MARK_SQUARE,
										'triangle'	=> MARK_UTRIANGLE,
										'x'			=> MARK_X,
										'star'		=> MARK_STAR,
										'dot'		=> MARK_FILLEDCIRCLE,
										'dash'		=> MARK_DTRIANGLE,
										'circle'	=> MARK_CIRCLE,
										'plus'		=> MARK_CROSS
									);


	private $_chart	= null;

	private $_graph	= null;

	private static $_plotColour	= 0;

	private static $_plotMark	= 0;


	private function _formatPointMarker($seriesPlot,$markerID) {
		$plotMarkKeys = array_keys(self::$_markSet);
		if (is_null($markerID)) {
			//	Use default plot marker (next marker in the series)
			self::$_plotMark %= count(self::$_markSet);
			$seriesPlot->mark->SetType(self::$_markSet[$plotMarkKeys[self::$_plotMark++]]);
		} elseif ($markerID !== 'none') {
			//	Use specified plot marker (if it exists)
			if (isset(self::$_markSet[$markerID])) {
				$seriesPlot->mark->SetType(self::$_markSet[$markerID]);
			} else {
				//	If the specified plot marker doesn't exist, use default plot marker (next marker in the series)
				self::$_plotMark %= count(self::$_markSet);
				$seriesPlot->mark->SetType(self::$_markSet[$plotMarkKeys[self::$_plotMark++]]);
			}
		} else {
			//	Hide plot marker
			$seriesPlot->mark->Hide();
		}
		$seriesPlot->mark->SetColor(self::$_colourSet[self::$_plotColour]);
		$seriesPlot->mark->SetFillColor(self::$_colourSet[self::$_plotColour]);
		$seriesPlot->SetColor(self::$_colourSet[self::$_plotColour++]);

		return $seriesPlot;
	}	//	function _formatPointMarker()


	private function _formatDataSetLabels($groupID, $datasetLabels, $labelCount, $rotation = '') {
		$datasetLabelFormatCode = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getFormatCode();
		if (!is_null($datasetLabelFormatCode)) {
			//	Retrieve any label formatting code
			$datasetLabelFormatCode = stripslashes($datasetLabelFormatCode);
		}

		$testCurrentIndex = 0;
		foreach($datasetLabels as $i => $datasetLabel) {
			if (is_array($datasetLabel)) {
				if ($rotation == 'bar') {
					$datasetLabels[$i] = implode(" ",$datasetLabel);
				} else {
					$datasetLabel = array_reverse($datasetLabel);
					$datasetLabels[$i] = implode("\n",$datasetLabel);
				}
			} else {
				//	Format labels according to any formatting code
				if (!is_null($datasetLabelFormatCode)) {
					$datasetLabels[$i] = PHPExcel_Style_NumberFormat::toFormattedString($datasetLabel,$datasetLabelFormatCode);
				}
			}
			++$testCurrentIndex;
		}

		return $datasetLabels;
	}	//	function _formatDataSetLabels()


	private function _percentageSumCalculation($groupID,$seriesCount) {
		//	Adjust our values to a percentage value across all series in the group
		for($i = 0; $i < $seriesCount; ++$i) {
			if ($i == 0) {
				$sumValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
			} else {
				$nextValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
				foreach($nextValues as $k => $value) {
					if (isset($sumValues[$k])) {
						$sumValues[$k] += $value;
					} else {
						$sumValues[$k] = $value;
					}
				}
			}
		}

		return $sumValues;
	}	//	function _percentageSumCalculation()


	private function _percentageAdjustValues($dataValues,$sumValues) {
		foreach($dataValues as $k => $dataValue) {
			$dataValues[$k] = $dataValue / $sumValues[$k] * 100;
		}

		return $dataValues;
	}	//	function _percentageAdjustValues()


	private function _getCaption($captionElement) {
		//	Read any caption
		$caption = (!is_null($captionElement)) ? $captionElement->getCaption() : NULL;
		//	Test if we have a title caption to display
		if (!is_null($caption)) {
			//	If we do, it could be a plain string or an array
			if (is_array($caption)) {
				//	Implode an array to a plain string
				$caption = implode('',$caption);
			}
		}
		return $caption;
	}	//	function _getCaption()


	private function _renderTitle() {
		$title = $this->_getCaption($this->_chart->getTitle());
		if (!is_null($title)) {
			$this->_graph->title->Set($title);
		}
	}	//	function _renderTitle()


	private function _renderLegend() {
		$legend = $this->_chart->getLegend();
		if (!is_null($legend)) {
			$legendPosition = $legend->getPosition();
			$legendOverlay = $legend->getOverlay();
			switch ($legendPosition) {
				case 'r'	:
					$this->_graph->legend->SetPos(0.01,0.5,'right','center');	//	right
					$this->_graph->legend->SetColumns(1);
					break;
				case 'l'	:
					$this->_graph->legend->SetPos(0.01,0.5,'left','center');	//	left
					$this->_graph->legend->SetColumns(1);
					break;
				case 't'	:
					$this->_graph->legend->SetPos(0.5,0.01,'center','top');	//	top
					break;
				case 'b'	:
					$this->_graph->legend->SetPos(0.5,0.99,'center','bottom');	//	bottom
					break;
				default		:
					$this->_graph->legend->SetPos(0.01,0.01,'right','top');	//	top-right
					$this->_graph->legend->SetColumns(1);
					break;
			}
		} else {
			$this->_graph->legend->Hide();
		}
	}	//	function _renderLegend()


	private function _renderCartesianPlotArea($type='textlin') {
		$this->_graph = new Graph(self::$_width,self::$_height);
		$this->_graph->SetScale($type);

		$this->_renderTitle();

		//	Rotate for bar rather than column chart
		$rotation = $this->_chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotDirection();
		$reverse = ($rotation == 'bar') ? true : false;

		$xAxisLabel = $this->_chart->getXAxisLabel();
		if (!is_null($xAxisLabel)) {
			$title = $this->_getCaption($xAxisLabel);
			if (!is_null($title)) {
				$this->_graph->xaxis->SetTitle($title,'center');
				$this->_graph->xaxis->title->SetMargin(35);
				if ($reverse) {
					$this->_graph->xaxis->title->SetAngle(90);
					$this->_graph->xaxis->title->SetMargin(90);
				}
			}
		}

		$yAxisLabel = $this->_chart->getYAxisLabel();
		if (!is_null($yAxisLabel)) {
			$title = $this->_getCaption($yAxisLabel);
			if (!is_null($title)) {
				$this->_graph->yaxis->SetTitle($title,'center');
				if ($reverse) {
					$this->_graph->yaxis->title->SetAngle(0);
					$this->_graph->yaxis->title->SetMargin(-55);
				}
			}
		}
	}	//	function _renderCartesianPlotArea()


	private function _renderPiePlotArea($doughnut = False) {
		$this->_graph = new PieGraph(self::$_width,self::$_height);

		$this->_renderTitle();
	}	//	function _renderPiePlotArea()


	private function _renderRadarPlotArea() {
		$this->_graph = new RadarGraph(self::$_width,self::$_height);
		$this->_graph->SetScale('lin');

		$this->_renderTitle();
	}	//	function _renderRadarPlotArea()


	private function _renderPlotLine($groupID, $filled = false, $combination = false, $dimensions = '2d') {
		$grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();

        $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
		if ($labelCount > 0) {
			$datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
			$datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount);
			$this->_graph->xaxis->SetTickLabels($datasetLabels);
		}

		$seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
		$seriesPlots = array();
		if ($grouping == 'percentStacked') {
			$sumValues = $this->_percentageSumCalculation($groupID,$seriesCount);
		}

		//	Loop through each data series in turn
		for($i = 0; $i < $seriesCount; ++$i) {
			$dataValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
			$marker = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();

			if ($grouping == 'percentStacked') {
				$dataValues = $this->_percentageAdjustValues($dataValues,$sumValues);
			}

			//	Fill in any missing values in the $dataValues array
			$testCurrentIndex = 0;
			foreach($dataValues as $k => $dataValue) {
				while($k != $testCurrentIndex) {
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
				$seriesPlot->SetFillColor(self::$_colourSet[self::$_plotColour++]);
			} else {
				//	Set the appropriate plot marker
				$this->_formatPointMarker($seriesPlot,$marker);
			}
			$dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
			$seriesPlot->SetLegend($dataLabel);

			$seriesPlots[] = $seriesPlot;
		}

		if ($grouping == 'standard') {
			$groupPlot = $seriesPlots;
		} else {
			$groupPlot = new AccLinePlot($seriesPlots);
		}
		$this->_graph->Add($groupPlot);
	}	//	function _renderPlotLine()


	private function _renderPlotBar($groupID, $dimensions = '2d') {
		$rotation = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotDirection();
		//	Rotate for bar rather than column chart
		if (($groupID == 0) && ($rotation == 'bar')) {
			$this->_graph->Set90AndMargin();
		}
		$grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();

        $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
		if ($labelCount > 0) {
			$datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
			$datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount, $rotation);
			//	Rotate for bar rather than column chart
			if ($rotation == 'bar') {
				$datasetLabels = array_reverse($datasetLabels);
				$this->_graph->yaxis->SetPos('max');
				$this->_graph->yaxis->SetLabelAlign('center','top');
				$this->_graph->yaxis->SetLabelSide(SIDE_RIGHT);
			}
			$this->_graph->xaxis->SetTickLabels($datasetLabels);
		}


		$seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
		$seriesPlots = array();
		if ($grouping == 'percentStacked') {
			$sumValues = $this->_percentageSumCalculation($groupID,$seriesCount);
		}

		//	Loop through each data series in turn
		for($j = 0; $j < $seriesCount; ++$j) {
			$dataValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($j)->getDataValues();
			if ($grouping == 'percentStacked') {
				$dataValues = $this->_percentageAdjustValues($dataValues,$sumValues);
			}

			//	Fill in any missing values in the $dataValues array
			$testCurrentIndex = 0;
			foreach($dataValues as $k => $dataValue) {
				while($k != $testCurrentIndex) {
					$dataValues[$testCurrentIndex] = null;
					++$testCurrentIndex;
				}
				++$testCurrentIndex;
			}

			//	Reverse the $dataValues order for bar rather than column chart
			if ($rotation == 'bar') {
				$dataValues = array_reverse($dataValues);
			}
			$seriesPlot = new BarPlot($dataValues);
			$seriesPlot->SetColor('black');
			$seriesPlot->SetFillColor(self::$_colourSet[self::$_plotColour++]);
			if ($dimensions == '3d') {
				$seriesPlot->SetShadow();
			}
			if (!$this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($j)) {
				$dataLabel = '';
			} else {
				$dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($j)->getDataValue();
			}
			$seriesPlot->SetLegend($dataLabel);

			$seriesPlots[] = $seriesPlot;
		}
		//	Reverse the plot order for bar rather than column chart
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

		$this->_graph->Add($groupPlot);
	}	//	function _renderPlotBar()


	private function _renderPlotScatter($groupID,$bubble) {
		$grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
		$scatterStyle = $bubbleSize = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();

		$seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
		$seriesPlots = array();

		//	Loop through each data series in turn
		for($i = 0; $i < $seriesCount; ++$i) {
			$dataValuesY = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
			$dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();

			foreach($dataValuesY as $k => $dataValueY) {
				$dataValuesY[$k] = $k;
			}

			$seriesPlot = new ScatterPlot($dataValuesX,$dataValuesY);
			if ($scatterStyle == 'lineMarker') {
				$seriesPlot->SetLinkPoints();
				$seriesPlot->link->SetColor(self::$_colourSet[self::$_plotColour]);
			} elseif ($scatterStyle == 'smoothMarker') {
				$spline = new Spline($dataValuesY,$dataValuesX);
				list($splineDataY,$splineDataX) = $spline->Get(count($dataValuesX) * self::$_width / 20);
				$lplot = new LinePlot($splineDataX,$splineDataY);
				$lplot->SetColor(self::$_colourSet[self::$_plotColour]);

				$this->_graph->Add($lplot);
			}

			if ($bubble) {
				$this->_formatPointMarker($seriesPlot,'dot');
				$seriesPlot->mark->SetColor('black');
				$seriesPlot->mark->SetSize($bubbleSize);
			} else {
				$marker = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();
				$this->_formatPointMarker($seriesPlot,$marker);
			}
			$dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
			$seriesPlot->SetLegend($dataLabel);

			$this->_graph->Add($seriesPlot);
		}
	}	//	function _renderPlotScatter()


	private function _renderPlotRadar($groupID) {
		$radarStyle = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();

		$seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
		$seriesPlots = array();

		//	Loop through each data series in turn
		for($i = 0; $i < $seriesCount; ++$i) {
			$dataValuesY = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
			$dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
			$marker = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();

			$dataValues = array();
			foreach($dataValuesY as $k => $dataValueY) {
				$dataValues[$k] = implode(' ',array_reverse($dataValueY));
			}
			$tmp = array_shift($dataValues);
			$dataValues[] = $tmp;
			$tmp = array_shift($dataValuesX);
			$dataValuesX[] = $tmp;

			$this->_graph->SetTitles(array_reverse($dataValues));

			$seriesPlot = new RadarPlot(array_reverse($dataValuesX));

			$dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
			$seriesPlot->SetColor(self::$_colourSet[self::$_plotColour++]);
			if ($radarStyle == 'filled') {
				$seriesPlot->SetFillColor(self::$_colourSet[self::$_plotColour]);
			}
			$this->_formatPointMarker($seriesPlot,$marker);
			$seriesPlot->SetLegend($dataLabel);

			$this->_graph->Add($seriesPlot);
		}
	}	//	function _renderPlotRadar()


	private function _renderPlotContour($groupID) {
		$contourStyle = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();

		$seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
		$seriesPlots = array();

		$dataValues = array();
		//	Loop through each data series in turn
		for($i = 0; $i < $seriesCount; ++$i) {
			$dataValuesY = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
			$dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();

			$dataValues[$i] = $dataValuesX;
		}
		$seriesPlot = new ContourPlot($dataValues);

		$this->_graph->Add($seriesPlot);
	}	//	function _renderPlotContour()


	private function _renderPlotStock($groupID) {
		$seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
		$plotOrder = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotOrder();

		$dataValues = array();
		//	Loop through each data series in turn and build the plot arrays
		foreach($plotOrder as $i => $v) {
			$dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($v)->getDataValues();
			foreach($dataValuesX as $j => $dataValueX) {
				$dataValues[$plotOrder[$i]][$j] = $dataValueX;
			}
		}
		if(empty($dataValues)) {
			return;
		}

		$dataValuesPlot = array();
        // Flatten the plot arrays to a single dimensional array to work with jpgraph
		for($j = 0; $j < count($dataValues[0]); $j++) {
			for($i = 0; $i < $seriesCount; $i++) {
				$dataValuesPlot[] = $dataValues[$i][$j];
			}
		}

        // Set the x-axis labels
        $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
		if ($labelCount > 0) {
			$datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
			$datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount);
			$this->_graph->xaxis->SetTickLabels($datasetLabels);
		}

		$seriesPlot = new StockPlot($dataValuesPlot);
		$seriesPlot->SetWidth(20);

		$this->_graph->Add($seriesPlot);
	}	//	function _renderPlotStock()


	private function _renderAreaChart($groupCount, $dimensions = '2d') {
		require_once('jpgraph_line.php');

		$this->_renderCartesianPlotArea();

		for($i = 0; $i < $groupCount; ++$i) {
			$this->_renderPlotLine($i,True,False,$dimensions);
		}
	}	//	function _renderAreaChart()


	private function _renderLineChart($groupCount, $dimensions = '2d') {
		require_once('jpgraph_line.php');

		$this->_renderCartesianPlotArea();

		for($i = 0; $i < $groupCount; ++$i) {
			$this->_renderPlotLine($i,False,False,$dimensions);
		}
	}	//	function _renderLineChart()


	private function _renderBarChart($groupCount, $dimensions = '2d') {
		require_once('jpgraph_bar.php');

		$this->_renderCartesianPlotArea();

		for($i = 0; $i < $groupCount; ++$i) {
			$this->_renderPlotBar($i,$dimensions);
		}
	}	//	function _renderBarChart()


	private function _renderScatterChart($groupCount) {
		require_once('jpgraph_scatter.php');
		require_once('jpgraph_regstat.php');
		require_once('jpgraph_line.php');

		$this->_renderCartesianPlotArea('linlin');

		for($i = 0; $i < $groupCount; ++$i) {
			$this->_renderPlotScatter($i,false);
		}
	}	//	function _renderScatterChart()


	private function _renderBubbleChart($groupCount) {
		require_once('jpgraph_scatter.php');

		$this->_renderCartesianPlotArea('linlin');

		for($i = 0; $i < $groupCount; ++$i) {
			$this->_renderPlotScatter($i,true);
		}
	}	//	function _renderBubbleChart()


	private function _renderPieChart($groupCount, $dimensions = '2d', $doughnut = False, $multiplePlots = False) {
		require_once('jpgraph_pie.php');
		if ($dimensions == '3d') {
			require_once('jpgraph_pie3d.php');
		}

		$this->_renderPiePlotArea($doughnut);

		$iLimit = ($multiplePlots) ? $groupCount : 1;
		for($groupID = 0; $groupID < $iLimit; ++$groupID) {
			$grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
			$exploded = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();
			if ($groupID == 0) {
		        $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
				if ($labelCount > 0) {
					$datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
					$datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount);
				}
			}

			$seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
			$seriesPlots = array();
			//	For pie charts, we only display the first series: doughnut charts generally display all series
			$jLimit = ($multiplePlots) ? $seriesCount : 1;
			//	Loop through each data series in turn
			for($j = 0; $j < $jLimit; ++$j) {
				$dataValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($j)->getDataValues();

				//	Fill in any missing values in the $dataValues array
				$testCurrentIndex = 0;
				foreach($dataValues as $k => $dataValue) {
					while($k != $testCurrentIndex) {
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

				$seriesPlot->SetColor(self::$_colourSet[self::$_plotColour++]);
				if (count($datasetLabels) > 0)
					$seriesPlot->SetLabels(array_fill(0,count($datasetLabels),''));
				if ($dimensions != '3d') {
					$seriesPlot->SetGuideLines(false);
				}
				if ($j == 0) {
					if ($exploded) {
						$seriesPlot->ExplodeAll();
					}
					$seriesPlot->SetLegends($datasetLabels);
				}

				$this->_graph->Add($seriesPlot);
			}
		}
	}	//	function _renderPieChart()


	private function _renderRadarChart($groupCount) {
		require_once('jpgraph_radar.php');

		$this->_renderRadarPlotArea();

		for($groupID = 0; $groupID < $groupCount; ++$groupID) {
			$this->_renderPlotRadar($groupID);
		}
	}	//	function _renderRadarChart()


	private function _renderStockChart($groupCount) {
		require_once('jpgraph_stock.php');

		$this->_renderCartesianPlotArea('intint');

		for($groupID = 0; $groupID < $groupCount; ++$groupID) {
			$this->_renderPlotStock($groupID);
		}
	}	//	function _renderStockChart()


	private function _renderContourChart($groupCount,$dimensions) {
		require_once('jpgraph_contour.php');

		$this->_renderCartesianPlotArea('intint');

		for($i = 0; $i < $groupCount; ++$i) {
			$this->_renderPlotContour($i);
		}
	}	//	function _renderContourChart()


	private function _renderCombinationChart($groupCount,$dimensions,$outputDestination) {
		require_once('jpgraph_line.php');
		require_once('jpgraph_bar.php');
		require_once('jpgraph_scatter.php');
		require_once('jpgraph_regstat.php');
		require_once('jpgraph_line.php');

		$this->_renderCartesianPlotArea();

		for($i = 0; $i < $groupCount; ++$i) {
			$dimensions = null;
			$chartType = $this->_chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
			switch ($chartType) {
				case 'area3DChart' :
					$dimensions = '3d';
				case 'areaChart' :
					$this->_renderPlotLine($i,True,True,$dimensions);
					break;
				case 'bar3DChart' :
					$dimensions = '3d';
				case 'barChart' :
					$this->_renderPlotBar($i,$dimensions);
					break;
				case 'line3DChart' :
					$dimensions = '3d';
				case 'lineChart' :
					$this->_renderPlotLine($i,False,True,$dimensions);
					break;
				case 'scatterChart' :
					$this->_renderPlotScatter($i,false);
					break;
				case 'bubbleChart' :
					$this->_renderPlotScatter($i,true);
					break;
				default	:
					$this->_graph = null;
					return false;
			}
		}

		$this->_renderLegend();

		$this->_graph->Stroke($outputDestination);
		return true;
	}	//	function _renderCombinationChart()


	public function render($outputDestination) {
        self::$_plotColour = 0;

		$groupCount = $this->_chart->getPlotArea()->getPlotGroupCount();

		$dimensions = null;
		if ($groupCount == 1) {
			$chartType = $this->_chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotType();
		} else {
			$chartTypes = array();
			for($i = 0; $i < $groupCount; ++$i) {
				$chartTypes[] = $this->_chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
			}
			$chartTypes = array_unique($chartTypes);
			if (count($chartTypes) == 1) {
				$chartType = array_pop($chartTypes);
			} elseif (count($chartTypes) == 0) {
				echo 'Chart is not yet implemented<br />';
				return false;
			} else {
				return $this->_renderCombinationChart($groupCount,$dimensions,$outputDestination);
			}
		}

		switch ($chartType) {
			case 'area3DChart' :
				$dimensions = '3d';
			case 'areaChart' :
				$this->_renderAreaChart($groupCount,$dimensions);
				break;
			case 'bar3DChart' :
				$dimensions = '3d';
			case 'barChart' :
				$this->_renderBarChart($groupCount,$dimensions);
				break;
			case 'line3DChart' :
				$dimensions = '3d';
			case 'lineChart' :
				$this->_renderLineChart($groupCount,$dimensions);
				break;
			case 'pie3DChart' :
				$dimensions = '3d';
			case 'pieChart' :
				$this->_renderPieChart($groupCount,$dimensions,False,False);
				break;
			case 'doughnut3DChart' :
				$dimensions = '3d';
			case 'doughnutChart' :
				$this->_renderPieChart($groupCount,$dimensions,True,True);
				break;
			case 'scatterChart' :
				$this->_renderScatterChart($groupCount);
				break;
			case 'bubbleChart' :
				$this->_renderBubbleChart($groupCount);
				break;
			case 'radarChart' :
				$this->_renderRadarChart($groupCount);
				break;
			case 'surface3DChart' :
				$dimensions = '3d';
			case 'surfaceChart' :
				$this->_renderContourChart($groupCount,$dimensions);
				break;
			case 'stockChart' :
				$this->_renderStockChart($groupCount,$dimensions);
				break;
			default	:
				echo $chartType.' is not yet implemented<br />';
				return false;
		}
		$this->_renderLegend();

		$this->_graph->Stroke($outputDestination);
		return true;
	}	//	function render()


	/**
	 * Create a new PHPExcel_Chart_Renderer_jpgraph
	 */
	public function __construct(PHPExcel_Chart $chart)
	{
		$this->_graph	= null;
		$this->_chart	= $chart;
	}	//	function __construct()

}	//	PHPExcel_Chart_Renderer_jpgraph
