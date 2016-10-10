<?php
/**
 *	@package JAMA
 *
 *	Cholesky decomposition class
 *
 *	For a symmetric, positive definite matrix A, the Cholesky decomposition
 *	is an lower triangular matrix L so that A = L*L'.
 *
 *	If the matrix is not symmetric or positive definite, the constructor
 *	returns a partial decomposition and sets an internal flag that may
 *	be queried by the isSPD() method.
 *
 *	@author Paul Meagher
 *	@author Michael Bommarito
 *	@version 1.2
 */
class CholeskyDecomposition {

	/**
	 *	Decomposition storage
	 *	@var array
	 *	@access private
	 */
	private $L = array();

	/**
	 *	Matrix row and column dimension
	 *	@var int
	 *	@access private
	 */
	private $m;

	/**
	 *	Symmetric positive definite flag
	 *	@var boolean
	 *	@access private
	 */
	private $isspd = true;


	/**
	 *	CholeskyDecomposition
	 *
	 *	Class constructor - decomposes symmetric positive definite matrix
	 *	@param mixed Matrix square symmetric positive definite matrix
	 */
	public function __construct($A = null) {
		if ($A instanceof Matrix) {
			$this->L = $A->getArray();
			$this->m = $A->getRowDimension();

			for($i = 0; $i < $this->m; ++$i) {
				for($j = $i; $j < $this->m; ++$j) {
					for($sum = $this->L[$i][$j], $k = $i - 1; $k >= 0; --$k) {
						$sum -= $this->L[$i][$k] * $this->L[$j][$k];
					}
					if ($i == $j) {
						if ($sum >= 0) {
							$this->L[$i][$i] = sqrt($sum);
						} else {
							$this->isspd = false;
						}
					} else {
						if ($this->L[$i][$i] != 0) {
							$this->L[$j][$i] = $sum / $this->L[$i][$i];
						}
					}
				}

				for ($k = $i+1; $k < $this->m; ++$k) {
					$this->L[$i][$k] = 0.0;
				}
			}
		} else {
			throw new PHPExcel_Calculation_Exception(JAMAError(ArgumentTypeException));
		}
	}	//	function __construct()


	/**
	 *	Is the matrix symmetric and positive definite?
	 *
	 *	@return boolean
	 */
	public function isSPD() {
		return $this->isspd;
	}	//	function isSPD()


	/**
	 *	getL
	 *
	 *	Return triangular factor.
	 *	@return Matrix Lower triangular matrix
	 */
	public function getL() {
		return new Matrix($this->L);
	}	//	function getL()


	/**
	 *	Solve A*X = B
	 *
	 *	@param $B Row-equal matrix
	 *	@return Matrix L * L' * X = B
	 */
	public function solve($B = null) {
		if ($B instanceof Matrix) {
			if ($B->getRowDimension() == $this->m) {
				if ($this->isspd) {
					$X  = $B->getArrayCopy();
					$nx = $B->getColumnDimension();

					for ($k = 0; $k < $this->m; ++$k) {
						for ($i = $k + 1; $i < $this->m; ++$i) {
							for ($j = 0; $j < $nx; ++$j) {
								$X[$i][$j] -= $X[$k][$j] * $this->L[$i][$k];
							}
						}
						for ($j = 0; $j < $nx; ++$j) {
							$X[$k][$j] /= $this->L[$k][$k];
						}
					}

					for ($k = $this->m - 1; $k >= 0; --$k) {
						for ($j = 0; $j < $nx; ++$j) {
							$X[$k][$j] /= $this->L[$k][$k];
						}
						for ($i = 0; $i < $k; ++$i) {
							for ($j = 0; $j < $nx; ++$j) {
								$X[$i][$j] -= $X[$k][$j] * $this->L[$k][$i];
							}
						}
					}

					return new Matrix($X, $this->m, $nx);
				} else {
					throw new PHPExcel_Calculation_Exception(JAMAError(MatrixSPDException));
				}
			} else {
				throw new PHPExcel_Calculation_Exception(JAMAError(MatrixDimensionException));
			}
		} else {
			throw new PHPExcel_Calculation_Exception(JAMAError(ArgumentTypeException));
		}
	}	//	function solve()

}	//	class CholeskyDecomposition
