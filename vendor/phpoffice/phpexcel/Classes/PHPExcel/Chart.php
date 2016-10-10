<?php

/**
 * PHPExcel_Chart
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
 * @package        PHPExcel_Chart
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license        http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version        ##VERSION##, ##DATE##
 */
class PHPExcel_Chart
{
    /**
     * Chart Name
     *
     * @var string
     */
    private $name = '';

    /**
     * Worksheet
     *
     * @var PHPExcel_Worksheet
     */
    private $worksheet;

    /**
     * Chart Title
     *
     * @var PHPExcel_Chart_Title
     */
    private $title;

    /**
     * Chart Legend
     *
     * @var PHPExcel_Chart_Legend
     */
    private $legend;

    /**
     * X-Axis Label
     *
     * @var PHPExcel_Chart_Title
     */
    private $xAxisLabel;

    /**
     * Y-Axis Label
     *
     * @var PHPExcel_Chart_Title
     */
    private $yAxisLabel;

    /**
     * Chart Plot Area
     *
     * @var PHPExcel_Chart_PlotArea
     */
    private $plotArea;

    /**
     * Plot Visible Only
     *
     * @var boolean
     */
    private $plotVisibleOnly = true;

    /**
     * Display Blanks as
     *
     * @var string
     */
    private $displayBlanksAs = '0';

    /**
     * Chart Asix Y as
     *
     * @var PHPExcel_Chart_Axis
     */
    private $yAxis;

    /**
     * Chart Asix X as
     *
     * @var PHPExcel_Chart_Axis
     */
    private $xAxis;

    /**
     * Chart Major Gridlines as
     *
     * @var PHPExcel_Chart_GridLines
     */
    private $majorGridlines;

    /**
     * Chart Minor Gridlines as
     *
     * @var PHPExcel_Chart_GridLines
     */
    private $minorGridlines;

    /**
     * Top-Left Cell Position
     *
     * @var string
     */
    private $topLeftCellRef = 'A1';


    /**
     * Top-Left X-Offset
     *
     * @var integer
     */
    private $topLeftXOffset = 0;


    /**
     * Top-Left Y-Offset
     *
     * @var integer
     */
    private $topLeftYOffset = 0;


    /**
     * Bottom-Right Cell Position
     *
     * @var string
     */
    private $bottomRightCellRef = 'A1';


    /**
     * Bottom-Right X-Offset
     *
     * @var integer
     */
    private $bottomRightXOffset = 10;


    /**
     * Bottom-Right Y-Offset
     *
     * @var integer
     */
    private $bottomRightYOffset = 10;


