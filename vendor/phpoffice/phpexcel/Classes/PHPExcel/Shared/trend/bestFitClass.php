<?php

/**
 * PHPExcel_Best_Fit
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
 * @package    PHPExcel_Shared_Trend
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Best_Fit
{
    /**
     * Indicator flag for a calculation error
     *
     * @var    boolean
     **/
    protected $error = false;

    /**
     * Algorithm type to use for best-fit
     *
     * @var    string
     **/
    protected $bestFitType = 'undetermined';

    /**
     * Number of entries in the sets of x- and y-value arrays
     *
     * @var    int
     **/
    protected $valueCount = 0;

    /**
     * X-value dataseries of values
     *
     * @var    float[]
     **/
    protected $xValues = array();

    /**
     * Y-value dataseries of values
     *
     * @var    float[]
     **/
    protected $yValues = array();

    /**
     * Flag indicating whether values should be adjusted to Y=0
     *
     * @var    boolean
     **/
    protected $adjustToZero = false;

    /**
     * Y-value series of best-fit values
     *
     * @var    float[]
     **/
    protected $yBestFitValues = array();

    protected $goodnessOfFit = 1;

    protected $stdevOfResiduals = 0;

    protected $covariance = 0;

    protected $correlation = 0;

    protected $SSRegression = 0;

    protected $SSResiduals = 0;

    protected $DFResiduals = 0;

    protected $f = 0;

    protected $slope = 0;

    protected $slopeSE = 0;

    protected $intersect = 0;

    protected $intersectSE = 0;

    protected $xOffset = 0;

    protected $yOffset = 0;


    public function getError()
    {
        return $this->error;
    }


    public function getBestFitType()
    {
        return $this->bestFitType;
    }

    /**
     * Return the Y-Value for a specified value of X
     *
     * @param     float        $xValue            X-Value
     * @return     float                        Y-Value
     */
    public function getValueOfYForX($xValue)
    {
        return false;
    }

    /**
     * Return the X-Value for a specified value of Y
     *
     * @param     float        $yValue            Y-Value
     * @return     float                        X-Value
     */
    public function getValueOfXForY($yValue)
    {
        return false;
    }

    /**
     * Return the original set of X-Values
     *
     * @return     float[]                X-Values
     */
    public function getXValues()
    {
        return $this->xValues;
    }

    /**
     * Return the Equation of the best-fit line
     *
     * @param     int        $dp        Number of places of decimal precision to display
     * @return     string
     */
    public function getEquation($dp = 0)
    {
        return false;
    }

    /**
     * Return the Slope of the line
     *
     * @param     int        $dp        Number of places of decimal precision to display
     * @return     string
     */
    public function getSlope($dp = 0)
    {
        if ($dp != 0) {
            return round($this->slope, $dp);
        }
        return $this->slope;
    }

    /**
     * Return the standard error of the Slope
     *
     * @param     int        $dp        Number of places of decimal precision to display
     * @return     string
     */
    public function getSlopeSE($dp = 0)
    {
        if ($dp != 0) {
            return round($this->slopeSE, $dp);
        }
        return $this->slopeSE;
    }

    /**
     * Return the Value of X where it intersects Y = 0
     *
     * @param     int        $dp        Number of places of decimal precision to display
     * @return     string
     */
    public function getIntersect($dp = 0)
    {
        if ($dp != 0) {
            return round($this->intersect, $dp);
        }
        return $this->intersect;
    }

    /**
     * Return the standard error of the Intersect
     *
     * @param     int        $dp        Number of places of decimal precision to display
     * @return     string
     */
    public function getIntersectSE($dp = 0)
    {
        if ($dp != 0) {
            return round($this->intersectSE, $dp);
        }
        return $this->intersectSE;
    }

    /**
     * Return the goodness of fit for this regression
     *
     * @param     int        $dp        Number of places of decimal precision to return
     * @return     float
     */
    public function getGoodnessOfFit($dp = 0)
    {
        if ($dp != 0) {
            return round($this->goodnessOfFit, $dp);
        }
        return $this->goodnessOfFit;
    }

    public function getGoodnessOfFitPercent($dp = 0)
    {
        if ($dp != 0) {
            return round($this->goodnessOfFit * 100, $dp);
        }
        return $this->goodnessOfFit * 100;
    }

    /**
     * Return the standard deviation of the residuals for this regression
     *
     * @param     int        $dp        Number of places of decimal precision to return
     * @return     float
     */
    public function getStdevOfResiduals($dp = 0)
    {
        if ($dp != 0) {
            return round($this->stdevOfResiduals, $dp);
        }
        return $this->stdevOfResiduals;
    }

    public function getSSRegression($dp = 0)
    {
        if ($dp != 0) {
            return round($this->SSRegression, $dp);
        }
        return $this->SSRegression;
    }

    public function getSSResiduals($dp = 0)
    {
        if ($dp != 0) {
            return round($this->SSResiduals, $dp);
        }
        return $this->SSResiduals;
    }

    public function getDFResiduals($dp = 0)
    {
        if ($dp != 0) {
            return round($this->DFResiduals, $dp);
        }
        return $this->DFResiduals;
    }

    public function getF($dp = 0)
    {
        if ($dp != 0) {
            return round($this->f, $dp);
        }
        return $this->f;
    }

    public function getCovariance($dp = 0)
    {
        if ($dp != 0) {
            return round($this->covariance, $dp);
        }
        return $this->covariance;
    }

    public function getCorrelation($dp = 0)
    {
        if ($dp != 0) {
            return round($this->correlation, $dp);
        }
        return $this->correlation;
    }

    public function getYBestFitValues()
    {
        return $this->yBestFitValues;
    }

    protected function calculateGoodnessOfFit($sumX, $sumY, $sumX2, $sumY2, $sumXY, $meanX, $meanY, $const)
    {
        $SSres = $SScov = $SScor = $SStot = $SSsex = 0.0;
        foreach ($this->xValues as $xKey => $xValue) {
            $bestFitY = $this->yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);

            $SSres += ($this->yValues[$xKey] - $bestFitY) * ($this->yValues[$xKey] - $bestFitY);
            if ($const) {
                $SStot += ($this->yValues[$xKey] - $meanY) * ($this->yValues[$xKey] - $meanY);
            } else {
                $SStot += $this->yValues[$xKey] * $this->yValues[$xKey];
            }
            $SScov += ($this->xValues[$xKey] - $meanX) * ($this->yValues[$xKey] - $meanY);
            if ($const) {
                $SSsex += ($this->xValues[$xKey] - $meanX) * ($this->xValues[$xKey] - $meanX);
            } else {
                $SSsex += $this->xValues[$xKey] * $this->xValues[$xKey];
            }
        }

        $this->SSResiduals = $SSres;
        $this->DFResiduals = $this->valueCount - 1 - $const;

        if ($this->DFResiduals == 0.0) {
            $this->stdevOfResiduals = 0.0;
        } else {
            $this->stdevOfResiduals = sqrt($SSres / $this->DFResiduals);
        }
        if (($SStot == 0.0) || ($SSres == $SStot)) {
            $this->goodnessOfFit = 1;
        } else {
            $this->goodnessOfFit = 1 - ($SSres / $SStot);
        }

        $this->SSRegression = $this->goodnessOfFit * $SStot;
        $this->covariance = $SScov / $this->valueCount;
        $this->correlation = ($this->valueCount * $sumXY - $sumX * $sumY) / sqrt(($this->valueCount * $sumX2 - pow($sumX, 2)) * ($this->valueCount * $sumY2 - pow($sumY, 2)));
        $this->slopeSE = $this->stdevOfResiduals / sqrt($SSsex);
        $this->intersectSE = $this->stdevOfResiduals * sqrt(1 / ($this->valueCount - ($sumX * $sumX) / $sumX2));
        if ($this->SSResiduals != 0.0) {
            if ($this->DFResiduals == 0.0) {
                $this->f = 0.0;
            } else {
                $this->f = $this->SSRegression / ($this->SSResiduals / $this->DFResiduals);
            }
        } else {
            if ($this->DFResiduals == 0.0) {
                $this->f = 0.0;
            } else {
                $this->f = $this->SSRegression / $this->DFResiduals;
            }
        }
    }

    protected function leastSquareFit($yValues, $xValues, $const)
    {
        // calculate sums
        $x_sum = array_sum($xValues);
        $y_sum = array_sum($yValues);
        $meanX = $x_sum / $this->valueCount;
        $meanY = $y_sum / $this->valueCount;
        $mBase = $mDivisor = $xx_sum = $xy_sum = $yy_sum = 0.0;
        for ($i = 0; $i < $this->valueCount; ++$i) {
            $xy_sum += $xValues[$i] * $yValues[$i];
            $xx_sum += $xValues[$i] * $xValues[$i];
            $yy_sum += $yValues[$i] * $yValues[$i];

            if ($const) {
                $mBase += ($xValues[$i] - $meanX) * ($yValues[$i] - $meanY);
                $mDivisor += ($xValues[$i] - $meanX) * ($xValues[$i] - $meanX);
            } else {
                $mBase += $xValues[$i] * $yValues[$i];
                $mDivisor += $xValues[$i] * $xValues[$i];
            }
        }

        // calculate slope
//        $this->slope = (($this->valueCount * $xy_sum) - ($x_sum * $y_sum)) / (($this->valueCount * $xx_sum) - ($x_sum * $x_sum));
        $this->slope = $mBase / $mDivisor;

        // calculate intersect
//        $this->intersect = ($y_sum - ($this->slope * $x_sum)) / $this->valueCount;
        if ($const) {
            $this->intersect = $meanY - ($this->slope * $meanX);
        } else {
            $this->intersect = 0;
        }

        $this->calculateGoodnessOfFit($x_sum, $y_sum, $xx_sum, $yy_sum, $xy_sum, $meanX, $meanY, $const);
    }

    /**
     * Define the regression
     *
     * @param    float[]        $yValues    The set of Y-values for this regression
     * @param    float[]        $xValues    The set of X-values for this regression
     * @param    boolean        $const
     */
    public function __construct($yValues, $xValues = array(), $const = true)
    {
        //    Calculate number of points
        $nY = count($yValues);
        $nX = count($xValues);

        //    Define X Values if necessary
        if ($nX == 0) {
            $xValues = range(1, $nY);
            $nX = $nY;
        } elseif ($nY != $nX) {
            //    Ensure both arrays of points are the same size
            $this->error = true;
            return false;
        }

        $this->valueCount = $nY;
        $this->xValues = $xValues;
        $this->yValues = $yValues;
    }
}
