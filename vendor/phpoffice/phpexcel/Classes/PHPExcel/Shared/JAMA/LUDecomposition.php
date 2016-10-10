<?php
/**
 *    @package JAMA
 *
 *    For an m-by-n matrix A with m >= n, the LU decomposition is an m-by-n
 *    unit lower triangular matrix L, an n-by-n upper triangular matrix U,
 *    and a permutation vector piv of length m so that A(piv,:) = L*U.
 *    If m < n, then L is m-by-m and U is m-by-n.
 *
 *    The LU decompostion with pivoting always exists, even if the matrix is
 *    singular, so the constructor will never fail. The primary use of the
 *    LU decomposition is in the solution of square systems of simultaneous
 *    linear equations. This will fail if isNonsingular() returns false.
 *
 *    @author Paul Meagher
 *    @author Bartosz Matosiuk
 *    @author Michael Bommarito
 *    @version 1.1
 *    @license PHP v3.0
 */
class PHPExcel_Shared_JAMA_LUDecomposition
{
    const MATRIX_SINGULAR_EXCEPTION    = "Can only perform operation on singular matrix.";
    const MATRIX_SQUARE_EXCEPTION      = "Mismatched Row dimension";

    /**
     *    Decomposition storage
     *    @var array
     */
    private $LU = array();

    /**
     *    Row dimension.
     *    @var int
     */
    private $m;

    /**
     *    Column dimension.
     *    @var int
     */
    private $n;

    /**
     *    Pivot sign.
     *    @var int
     */
    private $pivsign;

    /**
     *    Internal storage of pivot vector.
     *    @var array
     */
    private $piv = array();

    /**
     *    LU Decomposition constructor.
     *
     *    @param $A Rectangular matrix
     *    @return Structure to access L, U and piv.
     */
    public function __construct($A)
    {
        if ($A instanceof PHPExcel_Shared_JAMA_Matrix) {
            // Use a "left-looking", dot-product, Crout/Doolittle algorithm.
            $this->LU = $A->getArray();
            $this->m  = $A->getRowDimension();
            $this->n  = $A->getColumnDimension();
            for ($i = 0; $i < $this->m; ++$i) {
                $this->piv[$i] = $i;
            }
            $this->pivsign = 1;
            $LUrowi = $LUcolj = array();

            // Outer loop.
            for ($j = 0; $j < $this->n; ++$j) {
                // Make a copy of the j-th column to localize references.
                for ($i = 0; $i < $this->m; ++$i) {
                    $LUcolj[$i] = &$this->LU[$i][$j];
                }
                // Apply previous transformations.
                for ($i = 0; $i < $this->m; ++$i) {
                    $LUrowi = $this->LU[$i];
                    // Most of the time is spent in the following dot product.
                    $kmax = min($i, $j);
                    $s = 0.0;
                    for ($k = 0; $k < $kmax; ++$k) {
                        $s += $LUrowi[$k] * $LUcolj[$k];
                    }
                    $LUrowi[$j] = $LUcolj[$i] -= $s;
                }
                // Find pivot and exchange if necessary.
                $p = $j;
                for ($i = $j+1; $i < $this->m; ++$i) {
                    if (abs($LUcolj[$i]) > abs($LUcolj[$p])) {
                        $p = $i;
                    }
                }
                if ($p != $j) {
                    for ($k = 0; $k < $this->n; ++$k) {
                        $t = $this->LU[$p][$k];
                        $this->LU[$p][$k] = $this->LU[$j][$k];
                        $this->LU[$j][$k] = $t;
                    }
                    $k = $this->piv[$p];
                    $this->piv[$p] = $this->piv[$j];
                    $this->piv[$j] = $k;
                    $this->pivsign = $this->pivsign * -1;
                }
                // Compute multipliers.
                if (($j < $this->m) && ($this->LU[$j][$j] != 0.0)) {
                    for ($i = $j+1; $i < $this->m; ++$i) {
                        $this->LU[$i][$j] /= $this->LU[$j][$j];
                    }
                }
            }
        } else {
            throw new PHPExcel_Calculation_Exception(PHPExcel_Shared_JAMA_Matrix::ARGUMENT_TYPE_EXCEPTION);
        }
    }    //    function __construct()