    /**
     * Create a new PHPExcel_Chart
     */
    public function __construct($name, PHPExcel_Chart_Title $title = null, PHPExcel_Chart_Legend $legend = null, PHPExcel_Chart_PlotArea $plotArea = null, $plotVisibleOnly = true, $displayBlanksAs = '0', PHPExcel_Chart_Title $xAxisLabel = null, PHPExcel_Chart_Title $yAxisLabel = null, PHPExcel_Chart_Axis $xAxis = null, PHPExcel_Chart_Axis $yAxis = null, PHPExcel_Chart_GridLines $majorGridlines = null, PHPExcel_Chart_GridLines $minorGridlines = null)
    {
        $this->name = $name;
        $this->title = $title;
        $this->legend = $legend;
        $this->xAxisLabel = $xAxisLabel;
        $this->yAxisLabel = $yAxisLabel;
        $this->plotArea = $plotArea;
        $this->plotVisibleOnly = $plotVisibleOnly;
        $this->displayBlanksAs = $displayBlanksAs;
        $this->xAxis = $xAxis;
        $this->yAxis = $yAxis;
        $this->majorGridlines = $majorGridlines;
        $this->minorGridlines = $minorGridlines;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Worksheet
     *
     * @return PHPExcel_Worksheet
     */
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    /**
     * Set Worksheet
     *
     * @param    PHPExcel_Worksheet    $pValue
     * @throws    PHPExcel_Chart_Exception
     * @return PHPExcel_Chart
     */
    public function setWorksheet(PHPExcel_Worksheet $pValue = null)
    {
        $this->worksheet = $pValue;

        return $this;
    }

    /**
     * Get Title
     *
     * @return PHPExcel_Chart_Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title
     *
     * @param    PHPExcel_Chart_Title $title
     * @return    PHPExcel_Chart
     */
    public function setTitle(PHPExcel_Chart_Title $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Legend
     *
     * @return PHPExcel_Chart_Legend
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set Legend
     *
     * @param    PHPExcel_Chart_Legend $legend
     * @return    PHPExcel_Chart
     */
    public function setLegend(PHPExcel_Chart_Legend $legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get X-Axis Label
     *
     * @return PHPExcel_Chart_Title
     */
    public function getXAxisLabel()
    {
        return $this->xAxisLabel;
    }

    /**
     * Set X-Axis Label
     *
     * @param    PHPExcel_Chart_Title $label
     * @return    PHPExcel_Chart
     */
    public function setXAxisLabel(PHPExcel_Chart_Title $label)
    {
        $this->xAxisLabel = $label;

        return $this;
    }

    /**
     * Get Y-Axis Label
     *
     * @return PHPExcel_Chart_Title
     */
    public function getYAxisLabel()
    {
        return $this->yAxisLabel;
    }

    /**
     * Set Y-Axis Label
     *
     * @param    PHPExcel_Chart_Title $label
     * @return    PHPExcel_Chart
     */
    public function setYAxisLabel(PHPExcel_Chart_Title $label)
    {
        $this->yAxisLabel = $label;

        return $this;
    }

    /**
     * Get Plot Area
     *
     * @return PHPExcel_Chart_PlotArea
     */
    public function getPlotArea()
    {
        return $this->plotArea;
    }

    /**
     * Get Plot Visible Only
     *
     * @return boolean
     */
    public function getPlotVisibleOnly()
    {
        return $this->plotVisibleOnly;
    }

    /**
     * Set Plot Visible Only
     *
     * @param boolean $plotVisibleOnly
     * @return PHPExcel_Chart
     */
    public function setPlotVisibleOnly($plotVisibleOnly = true)
    {
        $this->plotVisibleOnly = $plotVisibleOnly;

        return $this;
    }

    /**
     * Get Display Blanks as
     *
     * @return string
     */
    public function getDisplayBlanksAs()
    {
        return $this->displayBlanksAs;
    }

    /**
     * Set Display Blanks as
     *
     * @param string $displayBlanksAs
     * @return PHPExcel_Chart
     */
    public function setDisplayBlanksAs($displayBlanksAs = '0')
    {
        $this->displayBlanksAs = $displayBlanksAs;
    }


    /**
     * Get yAxis
     *
     * @return PHPExcel_Chart_Axis
     */
    public function getChartAxisY()
    {
        if ($this->yAxis !== null) {
            return $this->yAxis;
        }

        return new PHPExcel_Chart_Axis();
    }

    /**
     * Get xAxis
     *
     * @return PHPExcel_Chart_Axis
     */
    public function getChartAxisX()
    {
        if ($this->xAxis !== null) {
            return $this->xAxis;
        }

        return new PHPExcel_Chart_Axis();
    }

    /**
     * Get Major Gridlines
     *
     * @return PHPExcel_Chart_GridLines
     */
    public function getMajorGridlines()
    {
        if ($this->majorGridlines !== null) {
            return $this->majorGridlines;
        }

        return new PHPExcel_Chart_GridLines();
    }

    /**
     * Get Minor Gridlines
     *
     * @return PHPExcel_Chart_GridLines
     */
    public function getMinorGridlines()
    {
        if ($this->minorGridlines !== null) {
            return $this->minorGridlines;
        }

        return new PHPExcel_Chart_GridLines();
    }


    /**
     * Set the Top Left position for the chart
     *
     * @param    string    $cell
     * @param    integer    $xOffset
     * @param    integer    $yOffset
     * @return PHPExcel_Chart
     */
    public function setTopLeftPosition($cell, $xOffset = null, $yOffset = null)
    {
        $this->topLeftCellRef = $cell;
        if (!is_null($xOffset)) {
            $this->setTopLeftXOffset($xOffset);
        }
        if (!is_null($yOffset)) {
            $this->setTopLeftYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the top left position of the chart
     *
     * @return array    an associative array containing the cell address, X-Offset and Y-Offset from the top left of that cell
     */
    public function getTopLeftPosition()
    {
        return array(
            'cell'    => $this->topLeftCellRef,
            'xOffset' => $this->topLeftXOffset,
            'yOffset' => $this->topLeftYOffset
        );
    }

    /**
     * Get the cell address where the top left of the chart is fixed
     *
     * @return string
     */
    public function getTopLeftCell()
    {
        return $this->topLeftCellRef;
    }

    /**
     * Set the Top Left cell position for the chart
     *
     * @param    string    $cell
     * @return PHPExcel_Chart
     */
    public function setTopLeftCell($cell)
    {
        $this->topLeftCellRef = $cell;

        return $this;
    }

    /**
     * Set the offset position within the Top Left cell for the chart
     *
     * @param    integer    $xOffset
     * @param    integer    $yOffset
     * @return PHPExcel_Chart
     */
    public function setTopLeftOffset($xOffset = null, $yOffset = null)
    {
        if (!is_null($xOffset)) {
            $this->setTopLeftXOffset($xOffset);
        }
        if (!is_null($yOffset)) {
            $this->setTopLeftYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the offset position within the Top Left cell for the chart
     *
     * @return integer[]
     */
    public function getTopLeftOffset()
    {
        return array(
            'X' => $this->topLeftXOffset,
            'Y' => $this->topLeftYOffset
        );
    }

    public function setTopLeftXOffset($xOffset)
    {
        $this->topLeftXOffset = $xOffset;

        return $this;
    }

    public function getTopLeftXOffset()
    {
        return $this->topLeftXOffset;
    }

    public function setTopLeftYOffset($yOffset)
    {
        $this->topLeftYOffset = $yOffset;

        return $this;
    }

    public function getTopLeftYOffset()
    {
        return $this->topLeftYOffset;
    }

    /**
     * Set the Bottom Right position of the chart
     *
     * @param    string    $cell
     * @param    integer    $xOffset
     * @param    integer    $yOffset
     * @return PHPExcel_Chart
     */
    public function setBottomRightPosition($cell, $xOffset = null, $yOffset = null)
    {
        $this->bottomRightCellRef = $cell;
        if (!is_null($xOffset)) {
            $this->setBottomRightXOffset($xOffset);
        }
        if (!is_null($yOffset)) {
            $this->setBottomRightYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the bottom right position of the chart
     *
     * @return array    an associative array containing the cell address, X-Offset and Y-Offset from the top left of that cell
     */
    public function getBottomRightPosition()
    {
        return array(
            'cell'    => $this->bottomRightCellRef,
            'xOffset' => $this->bottomRightXOffset,
            'yOffset' => $this->bottomRightYOffset
        );
    }

    public function setBottomRightCell($cell)
    {
        $this->bottomRightCellRef = $cell;

        return $this;
    }

    /**
     * Get the cell address where the bottom right of the chart is fixed
     *
     * @return string
     */
    public function getBottomRightCell()
    {
        return $this->bottomRightCellRef;
    }

    /**
     * Set the offset position within the Bottom Right cell for the chart
     *
     * @param    integer    $xOffset
     * @param    integer    $yOffset
     * @return PHPExcel_Chart
     */
    public function setBottomRightOffset($xOffset = null, $yOffset = null)
    {
        if (!is_null($xOffset)) {
            $this->setBottomRightXOffset($xOffset);
        }
        if (!is_null($yOffset)) {
            $this->setBottomRightYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the offset position within the Bottom Right cell for the chart
     *
     * @return integer[]
     */
    public function getBottomRightOffset()
    {
        return array(
            'X' => $this->bottomRightXOffset,
            'Y' => $this->bottomRightYOffset
        );
    }

    public function setBottomRightXOffset($xOffset)
    {
        $this->bottomRightXOffset = $xOffset;

        return $this;
    }

    public function getBottomRightXOffset()
    {
        return $this->bottomRightXOffset;
    }

    public function setBottomRightYOffset($yOffset)
    {
        $this->bottomRightYOffset = $yOffset;

        return $this;
    }

    public function getBottomRightYOffset()
    {
        return $this->bottomRightYOffset;
    }


    public function refresh()
    {
        if ($this->worksheet !== null) {
            $this->plotArea->refresh($this->worksheet);
        }
    }

    public function render($outputDestination = null)
    {
        $libraryName = PHPExcel_Settings::getChartRendererName();
        if (is_null($libraryName)) {
            return false;
        }
        //    Ensure that data series values are up-to-date before we render
        $this->refresh();

        $libraryPath = PHPExcel_Settings::getChartRendererPath();
        $includePath = str_replace('\\', '/', get_include_path());
        $rendererPath = str_replace('\\', '/', $libraryPath);
        if (strpos($rendererPath, $includePath) === false) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $libraryPath);
        }

        $rendererName = 'PHPExcel_Chart_Renderer_'.$libraryName;
        $renderer = new $rendererName($this);

        if ($outputDestination == 'php://output') {
            $outputDestination = null;
        }
        return $renderer->render($outputDestination);
    }
}
