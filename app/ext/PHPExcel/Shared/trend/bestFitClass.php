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
 * @package    PHPExcel_Shared_Trend
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Best_Fit
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_Trend
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Best_Fit
{
	/**
	 * Indicator flag for a calculation error
	 *
	 * @var	boolean
	 **/
	protected $_error				= False;

	/**
	 * Algorithm type to use for best-fit
	 *
	 * @var	string
	 **/
	protected $_bestFitType			= 'undetermined';

	/**
	 * Number of entries in the sets of x- and y-value arrays
	 *
	 * @var	int
	 **/
	protected $_valueCount			= 0;

	/**
	 * X-value dataseries of values
	 *
	 * @var	float[]
	 **/
	protected $_xValues				= array();

	/**
	 * Y-value dataseries of values
	 *
	 * @var	float[]
	 **/
	protected $_yValues				= array();

	/**
	 * Flag indicating whether values should be adjusted to Y=0
	 *
	 * @var	boolean
	 **/
	protected $_adjustToZero		= False;

	/**
	 * Y-value series of best-fit values
	 *
	 * @var	float[]
	 **/
	protected $_yBestFitValues		= array();

	protected $_goodnessOfFit 		= 1;

	protected $_stdevOfResiduals	= 0;

	protected $_covariance			= 0;

	protected $_correlation			= 0;

	protected $_SSRegression		= 0;

	protected $_SSResiduals			= 0;

	protected $_DFResiduals			= 0;

	protected $_F					= 0;

	protected $_slope				= 0;

	protected $_slopeSE				= 0;

	protected $_intersect			= 0;

	protected $_intersectSE			= 0;

	protected $_Xoffset				= 0;

	protected $_Yoffset				= 0;


	public function getError() {
		return $this->_error;
	}	//	function getBestFitType()


	public function getBestFitType() {
		return $this->_bestFitType;
	}	//	function getBestFitType()


	/**
	 * Return the Y-Value for a specified value of X
	 *
	 * @param	 float		$xValue			X-Value
	 * @return	 float						Y-Value
	 */
	public function getValueOfYForX($xValue) {
		return False;
	}	//	function getValueOfYForX()


	/**
	 * Return the X-Value for a specified value of Y
	 *
	 * @param	 float		$yValue			Y-Value
	 * @return	 float						X-Value
	 */
	public function getValueOfXForY($yValue) {
		return False;
	}	//	function getValueOfXForY()


	/**
	 * Return the original set of X-Values
	 *
	 * @return	 float[]				X-Values
	 */
	public function getXValues() {
		return $this->_xValues;
	}	//	function getValueOfXForY()


	/**
	 * Return the Equation of the best-fit line
	 *
	 * @param	 int		$dp		Number of places of decimal precision to display
	 * @return	 string
	 */
	public function getEquation($dp=0) {
		return False;
	}	//	function getEquation()


	/**
	 * Return the Slope of the line
	 *
	 * @param	 int		$dp		Number of places of decimal precision to display
	 * @return	 string
	 */
	public function getSlope($dp=0) {
		if ($dp != 0) {
			return round($this->_slope,$dp);
		}
		return $this->_slope;
	}	//	function getSlope()


	/**
	 * Return the standard error of the Slope
	 *
	 * @param	 int		$dp		Number of places of decimal precision to display
	 * @return	 string
	 */
	public function getSlopeSE($dp=0) {
		if ($dp != 0) {
			return round($this->_slopeSE,$dp);
		}
		return $this->_slopeSE;
	}	//	function getSlopeSE()


	/**
	 * Return the Value of X where it intersects Y = 0
	 *
	 * @param	 int		$dp		Number of places of decimal precision to display
	 * @return	 string
	 */
	public function getIntersect($dp=0) {
		if ($dp != 0) {
			return round($this->_intersect,$dp);
		}
		return $this->_intersect;
	}	//	function getIntersect()


	/**
	 * Return the standard error of the Intersect
	 *
	 * @param	 int		$dp		Number of places of decimal precision to display
	 * @return	 string
	 */
	public function getIntersectSE($dp=0) {
		if ($dp != 0) {
			return round($this->_intersectSE,$dp);
		}
		return $this->_intersectSE;
	}	//	function getIntersectSE()


	/**
	 * Return the goodness of fit for this regression
	 *
	 * @param	 int		$dp		Number of places of decimal precision to return
	 * @return	 float
	 */
	public function getGoodnessOfFit($dp=0) {
		if ($dp != 0) {
			return round($this->_goodnessOfFit,$dp);
		}
		return $this->_goodnessOfFit;
	}	//	function getGoodnessOfFit()


	public function getGoodnessOfFitPercent($dp=0) {
		if ($dp != 0) {
			return round($this->_goodnessOfFit * 100,$dp);
		}
		return $this->_goodnessOfFit * 100;
	}	//	function getGoodnessOfFitPercent()


	/**
	 * Return the standard deviation of the residuals for this regression
	 *
	 * @param	 int		$dp		Number of places of decimal precision to return
	 * @return	 float
	 */
	public function getStdevOfResiduals($dp=0) {
		if ($dp != 0) {
			return round($this->_stdevOfResiduals,$dp);
		}
		return $this->_stdevOfResiduals;
	}	//	function getStdevOfResiduals()


	public function getSSRegression($dp=0) {
		if ($dp != 0) {
			return round($this->_SSRegression,$dp);
		}
		return $this->_SSRegression;
	}	//	function getSSRegression()


	public function getSSResiduals($dp=0) {
		if ($dp != 0) {
			return round($this->_SSResiduals,$dp);
		}
		return $this->_SSResiduals;
	}	//	function getSSResiduals()


	public function getDFResiduals($dp=0) {
		if ($dp != 0) {
			return round($this->_DFResiduals,$dp);
		}
		return $this->_DFResiduals;
	}	//	function getDFResiduals()


	public function getF($dp=0) {
		if ($dp != 0) {
			return round($this->_F,$dp);
		}
		return $this->_F;
	}	//	function getF()


	public function getCovariance($dp=0) {
		if ($dp != 0) {
			return round($this->_covariance,$dp);
		}
		return $this->_covariance;
	}	//	function getCovariance()


	public function getCorrelation($dp=0) {
		if ($dp != 0) {
			return round($this->_correlation,$dp);
		}
		return $this->_correlation;
	}	//	function getCorrelation()


	public function getYBestFitValues() {
		return $this->_yBestFitValues;
	}	//	function getYBestFitValues()


	protected function _calculateGoodnessOfFit($sumX,$sumY,$sumX2,$sumY2,$sumXY,$meanX,$meanY, $const) {
		$SSres = $SScov = $SScor = $SStot = $SSsex = 0.0;
		foreach($this->_xValues as $xKey => $xValue) {
			$bestFitY = $this->_yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);

			$SSres += ($this->_yValues[$xKey] - $bestFitY) * ($this->_yValues[$xKey] - $bestFitY);
			if ($const) {
				$SStot += ($this->_yValues[$xKey] - $meanY) * ($this->_yValues[$xKey] - $meanY);
			} else {
				$SStot += $this->_yValues[$xKey] * $this->_yValues[$xKey];
			}
			$SScov += ($this->_xValues[$xKey] - $meanX) * ($this->_yValues[$xKey] - $meanY);
			if ($const) {
				$SSsex += ($this->_xValues[$xKey] - $meanX) * ($this->_xValues[$xKey] - $meanX);
			} else {
				$SSsex += $this->_xValues[$xKey] * $this->_xValues[$xKey];
			}
		}

		$this->_SSResiduals = $SSres;
		$this->_DFResiduals = $this->_valueCount - 1 - $const;

		if ($this->_DFResiduals == 0.0) {
			$this->_stdevOfResiduals = 0.0;
		} else {
			$this->_stdevOfResiduals = sqrt($SSres / $this->_DFResiduals);
		}
		if (($SStot == 0.0) || ($SSres == $SStot)) {
			$this->_goodnessOfFit = 1;
		} else {
			$this->_goodnessOfFit = 1 - ($SSres / $SStot);
		}

		$this->_SSRegression = $this->_goodnessOfFit * $SStot;
		$this->_covariance = $SScov / $this->_valueCount;
		$this->_correlation = ($this->_valueCount * $sumXY - $sumX * $sumY) / sqrt(($this->_valueCount * $sumX2 - pow($sumX,2)) * ($this->_valueCount * $sumY2 - pow($sumY,2)));
		$this->_slopeSE = $this->_stdevOfResiduals / sqrt($SSsex);
		$this->_intersectSE = $this->_stdevOfResiduals * sqrt(1 / ($this->_valueCount - ($sumX * $sumX) / $sumX2));
		if ($this->_SSResiduals != 0.0) {
			if ($this->_DFResiduals == 0.0) {
				$this->_F = 0.0;
			} else {
				$this->_F = $this->_SSRegression / ($this->_SSResiduals / $this->_DFResiduals);
			}
		} else {
			if ($this->_DFResiduals == 0.0) {
				$this->_F = 0.0;
			} else {
				$this->_F = $this->_SSRegression / $this->_DFResiduals;
			}
		}
	}	//	function _calculateGoodnessOfFit()


	protected function _leastSquareFit($yValues, $xValues, $const) {
		// calculate sums
		$x_sum = array_sum($xValues);
		$y_sum = array_sum($yValues);
		$meanX = $x_sum / $this->_valueCount;
		$meanY = $y_sum / $this->_valueCount;
		$mBase = $mDivisor = $xx_sum = $xy_sum = $yy_sum = 0.0;
		for($i = 0; $i < $this->_valueCount; ++$i) {
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
//		$this->_slope = (($this->_valueCount * $xy_sum) - ($x_sum * $y_sum)) / (($this->_valueCount * $xx_sum) - ($x_sum * $x_sum));
		$this->_slope = $mBase / $mDivisor;

		// calculate intersect
//		$this->_intersect = ($y_sum - ($this->_slope * $x_sum)) / $this->_valueCount;
		if ($const) {
			$this->_intersect = $meanY - ($this->_slope * $meanX);
		} else {
			$this->_intersect = 0;
		}

		$this->_calculateGoodnessOfFit($x_sum,$y_sum,$xx_sum,$yy_sum,$xy_sum,$meanX,$meanY,$const);
	}	//	function _leastSquareFit()


	/**
	 * Define the regression
	 *
	 * @param	float[]		$yValues	The set of Y-values for this regression
	 * @param	float[]		$xValues	The set of X-values for this regression
	 * @param	boolean		$const
	 */
	function __construct($yValues, $xValues=array(), $const=True) {
		//	Calculate number of points
		$nY = count($yValues);
		$nX = count($xValues);

		//	Define X Values if necessary
		if ($nX == 0) {
			$xValues = range(1,$nY);
			$nX = $nY;
		} elseif ($nY != $nX) {
			//	Ensure both arrays of points are the same size
			$this->_error = True;
			return False;
		}

		$this->_valueCount = $nY;
		$this->_xValues = $xValues;
		$this->_yValues = $yValues;
	}	//	function __construct()

}	//	class bestFit