    /**
     *    Get lower triangular factor.
     *
     *    @return array Lower triangular factor
     */
    public function getL()
    {
        for ($i = 0; $i < $this->m; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i > $j) {
                    $L[$i][$j] = $this->LU[$i][$j];
                } elseif ($i == $j) {
                    $L[$i][$j] = 1.0;
                } else {
                    $L[$i][$j] = 0.0;
                }
            }
        }
        return new PHPExcel_Shared_JAMA_Matrix($L);
    }    //    function getL()

    /**
     *    Get upper triangular factor.
     *
     *    @return array Upper triangular factor
     */
    public function getU()
    {
        for ($i = 0; $i < $this->n; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i <= $j) {
                    $U[$i][$j] = $this->LU[$i][$j];
                } else {
                    $U[$i][$j] = 0.0;
                }
            }
        }
        return new PHPExcel_Shared_JAMA_Matrix($U);
    }    //    function getU()

    /**
     *    Return pivot permutation vector.
     *
     *    @return array Pivot vector
     */
    public function getPivot()
    {
        return $this->piv;
    }    //    function getPivot()

    /**
     *    Alias for getPivot
     *
     *    @see getPivot
     */
    public function getDoublePivot()
    {
        return $this->getPivot();
    }    //    function getDoublePivot()

    /**
     *    Is the matrix nonsingular?
     *
     *    @return true if U, and hence A, is nonsingular.
     */
    public function isNonsingular()
    {
        for ($j = 0; $j < $this->n; ++$j) {
            if ($this->LU[$j][$j] == 0) {
                return false;
            }
        }
        return true;
    }    //    function isNonsingular()

    /**
     *    Count determinants
     *
     *    @return array d matrix deterninat
     */
    public function det()
    {
        if ($this->m == $this->n) {
            $d = $this->pivsign;
            for ($j = 0; $j < $this->n; ++$j) {
                $d *= $this->LU[$j][$j];
            }
            return $d;
        } else {
            throw new PHPExcel_Calculation_Exception(PHPExcel_Shared_JAMA_Matrix::MATRIX_DIMENSION_EXCEPTION);
        }
    }    //    function det()

    /**
     *    Solve A*X = B
     *
     *    @param  $B  A Matrix with as many rows as A and any number of columns.
     *    @return  X so that L*U*X = B(piv,:)
     *    @PHPExcel_Calculation_Exception  IllegalArgumentException Matrix row dimensions must agree.
     *    @PHPExcel_Calculation_Exception  RuntimeException  Matrix is singular.
     */
    public function solve($B)
    {
        if ($B->getRowDimension() == $this->m) {
            if ($this->isNonsingular()) {
                // Copy right hand side with pivoting
                $nx = $B->getColumnDimension();
                $X  = $B->getMatrix($this->piv, 0, $nx-1);
                // Solve L*Y = B(piv,:)
                for ($k = 0; $k < $this->n; ++$k) {
                    for ($i = $k+1; $i < $this->n; ++$i) {
                        for ($j = 0; $j < $nx; ++$j) {
                            $X->A[$i][$j] -= $X->A[$k][$j] * $this->LU[$i][$k];
                        }
                    }
                }
                // Solve U*X = Y;
                for ($k = $this->n-1; $k >= 0; --$k) {
                    for ($j = 0; $j < $nx; ++$j) {
                        $X->A[$k][$j] /= $this->LU[$k][$k];
                    }
                    for ($i = 0; $i < $k; ++$i) {
                        for ($j = 0; $j < $nx; ++$j) {
                            $X->A[$i][$j] -= $X->A[$k][$j] * $this->LU[$i][$k];
                        }
                    }
                }
                return $X;
            } else {
                throw new PHPExcel_Calculation_Exception(self::MATRIX_SINGULAR_EXCEPTION);
            }
        } else {
            throw new PHPExcel_Calculation_Exception(self::MATRIX_SQUARE_EXCEPTION);
        }
    }
}
